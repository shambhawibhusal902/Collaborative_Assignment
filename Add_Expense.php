<?php
// Add_Expense.php (Combined Add, View, Edit, Delete)

// Start session
session_start();

// Include database configuration and operations
require_once 'config/db_config.php'; // Adjust path if needed
require_once 'config/db_operations.php'; // Adjust path if needed

$message = ''; // To store success or error messages
$message_type = ''; // 'success' or 'error'

// --- IMPORTANT: User Authentication ---
if (!isset($_SESSION['user_id'])) {
    // Fallback: Try to fetch the first user ID if no session exists (for initial setup/testing)
    // WARNING: This fallback is NOT secure for production. Always require login.
    $result = mysqli_query($conn, "SELECT id FROM users LIMIT 1");
    if ($result && mysqli_num_rows($result) > 0) {
        $_SESSION['user_id'] = mysqli_fetch_assoc($result)['id'];
        error_log("Warning: No user session found. Using first user ID ({$_SESSION['user_id']}) as fallback in Add_Expense.php");
    } else {
         die("Error: No users found in the database and no user session active. Please register a user or ensure you are logged in.");
    }
     mysqli_free_result($result);
}
$user_id = $_SESSION['user_id'];
// --- End Authentication ---

// --- Handle Delete Expense Request (Moved from delete_expense.php) ---
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $expense_id_to_delete = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    $redirect_url_self = htmlspecialchars($_SERVER['PHP_SELF']); // Redirect back to this page

    if (!$expense_id_to_delete) {
        // Invalid ID format
        error_log("Delete Expense Error: Invalid ID format. UserID={$user_id}.");
        header('Location: ' . $redirect_url_self . '?status=expense_delete_error&reason=invalid_id');
        exit;
    }

    error_log("Delete Expense Attempt: UserID={$user_id}, ExpenseID={$expense_id_to_delete}");

    // Authorization: Verify this expense record belongs to the logged-in user
    $expense = getRecordById('expenses', $expense_id_to_delete); // Fetch expense details

    if (!$expense) {
        // Expense not found
        error_log("Delete Expense Error: Record not found ID={$expense_id_to_delete}. UserID={$user_id}");
        header('Location: ' . $redirect_url_self . '?status=expense_delete_error&reason=not_found');
        exit;
    }

    // Check if the user_id on the expense record matches the logged-in user
    if ($expense['user_id'] != $user_id) {
         // User does not own this expense - Deny deletion
         error_log("Delete Expense Error: Authorization failed. User {$user_id} cannot delete record {$expense_id_to_delete} owned by {$expense['user_id']}.");
         header('Location: ' . $redirect_url_self . '?status=expense_delete_error&reason=auth_failed');
         exit;
    }

    // --- Attempt Deletion ---
    error_log("Attempting delete for ExpenseID={$expense_id_to_delete} by UserID={$user_id}");
    $deleteResult = deleteRecord('expenses', $expense_id_to_delete);
    $db_error_after_delete = mysqli_error($conn); // Capture any immediate error

    // --- Verification Step ---
    $checkRecord = getRecordById('expenses', $expense_id_to_delete);
    $record_is_gone = !$checkRecord;

    error_log("Delete attempt for ExpenseID={$expense_id_to_delete}: deleteRecord returned=" . ($deleteResult ? 'true' : 'false') . ", record_is_gone=" . ($record_is_gone ? 'true' : 'false'));

    // --- Decide Redirect based on Verification ---
    if ($record_is_gone) {
        if (!$deleteResult) {
            error_log("INFO: Delete for ExpenseID={$expense_id_to_delete} succeeded (record gone), but deleteRecord returned false. Forcing success redirect.");
        } else {
            error_log("SUCCESS: Delete for ExpenseID={$expense_id_to_delete} confirmed. Redirecting status=expense_deleted.");
        }
        header('Location: ' . $redirect_url_self . '?status=expense_deleted');
        exit; // IMPORTANT: Stop script execution after redirect
    } else {
        // If the record is *still there*, the deletion failed.
        $reason = 'execute_failed';
        if ($deleteResult) {
            $reason = 'verify_failed';
            error_log("WARNING: Delete for ExpenseID={$expense_id_to_delete} failed verification despite deleteRecord returning true.");
        }
        error_log("FAILURE: Delete for ExpenseID={$expense_id_to_delete} failed (record still exists). deleteRecord returned=" . ($deleteResult ? 'true' : 'false') . ". DB Error (if any): {$db_error_after_delete}");
        header('Location: ' . $redirect_url_self . '?status=expense_delete_error&reason=' . $reason);
        exit; // IMPORTANT: Stop script execution after redirect
    }
}
// --- End Delete Expense Handling ---


