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

        .income-container {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
        }


        /* Add Income Styles */
        .add-income {
            padding: 20px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
            width: 51%;
            height: 550px;
            margin-top: 10px;
            margin-left: 30px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .add-income h2 {
            margin-top: 0;
            margin-bottom: 35px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }

        .income-form {
            display: flex;
            flex-direction: column;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .income-form label {
            margin-bottom: 15px;
            display: block;
        }

        .income-form input,
        .income-form select {
            padding: 10px;
            width: 100%;
            border:
                1px solid #ddd;
            border-radius: 5px;
        }

        .income-form button {
            padding: 12px;
            background-color: #00563F;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            align-self: flex-end;
            margin-top: 38px;
        }

        .income-form button:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 5px rgba(0, 0, 0, 0.1);
        }

        /* PastIncomes Styles */
        .past-incomes {
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

        .past-incomes h2 {
            margin-top: 0;
            margin-bottom: 30px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }

        .past-incomes ul {
            list-style: none;
            padding: 0;
        }

        .past-incomes li {
            padding: 24px 0;
            border-bottom: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .past-incomes .actions {
            margin-left: auto;
            display: flex;
            gap: 10px;
        }

        .past-incomes .edit-btn,
        .past-incomes .delete-btn {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }

        .past-incomes .edit-btn:hover,
        .past-incomes .delete-btn:hover {
            color: #28a745;
        }

        /* Toast notification */
        .toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: var(--primary-color);
            color: white;
            padding: 15px 25px;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            display: none;
            z-index: 100;
            animation: fadeIn 0.3s, fadeOut 0.3s 2.7s;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
            }

            to {
                opacity: 0;
            }
        }

        /* Modal for editing */
        .modal {
            display: none;
            position: fixed;
            z-index: 100;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            position: relative;
            background-color: white;
            margin: 10% auto;
            padding: 20px;
            border-radius: 5px;
            width: 50%;
            max-width: 500px;
        }

        .close-btn {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 20px;
            cursor: pointer;
        }

        .modal h3 {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }

        .modal-form {
            display: flex;
            flex-direction: column;
        }

        .modal-form .form-group {
            margin-bottom: 15px;
        }

        .modal-form label {
            margin-bottom: 5px;
            display: block;
        }

        .modal-form input,
        .modal-form select {
            padding: 8px;
            width: 100%;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .modal-form button {
            padding: 10px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 15px;
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
            <li class="nav-item" onclick="window.location.href='Add_Expense_Page.html'" style="cursor: pointer;">
                <span class="nav-icon">💸</span>
                Expense
            </li>
            <li class="nav-item active">
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
        <!-- AddIncome Component -->
        <div class="income-container">
            <div class="add-income section">
                <h2>Add Income</h2>
                <form class="income-form" id="incomeForm">
                    <div class="form-group">
                        <label>
                            Category
                            <select id="incomeCategory" required>
                                <option value="">Select Category</option>
                                <option value="Salary">Salary</option>
                                <option value="Business">Business</option>
                                <option value="Side Hustle">Side Hustle</option>
                                <option value="Gifts">Gifts</option>
                                <option value="Bonus">Bonus</option>
                                <option value="Freelance">Freelance</option>
                                <option value="Other">Other</option>
                            </select>
                        </label>
                    </div>

                    <div class="form-group">
                        <label>Amount</label>
                        <input type="number" id="incomeAmount" placeholder="Enter amount" required min="1" />
                    </div>
                    <div class="form-group">
                        <label>Date</label>
                        <input type="date" id="incomeDate" required />
                    </div>

                    <button type="submit">Post Income</button>
                </form>
            </div>

            <!-- PastIncomes Component -->
            <div class="past-incomes section">
                <h2>Past Incomes</h2>
                <ul id="incomesList">
                    <!-- Income items will be populated here -->
                </ul>
            </div>
        </div>
    </main>

    <!-- Toast notification -->
    <div class="toast" id="toast"></div>

    <!-- Edit Income Modal -->
    <div class="modal" id="editModal">
        <div class="modal-content">
            <span class="close-btn" id="closeModal">&times;</span>
            <h3>Edit Income</h3>
            <form class="modal-form" id="editForm">
                <input type="hidden" id="editIncomeId">
                <div class="form-group">
                    <label>
                        Category
                        <select id="editCategory" required>
                            <option value="">Select Category</option>
                            <option value="Salary">Salary</option>
                            <option value="Business">Business</option>
                            <option value="Side Hustle">Side Hustle</option>
                            <option value="Gifts">Gifts</option>
                            <option value="Bonus">Bonus</option>
                            <option value="Freelance">Freelance</option>
                            <option value="Other">Other</option>
                        </select>
                    </label>
                </div>

                <div class="form-group">
                    <label>Amount</label>
                    <input type="number" id="editAmount" placeholder="Enter amount" required min="1" />
                </div>
                <div class="form-group">
                    <label>Date</label>
                    <input type="date" id="editDate" required />
                </div>

                <button type="submit">Update Income</button>
            </form>
        </div>
    </div>

    <script>
        // ===============================
        // DATA MANAGEMENT
        // ===============================

        // Income data structure
        class IncomeManager {
            constructor() {
                // Load incomes from localStorage or initialize with empty array
                this.incomes = JSON.parse(localStorage.getItem('incomes')) || [];

                // If no incomes, add some sample data
                if (this.incomes.length === 0) {
                    const today = new Date();
                    const oneMonthAgo = new Date(today);
                    oneMonthAgo.setMonth(today.getMonth() - 1);

                    const twoWeeksAgo = new Date(today);
                    twoWeeksAgo.setDate(today.getDate() - 14);

                    this.incomes = [
                        {
                            id: this.generateId(),
                            category: 'Bonus',
                            amount: 2000,
                            date: '2024-04-13',
                            type: 'Income'
                        },
                        {
                            id: this.generateId(),
                            category: 'Salary',
                            amount: 50000,
                            date: '2024-04-01',
                            type: 'Income'
                        },
                        {
                            id: this.generateId(),
                            category: 'Freelance',
                            amount: 15000,
                            date: '2024-03-25',
                            type: 'Income'
                        }
                    ];
                    this.saveToStorage();
                }
            }

            // Generate a unique ID
            generateId() {
                return Date.now().toString(36) + Math.random().toString(36).substr(2);
            }

            // Save incomes to localStorage
            saveToStorage() {
                localStorage.setItem('incomes', JSON.stringify(this.incomes));
            }

            // Add a new income
            addIncome(income) {
                const newIncome = {
                    id: this.generateId(),
                    ...income,
                    type: 'Income'  // Set type to Income
                };
                this.incomes.unshift(newIncome);  // Add to beginning of array
                this.saveToStorage();
                return newIncome;
            }

            // Update an existing income
            updateIncome(id, updatedIncome) {
                const index = this.incomes.findIndex(income => income.id === id);
                if (index !== -1) {
                    this.incomes[index] = {
                        ...this.incomes[index],
                        ...updatedIncome
                    };
                    this.saveToStorage();
                    return this.incomes[index];
                }
                return null;
            }

            // Delete an income
            deleteIncome(id) {
                this.incomes = this.incomes.filter(income => income.id !== id);
                this.saveToStorage();
            }

            // Get all incomes
            getAllIncomes() {
                return this.incomes;
            }

            // Get income by ID
            getIncomeById(id) {
                return this.incomes.find(income => income.id === id);
            }
        }

        // ===============================
        // UI MANAGEMENT
        // ===============================

        // Initialize income manager
        const incomeManager = new IncomeManager();

        // Set default date to today
        document.addEventListener('DOMContentLoaded', function () {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('incomeDate').value = today;

            // Initialize income list
            refreshIncomeList();

            // Setup form submission
            setupFormSubmission();

            // Setup modal events
            setupModalEvents();
        });

        // Function to display incomes in the list
        function refreshIncomeList() {
            const incomesList = document.getElementById('incomesList');
            incomesList.innerHTML = '';

            const incomes = incomeManager.getAllIncomes();

            incomes.forEach(income => {
                const listItem = document.createElement('li');

                // Format the amount with currency
                const formattedAmount = `Rs.${income.amount}`;

                listItem.innerHTML = `
                    ${income.category} - ${formattedAmount} - ${income.date} - ${income.type}
                    <span class="actions">
                        <button class="edit-btn" data-id="${income.id}">🖊️</button>
                        <button class="delete-btn" data-id="${income.id}">🗑️</button>
                    </span>
                `;

                incomesList.appendChild(listItem);
            });

            // Add event listeners to edit and delete buttons
            addActionButtonListeners();
        }

        // Setup form submission
        function setupFormSubmission() {
            const incomeForm = document.getElementById('incomeForm');

            incomeForm.addEventListener('submit', function (e) {
                e.preventDefault();

                const category = document.getElementById('incomeCategory').value;
                const amount = parseFloat(document.getElementById('incomeAmount').value);
                const date = document.getElementById('incomeDate').value;

                if (!category || isNaN(amount) || amount <= 0 || !date) {
                    showToast('Please fill all fields correctly');
                    return;
                }

                // Add income
                incomeManager.addIncome({
                    category,
                    amount,
                    date
                });

                // Reset form
                incomeForm.reset();
                document.getElementById('incomeDate').value = new Date().toISOString().split('T')[0];

                // Refresh the list
                refreshIncomeList();

                showToast('Income added successfully');
            });
        }

        // Add event listeners to edit and delete buttons
        function addActionButtonListeners() {
            // Edit buttons
            document.querySelectorAll('.edit-btn').forEach(button => {
                button.addEventListener('click', function () {
                    const id = this.getAttribute('data-id');
                    openEditModal(id);
                });
            });

            // Delete buttons
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function () {
                    const id = this.getAttribute('data-id');

                    if (confirm('Are you sure you want to delete this income?')) {
                        incomeManager.deleteIncome(id);
                        refreshIncomeList();
                        showToast('Income deleted successfully');
                    }
                });
            });
        }

        // Setup modal events
        function setupModalEvents() {
            const modal = document.getElementById('editModal');
            const closeBtn = document.getElementById('closeModal');
            const editForm = document.getElementById('editForm');

            // Close modal when clicking the X
            closeBtn.addEventListener('click', function () {
                modal.style.display = 'none';
            });

            // Close modal when clicking outside
            window.addEventListener('click', function (event) {
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            });

            // Handle edit form submission
            editForm.addEventListener('submit', function (e) {
                e.preventDefault();

                const id = document.getElementById('editIncomeId').value;
                const category = document.getElementById('editCategory').value;
                const amount = parseFloat(document.getElementById('editAmount').value);
                const date = document.getElementById('editDate').value;

                if (!category || isNaN(amount) || amount <= 0 || !date) {
                    showToast('Please fill all fields correctly');
                    return;
                }

                // Update income
                incomeManager.updateIncome(id, {
                    category,
                    amount,
                    date
                });

                // Close modal
                modal.style.display = 'none';

                // Refresh the list
                refreshIncomeList();

                showToast('Income updated successfully');
            });
        }

        // Open edit modal with income data
        function openEditModal(id) {
            const income = incomeManager.getIncomeById(id);

            if (income) {
                document.getElementById('editIncomeId').value = income.id;
                document.getElementById('editCategory').value = income.category;
                document.getElementById('editAmount').value = income.amount;
                document.getElementById('editDate').value = income.date;

                document.getElementById('editModal').style.display = 'block';
            }
        }

        // Show toast notification
        function showToast(message) {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.style.display = 'block';

            // Hide after 3 seconds
            setTimeout(() => {
                toast.style.display = 'none';
            }, 3000);
        }

        // Function to navigate to other pages (placeholders for now)
        function navigateTo(page) {
            console.log(`Navigating to ${page}`);
            // For actual navigation, you would uncomment this:
            // window.location.href = page;

            // For now, just update active class
            if (page !== 'Add_Income_Page.html') {
                document.querySelectorAll('.nav-item').forEach(item => {
                    item.classList.remove('active');
                    if (item.textContent.trim().includes(page.split('_')[0].trim())) {
                        item.classList.add('active');
                    }
                });
            }
        }

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

            // Navigation functionality (now managed by navigateTo function)
        });
    </script>
</body>

</html>