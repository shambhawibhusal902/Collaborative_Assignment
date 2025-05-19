<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// --- Database Connection ---
$host = '127.0.0.1';        
$dbname = 'collaborative_db'; //database name
$username_db = 'root';      
$password_db = '';     
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $username_db, $password_db, $options);
} catch (\PDOException $e) {
    error_log("Database Connection Error: " . $e->getMessage());
    // For a user-facing page, provide a less technical error message
    die("We're having trouble connecting to our services. Please try again later.");
}
// --- End Database Connection ---


// --- User Session Data (Ensure these are set on login) ---
if (!isset($_SESSION['user_id'])) {
    // This is a fallback for testing. In a real app, you'd redirect to login.
    $_SESSION['user_id'] = 1; // Example: Default to user 1
    $_SESSION['username'] = 'Sam'; // Example: Default username
    // For a live site, uncomment the line below and remove the defaults:
    // die("User not logged in. Please <a href='login.php'>login</a>.");
}

$user_id = $_SESSION['user_id'];
$user_name_display = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'User';
$user_initial_display = !empty($user_name_display) ? strtoupper(substr($user_name_display, 0, 1)) : 'U';


// --- Fetch Total Allocated Budget for Current Month ---
$total_budget_allocated_current_month = 0;
try {
    // This query sums up all budget amounts for the current user for the current month.
    // It assumes 'budgets.date' represents the month the budget applies to.
    $sql_total_budget = "SELECT
                            COALESCE(SUM(b.amount), 0) AS total_budget
                        FROM
                            budgets b
                        WHERE
                            b.user_id = :user_id
                            AND MONTH(b.date) = MONTH(CURDATE())
                            AND YEAR(b.date) = YEAR(CURDATE())";
    $stmt_total_budget = $pdo->prepare($sql_total_budget);
    $stmt_total_budget->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt_total_budget->execute();
    $result_total_budget = $stmt_total_budget->fetch(PDO::FETCH_ASSOC);
    if ($result_total_budget) {
        $total_budget_allocated_current_month = (float)$result_total_budget['total_budget'];
    }
} catch (PDOException $e) {
    error_log("Error fetching total budget for current month: " . $e->getMessage());
    // You might want to display a friendly error or set a default value
}


// --- Fetch Budget Overview Data for Current Month ---
$budgets_overview_data = [];
try {
    // This query gets each budget for the current month, its allocated amount,
    // and sums up expenses in the same category and for the same month/year as the budget.
    $sql_budgets_overview = "SELECT
                                b.category AS budget_category,
                                b.amount AS budget_allocated,
                                b.date AS budget_date, -- Keep budget date for matching expenses
                                COALESCE(SUM(e.amount), 0) AS total_spent
                            FROM
                                budgets b
                            LEFT JOIN
                                expenses e ON b.user_id = e.user_id
                                           AND b.category = e.category
                                           AND MONTH(e.date) = MONTH(b.date) -- Match expenses to the budget's month
                                           AND YEAR(e.date) = YEAR(b.date)   -- Match expenses to the budget's year
                            WHERE
                                b.user_id = :user_id
                                AND MONTH(b.date) = MONTH(CURDATE()) -- Budgets for the current calendar month
                                AND YEAR(b.date) = YEAR(CURDATE())   -- Budgets for the current calendar year
                            GROUP BY
                                b.id, b.category, b.amount, b.date -- Group by budget details
                            ORDER BY
                                b.category;";
    $stmt_budgets_overview = $pdo->prepare($sql_budgets_overview);
    $stmt_budgets_overview->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt_budgets_overview->execute();
    $budgets_overview_data = $stmt_budgets_overview->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Error fetching budget overview data: " . $e->getMessage());
    
}

