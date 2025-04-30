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

            /* Spacing */
            --space-xs: 5px;
            --space-sm: 10px;
            --space-md: 15px;
            --space-lg: 20px;
            --space-xl: 30px;

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
            background-color: #444;
            margin-right: var(--space-md);
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-weight: bold;
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
            padding-bottom: var(--space-sm);
        }

        /* Cards styles */
        .cards-container {
            display: flex;
            gap: var(--space-lg);
            margin-bottom: var(--space-xl);
        }

        .card {
            flex: 1;
            background-color: var(--white);
            border-radius: var(--card-radius);
            padding: var(--space-lg) var(--space-xl);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            min-height: 150px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            margin-left: 25px;
            width: 100%;

        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .card-income {
            background-color: var(--primary-color);
            color: var(--white);

        }

        .card-expense {
            border-left: 4px solid var(--primary-color);
        }

        .card-savings {
            border-left: 4px solid var(--primary-color);
        }

        .card-title {
            font-size: 12px;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: var(--space-sm);
            color: inherit;
            opacity: 0.8;
        }

        .card-amount {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: var(--space-md);
        }

        .card-stats {
            display: flex;
            align-items: center;
            font-size: 12px;
            margin-top: auto;
        }

        .stats-up {
            color: var(--stats-down);
        }

        .stats-down {
            color: var(--stats-up);
        }

        /* Chart container styles */
        .chart-container {
            background-color: var(--white);
            border-radius: var(--card-radius);
            padding: var(--space-lg) var(--space-xl);
            margin-bottom: var(--space-xl);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border-light);
            width: 101%;
            margin-left: 20px;
            margin-top: 50px;
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

        /* Bar chart styles */
        .bar-chart-container {
            position: relative;
            height: 350px;
            margin-top: var(--space-lg);
            padding-bottom: var(--space-xl);
            padding-top: 10px;
        }

        .bar-chart-grid {
            position: absolute;
            width: 100%;
            height: 300px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            left: 30px;
            z-index: 1;

        }

        .grid-line {
            width: 95%;
            height: 1px;
            background-color: var(--border-light);
            position: relative;
        }


        .grid-line-label {
            position: absolute;
            left: -28px;
            /* Move it slightly inside */
            top: -9px;
            font-size: 12px;
            color: var(--text-light);
        }



        .bar-chart {
            position: relative;
            display: flex;
            height: 300px;
            align-items: flex-end;
            justify-content: space-evenly;
            padding: 0 var(--space-sm);
            z-index: 2;
        }

        .bar {
            width: 50px;
            background-color: var(--primary-color);
            border-radius: 4px 4px 0 0;
            position: relative;
            transition: height 0.3s ease;
        }

        .bar:hover {
            opacity: 0.9;
        }

        .bar-label {
            position: absolute;
            bottom: -25px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 12px;
            color: var(--text-light);
            white-space: nowrap;
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
            /* Increased height */
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

        .green-color {
            background-color: #8BC34A;
        }

        .dark-green-color {
            background-color: var(--primary-color);
        }

        .blue-color {
            background-color: var(--secondary-color);
        }

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

            .bar {
                width: 60px;
            }
        }

        /* Responsive styles */
        @media (max-width: 1280px) {
            .main-content {
                padding: var(--space-lg);
            }

            .bar {
                width: 45px;
            }
        }

        @media (max-width: 992px) {
            .menu-toggle {
                display: block;
            }

            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                padding: var(--space-lg);
                padding-top: 60px;
                max-width: 100%;
            }

            .cards-container {
                flex-direction: column;
                gap: var(--space-md);
            }

            .card {
                width: 100%;
                min-height: 120px;
                margin-bottom: var(--space-sm);
            }
        }

        @media (max-width: 768px) {
            .pie-chart-container {
                flex-direction: column;
            }

            .bar {
                width: 40px;
            }

            .chart-container {
                padding: var(--space-md);
            }

            .bar-chart {
                justify-content: space-between;
            }
        }
    </style>
</head>