// Check for messages from redirects (Expanded for Expenses - Includes delete statuses)
if (isset($_GET['status'])) {
    switch ($_GET['status']) {
        // Expense Statuses
        case 'expense_deleted': $message = 'Expense record deleted successfully!'; $message_type = 'success'; break;
        case 'expense_delete_error': $message = 'Error deleting expense record.'; $message_type = 'error'; break;
        case 'expense_updated': $message = 'Expense record updated successfully!'; $message_type = 'success'; break;
        case 'expense_update_error': $message = 'Error updating expense record.'; $message_type = 'error'; break;
        case 'expense_update_auth_error': $message = 'Error updating record: Permission denied.'; $message_type = 'error'; break;
        case 'expense_added': $message = 'Expense record added successfully!'; $message_type = 'success'; break;
        case 'expense_add_error': $message = 'Error adding expense record.'; $message_type = 'error'; break;
        // Keep Income Statuses if needed
        case 'deleted': $message = 'Income record deleted successfully!'; $message_type = 'success'; break;
        case 'delete_error': $message = 'Error deleting income record.'; $message_type = 'error'; break;
        case 'updated': $message = 'Income record updated successfully!'; $message_type = 'success'; break;
        case 'update_error': $message = 'Error updating income record.'; $message_type = 'error'; break;
    }
    // Add reason if provided
    if (isset($_GET['reason']) && ($message_type == 'error')) {
        $message .= ' Reason: ' . htmlspecialchars($_GET['reason']);
    }
}


// --- Handle Add Expense Form Submission ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_expense'])) {
    // ... (Keep existing Add Expense logic exactly as it was) ...
     $category = isset($_POST['expenseCategory']) ? trim($_POST['expenseCategory']) : '';
     $amount = isset($_POST['expenseAmount']) ? trim($_POST['expenseAmount']) : '';
     $date = isset($_POST['expenseDate']) ? trim($_POST['expenseDate']) : '';
     $description = isset($_POST['expenseDescription']) ? trim($_POST['expenseDescription']) : '';

     if (empty($category) || empty($amount) || !is_numeric($amount) || $amount <= 0 || empty($date)) {
         $message = 'Invalid input. Please fill Category, Amount, and Date correctly.';
         $message_type = 'error';
     } else {
         $data = [
             'user_id' => $user_id,
             'category' => $category,
             'amount' => (float)$amount,
             'date' => $date,
             'description' => $description
         ];

         insertRecord('expenses', $data);
         $last_inserted_id = mysqli_insert_id($conn);

         if ($last_inserted_id > 0) {
             header("Location: " . htmlspecialchars($_SERVER['PHP_SELF']) . "?status=expense_added");
             exit;
         } else {
              $db_error = mysqli_error($conn);
              error_log("Expense Add Error (User ID: {$user_id}): " . $db_error);
              header("Location: " . htmlspecialchars($_SERVER['PHP_SELF']) . "?status=expense_add_error");
              exit;
         }
     }
}


