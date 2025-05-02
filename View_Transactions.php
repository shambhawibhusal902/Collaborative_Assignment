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
            --accent-color: #0c9;

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

        /* Sidebar styles */
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
            display: flex;
            flex-direction: column;
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

        .avatar,
        .user-avatar {
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

        .user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
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

        /* Content styles */
        .content {
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

        /* Category selector styles */
        .category-selector {
            margin-bottom: 30px;
        }

        .category-label {
            font-size: 14px;
            color: #666;
            margin-bottom: 8px;
        }

        .category-dropdown {
            position: relative;
            width: 100%;
            max-width: 1000px;
        }

        .category-select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: white;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .category-options {
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            background-color: white;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 4px 4px;
            z-index: 10;
            display: none;
            max-height: 300px;
            overflow-y: auto;
        }

        .category-options.show {
            display: block;
        }

        .category-option {
            padding: 10px 15px;
            cursor: pointer;
        }

        .category-option:hover {
            background-color: #f0f0f0;
        }

        /* Total section styles */
        .total-section {
            margin-bottom: 30px;
        }

        .total-label {
            font-size: 16px;
            color: #333;
            margin-bottom: 10px;
        }

        .total-amount {
            font-size: 28px;
            font-weight: bold;
            color: #00563F !important;
        }

        /* Search section styles */
        .search-section {
            margin-bottom: 30px;
            max-width: 1000px;
            position: relative;
        }

        .search-input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="%23999" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>');
            background-repeat: no-repeat;
            background-position: 15px center;
            padding-left: 40px;
        }

        .clear-search-btn {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #999;
            font-size: 12px;
            padding: 5px;
        }

        .clear-search-btn:hover {
            color: #333;
        }

        /* Transactions table styles */
        .transactions-table {
            width: 100%;
            border-collapse: collapse;
            max-width: 1000px;
        }

        .transactions-table th {
            text-align: left;
            padding: 15px 10px;
            font-size: 12px;
            color: #666;
            font-weight: normal;
            text-transform: uppercase;
            border-bottom: 1px solid #ddd;
        }

        .transactions-table td {
            padding: 15px 10px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }

        .transactions-table th:last-child,
        .transactions-table td:last-child {
            text-align: right;
        }

        .amount {
            color: #000;
        }

        .category-text {
            color: #000;
        }

        /* Arrow icon for dropdown */
        .arrow {
            border: solid #666;
            border-width: 0 2px 2px 0;
            display: inline-block;
            padding: 3px;
            transform: rotate(45deg);
            margin-top: -3px;
        }

        /* Export data button styles */
        .export-btn {
            background-color: transparent;
            color: var(--primary-light);
            border: 1px solid var(--primary-light);
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;

        }

        .export-btn:hover {
            background-color: #00563F;
        }

        /* Mobile menu toggle */
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

            .content {
                margin-left: 0;
                padding: var(--space-lg);
                padding-top: 60px;
                max-width: 100%;
            }
        }

        @media (max-width: 768px) {
            body {
                flex-direction: column;
            }

            .content {
                height: auto;
                padding: 10px;
                padding-top: 60px;
            }
        }
    </style>
</head>

