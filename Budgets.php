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

        /* Main content styles */
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
            position: relative;
            padding-bottom: 50px; /* Ensure space for buttons */
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

        /* Edit and Delete button styling */
        .edit-btn,
        .delete-btn {
            position: absolute;
            bottom: 15px;
            background: none;
            border: none;
            color: #888;
            cursor: pointer;
            font-size: 20px;
            padding: 5px;
            transition: color 0.3s;
        }

        .delete-btn {
            right: 15px;
        }
        .delete-btn:hover {
            color: #d9534f;
        }

        .edit-btn {
            right: 55px;
        }
        .edit-btn:hover {
            color: #007bff;
        }


        /* Modal styling */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s;
        }

        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .modal-container {
            background-color: white;
            padding: 25px;
            border-radius: 8px;
            width: 90%;
            max-width: 500px; 
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            position: relative;
        }

        .modal-title {
            font-size: 24px;
            margin-bottom: 20px; 
            color: var(--primary-color);
        }

        .modal-close {
            position: absolute;
            top: 10px;  
            right: 15px; 
            background: none;
            border: none;
            font-size: 28px; 
            cursor: pointer;
            color: #888;
            line-height: 1; 
            padding: 5px; 
        }

        .modal-close:hover {
            color: var(--primary-color);
        }

        /* Form styling */
        .form-group {
            margin-bottom: 15px; 
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: var(--text-dark);
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--border-light);
            border-radius: 4px;
            font-size: 16px;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(0, 86, 63, 0.2);
        }

        textarea.form-control {
            min-height: 70px; 
            resize: vertical;
        }

        .btn-submit {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
            margin-top: 10px; 
        }

        .btn-submit:hover {
            background-color: #004530;
        }

        /* Confirmation dialog */
        .confirm-dialog {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s;
        }

        .confirm-dialog.active {
            opacity: 1;
            visibility: visible;
        }

        .confirm-container {
            background-color: white;
            padding: 25px;
            border-radius: 8px;
            width: 90%;
            max-width: 400px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            text-align: center;
        }

        .confirm-container h3 {
            margin-bottom: 20px;
            color: var(--text-dark);
        }

        .confirm-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 20px;
        }

        .btn-cancel {
            background-color: #6c757d;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-cancel:hover {
            background-color: #5a6268;
        }

        .btn-delete {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-delete:hover {
            background-color: #c82333;
        }
    </style>
</head>

<body>
    <button class="menu-toggle" id="menuToggle">☰</button>

    <div class="sidebar" id="sidebar">
        <div class="logo">Expenzo</div>
        <div class="user-profile">
            <div class="avatar" onclick="window.location.href='Profile.html'" style="cursor: pointer;">S</div>
            <div class="user-name" onclick="window.location.href='Profile.html'" style="cursor: pointer;">Sam</div>
        </div>
        <ul class="nav-menu">
            <li class="nav-item" onclick="window.location.href='Total_Expense.html'" style="cursor: pointer;">
                <span class="nav-icon">📊</span>
                Dashboard
            </li>
            <li class="nav-item" onclick="window.location.href='Add_Expense.html'" style="cursor: pointer;">
                <span class="nav-icon">💸</span>
                Expense
            </li>
            <li class="nav-item" onclick="window.location.href='Add_Income.html'" style="cursor: pointer;">
                <span class="nav-icon">💰</span>
                Income
            </li>
            <li class="nav-item active">
                <span class="nav-icon">💹</span>
                Budgets
            </li>
            <li class="nav-item" onclick="window.location.href='View_Transactions.html'" style="cursor: pointer;">
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
                <button class="new-button" id="openModalBtn">New</button>
            </h2>
            <div class="budget-cards-container" id="budget-cards">
                <!-- Budget cards will be inserted here -->
            </div>
        </div>
    </main>

    <!-- Modal for adding/editing budget -->
    <div class="modal-overlay" id="budgetModal">
        <div class="modal-container">
            <button class="modal-close" id="closeModalBtn">×</button>
            <h2 class="modal-title" id="modalTitle">Add New Budget</h2>
            <form id="addBudgetForm">
                <div class="form-group">
                    <label for="category">Category</label>
                    <select class="form-control" id="category" required>
                        <option value="" disabled selected>Select Category</option>
                        <option value="Food">Food</option>
                        <option value="Transportation">Transportation</option>
                        <option value="Shopping">Shopping</option>
                        <option value="Entertainment">Entertainment</option>
                        <option value="Utilities">Utilities</option>
                        <option value="Groceries">Groceries</option>
                        <option value="Transport">Transport</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="budgetAmount">Budget Amount</label>
                    <input type="number" class="form-control" id="budgetAmount" placeholder="Enter amount" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="budgetStartDate">Start Date</label>
                    <input type="date" class="form-control" id="budgetStartDate" required>
                </div>
                <div class="form-group">
                    <label for="budgetEndDate">End Date</label>
                    <input type="date" class="form-control" id="budgetEndDate" required>
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea class="form-control" id="description" placeholder="Add a brief description"></textarea>
                </div>
                <button type="submit" class="btn-submit" id="modalSubmitBtn">Add Budget</button>
            </form>
        </div>
    </div>

    <!-- Confirmation dialog for deleting budget -->
    <div class="confirm-dialog" id="confirmDialog">
        <div class="confirm-container">
            <h3>Are you sure you want to delete?</h3>
            <div class="confirm-buttons">
                <button class="btn-cancel" id="cancelDelete">Cancel</button>
                <button class="btn-delete" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
    <script>
        //Constants
        const API_URL = 'budget_backend.php'; 

        //Global State
        let budgets = [];
        let editingBudgetId = null;

        //DOM Elements
        const menuToggle = document.getElementById('menuToggle');
        const sidebar = document.getElementById('sidebar');
        const budgetCardsContainer = document.getElementById('budget-cards');
        const modal = document.getElementById('budgetModal');
        const modalTitleElement = document.getElementById('modalTitle');
        const modalSubmitButton = document.getElementById('modalSubmitBtn');
        const openModalBtn = document.getElementById('openModalBtn');
        const closeModalBtn = document.getElementById('closeModalBtn');
        const addBudgetForm = document.getElementById('addBudgetForm');
        const categoryInput = document.getElementById('category');
        const budgetAmountInput = document.getElementById('budgetAmount');
        const budgetStartDateInput = document.getElementById('budgetStartDate');
        const budgetEndDateInput = document.getElementById('budgetEndDate');
        const descriptionInput = document.getElementById('description');
        const confirmDialog = document.getElementById('confirmDialog');
        const cancelDeleteBtn = document.getElementById('cancelDelete');
        const confirmDeleteBtn = document.getElementById('confirmDelete');
        let budgetToDeleteId = null;


        //Utility Functions 
        function formatDateForDisplay(dateString) {
            if (!dateString) return 'N/A';
            // Assuming dateString is 'YYYY-MM-DD'
            const [year, month, day] = dateString.split('-');
            return `${month}/${day}/${year}`;
        }

        // API Call Functions 
        async function fetchBudgets() {
            try {
                const response = await fetch(API_URL);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const serverBudgets = await response.json();
                
                // Adapt server data to frontend structure
                budgets = serverBudgets.map(b => ({
                    id: b.id,
                    category: b.name.replace(' Budget', ''), // Extract category from name
                    name: b.name,
                    total: parseFloat(b.total) || 0,
                    spent: parseFloat(b.spent) || 0,
                    remaining: parseFloat(b.remaining) || 0,
                    status: b.status || 'Monthly (Active)',
                    startDate: b.date, 
                    endDate: b.date, 
                    description: b.description || ''
                }));
                renderBudgetCards();
            } catch (error) {
                console.error("Error fetching budgets:", error);
                budgetCardsContainer.innerHTML = "<p>Error loading budgets. Please try again later.</p>";
            }
        }

        async function addBudgetToServer(budgetData) {
            try {
                const response = await fetch(API_URL, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(budgetData)
                });
                const result = await response.json();
                if (result.success) {
                    await fetchBudgets(); 
                    return true;
                } else {
                    alert(`Error adding budget: ${result.message || 'Unknown error'}`);
                    return false;
                }
            } catch (error) {
                console.error("Error adding budget:", error);
                alert("Failed to add budget. Please check your connection.");
                return false;
            }
        }

        async function updateBudgetOnServer(budgetId, budgetData) {
            try {
                const payload = { id: budgetId, ...budgetData };
                console.log("Sending to server for update:", payload); // For debugging

                const response = await fetch(API_URL, {
                    method: 'PUT', // Use PUT for updates
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload) 
                });
                const resultText = await response.text(); 
                console.log("Raw server response:", resultText);
                const result = JSON.parse(resultText); 

                if (result.success) {
                    await fetchBudgets(); 
                    return true;
                } else {
                    alert(`Error updating budget: ${result.message || 'Unknown error'}`);
                    return false;
                }
            } catch (error) {
                console.error("Error updating budget:", error);
                alert("Failed to update budget. Please check your connection or server logs.");
                return false;
            }
        }

        async function deleteBudgetFromServer(id) {
            try {
                const response = await fetch(API_URL, {
                    method: 'DELETE',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: id })
                });
                const result = await response.json();
                if (result.success) {
                    await fetchBudgets(); // Refresh list
                    return true;
                } else {
                    alert(`Error deleting budget: ${result.message || 'Unknown error'}`);
                    return false;
                }
            } catch (error) {
                console.error("Error deleting budget:", error);
                alert("Failed to delete budget. Please check your connection.");
                return false;
            }
        }
        
        //DOM Manipulation & Rendering 
        function createBudgetCard(budget) {
            const progress = budget.total > 0 ? (budget.spent / budget.total) * 100 : 0;
            const budgetCard = document.createElement('div');
            budgetCard.className = 'budget-card';
            budgetCard.dataset.id = budget.id;

            const displayStartDate = formatDateForDisplay(budget.startDate);
            const displayEndDate = formatDateForDisplay(budget.endDate); 

            budgetCard.innerHTML = `
                <h3>${budget.name || `${budget.category} Budget`}</h3>
                <p>Total: Rs ${budget.total.toFixed(2)}</p>
                <p>Spent: Rs ${budget.spent.toFixed(2)}</p>
                <p>Remaining: Rs ${budget.remaining.toFixed(2)}</p>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: ${Math.min(100, Math.max(0,progress)).toFixed(2)}%"></div>
                </div>
                <p>Dates: ${displayStartDate} - ${displayEndDate}</p>
                <p class="status">${budget.status}</p>
                <button class="edit-btn" onclick="openEditModal(${budget.id})">✏️</button>
                <button class="delete-btn" onclick="showDeleteConfirmation(${budget.id})">🗑️</button>
            `;
            return budgetCard;
        }
        
        function renderBudgetCards() {
            budgetCardsContainer.innerHTML = ''; 
            if (budgets.length === 0) {
                budgetCardsContainer.innerHTML = '<p>No budgets found. Add one to get started!</p>';
                return;
            }
            budgets.forEach(budget => {
                const budgetCard = createBudgetCard(budget);
                budgetCardsContainer.appendChild(budgetCard);
            });
        }

        //Modal & Form Logic
        function resetAndPrepareModalForAdd() {
            editingBudgetId = null;
            modalTitleElement.textContent = 'Add New Budget';
            modalSubmitButton.textContent = 'Add Budget';
            addBudgetForm.reset();
            budgetAmountInput.placeholder = 'Enter amount'; 
            budgetAmountInput.step = "0.01"; // ensure it's set for add mode
            budgetAmountInput.required = true; 
            budgetStartDateInput.value = new Date().toISOString().split('T')[0];
            budgetEndDateInput.value = ''; 
        }

        function closeModalAndReset() {
            modal.classList.remove('active');
            resetAndPrepareModalForAdd(); 
        }

        window.openEditModal = function(id) { 
            const budgetToEdit = budgets.find(b => b.id === id);
            if (!budgetToEdit) {
                console.error("Budget not found for editing with ID:", id);
                return;
            }

            editingBudgetId = id;
            modalTitleElement.textContent = 'Edit Budget';
            modalSubmitButton.textContent = 'Update Budget';

            categoryInput.value = budgetToEdit.category;
            budgetAmountInput.value = ''; 
            budgetAmountInput.placeholder = 'Amount to add/subtract (e.g., 50 or -20)';
            budgetAmountInput.step = "any"; // Allows negative and decimal for adjustments
            budgetAmountInput.required = false; 
            budgetStartDateInput.value = budgetToEdit.startDate; 
            budgetEndDateInput.value = budgetToEdit.endDate; // For UI consistency, show same date
            descriptionInput.value = budgetToEdit.description;
            
            modal.classList.add('active');
        }

        // --- Event Listeners ---
        document.addEventListener('DOMContentLoaded', function () {
            menuToggle.addEventListener('click', () => sidebar.classList.toggle('active'));

            document.addEventListener('click', (event) => {
                if (window.innerWidth <= 992 &&
                    !sidebar.contains(event.target) &&
                    !menuToggle.contains(event.target) &&
                    sidebar.classList.contains('active')) {
                    sidebar.classList.remove('active');
                }
            });
            
            fetchBudgets(); // Load initial budgets from server
        });

        openModalBtn.addEventListener('click', () => {
            resetAndPrepareModalForAdd();
            modal.classList.add('active');
        });
        
        closeModalBtn.addEventListener('click', closeModalAndReset);

        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeModalAndReset();
            }
        });

        addBudgetForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const category = categoryInput.value;
            const startDate = budgetStartDateInput.value;
            const endDate = budgetEndDateInput.value; // Frontend captures this
            const description = descriptionInput.value || `${category} budget`;

            if (startDate && endDate && new Date(startDate) > new Date(endDate)) {
                alert("Start Date cannot be after End Date.");
                return;
            }

            if (editingBudgetId !== null) { // EDIT LOGIC 
                const amountAdjustmentValue = budgetAmountInput.value.trim();

                const budgetDataForUpdate = {
                    // id is sent separately in updateBudgetOnServer
                    category: category,
                    description: description,
                    date: startDate 
                };

                if (amountAdjustmentValue !== "") {
                    if (isNaN(parseFloat(amountAdjustmentValue))) {
                        alert("Budget amount adjustment must be a number (e.g., 50 or -20).");
                        return;
                    }
                    budgetDataForUpdate.amountAdjustment = parseFloat(amountAdjustmentValue);
                }
                // If budgetAmountInput is empty, amountAdjustment will not be sent,
                // and the backend will only update other fields if they changed.

                const success = await updateBudgetOnServer(editingBudgetId, budgetDataForUpdate);
                if (success) {
                    closeModalAndReset();
                }
               
            } else { // ADD NEW BUDGET LOGIC 
                const amount = parseFloat(budgetAmountInput.value);
                if (isNaN(amount) || amount <= 0) { 
                    alert("Please enter a valid positive budget amount.");
                    return;
                }
                const newBudgetDataForServer = {
                    category: category,
                    amount: amount,
                    date: startDate, 
                    description: description
                };
                
                const success = await addBudgetToServer(newBudgetDataForServer);
                if (success) {
                    closeModalAndReset();
                }
            }
        });

        // Delete Confirmation
        window.showDeleteConfirmation = function(id) {
            budgetToDeleteId = id;
            confirmDialog.classList.add('active');
        }

        cancelDeleteBtn.addEventListener('click', function() {
            confirmDialog.classList.remove('active');
            budgetToDeleteId = null;
        });

        confirmDeleteBtn.addEventListener('click', async function() {
            if (budgetToDeleteId !== null) {
                const success = await deleteBudgetFromServer(budgetToDeleteId);
                if (success) {
                    confirmDialog.classList.remove('active');
                    budgetToDeleteId = null;
                }
            }
        });

    </script>
    
</body>

</html>