// --- Handle Edit Expense Form Submission (from Modal) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_expense'])) {
    // ... (Keep existing Edit Expense logic exactly as it was) ...
    $expense_id = isset($_POST['editExpenseId']) ? (int)$_POST['editExpenseId'] : 0;
    $category = isset($_POST['editCategory']) ? trim($_POST['editCategory']) : '';
    $amount = isset($_POST['editAmount']) ? trim($_POST['editAmount']) : '';
    $date = isset($_POST['editDate']) ? trim($_POST['editDate']) : '';
    $description = isset($_POST['editDescription']) ? trim($_POST['editDescription']) : '';

    if ($expense_id <= 0 || empty($category) || empty($amount) || !is_numeric($amount) || $amount <= 0 || empty($date)) {
         header("Location: " . htmlspecialchars($_SERVER['PHP_SELF']) . "?status=expense_update_error&reason=invalid_input");
         exit;
    } else {
        $check_sql = "SELECT id FROM expenses WHERE id = ? AND user_id = ?";
        $stmt_check = mysqli_prepare($conn, $check_sql);
        $owns_record = false;
        if ($stmt_check) {
            mysqli_stmt_bind_param($stmt_check, "ii", $expense_id, $user_id);
            mysqli_stmt_execute($stmt_check);
            mysqli_stmt_store_result($stmt_check);
            $owns_record = mysqli_stmt_num_rows($stmt_check) > 0;
            mysqli_stmt_close($stmt_check);
        } else {
             error_log("Update ownership check failed (Expense ID: {$expense_id}): " . mysqli_error($conn));
             header("Location: " . htmlspecialchars($_SERVER['PHP_SELF']) . "?status=expense_update_error&reason=db_check_failed");
             exit;
        }

        if ($owns_record) {
            $update_data = [
                'category' => $category,
                'amount' => (float)$amount,
                'date' => $date,
                'description' => $description
            ];

            $update_function_returned = updateRecord('expenses', $expense_id, $update_data);
            $db_error_after_update = trim(mysqli_error($conn));

            if ($update_function_returned || (!$update_function_returned && empty($db_error_after_update)) ) {
                error_log("Expense Update SUCCESS or 0 rows affected: ID={$expense_id}. Redirecting status=expense_updated.");
                header("Location: " . htmlspecialchars($_SERVER['PHP_SELF']) . "?status=expense_updated");
                exit;
            } else {
                error_log("Expense Update FAILED: ID={$expense_id}, User ID: {$user_id}. DB Error: {$db_error_after_update}");
                header("Location: " . htmlspecialchars($_SERVER['PHP_SELF']) . "?status=expense_update_error&reason=db_update_failed");
                exit;
            }
        } else {
            error_log("Unauthorized expense update attempt (Expense ID: {$expense_id}, User ID: {$user_id})");
            header("Location: " . htmlspecialchars($_SERVER['PHP_SELF']) . "?status=expense_update_auth_error");
            exit;
        }
    }
}

// --- Fetch Existing Expenses ---
// ... (Keep existing Fetch Expenses logic exactly as it was, including sorting/filtering) ...
$current_user_id = $user_id;
$expenses = [];
$fetch_error = '';
$sort_options = [
    'date-desc' => ['column' => 'date', 'order' => 'DESC'],
    'date-asc' => ['column' => 'date', 'order' => 'ASC'],
    'amount-desc' => ['column' => 'amount', 'order' => 'DESC'],
    'amount-asc' => ['column' => 'amount', 'order' => 'ASC'],
    'category-asc' => ['column' => 'category', 'order' => 'ASC'],
    'category-desc' => ['column' => 'category', 'order' => 'DESC'],
];
$sort_key = isset($_GET['sortBy']) && array_key_exists($_GET['sortBy'], $sort_options) ? $_GET['sortBy'] : 'date-desc';
$sort_by_column = $sort_options[$sort_key]['column'];
$sort_order = $sort_options[$sort_key]['order'];
$category_filter = isset($_GET['categoryFilter']) && $_GET['categoryFilter'] !== 'all' ? trim($_GET['categoryFilter']) : null;
$sql = "SELECT * FROM expenses WHERE user_id = ?";
$params = [$current_user_id];
$types = 'i';
if ($category_filter) {
    $sql .= " AND category = ?";
    $params[] = $category_filter;
    $types .= 's';
}
$sql .= " ORDER BY `$sort_by_column` $sort_order";
$stmt = mysqli_prepare($conn, $sql);
if ($stmt) {
    if (count($params) > 0) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        if ($result) {
            $expenses = mysqli_fetch_all($result, MYSQLI_ASSOC);
            mysqli_free_result($result);
        } else { $fetch_error = "Error fetching results: " . mysqli_error($conn); }
    } else { $fetch_error = "Error executing statement: " . mysqli_stmt_error($stmt); }
    mysqli_stmt_close($stmt);
} else { $fetch_error = "Error preparing statement: " . mysqli_error($conn); }
if ($fetch_error) {
    error_log("Expense Fetch Error (User ID: {$current_user_id}): " . $fetch_error);
    if (empty($message)) { $message = "Error loading expense history."; $message_type = 'error'; }
}

