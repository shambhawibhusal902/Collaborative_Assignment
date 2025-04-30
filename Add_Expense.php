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

        .dashboard-container {
            display: flex;
            width: 100%;
            justify-content: space-between;
        }

        /* Add Expense Styles */
        .add-expense {
            padding: 20px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
            width: 51%;
            margin-top: 10px;
            height: 550px;
            margin-top: 10px;
            margin-left: 30px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .add-expense h2 {
            margin-top: 0;
            margin-bottom: 25px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }

        .add-expense form {
            display: flex;
            flex-direction: column;
        }

        .add-expense label {
            margin-bottom: 15px;
            display: flex;
            flex-direction: column;
        }

        .add-expense input,
        .add-expense select,
        .add-expense textarea {
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .add-expense button {
            padding: 10px;
            background-color: #00563F;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px;
        }

        .add-expense button:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 5px rgba(0, 0, 0, 0.1);
        }

        /* Expense List Styles - INCREASED WIDTH */
        .expense-list {
            padding: 20px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
            width: 45%;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            height: 550px;
            margin-right: -35px;
            margin-top: 10px;
            overflow-y: auto;
        }

        .expense-list h2 {
            margin-top: 0;
            margin-bottom: 30px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }

        .expense-list ul {
            list-style: none;
            padding: 0;
        }

        .expense-list li {
            padding: 28px 0;
            border-bottom: 1px solid #ddd;
            display: flex;
            flex-wrap: wrap;
        }

        .expense-list .category {
            font-weight: bold;
            margin-right: 5px;
        }

        .expense-list .description {
            color: #333;
        }

        .expense-list .date {
            color: #888;
            margin-left: auto;
            margin-right: 10px;
        }

        .expense-list .amount {
            font-weight: bold;
        }

        /* Success message style */
        .success-message {
            background-color: var(--primary-light);
            color: var(--dark-bg);
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            display: none;
        }

        /* Delete button style */
        .delete-btn {
            margin-left: 10px;
            color: #ff5252;
            cursor: pointer;
        }

        /* Filter controls */
        .filter-controls {
            display: flex;
            margin-bottom: 15px;
            gap: 10px;
        }

        .filter-controls select {
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ddd;
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
            <li class="nav-item" onclick="window.location.href='Total_Expense.html'" style="cursor: pointer;">
                <span class="nav-icon">📊</span>
                Dashboard
            </li>
            <li class="nav-item active">
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
        <div class="dashboard-container">
            <!-- Add Expense Form -->
            <div class="add-expense">
                <h2>Add Expense</h2>
                <div id="successMessage" class="success-message">Expense added successfully!</div>
                <form id="expenseForm">
                    <label>
                        Category
                        <select id="expenseCategory" required>
                            <option value="">Select Category</option>
                            <option value="Food">Food</option>
                            <option value="Transportation">Transportation</option>
                            <option value="Shopping">Shopping</option>
                            <option value="Entertainment">Entertainment</option>
                            <option value="Utilities">Utilities</option>
                            <option value="Other">Other</option>
                        </select>
                    </label>
                    <label>
                        Expense Amount
                        <input type="number" id="expenseAmount" placeholder="Rs. 0.00" required min="0.01"
                            step="0.01" />
                    </label>
                    <label>
                        Date
                        <input type="date" id="expenseDate" required />
                    </label>
                    <label>
                        Description
                        <textarea id="expenseDescription" placeholder="Add a brief description" required></textarea>
                    </label>
                    <button type="submit">Add Expense</button>
                </form>
            </div>

            <!-- Expense List -->
            <div class="expense-list">
                <h2>Recent expenses</h2>
                <div class="filter-controls">
                    <select id="categoryFilter">
                        <option value="all">All Categories</option>
                        <option value="Food">Food</option>
                        <option value="Transportation">Transportation</option>
                        <option value="Shopping">Shopping</option>
                        <option value="Entertainment">Entertainment</option>
                        <option value="Utilities">Utilities</option>
                        <option value="Other">Other</option>
                    </select>
                    <select id="sortBy">
                        <option value="date-desc">Date (Newest First)</option>
                        <option value="date-asc">Date (Oldest First)</option>
                        <option value="amount-desc">Amount (Highest First)</option>
                        <option value="amount-asc">Amount (Lowest First)</option>
                    </select>
                </div>
                <ul id="expensesList">
                    <!-- Expenses will be populated here by JavaScript -->
                </ul>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize date field with today's date
            const today = new Date();
            const formattedDate = today.toISOString().substr(0, 10);
            document.getElementById('expenseDate').value = formattedDate;

            // Menu toggle functionality
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

            // BACKEND IMPLEMENTATION

            // Data model and persistence
            class ExpenseManager {
                constructor() {
                    this.expenses = JSON.parse(localStorage.getItem('expenses')) || this.initializeDefaultExpenses();
                }

                initializeDefaultExpenses() {
                    // Demo data if no expenses are saved
                    return [
                        { id: 1, category: 'Food', description: 'Lunch at café', date: '2024-03-25', amount: 45.50 },
                        { id: 2, category: 'Transportation', description: 'Uber ride', date: '2024-03-24', amount: 20.00 },
                        { id: 3, category: 'Shopping', description: 'Groceries', date: '2024-03-23', amount: 75.30 },
                        { id: 4, category: 'Entertainment', description: 'Movie ticket', date: '2024-03-22', amount: 12.75 },
                        { id: 5, category: 'Utilities', description: 'Phone bill', date: '2024-03-21', amount: 55.00 }
                    ];
                }

                getAllExpenses() {
                    return this.expenses;
                }

                addExpense(category, description, date, amount) {
                    const newExpense = {
                        id: Date.now(), // Simple way to generate a unique ID
                        category,
                        description,
                        date,
                        amount: parseFloat(amount)
                    };
                    this.expenses.push(newExpense);
                    this.saveExpenses();
                    return newExpense;
                }

                deleteExpense(id) {
                    this.expenses = this.expenses.filter(expense => expense.id !== id);
                    this.saveExpenses();
                }

                saveExpenses() {
                    localStorage.setItem('expenses', JSON.stringify(this.expenses));
                }

                filterExpenses(category, sortBy) {
                    let filteredExpenses = [...this.expenses];

                    // Filter by category if not 'all'
                    if (category !== 'all') {
                        filteredExpenses = filteredExpenses.filter(expense => expense.category === category);
                    }

                    // Sort based on criteria
                    switch (sortBy) {
                        case 'date-desc':
                            filteredExpenses.sort((a, b) => new Date(b.date) - new Date(a.date));
                            break;
                        case 'date-asc':
                            filteredExpenses.sort((a, b) => new Date(a.date) - new Date(b.date));
                            break;
                        case 'amount-desc':
                            filteredExpenses.sort((a, b) => b.amount - a.amount);
                            break;
                        case 'amount-asc':
                            filteredExpenses.sort((a, b) => a.amount - b.amount);
                            break;
                    }

                    return filteredExpenses;
                }
            }

            // Initialize expense manager
            const expenseManager = new ExpenseManager();

            // UI Controller
            class UIController {
                constructor(expenseManager) {
                    this.expenseManager = expenseManager;
                    this.expenseForm = document.getElementById('expenseForm');
                    this.expensesList = document.getElementById('expensesList');
                    this.categoryFilter = document.getElementById('categoryFilter');
                    this.sortBy = document.getElementById('sortBy');
                    this.successMessage = document.getElementById('successMessage');

                    this.setupEventListeners();
                    this.displayExpenses();
                }

                setupEventListeners() {
                    // Form submission
                    this.expenseForm.addEventListener('submit', (e) => {
                        e.preventDefault();
                        this.addExpense();
                    });

                    // Filtering and sorting
                    this.categoryFilter.addEventListener('change', () => this.displayExpenses());
                    this.sortBy.addEventListener('change', () => this.displayExpenses());
                }

                addExpense() {
                    const category = document.getElementById('expenseCategory').value;
                    const description = document.getElementById('expenseDescription').value;
                    const date = document.getElementById('expenseDate').value;
                    const amount = document.getElementById('expenseAmount').value;

                    if (!category || !description || !date || !amount) {
                        alert('Please fill in all fields');
                        return;
                    }

                    this.expenseManager.addExpense(category, description, date, amount);
                    this.displayExpenses();
                    this.resetForm();
                    this.showSuccessMessage();
                }

                displayExpenses() {
                    const category = this.categoryFilter.value;
                    const sortBy = this.sortBy.value;
                    const expenses = this.expenseManager.filterExpenses(category, sortBy);

                    this.expensesList.innerHTML = '';

                    if (expenses.length === 0) {
                        this.expensesList.innerHTML = '<p>No expenses found.</p>';
                        return;
                    }

                    expenses.forEach(expense => {
                        const li = document.createElement('li');
                        li.innerHTML = `
                            <span class="category">${expense.category}</span>
                            <span class="description">- ${expense.description}</span>
                            <span class="date">${expense.date}</span>
                            <span class="amount">Rs.${expense.amount.toFixed(2)}</span>
                            <span class="delete-btn" data-id="${expense.id}">🗑️</span>
                        `;
                        this.expensesList.appendChild(li);
                    });

                    // Add delete event listeners
                    document.querySelectorAll('.delete-btn').forEach(btn => {
                        btn.addEventListener('click', (e) => {
                            const id = parseInt(e.target.getAttribute('data-id'));
                            this.deleteExpense(id);
                        });
                    });
                }

                deleteExpense(id) {
                    if (confirm('Are you sure you want to delete this expense?')) {
                        this.expenseManager.deleteExpense(id);
                        this.displayExpenses();
                    }
                }

                resetForm() {
                    document.getElementById('expenseCategory').value = '';
                    document.getElementById('expenseDescription').value = '';
                    document.getElementById('expenseAmount').value = '';

                    // Reset date to today
                    const today = new Date();
                    const formattedDate = today.toISOString().substr(0, 10);
                    document.getElementById('expenseDate').value = formattedDate;
                }

                showSuccessMessage() {
                    this.successMessage.style.display = 'block';
                    setTimeout(() => {
                        this.successMessage.style.display = 'none';
                    }, 3000);
                }
            }

            // Initialize the UI
            const ui = new UIController(expenseManager);
        });
    </script>
</body>

</html>