<body>
    <button class="menu-toggle" id="menuToggle">☰</button>

    <div class="sidebar" id="sidebar">
        <div class="logo">Expenzo</div>
        <div class="user-profile">
            <div class="avatar" onclick="window.location.href='Profile_Page.html'" style="cursor: pointer;">S</div>
            <div class="user-name" onclick="window.location.href='Profile_Page.html'" style="cursor: pointer;">Sam</div>
        </div>
        <ul class="nav-menu">
            <li class="nav-item active">
                <span class="nav-icon">📊</span>
                Dashboard
            </li>
            <li class="nav-item" onclick="window.location.href='Add_Expense_Page.html'" style="cursor: pointer;">
                <span class="nav-icon">💸</span>
                Expense
            </li>
            <li class="nav-item" onclick="window.location.href='Add_Income_Page.html'" style="cursor: pointer;">
                <span class="nav-icon">💰</span>
                Income
            </li>
            <li class="nav-item" onclick="window.location.href='Budgets_Page.html'" style="cursor: pointer;">
                <span class="nav-icon">💹</span>
                Budgets
            </li>
            <li class="nav-item" onclick="window.location.href='View_Transactions_Page.html'" style="cursor: pointer;">
                <span class="nav-icon">📋</span>
                View Transactions
            </li>

        </ul>
    </div>

    <main class="main-content">
        <header class="header"></header>

        <div class="cards-container">
            <div class="card card-income">
                <div class="card-title">TOTAL INCOME</div>
                <div class="card-amount">Rs 50,000</div>
                <div class="card-stats">
                    <span class="stats-up">↗ 15% vs last 30 days</span>
                </div>
            </div>

            <div class="card card-expense" onclick="window.location.href='Total_Expense.html'" style="cursor: pointer;">
                <div class="card-title">TOTAL EXPENSE</div>
                <div class="card-amount">Rs 25,000</div>
                <div class="card-stats">
                    <span class="stats-down">↘ 3% vs last 30 days</span>
                </div>
            </div>

            <div class="card card-savings" onclick="window.location.href='Total_Savings.html'" style="cursor: pointer;">
                <div class="card-title">TOTAL SAVINGS</div>
                <div class="card-amount">Rs 10,000</div>
                <div class="card-stats">
                    <span class="stats-down">↗ 5% vs last 30 days</span>
                </div>
            </div>
        </div>

        <section class="chart-container">
            <div class="chart-title">Top 5 Income Source</div>
            <div class="bar-chart-container">
                <div class="bar-chart-grid">
                    <div class="grid-line"><span class="grid-line-label">10k</span></div>
                    <div class="grid-line"><span class="grid-line-label">8k</span></div>
                    <div class="grid-line"><span class="grid-line-label">6k</span></div>
                    <div class="grid-line"><span class="grid-line-label">4k</span></div>
                    <div class="grid-line"><span class="grid-line-label">2k</span></div>
                    <div class="grid-line"><span class="grid-line-label">0</span></div>
                </div>
                <div class="bar-chart">
                    <div class="bar" style="height: 200px;">
                        <div class="bar-label">Side hustles</div>
                    </div>
                    <div class="bar" style="height: 100px;">
                        <div class="bar-label">Investments</div>
                    </div>
                    <div class="bar" style="height: 150px;">
                        <div class="bar-label">Business Revenue</div>
                    </div>
                    <div class="bar" style="height: 300px;">
                        <div class="bar-label">Salary</div>
                    </div>
                    <div class="bar" style="height: 180px;">
                        <div class="bar-label">Others</div>
                    </div>
                </div>
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

            // Handle menu toggle for mobile
            menuToggle.addEventListener('click', function () {
                sidebar.classList.toggle('active');
            });

            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function (event) {
                if (window.innerWidth <= 992 &&
                    !sidebar.contains(event.target) &&
                    !menuToggle.contains(event.target) &&
                    sidebar.classList.contains('active')) {
                    sidebar.classList.remove('active');
                }
            });

            // Navigation functionality
            document.querySelectorAll('.nav-item').forEach(item => {
                item.addEventListener('click', function () {
                    document.querySelectorAll('.nav-item').forEach(navItem => {
                        navItem.classList.remove('active');
                    });
                    this.classList.add('active');

                    // Close sidebar on mobile after navigation
                    if (window.innerWidth <= 992) {
                        sidebar.classList.remove('active');
                    }
                });
            });

            // Draw pie chart
            drawPieChart();

            // Handle window resize events for responsive adjustments
            window.addEventListener('resize', function () {
                drawPieChart();
            });
        });

        // Function to draw pie chart
        function drawPieChart() {
            const canvas = document.getElementById('pieChart');
            const ctx = canvas.getContext('2d');

            // Set canvas dimensions with higher resolution for sharper rendering
            canvas.width = 600;
            canvas.height = 600;
            canvas.style.width = '300px';
            canvas.style.height = '300px';
            ctx.scale(2, 2);

            // Data for pie chart
            const data = [
                { value: 59, color: '#93C572' }, // Income - Light green
                { value: 29, color: '#00563F' }, // Expense - Dark green
                { value: 12, color: '#2A2C2C' }  // Savings - Blue
            ];


            // Draw pie chart
            let total = data.reduce((sum, item) => sum + item.value, 0);
            let startAngle = -0.5 * Math.PI; // Start from top

            const centerX = 150;
            const centerY = 150;
            const radius = 120;

            // Clear canvas first
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            // Draw pie slices
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

            // Draw center circle (white)
            ctx.beginPath();
            ctx.arc(centerX, centerY, radius * 0.4, 0, 2 * Math.PI);
            ctx.fillStyle = 'white';
            ctx.fill();

            // Add percentage labels on the pie
            ctx.font = 'bold 16px Arial';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';

            // Income label (59%)
            ctx.fillStyle = 'white';
            ctx.fillText('59%', centerX + 75, centerY - 12);

            // Expense label (29%)
            ctx.fillStyle = 'white';
            ctx.fillText('29%', centerX - 70, centerY + 12);

            // Savings label (12%)
            ctx.fillStyle = '#white';
            ctx.fillText('12%', centerX - 22, centerY - 60);
        }
    </script>
</body>

</html>