// --- Get Distinct Categories for Filter ---
// ... (Keep existing Category Fetch logic exactly as it was) ...
$expense_categories = [];
$cat_sql = "SELECT DISTINCT category FROM expenses WHERE user_id = ? ORDER BY category ASC";
$cat_stmt = mysqli_prepare($conn, $cat_sql);
if ($cat_stmt) {
    mysqli_stmt_bind_param($cat_stmt, 'i', $current_user_id);
    if (mysqli_stmt_execute($cat_stmt)) {
        $cat_result = mysqli_stmt_get_result($cat_stmt);
        while ($row = mysqli_fetch_assoc($cat_result)) {
            $expense_categories[] = $row['category'];
        }
        mysqli_free_result($cat_result);
    } else { error_log("Error fetching expense categories: " . mysqli_stmt_error($cat_stmt)); }
    mysqli_stmt_close($cat_stmt);
} else { error_log("Error preparing category statement: " . mysqli_error($conn)); }

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expenzo - Add/View Expenses</title>
    <style>
        /* --- PASTE ALL YOUR EXISTING CSS HERE --- */
         :root { /* Colors */ --primary-color: #00563F; --primary-light: #93C572; --secondary-color: #2A2C2C; --dark-bg: #222; --light-bg: #f5f5f5; --white: #ffffff; --border-light: #e0e0e0; --text-dark: #333; --text-light: #777; /* Spacing */ --space-xs: 5px; --space-sm: 10px; --space-md: 15px; --space-lg: 20px; --space-xl: 30px; /* Elements */ --card-radius: 8px; --sidebar-width: 250px; --avatar-size: 40px; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { display: flex; background-color: var(--light-bg); min-height: 100vh; overflow-x: hidden; }
        .sidebar { width: var(--sidebar-width); background-color: var(--dark-bg); color: var(--white); height: 100vh; padding: var(--space-lg); position: fixed; left: 0; top: 0; z-index: 10; transition: transform 0.3s ease; }
        .logo { font-size: 24px; font-weight: bold; margin-bottom: var(--space-xl); padding-bottom: var(--space-sm); border-bottom: 1px solid #444; }
        .user-profile { display: flex; align-items: center; margin-bottom: var(--space-xl); }
        .avatar { width: var(--avatar-size); height: var(--avatar-size); border-radius: 50%; background-color: #444; margin-right: var(--space-md); overflow: hidden; display: flex; align-items: center; justify-content: center; color: var(--white); font-weight: bold; cursor:pointer; }
        .user-name { cursor:pointer; }
        .nav-menu { list-style: none; }
        .nav-item { display: flex; align-items: center; padding: var(--space-lg) 0; cursor: pointer; transition: all 0.3s; }
        .nav-item:hover { color: var(--primary-light); }
        .nav-item.active { color: var(--primary-light); font-weight: bold; }
        .nav-icon { margin-right: var(--space-md); width: 20px; text-align: center; }
        .main-content { flex: 1; padding: var(--space-lg) var(--space-xl); margin-left: var(--sidebar-width); max-width: calc(1440px - var(--sidebar-width)); width: 100%; transition: margin-left 0.3s ease; }
        .header {  width: 1150px; margin-left:30px; margin-bottom: 20px; /* Reduced margin */ border-bottom: 1px solid var(--border-light); padding-bottom: var(--space-md); }
        .menu-toggle { display: none; position: fixed; top: var(--space-sm); left: var(--space-sm); z-index: 20; background-color: var(--primary-color); color: var(--white); border: none; border-radius: 4px; width: 40px; height: 40px; font-size: 20px; cursor: pointer; }
        @media (max-width: 992px) { .menu-toggle { display: block; } .sidebar { transform: translateX(-100%); } .sidebar.active { transform: translateX(0); } .main-content { margin-left: 0; padding: var(--space-lg); padding-top: 60px; /* Adjust top padding for menu toggle */ max-width: 100%; } }
        .dashboard-container { display: flex; flex-wrap: wrap; /* Allow wrapping */ gap: 20px; }
        .add-expense { padding: 20px;  margin-left: 35px; max-width: 50%; background-color: #fff; border: 1px solid #ddd; border-radius: var(--card-radius); box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05); flex: 1 1 400px; height:565px; }
         .add-expense h2 { margin-top: 0; margin-bottom: 25px; border-bottom: 1px solid #eee; padding-bottom: 10px; font-size: 1.3em; color: var(--text-dark); }
         .expense-form { display: flex; flex-direction: column; }
         .form-group { margin-bottom: 18px; }
         .expense-form label { margin-bottom: 8px; display: block; font-weight: 600; font-size: 0.95em; color: #555; }
         .expense-form input, .expense-form select, .expense-form textarea { padding: 10px 12px; width: 100%; border: 1px solid #ccc; border-radius: 4px; font-size: 1rem; }
         .expense-form textarea { min-height: 80px; resize: vertical; }
         .expense-form input:focus, .expense-form select:focus, .expense-form textarea:focus { border-color: var(--primary-color); outline: none; box-shadow: 0 0 0 2px rgba(0, 86, 63, 0.2); }
         .expense-form button { padding: 12px 25px; background-color: var(--primary-color); color: white; border: none; border-radius: 4px; cursor: pointer; align-self: flex-start; margin-top: 10px; font-size: 1rem; font-weight: 500; transition: background-color 0.2s ease; }
         .expense-form button:hover { background-color: #00402e; }
        .expense-list-container { padding: 20px; background-color: #fff; border: 1px solid #ddd; border-radius: var(--card-radius); box-shadow: 0 2px 4px rgba(0,0,0,0.05); min-height: 500px; flex: 1 1 400px; max-width: 48%; min-height: 500px; display: flex; flex-direction: column;  max-height: 565px; /* Example: Set a max-height for the whole box */ display: flex; flex-direction: column; margin-right: -50px; margin-left: 20px; flex: 1 1 400px; }
         .expense-list-container h2 { margin-top: 0; margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 10px; font-size: 1.3em; color: var(--text-dark); flex-shrink: 0; }
         .expense-list-container ul { list-style: none; padding: 0; margin: 0; overflow-y: auto; flex-grow: 1; }
         .expense-list-container li { padding: 12px 5px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; gap: 10px; }
         .expense-list-container li:last-child { border-bottom: none; }
         .expense-info { display: flex; flex-direction: column; flex-grow: 1; overflow: hidden; margin-right:5px; }
         .expense-category { font-weight: 600; color: var(--text-dark); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-size: 0.95em; }
         .expense-description { font-size: 0.85em; color: var(--text-light); margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
         .expense-date { font-size: 0.8em; color: #999; margin-top: 3px; }
         .expense-amount-actions { display: flex; align-items: center; gap: 10px; flex-shrink: 0; }
         .expense-amount { font-weight: 600; color: #00563F; font-size: 0.95em; white-space: nowrap; }
         .actions { display: flex; gap: 6px; }
         .edit-btn, .delete-btn { background: none; border: none; cursor: pointer; font-size: 1.0rem; color: #888; padding: 2px; transition: color 0.2s ease; text-decoration: none; display: inline-block; vertical-align: middle; }
         .edit-btn:hover { color: var(--primary-color); }
         .delete-btn:hover { color: #d9534f; }
        .filter-controls { display: flex; flex-wrap: wrap; margin-bottom: 15px; gap: 10px; align-items: center; padding: 10px; background-color: #f8f9fa; border-radius: 4px; border: 1px solid #eee; flex-shrink: 0; }
        .filter-controls select { padding: 8px 10px; border-radius: 4px; border: 1px solid #ccc; font-size: 0.9em; height: 36px; flex-grow: 1; min-width: 120px; }
        .filter-controls button { padding: 8px 15px; background-color: var(--secondary-color); color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.9em; height: 36px; transition: background-color 0.2s ease; }
        .filter-controls button:hover { background-color: #444; }
        .message-box { padding: 12px 15px; margin-bottom: 20px; border-radius: 4px; border: 1px solid transparent; font-size: 0.95em; display: flex; justify-content: space-between; align-items: center; opacity: 1; transition: opacity 0.5s ease-out; /* Added for fade out */ }
        .message-success { background-color: #d1e7dd; color: #0f5132; border-color: #badbcc; }
        .message-error { background-color: #f8d7da; color: #842029; border-color: #f5c2c7; }
        .message-info { background-color: #cff4fc; color: #055160; border-color: #b6effb; }
        .message-box .close-msg { background: none; border: none; font-size: 1.4em; font-weight: bold; cursor: pointer; color: inherit; opacity: 0.6; padding: 0 5px; line-height: 1; }
        .message-box .close-msg:hover { opacity: 1; }
        .modal { display: none; position: fixed; z-index: 1050; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); overflow: auto; animation: fadeInModal 0.3s; }
        @keyframes fadeInModal { from { opacity: 0; } to { opacity: 1; } }
        .modal-content { position: relative; background-color: white; margin: 10% auto; padding: 25px 30px; border-radius: var(--card-radius); width: 90%; max-width: 500px; box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
        .close-btn { position: absolute; top: 10px; right: 15px; font-size: 1.5rem; color: #aaa; cursor: pointer; line-height: 1; border:none; background:none; }
        .close-btn:hover { color: #333; }
        .modal h3 { margin-top:0; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid #eee; font-size: 1.25em; }
        .modal-form .form-group { margin-bottom: 15px; }
        .modal-form label { margin-bottom: 6px; display: block; font-weight: 600; font-size: 0.9em; }
        .modal-form input, .modal-form select, .modal-form textarea { padding: 9px 12px; width: 100%; border: 1px solid #ccc; border-radius: 4px; font-size: 0.95rem; }
        .modal-form textarea { min-height: 70px; resize: vertical; }
        .modal-form input:focus, .modal-form select:focus, .modal-form textarea:focus { border-color: var(--primary-color); outline: none; box-shadow: 0 0 0 2px rgba(0, 86, 63, 0.2); }
        .modal-form button { padding: 10px 20px; background-color: var(--primary-color); color: white; border: none; border-radius: 4px; cursor: pointer; margin-top: 10px; font-size: 0.95rem; float: right; }
        .modal-form button:hover { background-color: #00402e; }
    </style>
</head>

<body>
    <button class="menu-toggle" id="menuToggle">☰</button>

    <!-- Sidebar -->
     <div class="sidebar" id="sidebar">
        <!-- ... (Sidebar HTML remains the same) ... -->
         <div class="logo">Expenzo</div>
         <div class="user-profile">
             <div class="avatar" onclick="window.location.href='#'">U</div> <!-- Placeholder -->
             <div class="user-name" onclick="window.location.href='#'">User <?php echo htmlspecialchars($user_id); ?></div> <!-- Display User ID -->
         </div>
         <ul class="nav-menu">
             <li class="nav-item" onclick="window.location.href='Total_Expense.php'"> <span class="nav-icon">📊</span> Dashboard </li>
             <li class="nav-item active"> <span class="nav-icon">💸</span> Expense </li> <!-- Active -->
             <li class="nav-item" onclick="window.location.href='Add_Income.php'"> <span class="nav-icon">💰</span> Income </li>
             <li class="nav-item" onclick="window.location.href='Budgets.php'"> <span class="nav-icon">💹</span> Budgets </li>
             <li class="nav-item" onclick="window.location.href='View_Transactions.php'"> <span class="nav-icon">📋</span> View Transactions </li>
         </ul>
    </div>


    <main class="main-content">
        <header class="header">
            <!-- PHP Status Messages -->
            <?php if (!empty($message)): ?>
            <div class="message-box <?php echo 'message-' . htmlspecialchars($message_type); ?>" id="statusMessage">
                <span><?php echo htmlspecialchars($message); ?></span>
                <button type="button" class="close-msg" onclick="this.parentElement.style.display='none'" aria-label="Close">×</button>
            </div>
            <?php endif; ?>
        </header>

        <div class="dashboard-container">
            <!-- Add Expense Form -->
            <div class="add-expense">
                 <!-- ... (Add Expense Form HTML remains the same) ... -->
                 <h2>Add New Expense</h2>
                 <form class="expense-form" id="addExpenseForm" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                     <div class="form-group">
                         <label for="expenseCategory">Category</label>
                         <select id="expenseCategory" name="expenseCategory" required>
                             <option value="">Select Category...</option>
                             <option value="Food">Food</option>
                             <option value="Transportation">Transportation</option>
                             <option value="Shopping">Shopping</option>
                             <option value="Entertainment">Entertainment</option>
                             <option value="Utilities">Utilities</option>
                             <option value="Housing">Housing</option>
                             <option value="Healthcare">Healthcare</option>
                             <option value="Personal Care">Personal Care</option>
                             <option value="Education">Education</option>
                             <option value="Debt Payment">Debt Payment</option>
                             <option value="Other">Other</option>
                         </select>
                     </div>
                     <div class="form-group">
                         <label for="expenseAmount">Amount (Rs.)</label>
                         <input type="number" id="expenseAmount" name="expenseAmount" placeholder="e.g., 500.00" required min="0.01" step="0.01" />
                     </div>
                     <div class="form-group">
                         <label for="expenseDate">Date</label>
                         <input type="date" id="expenseDate" name="expenseDate" required value="<?php echo date('Y-m-d'); ?>" />
                     </div>
                     <div class="form-group">
                         <label for="expenseDescription">Description (Optional)</label>
                         <textarea id="expenseDescription" name="expenseDescription" placeholder="e.g., Lunch with colleagues"></textarea>
                     </div>
                     <button type="submit" name="add_expense">Add Expense</button>
                 </form>
            </div>

            <!-- Expense List -->
            <div class="expense-list-container">
                <h2>Expense History</h2>
                <!-- Filter/Sort Form -->
                <form method="GET" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="filter-controls">
                     <!-- ... (Filter/Sort Form HTML remains the same) ... -->
                     <select name="categoryFilter" id="categoryFilter" title="Filter by Category">
                         <option value="all" <?php echo ($category_filter === null) ? 'selected' : ''; ?>>All Categories</option>
                         <?php foreach ($expense_categories as $cat): ?>
                         <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo ($category_filter === $cat) ? 'selected' : ''; ?>>
                             <?php echo htmlspecialchars($cat); ?>
                         </option>
                         <?php endforeach; ?>
                         <?php if ($category_filter !== null && !in_array($category_filter, $expense_categories)): ?>
                         <option value="<?php echo htmlspecialchars($category_filter); ?>" selected><?php echo htmlspecialchars($category_filter); ?> (No results)</option>
                         <?php endif; ?>
                     </select>
                     <select name="sortBy" id="sortBy" title="Sort By">
                         <option value="date-desc" <?php echo ($sort_key == 'date-desc') ? 'selected' : ''; ?>>Date (Newest)</option>
                         <option value="date-asc" <?php echo ($sort_key == 'date-asc') ? 'selected' : ''; ?>>Date (Oldest)</option>
                         <option value="amount-desc" <?php echo ($sort_key == 'amount-desc') ? 'selected' : ''; ?>>Amount (Highest)</option>
                         <option value="amount-asc" <?php echo ($sort_key == 'amount-asc') ? 'selected' : ''; ?>>Amount (Lowest)</option>
                         <option value="category-asc" <?php echo ($sort_key == 'category-asc') ? 'selected' : ''; ?>>Category (A-Z)</option>
                         <option value="category-desc" <?php echo ($sort_key == 'category-desc') ? 'selected' : ''; ?>>Category (Z-A)</option>
                     </select>
                     <button type="submit">Apply</button>
                </form>

                <!-- List populated by PHP -->
                <ul id="expensesList">
                    <?php if (!empty($fetch_error)): ?>
                        <li><p style="color: red; text-align: center; padding: 20px;"><?php echo htmlspecialchars($fetch_error); ?></p></li>
                    <?php elseif (empty($expenses)): ?>
                        <li><p style="text-align: center; padding: 20px; color: var(--text-light);">
                            <?php echo ($category_filter) ? 'No expenses match the selected filter.' : 'No expenses recorded yet.'; ?>
                        </p></li>
                    <?php else: ?>
                        <?php foreach ($expenses as $expense): ?>
                            <li data-id="<?php echo $expense['id']; ?>"
                                data-category="<?php echo htmlspecialchars($expense['category']); ?>"
                                data-amount="<?php echo htmlspecialchars($expense['amount']); ?>"
                                data-date="<?php echo htmlspecialchars($expense['date']); ?>"
                                data-description="<?php echo htmlspecialchars($expense['description']); ?>">
                                <div class="expense-info">
                                    <span class="expense-category"><?php echo htmlspecialchars($expense['category']); ?></span>
                                    <span class="expense-description"><?php echo !empty($expense['description']) ? htmlspecialchars($expense['description']) : '<i>No description</i>'; ?></span>
                                    <span class="expense-date"><?php echo date("M d, Y", strtotime($expense['date'])); ?></span>
                                </div>
                                <div class="expense-amount-actions">
                                    <span class="expense-amount">Rs.<?php echo number_format($expense['amount'], 2); ?></span>
                                    <span class="actions">
                                        <!-- Edit button triggers JS modal -->
                                        <button type="button" class="edit-btn" aria-label="Edit Expense" title="Edit Expense">🖊️</button>
                                        <!-- DELETE LINK MODIFIED -->
                                        <a href="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>?action=delete&id=<?php echo $expense['id']; ?>"
                                           class="delete-btn"
                                           aria-label="Delete Expense"
                                           title="Delete Expense"
                                           onclick="return confirm('Are you sure you want to permanently delete this expense record?');">
                                           🗑️
                                        </a>
                                    </span>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </main>

    <!-- Edit Expense Modal -->
    <div class="modal" id="editExpenseModal">
         <!-- ... (Edit Modal HTML remains the same) ... -->
         <div class="modal-content">
             <button type="button" class="close-btn" id="closeExpenseModal" aria-label="Close Modal">×</button>
             <h3>Edit Expense Record</h3>
             <form class="modal-form" id="editExpenseForm" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                 <input type="hidden" id="editExpenseId" name="editExpenseId">
                 <div class="form-group">
                     <label for="editCategory">Category</label>
                     <select id="editCategory" name="editCategory" required>
                          <option value="">Select Category...</option>
                          <option value="Food">Food</option>
                          <option value="Transportation">Transportation</option>
                          <option value="Shopping">Shopping</option>
                          <option value="Entertainment">Entertainment</option>
                          <option value="Utilities">Utilities</option>
                          <option value="Housing">Housing</option>
                          <option value="Healthcare">Healthcare</option>
                          <option value="Personal Care">Personal Care</option>
                          <option value="Education">Education</option>
                          <option value="Debt Payment">Debt Payment</option>
                          <option value="Other">Other</option>
                     </select>
                 </div>
                 <div class="form-group">
                     <label for="editAmount">Amount</label>
                     <input type="number" id="editAmount" name="editAmount" placeholder="Enter amount" required min="0.01" step="0.01" />
                 </div>
                 <div class="form-group">
                     <label for="editDate">Date</label>
                     <input type="date" id="editDate" name="editDate" required />
                 </div>
                  <div class="form-group">
                     <label for="editDescription">Description (Optional)</label>
                     <textarea id="editDescription" name="editDescription" placeholder="Update description"></textarea>
                 </div>
                 <button type="submit" name="update_expense">Update Expense</button>
             </form>
         </div>
    </div>


    <script>
        // --- PASTE ALL YOUR EXISTING JAVASCRIPT HERE ---
        // No changes needed in the JavaScript section for this merge.
        // The edit modal logic and delete confirmation logic remain the same.
        document.addEventListener('DOMContentLoaded', function () {
            const menuToggle = document.getElementById('menuToggle');
            const sidebar = document.getElementById('sidebar');
            if (menuToggle && sidebar) {
                menuToggle.addEventListener('click', () => sidebar.classList.toggle('active'));
                document.addEventListener('click', (event) => {
                    if (window.innerWidth <= 992 && sidebar.classList.contains('active') &&
                        !sidebar.contains(event.target) && !menuToggle.contains(event.target)) {
                        sidebar.classList.remove('active');
                    }
                });
            }

             try {
                const today = new Date();
                const formattedDate = today.toISOString().substr(0, 10);
                const expenseDateInput = document.getElementById('expenseDate');
                if (expenseDateInput && !expenseDateInput.value) {
                    expenseDateInput.value = formattedDate;
                }
            } catch (e) { console.error("Error setting default date:", e); }

            const expenseModal = document.getElementById('editExpenseModal');
            const closeExpenseBtn = document.getElementById('closeExpenseModal');
            const expensesList = document.getElementById('expensesList');

            function openEditExpenseModal(expenseData) {
                if (!expenseModal || !expenseData) { console.error("Expense Modal or expense data missing"); return; }
                document.getElementById('editExpenseId').value = expenseData.id || '';
                document.getElementById('editCategory').value = expenseData.category || '';
                document.getElementById('editAmount').value = expenseData.amount || '';
                document.getElementById('editDate').value = expenseData.date || '';
                document.getElementById('editDescription').value = expenseData.description || '';
                expenseModal.style.display = 'block';
            }

            if (expensesList && expenseModal) {
                 expensesList.addEventListener('click', function(event) {
                    const editButton = event.target.closest('.edit-btn');
                    if (editButton) {
                        event.preventDefault();
                        const listItem = editButton.closest('li[data-id]');
                        if (listItem && listItem.dataset) {
                            const expenseData = {
                                id: listItem.dataset.id,
                                category: listItem.dataset.category,
                                amount: listItem.dataset.amount,
                                date: listItem.dataset.date,
                                description: listItem.dataset.description
                            };
                            openEditExpenseModal(expenseData);
                        } else { console.error("Could not find parent list item or data attributes for edit button."); }
                    }
                    // Delete link click is handled by the href and onclick attribute directly
                });
            } else {
                 if (!expensesList) console.error("Expense list container (ul#expensesList) not found.");
                 if (!expenseModal) console.error("Edit expense modal (#editExpenseModal) not found.");
            }

            if (closeExpenseBtn && expenseModal) {
                closeExpenseBtn.addEventListener('click', () => { expenseModal.style.display = 'none'; });
            }
            window.addEventListener('click', (event) => {
                if (event.target === expenseModal) { expenseModal.style.display = 'none'; }
            });

            const statusMessageBox = document.getElementById('statusMessage');
            if (statusMessageBox) {
                const closeMsgBtn = statusMessageBox.querySelector('.close-msg');
                if (closeMsgBtn) {
                    setTimeout(() => {
                        if (statusMessageBox) { // Re-check if element still exists
                            statusMessageBox.style.opacity = '0';
                            setTimeout(() => {
                               if (statusMessageBox) statusMessageBox.style.display = 'none';
                             }, 600); // Match CSS transition duration
                        }
                    }, 7000); // 7 seconds
                }
            }
        });
    </script>
</body>
</html>