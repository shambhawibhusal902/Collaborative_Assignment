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

        .user-name,
        .username {
            font-size: 18px;
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


        .profile-content {
            width: 90%;
            display: flex;
            justify-content: center;
            padding: 20px;
        }

        .profile-card {
            width: 90%;
            background-color: #fff;
            border-radius: var(--card-radius);
            padding: 30px;

            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .profile-header {
            text-align: center;
            font-size: 22px;
            font-weight: 500;
            margin-bottom: 20px;
            color: #333;
        }

        .profile-avatar-container {
            position: relative;
            width: 100px;
            height: 100px;
            margin: 0 auto 30px;
        }

        .profile-avatar {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background-color: #ddd;
        }

        .edit-avatar-btn {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background-color: white;
            border: 1px solid #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 12px;
            color: #555;
        }

        .form-container {
            width: 100%;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            color: #555;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        .notification-section {
            margin-bottom: 30px;
        }

        .notification-header {
            font-size: 16px;
            font-weight: 500;
            margin-bottom: 15px;
            color: #333;
        }

        .notification-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .notification-item:last-child {
            border-bottom: none;
        }

        /* Toggle Switch */
        .switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
        }

        .slider.round {
            border-radius: 34px;
        }

        .slider.round:before {
            border-radius: 50%;
        }

        input:checked+.slider {
            background-color: #00563F;
        }

        input:checked+.slider:before {
            transform: translateX(26px);
        }

        /* Buttons */
        .action-buttons {
            display: flex;
            justify-content: space-between;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 15px;
            transition: background-color 0.2s;
        }

        .btn-icon {
            margin-right: 5px;
        }

        .btn-primary {
            background-color: #00563F;
            color: white;
        }

        .btn-primary:hover {
            background-color: #27ae60;
        }

        .btn-secondary {
            background-color: #f1f1f1;
            color: #333;
        }

        .btn-secondary:hover {
            background-color: #e0e0e0;
        }

        .btn-block {
            width: 100%;
            display: block;
        }

        /* Login Modal */
        .login-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .login-container h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        .login-footer {
            margin-top: 20px;
            text-align: center;
            font-size: 14px;
        }

        .login-footer a {
            color: #2ecc71;
            text-decoration: none;
        }

        .forgot-password {
            display: block;
            text-align: right;
            margin-top: 15px;
            margin-bottom: 10px;
        }

        /* Responsive Styles */
        @media screen and (max-width: 992px) {
            .action-buttons {
                flex-direction: column;
                gap: 10px;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <button class="menu-toggle" id="menuToggle">☰</button>

    <div class="sidebar" id="sidebar">
        <div class="logo">Expenzo</div>
        <div class="user-profile">
            <div class="avatar">S</div>
            <div class="user-name">Sam</div>
        </div>
        <ul class="nav-menu">
            <li class="nav-item" onclick="window.location.href='Total_Expense.html'" style="cursor: pointer;">
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
        <div class="profile-content">
            <div class="profile-card">
                <div class="profile-header">Profile</div>

                <div class="profile-avatar-container">
                    <div class="profile-avatar"></div>
                    <button class="edit-avatar-btn">✎</button>
                </div>

                <div class="form-container">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" value="alexfinance" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" value="alex@example.com" class="form-control">
                    </div>

                    <div class="notification-section">
                        <div class="notification-header">Notification Preferences</div>

                        <div class="notification-item">
                            <span>Budget Warnings</span>
                            <label class="switch">
                                <input type="checkbox" id="budget-warnings" checked>
                                <span class="slider round"></span>
                            </label>
                        </div>

                        <div class="notification-item">
                            <span>Expense Summaries</span>
                            <label class="switch">
                                <input type="checkbox" id="expense-summaries">
                                <span class="slider round"></span>
                            </label>
                        </div>
                    </div>

                    <div class="action-buttons">
                        <button id="sign-out-btn" class="btn btn-secondary">
                            <span class="btn-icon">⟲</span> Sign Out
                        </button>
                        <button id="save-changes-btn" class="btn btn-primary">Save Changes</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Login Modal (Hidden by default) -->
        <div id="login-modal" class="login-modal">
            <div class="login-container">
                <h2>Login to Expenzo</h2>
                <div class="form-group">
                    <label for="login-username">Username</label>
                    <input type="text" id="login-username" class="form-control">
                </div>
                <div class="form-group">
                    <label for="login-password">Password</label>
                    <input type="password" id="login-password" class="form-control">
                </div>
                <button id="login-btn" class="btn btn-primary btn-block">Login</button>
                <div class="login-footer">
                    <a href="#" class="forgot-password">Forgot Password?</a>
                    <p>Don't have an account? <a href="#" class="register-link">Register</a></p>
                </div>
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
        // Wait for DOM to fully load
        document.addEventListener('DOMContentLoaded', function () {
            // Get references to elements
            const signOutBtn = document.getElementById('sign-out-btn');
            const saveChangesBtn = document.getElementById('save-changes-btn');
            const loginModal = document.getElementById('login-modal');
            const loginBtn = document.getElementById('login-btn');
            const editAvatarBtn = document.querySelector('.edit-avatar-btn');

            // Toggle switches
            const budgetWarningsToggle = document.getElementById('budget-warnings');
            const expenseSummariesToggle = document.getElementById('expense-summaries');

            // Sign Out button functionality
            signOutBtn.addEventListener('click', function () {
                // Show confirmation dialog
                if (confirm("Are you sure you want to sign out?")) {
                    // Show login modal
                    loginModal.style.display = 'flex';
                }
            });

            // Save Changes button functionality
            saveChangesBtn.addEventListener('click', function () {
                // Get form values
                const username = document.getElementById('username').value;
                const email = document.getElementById('email').value;

                // Validate inputs
                if (!username || !email) {
                    alert("Please fill in all required fields.");
                    return;
                }

                // Create data object that would be sent to server
                const userData = {
                    username: username,
                    email: email,
                    notifications: {
                        budgetWarnings: budgetWarningsToggle.checked,
                        expenseSummaries: expenseSummariesToggle.checked
                    }
                };

                // Log the data (in a real app, this would be sent to a server)
                console.log("Saving user data:", userData);

                // Show success message
                alert("Profile changes saved successfully!");
            });

            // Login button functionality
            loginBtn.addEventListener('click', function () {
                const username = document.getElementById('login-username').value;
                const password = document.getElementById('login-password').value;

                // Basic validation
                if (!username || !password) {
                    alert("Please enter both username and password.");
                    return;
                }

                // Hide the login modal (simulating successful login)
                loginModal.style.display = 'none';

                // Update the profile with the new username (just for demo)
                document.getElementById('username').value = username;
            });

            // Edit avatar functionality
            editAvatarBtn.addEventListener('click', function () {
                // In a real app, this would open a file picker
                alert("This would open an avatar upload dialog in a real application.");
            });

            // Close login modal if user clicks outside of it
            window.addEventListener('click', function (event) {
                if (event.target === loginModal) {
                    loginModal.style.display = 'none';
                }
            });

            // Handle links in the login modal
            document.querySelector('.forgot-password').addEventListener('click', function (e) {
                e.preventDefault();
                alert("This would navigate to password recovery in a real application.");
            });

            document.querySelector('.register-link').addEventListener('click', function (e) {
                e.preventDefault();
                alert("This would navigate to registration in a real application.");
            });
        });
    </script>
</body>

</html>