<body>
    <!-- Mobile menu toggle button -->
    <button class="menu-toggle" id="menuToggle">☰</button>

    <!-- Sidebar navigation -->
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
            <li class="nav-item" onclick="window.location.href='Budgets.php'" style="cursor: pointer;">
                <span class="nav-icon">💹</span>
                Budgets
            </li>
            <li class="nav-item active">
                <span class="nav-icon">📋</span>
                View Transactions
            </li>

        </ul>
    </div>

    <div class="content">
        <header class="header"></header>

        <div class="category-selector">
            <div class="category-label">Select Category</div>
            <div class="category-dropdown">
                <div class="category-select" id="categorySelect">
                    <span>All Categories</span>
                    <i class="arrow"></i>
                </div>
                <div class="category-options" id="categoryOptions">
                    <div class="category-option" data-value="all">All Categories</div>
                    <div class="category-option" data-value="income">Income</div>
                    <div class="category-option" data-value="expense">Expense</div>
                </div>
            </div>
        </div>

        <div class="total-section">
            <div class="total-label">All Total</div>
            <div class="total-amount">$3336.25</div>
        </div>

        <div class="search-section">
            <input type="text" class="search-input" placeholder="Search Transactions" id="searchTransactions">
            <button id="clearSearch" class="clear-search-btn">✕</button>
        </div>

        <div style="display: flex; justify-content: flex-end; margin-bottom: 20px;">
            <button class="export-btn" id="exportData">Export Data</button>
        </div>

        <table class="transactions-table">
            <thead>
                <tr>
                    <th>DATE</th>
                    <th>DESCRIPTION</th>
                    <th>CATEGORY</th>
                    <th>SUB CATEGORY</th>
                    <th>AMOUNT</th>
                </tr>
            </thead>
            <tbody id="transactionsTableBody">
                <!-- Transaction rows will be dynamically inserted here by JavaScript -->
            </tbody>
        </table>
    </div>

    <script>// DOM Elements
        document.addEventListener('DOMContentLoaded', function () {
            // Sidebar elements
            const menuToggle = document.getElementById('menuToggle');
            const sidebar = document.getElementById('sidebar');
            const navItems = document.querySelectorAll('.nav-item');

            // Dropdown functionality
            const categorySelect = document.getElementById('categorySelect');
            const categoryOptions = document.getElementById('categoryOptions');
            const searchInput = document.getElementById('searchTransactions');
            const clearSearchBtn = document.getElementById('clearSearch');
            const exportDataBtn = document.getElementById('exportData');

            // Transaction data
            const transactions = [
                {
                    date: '2025-04-01',
                    description: 'Side hustle',
                    category: 'Income',
                    subcategory: 'Dropshipping',
                    amount: 1200.00
                },
                {
                    date: '2025-04-03',
                    description: 'Supermarket Shopping',
                    category: 'Expense',
                    subcategory: 'Food',
                    amount: 69.00
                },
                {
                    date: '2025-04-04',
                    description: 'Fuel Refill',
                    category: 'Expense',
                    subcategory: 'Transport',
                    amount: 59.00
                },
                {
                    date: '2025-04-05',
                    description: 'Monthly Salary',
                    category: 'Income',
                    subcategory: 'Salary',
                    amount: 2500.00
                },
                {
                    date: '2025-04-06',
                    description: 'Restaurant Dinner',
                    category: 'Expense',
                    subcategory: 'Food',
                    amount: 65.75
                },
                {
                    date: '2025-04-07',
                    description: 'Online Shopping',
                    category: 'Expense',
                    subcategory: 'Shopping',
                    amount: 120.00
                },
                {
                    date: '2025-04-08',
                    description: 'Gym Membership',
                    category: 'Expense',
                    subcategory: 'Health',
                    amount: 50.00
                }
            ];

            // Initialize the app
            function initApp() {
                renderTransactions(transactions);
                setupEventListeners();
                calculateTotalBalance(transactions);
            }

            // Render transactions to the table
            function renderTransactions(transactionsToRender) {
                const tableBody = document.getElementById('transactionsTableBody');
                tableBody.innerHTML = '';

                transactionsToRender.forEach(transaction => {
                    const row = document.createElement('tr');

                    row.innerHTML = `
                        <td>${transaction.date}</td>
                        <td>${transaction.description}</td>
                        <td><span class="category-text">${transaction.category}</span></td>
                        <td><span class="category-text">${transaction.subcategory}</span></td>
                        <td><span class="amount">$${transaction.amount.toFixed(2)}</span></td>
                    `;

                    tableBody.appendChild(row);
                });
            }

            // Calculate and display total balance
            function calculateTotalBalance(transactionsToCalculate) {
                let total = 0;

                transactionsToCalculate.forEach(transaction => {
                    if (transaction.category === 'Income') {
                        total += transaction.amount;
                    } else {
                        total -= transaction.amount;
                    }
                });

                const totalAmount = document.querySelector('.total-amount');
                totalAmount.textContent = `$${Math.abs(total).toFixed(2)}`;

                // Update color based on positive/negative balance
                if (total >= 0) {
                    totalAmount.style.color = '#0c9';
                } else {
                    totalAmount.style.color = '#f44';
                }
            }

            // Filter transactions by category
            function filterTransactionsByCategory(category) {
                if (category === 'all') {
                    renderTransactions(transactions);
                    calculateTotalBalance(transactions);
                    return;
                }

                const filteredTransactions = transactions.filter(transaction =>
                    transaction.category.toLowerCase() === category.toLowerCase()
                );

                renderTransactions(filteredTransactions);
                calculateTotalBalance(filteredTransactions);
            }

            // Filter transactions by search term
            function filterTransactionsBySearch(searchTerm) {
                if (!searchTerm) {
                    renderTransactions(transactions);
                    calculateTotalBalance(transactions);
                    return;
                }

                const filteredTransactions = transactions.filter(transaction =>
                    transaction.description.toLowerCase().includes(searchTerm.toLowerCase()) ||
                    transaction.category.toLowerCase().includes(searchTerm.toLowerCase()) ||
                    transaction.subcategory.toLowerCase().includes(searchTerm.toLowerCase()) ||
                    transaction.date.includes(searchTerm) ||
                    transaction.amount.toString().includes(searchTerm)
                );

                renderTransactions(filteredTransactions);
                calculateTotalBalance(filteredTransactions);
            }

            // Set up event listeners
            function setupEventListeners() {
                // Handle mobile menu toggle
                if (menuToggle) {
                    menuToggle.addEventListener('click', function () {
                        sidebar.classList.toggle('active');
                    });
                }

                // Close sidebar when clicking outside on mobile
                document.addEventListener('click', function (event) {
                    if (window.innerWidth <= 992 &&
                        sidebar && !sidebar.contains(event.target) &&
                        menuToggle && !menuToggle.contains(event.target) &&
                        sidebar.classList.contains('active')) {
                        sidebar.classList.remove('active');
                    }
                });

                // Navigation menu functionality
                navItems.forEach(item => {
                    item.addEventListener('click', function () {
                        navItems.forEach(navItem => {
                            navItem.classList.remove('active');
                        });
                        this.classList.add('active');

                        // Handle different menu actions
                        const menuAction = this.getAttribute('data-section');
                        handleMenuAction(menuAction);

                        // Close sidebar on mobile after navigation
                        if (window.innerWidth <= 992 && sidebar) {
                            sidebar.classList.remove('active');
                        }
                    });
                });

                // Toggle dropdown when clicking on the selector
                if (categorySelect) {
                    categorySelect.addEventListener('click', (e) => {
                        e.stopPropagation();
                        categoryOptions.classList.toggle('show');
                    });
                }

                // Close dropdown when clicking outside
                document.addEventListener('click', () => {
                    if (categoryOptions) {
                        categoryOptions.classList.remove('show');
                    }
                });

                // Prevent propagation for clicks inside the dropdown menu
                if (categoryOptions) {
                    categoryOptions.addEventListener('click', (e) => {
                        e.stopPropagation();
                    });
                }

                // Handle category selection
                const options = document.querySelectorAll('.category-option');
                options.forEach(option => {
                    option.addEventListener('click', function () {
                        const selectedCategory = this.dataset.value;
                        if (categorySelect) {
                            const span = categorySelect.querySelector('span');
                            if (span) {
                                span.textContent = this.textContent;
                            }
                        }
                        if (categoryOptions) {
                            categoryOptions.classList.remove('show');
                        }

                        filterTransactionsByCategory(selectedCategory);
                    });
                });

                // Search functionality with debounce
                let debounceTimeout;
                if (searchInput) {
                    searchInput.addEventListener('input', function () {
                        clearTimeout(debounceTimeout);
                        debounceTimeout = setTimeout(() => {
                            filterTransactionsBySearch(this.value);
                        }, 300);
                    });
                }

                // Clear search button functionality
                if (clearSearchBtn && searchInput) {
                    clearSearchBtn.addEventListener('click', () => {
                        searchInput.value = '';
                        filterTransactionsBySearch('');
                    });
                }

                // Export data functionality
                if (exportDataBtn) {
                    exportDataBtn.addEventListener('click', exportTransactions);
                }
            }

            // Handle menu item clicks
            function handleMenuAction(action) {
                console.log(`Menu action: ${action}`);

                // You would implement actual navigation/view switching here
            }

            // Add a new transaction
            function addTransaction(transaction) {
                transactions.push(transaction);
                renderTransactions(transactions);
                calculateTotalBalance(transactions);
            }

            // Delete a transaction
            function deleteTransaction(index) {
                transactions.splice(index, 1);
                renderTransactions(transactions);
                calculateTotalBalance(transactions);
            }

            // Edit a transaction
            function editTransaction(index, updatedTransaction) {
                transactions[index] = updatedTransaction;
                renderTransactions(transactions);
                calculateTotalBalance(transactions);
            }

            // Export data functionality
            function exportTransactions() {
                const dataStr = JSON.stringify(transactions, null, 2);
                const dataUri = 'data:application/json;charset=utf-8,' + encodeURIComponent(dataStr);

                const exportFileDefaultName = 'transactions.json';

                const linkElement = document.createElement('a');
                linkElement.setAttribute('href', dataUri);
                linkElement.setAttribute('download', exportFileDefaultName);
                linkElement.click();
            }

            // Initialize the application
            initApp();
        });</script>
</body>

</html>