// --- Other Dashboard Data (Total Income, Total Expense - assuming you have similar logic for these) ---
// fetching these from the database similarly
$total_income_display = "Rs 50,000";
$total_expense_display = "Rs 25,000";


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expenzo - Expense Tracker</title>
    <style>
        /* CSS Variables for consistent styling */
        :root {
            /* Colors */
            --primary-color: #00563F;
            --primary-light: #93C572;
            --secondary-color: #2A2C2C;
            --dark-bg: #222;
            --light-bg: #f5f5f5;
            --white: #ffffff;
            --border-light: #e0e0e0;
            --text-dark: #333;
            --text-light: #777;
            --stats-up: #00563F;
            --stats-down: #FFFFFF; 
            --card-bg: var(--white); 
            --progress-bar-bg: #e9ecef; 

            /* Spacing */
            --space-xs: 5px;
            --space-sm: 10px;
            --space-md: 15px;
            --space-lg: 20px;
            --space-xl: 30px;
            --space-xxl: 30px; 

            /* Elements */
            --card-radius: 8px;
            --sidebar-width: 250px;
            --avatar-size: 40px;
        }

        /* Reset and base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            display: flex;
            background-color: var(--light-bg);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Sidebar styles - keeping black navigation */
        .sidebar {
            width: var(--sidebar-width);
            background-color: var(--dark-bg);
            color: var(--white);
            height: 100vh;
            padding: var(--space-lg);
            position: fixed;
            left: 0;
            top: 0;
            z-index: 10;
            transition: transform 0.3s ease;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: var(--space-xl);
            padding-bottom: var(--space-sm);
            border-bottom: 1px solid #444;
        }

        .user-profile {
            display: flex;
            align-items: center;
            margin-bottom: var(--space-xl);
        }

        .avatar {
            width: var(--avatar-size);
            height: var(--avatar-size);
            border-radius: 50%;
            background-color: #444; /* Fallback, or specific color */
            margin-right: var(--space-md);
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-weight: bold;
        }

        .user-name { /* Added style for username */
            color: var(--white);
        }

        .nav-menu {
            list-style: none;
        }

        .nav-item {
            display: flex;
            align-items: center;
            padding: var(--space-lg) 0;
            cursor: pointer;
            transition: all 0.3s;
        }

        .nav-item:hover {
            color: var(--primary-light);
        }

        .nav-item.active {
            color: var(--primary-light);
            font-weight: bold;
        }

        .nav-icon {
            margin-right: var(--space-md);
            width: 20px;
            text-align: center;
        }

        /* Main content styles - optimized for 1440x1024 and responsive */
        .main-content {
            flex: 1;
            padding: var(--space-lg) var(--space-xl);
            margin-left: var(--sidebar-width);
            max-width: calc(1440px - var(--sidebar-width));
            width: 100%;
        }

        .header {
            margin-bottom: 30px;
            border-bottom: 1px solid var(--border-light);
            padding-bottom: var(--space-md);
        }

        /* Cards styles */
        .cards-container {
            display: flex;
            gap: var(--space-lg);
            margin-bottom: var(--space-xl);
        }

        .card {
            flex: 1;
            background-color: var(--card-bg);
            border-radius: var(--card-radius);
            padding: var(--space-lg) var(--space-xl);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            min-height: 150px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            /* margin-bottom: 25px; Removed, handled by container gap */
            /* margin-left: 25px; Removed, handled by container gap */
            width: 100%;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .card:not(:first-child) { /* Add margin to cards except the first one if needed */
            /* margin-left: var(--space-lg); */ /* Example if you need specific spacing */
        }


        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .card-income {
            border-left: 4px solid var(--primary-color);
        }

        .card-expense {
            border-left: 4px solid var(--primary-color);
        }

        .card-budgets { /* Changed from card-savings */
            background-color: var(--primary-color) !important; /* #00563F Dark green */
            color: var(--white);
        }
        .card-budgets .card-title,
        .card-budgets .card-amount,
        .card-budgets .card-stats,
        .card-budgets .stats-down {
            color: var(--white); 
        }


        .card-title {
            font-size: 12px;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: var(--space-sm);
            color: inherit; /* Inherits from parent, useful for .card-budgets */
            opacity: 0.8;
        }

        .card-amount {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: var(--space-md);
            color: inherit;
        }

        .card-stats {
            display: flex;
            align-items: center;
            font-size: 12px;
            margin-top: auto;
            color: inherit; 
        }

        .stats-up {
            color: var(--stats-up); /* Green for positive stats on light cards */
        }

        
        /* Details Row (Budget Overview) */
        .details-row {
            display: grid; 
            grid-template-columns: 1fr; 
            gap: var(--space-xl);
            margin-bottom: var(--space-xxl);
        }

        .budget-overview { 
            background-color: var(--white);
            border-radius: var(--card-radius);
            padding: var(--space-lg);
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }


        .budget-overview h2 {
            font-size: 1.2em;
            font-weight: 600;
            margin-bottom: var(--space-lg);
            color: var(--text-dark);
        }

        /* Progress Bar Styles */
        .progress-item {
            margin-bottom: 18px;
        }
        .progress-item:last-child {
            margin-bottom: 0; /* Remove bottom margin from the last item */
        }

        .progress-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .progress-info label {
            display: block;
            font-size: 0.9em;
            color: var(--text-medium, #555); /* Fallback color */
            margin-bottom: 5px;
            text-transform: capitalize;
        }

        .progress-info span {
            font-size: 0.8em;
            color: var(--text-light, #777); /* Fallback color */
        }

        .progress-bar-container {
            width: 100%;
            background-color: var(--progress-bar-bg);
            border-radius: 5px;
            height: 10px;
            overflow: hidden;
            /* margin-bottom: 20px; Should be on progress-item if needed */
        }

        .progress-bar {
            height: 100%;
            background-color: var(--primary-color); /* Dark Green for budget progress */
            border-radius: 5px;
            transition: width 0.5s ease-in-out;
        }

        /* Chart container styles */
        .chart-container {
            background-color: var(--white);
            border-radius: var(--card-radius);
            padding: var(--space-lg) var(--space-xl);
            margin-bottom: var(--space-xl);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border-light);
            width: 100%;
            /* margin-left: 25px; Removed, let main content padding handle it */
        }

        .chart-container:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .chart-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: var(--space-xl);
        }

        /* Pie chart styles */
        .pie-chart-container {
            display: flex;
            margin-top: var(--space-lg);
            align-items: center;
            justify-content: center;
            gap: var(--space-lg);
            flex-wrap: wrap;
            height: 300px;
            width: 100%;
        }

        .pie-chart {
            width: 360px; 
            height: 360px;
            position: relative;
        }

        .legend {
            padding: var(--space-lg);
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-width: 150px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            margin-bottom: var(--space-sm);
        }

        .legend-color {
            width: 15px;
            height: 15px;
            border-radius: 3px;
            margin-right: var(--space-sm);
        }

        .green-color { background-color: #93C572; } /* Corresponds to --primary-light */
        .dark-green-color { background-color: var(--primary-color); }
        .blue-color { background-color: var(--secondary-color); }


        /* Hamburger menu */
        .menu-toggle {
            display: none;
            position: fixed;
            top: var(--space-sm);
            left: var(--space-sm);
            z-index: 20;
            background-color: var(--primary-color);
            color: var(--white);
            border: none;
            border-radius: 4px;
            width: 40px;
            height: 40px;
            font-size: 20px;
            cursor: pointer;
        }

        /* Specific optimizations for 1440x1024 and similar screens */
        @media (min-width: 1440px) {
            .main-content {
                padding: var(--space-xl) var(--space-xl) var(--space-xl) calc(var(--space-xl) * 1.5);
                margin: 0 auto 0 var(--sidebar-width); 
            }
            .cards-container {
                gap: var(--space-xl); 
            }
            .card {
                padding: var(--space-xl);
                min-height: 180px;
            }
            .chart-container {
                padding: var(--space-xl);
            }
        }

        /* Responsive styles */
        @media (max-width: 1280px) {
            .main-content {
                padding: var(--space-lg);
            }
            .cards-container {
                flex-direction: row; /* Keep cards in a row for tablets */
                flex-wrap: wrap; /* Allow wrapping if they don't fit */
            }
            .card {
                 /* Allow cards to take up half width on medium screens, minus gap */
                flex-basis: calc(50% - var(--space-lg) / 2);
            }
            .card:nth-child(3) { /* Make the third card full width if it wraps */
                 flex-basis: 100%;
            }
        }


        @media (max-width: 992px) { /* Tablet and smaller */
            .menu-toggle { display: block; }
            .sidebar { transform: translateX(-100%); }
            .sidebar.active { transform: translateX(0); }
            .main-content {
                margin-left: 0;
                padding: var(--space-lg);
                padding-top: 60px; /* Account for menu toggle */
                max-width: 100%;
            }
            .cards-container {
                flex-direction: column; /* Stack cards vertically */
                gap: var(--space-md);
            }
            .card {
                width: 100%; /* Full width for stacked cards */
                min-height: 120px;
                margin-bottom: var(--space-sm); /* Spacing between stacked cards */
                flex-basis: auto; /* Reset flex-basis */
            }
        }

        @media (max-width: 768px) { /* Mobile */
            .pie-chart-container {
                flex-direction: column; /* Stack chart and legend */
            }
            .chart-container {
                padding: var(--space-md);
            }
            .details-row {
                grid-template-columns: 1fr; /* Ensure single column for budget overview */
            }
        }
         @media (max-width: 480px) { /* Small Mobile */
            .progress-info label { font-size: 0.85em; }
            .progress-info span { font-size: 0.75em; }
        }
    </style>
</head>

<body>
    <button class="menu-toggle" id="menuToggle">☰</button>

    <div class="sidebar" id="sidebar">
        <div class="logo">Expenzo</div>
        <div class="user-profile">
            <div class="avatar" onclick="window.location.href='Profile.php'" style="cursor: pointer;"><?php echo $user_initial_display; ?></div>
            <div class="user-name" onclick="window.location.href='Profile.php'" style="cursor: pointer;"><?php echo $user_name_display; ?></div>
        </div>
        <ul class="nav-menu">
            <li class="nav-item active" onclick="window.location.href='Dashboard.php';"> <!-- Make Dashboard link work -->
                <span class="nav-icon">📊</span>
                Dashboard
            </li>
            <li class="nav-item" onclick="window.location.href='Add_Expense.php'">
                <span class="nav-icon">💸</span>
                Expense
            </li>
            <li class="nav-item" onclick="window.location.href='Add_Income.php'">
                <span class="nav-icon">💰</span>
                Income
            </li>
            <li class="nav-item" onclick="window.location.href='Budgets.php'">
                <span class="nav-icon">💹</span>
                Budgets
            </li>
            <li class="nav-item" onclick="window.location.href='View_Transactions.php'">
                <span class="nav-icon">📋</span>
                View Transactions
            </li>
        </ul>
    </div>

    <main class="main-content">
        <header class="header">
             <!-- You can add a page title here if needed, e.g., <h1>Dashboard</h1> -->
        </header>

        <div class="cards-container">
            <div class="card card-income" onclick="window.location.href='Total_Income.php'" style="cursor: pointer;">
                <div class="card-title">TOTAL INCOME</div>
                <div class="card-amount"><?php echo htmlspecialchars($total_income_display); // Replace with dynamic data ?></div>
                <div class="card-stats">
                    <span class="stats-up">↗ 15% vs last 30 days</span> 
                </div>
            </div>

            <div class="card card-expense" onclick="window.location.href='Total_Expense.php'" style="cursor: pointer;">
                <div class="card-title">TOTAL EXPENSE</div>
                <div class="card-amount"><?php echo htmlspecialchars($total_expense_display); // Replace with dynamic data ?></div>
                <div class="card-stats">
                    <span class="stats-up">↘ 3% vs last 30 days</span>
                </div>
            </div>

            <div class="card card-budgets"> <!-- Changed class from card-savings -->
                <div class="card-title">TOTAL BUDGETS (Current Month)</div>
                <div class="card-amount">Rs <?php echo number_format($total_budget_allocated_current_month, 0); ?></div>
                <div class="card-stats">
                    <!-- This stat might need to be rethought for "Total Budgets" or made dynamic -->
                    <span class="stats-down">Overview of monthly limits</span>
                </div>
            </div>
        </div>

        <!-- Middle Row: Budget Overview -->
        <section class="details-row">
            <div class="budget-overview card"> <!-- Added .card class for consistent styling -->
                <h2>Budget Overview (Current Month)</h2>
                <?php if (empty($budgets_overview_data)): ?>
                    <p>No budgets set for the current month, or no expenses recorded yet.</p>
                <?php else: ?>
                    <?php foreach ($budgets_overview_data as $budget): ?>
                        <?php
                            $spent = (float)$budget['total_spent'];
                            $allocated = (float)$budget['budget_allocated'];
                            $percentage_spent = 0;
                            if ($allocated > 0) {
                                $percentage_spent = ($spent / $allocated) * 100;
                            } elseif ($spent > 0) { // Spent something with 0 budget
                                $percentage_spent = 100; // Or handle as error/overbudget
                            }
                            $percentage_spent_display = round(min($percentage_spent, 100)); // Cap at 100 for bar
                        ?>
                        <div class="progress-item">
                            <div class="progress-info">
                                <label><?php echo htmlspecialchars($budget['budget_category']); ?></label>
                                <span>Rs <?php echo number_format($spent, 0); ?> spent of <?php echo number_format($allocated, 0); ?></span>
                            </div>
                            <div class="progress-bar-container">
                                <div class="progress-bar" style="width: <?php echo $percentage_spent_display; ?>%;"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

        <section class="chart-container">
            <div class="chart-title">Report Overview</div>
            <div class="pie-chart-container">
                <canvas id="pieChart" class="pie-chart"></canvas>
                <div class="legend">
                    <div class="legend-item">
                        <div class="legend-color green-color"></div>
                        <div>Income (59%)</div>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color dark-green-color"></div>
                        <div>Expense (29%)</div>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color blue-color"></div>
                        <div>Savings (12%)</div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script>
    // Menu toggle functionality
    document.addEventListener('DOMContentLoaded', function () {
        const menuToggle = document.getElementById('menuToggle');
        const sidebar = document.getElementById('sidebar');
        const navItems = document.querySelectorAll('.nav-item'); // Get all nav items

        // Handle menu toggle for mobile
        if (menuToggle && sidebar) {
            menuToggle.addEventListener('click', function () {
                sidebar.classList.toggle('active');
            });
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function (event) {
            if (window.innerWidth <= 992 &&
                sidebar && sidebar.classList.contains('active') &&
                menuToggle && !sidebar.contains(event.target) &&
                !menuToggle.contains(event.target)) {
                sidebar.classList.remove('active');
            }
        });

        // Navigation functionality
        navItems.forEach(item => {
            item.addEventListener('click', function (event) {
                // If onclick attribute is present, let it handle navigation
                if (this.getAttribute('onclick')) {
                    return;
                }

                // Close sidebar on mobile after navigation
                if (window.innerWidth <= 992 && sidebar && sidebar.classList.contains('active')) {
                    sidebar.classList.remove('active');
                }
            });
        });

        // Draw pie chart
        if (document.getElementById('pieChart')) {
            drawPieChart();
        }

        // Handle window resize events for responsive adjustments
        window.addEventListener('resize', function () {
            if (document.getElementById('pieChart')) {
                drawPieChart();
            }
        });
    });

    // Function to draw pie chart (remains static as requested)
    function drawPieChart() {
        const canvas = document.getElementById('pieChart');
        if (!canvas) return; // Ensure canvas exists
        const ctx = canvas.getContext('2d');

        canvas.width = 600; // For HiDPI rendering
        canvas.height = 600;
        canvas.style.width = '300px';  
        canvas.style.height = '300px'; 
        ctx.scale(2, 2); 

        const data = [
            { value: 59, color: '#93C572' }, // Income
            { value: 29, color: '#00563F' }, // Expense
            { value: 12, color: '#2A2C2C' }  // Savings
        ];

        let total = data.reduce((sum, item) => sum + item.value, 0);
        let startAngle = -0.5 * Math.PI; // Start from top

        const centerX = 150; 
        const centerY = 150; 
        const radius = 120;  

        ctx.clearRect(0, 0, canvas.width, canvas.height);

        data.forEach(item => {
            const sliceAngle = (item.value / total) * 2 * Math.PI;
            const endAngle = startAngle + sliceAngle;

            ctx.beginPath();
            ctx.moveTo(centerX, centerY);
            ctx.arc(centerX, centerY, radius, startAngle, endAngle);
            ctx.closePath();
            ctx.fillStyle = item.color;
            ctx.fill();
            startAngle = endAngle;
        });

        // Draw center circle (donut hole)
        ctx.beginPath();
        ctx.arc(centerX, centerY, radius * 0.4, 0, 2 * Math.PI);
        ctx.fillStyle = 'white'; 
        ctx.fill();

        // Your existing code for labels:
        ctx.font = 'bold 16px Arial'; 
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';

        // Recalculate label positions based on angles for better accuracy
        startAngle = -0.5 * Math.PI; // Reset startAngle
        data.forEach(item => {
            const sliceAngle = (item.value / total) * 2 * Math.PI;
            const angle = startAngle + sliceAngle / 2; // Midpoint of the slice

            // Position text - adjust factor (e.g., radius * 0.7) for placement
            const textX = centerX + Math.cos(angle) * radius * 0.7;
            const textY = centerY + Math.sin(angle) * radius * 0.7;
            ctx.fillStyle = 'white'; 

            ctx.fillText(item.value + '%', textX, textY);
            startAngle += sliceAngle;
        });
    }
    </script>
</body>
</html>