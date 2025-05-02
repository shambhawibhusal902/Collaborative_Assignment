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

        /* Main content styles - just for header */
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

        /* Responsive styles */
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
        }

        /* Savings and Budgets styles */
        .savings-and-budgets {
            padding: 5px;


        }

        .savings-and-budgets h2 {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .savings-and-budgets h2 i {
            margin-right: 10px;
            color: #007bff;
        }

        .new-button {
            margin-left: auto;
            background-color: #00563F;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            margin-right: 100px;
            height: 35px;
            /* Added explicit height */
            width: 80px;
            font-size: 18px;


        }

        .new-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 5px rgba(0, 0, 0, 0.1);
        }

        /* Budget card styles */
        .budget-card {
            background: #ffffff;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            width: 95%;

        }

        .budget-card h3 {
            margin-top: 0;
            margin-bottom: 10px;
        }

        .budget-card p {
            margin: 5px 0;
        }

        .progress-bar {
            background: #e0e0e0;
            border-radius: 5px;
            overflow: hidden;
            margin: 10px 0;
            height: 10px;
        }

        .progress-fill {
            height: 100%;
            background: #00563F;
        }

        .status {
            color: #888;
            font-size: 0.9em;
        }

        .budget-cards-container {
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <button class="menu-toggle" id="menuToggle">☰</button>

    <div class="sidebar" id="sidebar">
        <div class="logo">Expenzo</div>
        <div class="user-profile">
            <div class="avatar" onclick="window.location.href='Profile.php'" style="cursor: pointer;">S</div>
            <div class="user-name" onclick="window.location.href='Profile.php'" style="cursor: pointer;">Sam</div>
        </div>
        <ul class="nav-menu">
            <li class="nav-item" onclick="window.location.href='Total_Expense.php'" style="cursor: pointer;">
                <span class="nav-icon">📊</span>
                Dashboard
            </li>
            <li class="nav-item" onclick="window.location.href='Add_Expense.php'" style="cursor: pointer;">
                <span class="nav-icon">💸</span>
                Expense
            </li>
            <li class="nav-item" onclick="window.location.href='Add_Income.php'" style="cursor: pointer;">
                <span class="nav-icon">💰</span>
                Income
            </li>
            <li class="nav-item active">
                <span class="nav-icon">💹</span>
                Budgets
            </li>
            <li class="nav-item" onclick="window.location.href='View_Transactions.php'" style="cursor: pointer;">
                <span class="nav-icon">📋</span>
                View Transactions
            </li>

        </ul>
    </div>

    <main class="main-content">
        <header class="header"></header>
        <div class="savings-and-budgets">
            <h2>
                💹 Budgets
                <button class="new-button">New</button>
            </h2>
            <div class="budget-cards-container" id="budget-cards">
                <!-- Budget cards will be inserted here by JavaScript -->
            </div>
        </div>
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
        });
        // Budget data (equivalent to the data in SavingsAndBudgets.js)
        const budgets = [
            { name: 'Food Budget', total: 500, spent: 253.75, remaining: 246.25, status: 'Monthly (Active)' },
            { name: 'Entertainment Budget', total: 200, spent: 51.25, remaining: 148.75, status: 'Monthly (Active)' },
            { name: 'Transaction Budget', total: 150, spent: 45.25, remaining: 104.75, status: 'Monthly (Active)' },
            { name: 'Food Budget', total: 300, spent: 132.20, remaining: 167.80, status: 'Monthly (Active)' }
        ];

        // Function to create budget cards (equivalent to the BudgetCard component)
        function createBudgetCard(budget, index) {
            const progress = (budget.spent / budget.total) * 100;

            const budgetCard = document.createElement('div');
            budgetCard.className = 'budget-card';

            budgetCard.innerHTML = `
        <h3>${budget.name}</h3>
        <p>Total: $${budget.total}</p>
        <p>Spent: $${budget.spent.toFixed(2)}</p>
        <p>Remaining: $${budget.remaining.toFixed(2)}</p>
        <div class="progress-bar">
          <div class="progress-fill" style="width: ${progress}%"></div>
        </div>
        <p class="status">${budget.status}</p>
      `;

            return budgetCard;
        }

        // Populate budget cards
        const budgetCardsContainer = document.getElementById('budget-cards');

        budgets.forEach((budget, index) => {
            const budgetCard = createBudgetCard(budget, index);
            budgetCardsContainer.appendChild(budgetCard);
        });
    </script>
</body>

</html>