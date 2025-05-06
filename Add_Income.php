<?php
// Add_Income.php (Combined Add, View, Edit, Delete)

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
        error_log("Warning: No user session found. Using first user ID ({$_SESSION['user_id']}) as fallback in Add_Income.php");
    } else {
         // If still no user ID, stop execution.
         die("Error: No users found in the database and no user session active. Please register a user or ensure you are logged in.");
    }
     mysqli_free_result($result);
}
$user_id = $_SESSION['user_id'];
// --- End Authentication ---

// --- Handle Delete Income Request (Moved from delete_income.php) ---
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $income_id_to_delete = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    $redirect_url_self = htmlspecialchars($_SERVER['PHP_SELF']); // Redirect back to this page

    if (!$income_id_to_delete) {
        // Invalid ID format
        error_log("Delete Income Error: Invalid ID format. UserID={$user_id}.");
        header('Location: ' . $redirect_url_self . '?status=delete_error&reason=invalid_id');
        exit;
    }

    error_log("Delete Income Attempt: UserID={$user_id}, IncomeID={$income_id_to_delete}");

    // Authorization: Verify this income record belongs to the logged-in user
    $income = getRecordById('incomes', $income_id_to_delete);

    if (!$income) {
        // Income not found
        error_log("Delete Income Error: Record not found ID={$income_id_to_delete}. UserID={$user_id}");
        header('Location: ' . $redirect_url_self . '?status=delete_error&reason=not_found');
        exit;
    }

    // Check if the user_id on the income record matches the logged-in user
    if ($income['user_id'] != $user_id) {
         // User does not own this income - Deny deletion
         error_log("Delete Income Error: Authorization failed. User {$user_id} cannot delete record {$income_id_to_delete} owned by {$income['user_id']}.");
         header('Location: ' . $redirect_url_self . '?status=delete_error&reason=auth_failed'); // Consistent reason
         exit;
    }

    // --- Attempt Deletion ---
    error_log("Attempting delete for IncomeID={$income_id_to_delete} by UserID={$user_id}");
    $deleteResult = deleteRecord('incomes', $income_id_to_delete);
    $db_error_after_delete = mysqli_error($conn);

    // --- Verification Step ---
    $checkRecord = getRecordById('incomes', $income_id_to_delete);
    $record_is_gone = !$checkRecord;

    error_log("Delete attempt for IncomeID={$income_id_to_delete}: deleteRecord returned=" . ($deleteResult ? 'true' : 'false') . ", record_is_gone=" . ($record_is_gone ? 'true' : 'false'));

    // --- Decide Redirect based on Verification ---
    if ($record_is_gone) {
        if (!$deleteResult) {
            error_log("INFO: Delete for IncomeID={$income_id_to_delete} succeeded (record gone), but deleteRecord returned false. Forcing success redirect.");
        } else {
            error_log("SUCCESS: Delete for IncomeID={$income_id_to_delete} confirmed. Redirecting status=deleted.");
        }
        header('Location: ' . $redirect_url_self . '?status=deleted');
        exit;
    } else {
        $reason = 'execute_failed';
        if ($deleteResult) { // If deleteRecord said true, but record is still there
            $reason = 'verify_failed';
            error_log("WARNING: Delete for IncomeID={$income_id_to_delete} failed verification despite deleteRecord returning true.");
        }
        error_log("FAILURE: Delete for IncomeID={$income_id_to_delete} failed (record still exists). deleteRecord returned=" . ($deleteResult ? 'true' : 'false') . ". DB Error (if any): {$db_error_after_delete}");
        header('Location: ' . $redirect_url_self . '?status=delete_error&reason=' . $reason);
        exit;
    }
}
// --- End Delete Income Handling ---


// Check for messages from redirects (Now includes delete statuses and reasons)
if (isset($_GET['status'])) {
    switch ($_GET['status']) {
        // Income Statuses
        case 'deleted': $message = 'Income record deleted successfully!'; $message_type = 'success'; break;
        case 'delete_error': $message = 'Error deleting income record.'; $message_type = 'error'; break;
        case 'updated': $message = 'Income record updated successfully!'; $message_type = 'success'; break;
        case 'update_error': $message = 'Error updating income record.'; $message_type = 'error'; break;
        case 'update_auth_error': $message = 'Error updating record: Permission denied.'; $message_type = 'error'; break;
        case 'added': $message = 'Income record added successfully!'; $message_type = 'success'; break;
        case 'add_error': $message = 'Error adding income record.'; $message_type = 'error'; break;
        // You might have expense-specific statuses if this page could ever receive them
        // case 'expense_deleted': ...
    }
    // Add reason if provided for error messages
    if (isset($_GET['reason']) && ($message_type == 'error')) {
        $message .= ' Reason: ' . htmlspecialchars($_GET['reason']);
    }
}


// --- Handle Add Income Form Submission ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_income'])) {
    $source = isset($_POST['incomeSource']) ? trim($_POST['incomeSource']) : '';
    $amount = isset($_POST['incomeAmount']) ? trim($_POST['incomeAmount']) : '';
    $date = isset($_POST['incomeDate']) ? trim($_POST['incomeDate']) : '';

    if (empty($source) || empty($amount) || !is_numeric($amount) || $amount <= 0 || empty($date)) {
        $message = 'Invalid input. Please fill in Source, Amount, and Date correctly.';
        $message_type = 'error';
    } else {
        $data = [
            'user_id' => $user_id,
            'source' => $source,
            'amount' => (float)$amount,
            'date' => $date
        ];

        insertRecord('incomes', $data);
        $last_inserted_id = mysqli_insert_id($conn);

        if ($last_inserted_id > 0) {
            header("Location: " . htmlspecialchars($_SERVER['PHP_SELF']) . "?status=added");
            exit;
        } else {
             $db_error = mysqli_error($conn);
             error_log("Income Add Error (User ID: {$user_id}): " . $db_error);
             // Redirect with error status for consistency
             header("Location: " . htmlspecialchars($_SERVER['PHP_SELF']) . "?status=add_error&reason=" . urlencode($db_error));
             exit;
             // $message = 'Failed to add income record. ' . $db_error; // Fallback if redirect fails
             // $message_type = 'error';
        }
    }
}

// --- Handle Edit Income Form Submission (from Modal) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_income'])) {
    $income_id = isset($_POST['editIncomeId']) ? (int)$_POST['editIncomeId'] : 0;
    $source = isset($_POST['editSource']) ? trim($_POST['editSource']) : '';
    $amount = isset($_POST['editAmount']) ? trim($_POST['editAmount']) : '';
    $date = isset($_POST['editDate']) ? trim($_POST['editDate']) : '';

    if ($income_id <= 0 || empty($source) || empty($amount) || !is_numeric($amount) || $amount <= 0 || empty($date)) {
         // Redirect with error status
         header("Location: " . htmlspecialchars($_SERVER['PHP_SELF']) . "?status=update_error&reason=invalid_input");
         exit;
         // $message = 'Invalid input for update. Please fill all required fields correctly.';
         // $message_type = 'error';
    } else {
        $check_sql = "SELECT id FROM incomes WHERE id = ? AND user_id = ?";
        $stmt_check = mysqli_prepare($conn, $check_sql);
        $owns_record = false;
        if ($stmt_check) {
            mysqli_stmt_bind_param($stmt_check, "ii", $income_id, $user_id);
            mysqli_stmt_execute($stmt_check);
            mysqli_stmt_store_result($stmt_check);
            $owns_record = mysqli_stmt_num_rows($stmt_check) > 0;
            mysqli_stmt_close($stmt_check);
        } else {
             error_log("Update ownership check failed (Income ID: {$income_id}): " . mysqli_error($conn));
             // Redirect with error status
             header("Location: " . htmlspecialchars($_SERVER['PHP_SELF']) . "?status=update_error&reason=db_check_failed");
             exit;
             // $message = 'Error checking record ownership.'; $message_type = 'error';
        }

        if ($owns_record) {
            $update_data = [
                'source' => $source,
                'amount' => (float)$amount,
                'date' => $date
            ];
            $update_function_returned = updateRecord('incomes', $income_id, $update_data);
            $db_error_after_update = trim(mysqli_error($conn));

            if ($update_function_returned || (!$update_function_returned && empty($db_error_after_update)) ) {
                error_log("Income Update SUCCESS or 0 rows affected: ID={$income_id}. Redirecting status=updated.");
                header("Location: " . htmlspecialchars($_SERVER['PHP_SELF']) . "?status=updated");
                exit;
            } else {
                error_log("Income Update FAILED: ID={$income_id}, User ID: {$user_id}. DB Error: {$db_error_after_update}");
                // Redirect with error status
                header("Location: " . htmlspecialchars($_SERVER['PHP_SELF']) . "?status=update_error&reason=" . urlencode($db_error_after_update));
                exit;
                // $message = 'Failed to update income record. Error: ' . htmlspecialchars($db_error_after_update);
                // $message_type = 'error';
            }
        } else { // Ownership check failed or record doesn't exist for this user
            error_log("Unauthorized income update attempt (Income ID: {$income_id}, User ID: {$user_id})");
            header("Location: " . htmlspecialchars($_SERVER['PHP_SELF']) . "?status=update_auth_error");
            exit;
        }
    }
}

// --- Fetch Existing Incomes ---
$current_user_id = $user_id;
$incomes = [];
$fetch_error = '';
$sort_options = [
    'date-desc' => ['column' => 'date', 'order' => 'DESC'], 'date-asc' => ['column' => 'date', 'order' => 'ASC'],
    'amount-desc' => ['column' => 'amount', 'order' => 'DESC'], 'amount-asc' => ['column' => 'amount', 'order' => 'ASC'],
    'source-asc' => ['column' => 'source', 'order' => 'ASC'], 'source-desc' => ['column' => 'source', 'order' => 'DESC'],];
$sort_key = isset($_GET['sortBy']) && array_key_exists($_GET['sortBy'], $sort_options) ? $_GET['sortBy'] : 'date-desc';
$sort_by_column = $sort_options[$sort_key]['column']; $sort_order = $sort_options[$sort_key]['order'];
$source_filter = isset($_GET['sourceFilter']) && $_GET['sourceFilter'] !== 'all' ? trim($_GET['sourceFilter']) : null;
$sql = "SELECT * FROM incomes WHERE user_id = ?"; $params = [$current_user_id]; $types = 'i';
if ($source_filter) { $sql .= " AND source = ?"; $params[] = $source_filter; $types .= 's'; }
$sql .= " ORDER BY `$sort_by_column` $sort_order";
$stmt = mysqli_prepare($conn, $sql);
if ($stmt) {
    if (count($params) > 0) { mysqli_stmt_bind_param($stmt, $types, ...$params); }
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        if ($result) { $incomes = mysqli_fetch_all($result, MYSQLI_ASSOC); mysqli_free_result($result); }
        else { $fetch_error = "Error fetching results: " . mysqli_error($conn); }
    } else { $fetch_error = "Error executing statement: " . mysqli_stmt_error($stmt); }
    mysqli_stmt_close($stmt);
} else { $fetch_error = "Error preparing statement: " . mysqli_error($conn); }
if ($fetch_error) { error_log("Income Fetch Error (User ID: {$current_user_id}): " . $fetch_error); if (empty($message)) { $message = "Error loading income history."; $message_type = 'error'; } }

// --- Get Distinct Sources ---
$income_sources = []; $cat_sql = "SELECT DISTINCT source FROM incomes WHERE user_id = ? ORDER BY source ASC"; $cat_stmt = mysqli_prepare($conn, $cat_sql);
if ($cat_stmt) { mysqli_stmt_bind_param($cat_stmt, 'i', $current_user_id); if (mysqli_stmt_execute($cat_stmt)) { $cat_result = mysqli_stmt_get_result($cat_stmt); while ($row = mysqli_fetch_assoc($cat_result)) { $income_sources[] = $row['source']; } mysqli_free_result($cat_result); } else { error_log("Error fetching income sources: " . mysqli_stmt_error($cat_stmt)); } mysqli_stmt_close($cat_stmt); }
else { error_log("Error preparing source statement: " . mysqli_error($conn)); }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expenzo - Add Income</title>
    <style>
        /* --- PASTE ALL YOUR CSS HERE --- */
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
        .header {width: 1150px; margin-left:30px; margin-bottom: 20px; border-bottom: 1px solid var(--border-light); padding-bottom: var(--space-md);}
        .menu-toggle { display: none; position: fixed; top: var(--space-sm); left: var(--space-sm); z-index: 20; background-color: var(--primary-color); color: var(--white); border: none; border-radius: 4px; width: 40px; height: 40px; font-size: 20px; cursor: pointer; }
        @media (max-width: 992px) { .menu-toggle { display: block; } .sidebar { transform: translateX(-100%); } .sidebar.active { transform: translateX(0); } .main-content { margin-left: 0; padding: var(--space-lg); padding-top: 60px; max-width: 100%; } }
        .income-container { display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 20px; }
        .add-income { padding: 20px;  margin-left: 35px; max-width: 50%; background-color: #fff; border: 1px solid #ddd; border-radius: var(--card-radius); box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05); flex: 1 1 400px; height:565px;}
        .add-income h2 { margin-top: 0; margin-bottom: 25px; border-bottom: 1px solid #eee; padding-bottom: 10px; font-size: 1.3em; color: var(--text-dark); }
        .income-form { display: flex; flex-direction: column; }
        .form-group { margin-bottom: 18px; }
        .income-form label { margin-bottom: 25px; display: block; font-weight: 600; font-size: 0.95em; color: #555; }
        .income-form input, .income-form select { padding: 10px 12px; width: 100%; border: 1px solid #ccc; border-radius: 4px; font-size: 1rem; }
        .income-form input:focus, .income-form select:focus { border-color: var(--primary-color); outline: none; box-shadow: 0 0 0 2px rgba(0, 86, 63, 0.2); }
        .income-form button { padding: 12px 25px; background-color: var(--primary-color); color: white; border: none; border-radius: 4px; cursor: pointer; align-self: flex-start; margin-top: 50px; font-size: 1rem; font-weight: 500; transition: background-color 0.2s ease; }
        .income-form button:hover { background-color: #00402e; }
        .past-incomes { padding: 20px; background-color: #fff; border: 1px solid #ddd; border-radius: var(--card-radius); box-shadow: 0 2px 4px rgba(0,0,0,0.05); flex: 1 1 400px; max-width: 48%; min-height: 500px; display: flex; flex-direction: column;  max-height: 565px; margin-right: -50px; }
        .past-incomes h2 { margin-top: 0; margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 10px; font-size: 1.3em; color: var(--text-dark); flex-shrink: 0; }
        .past-incomes ul { list-style: none; padding: 0; margin: 0; overflow-y: auto; flex-grow: 1; }
        .past-incomes li { padding: 12px 5px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; gap: 15px; }
        .past-incomes li:last-child { border-bottom: none; }
        .income-info { display: flex; flex-direction: column; flex-grow: 1; overflow: hidden; margin-right:10px; }
        .income-source { font-weight: 600; color: var(--text-dark); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .income-date { font-size: 0.85em; color: var(--text-light); margin-top: 2px; }
        .income-amount-actions { display: flex; align-items: center; gap: 15px; flex-shrink: 0; }
        .income-amount { font-weight: 600; color: var(--primary-color); font-size: 1em; }
        .actions { display: flex; gap: 8px; }
        .edit-btn, .delete-btn { background: none; border: none; cursor: pointer; font-size: 1.1rem; color: #888; padding: 2px; transition: color 0.2s ease; text-decoration: none; } /* Added text-decoration: none */
        .edit-btn:hover { color: var(--primary-color); }
        .delete-btn:hover { color: #d9534f; }
        .filter-controls { display: flex; flex-wrap: wrap; margin-bottom: 15px; gap: 10px; align-items: center; padding: 10px; background-color: #f8f9fa; border-radius: 4px; border: 1px solid #eee; flex-shrink: 0; }
        .filter-controls select { padding: 8px 10px; border-radius: 4px; border: 1px solid #ccc; font-size: 0.9em; height: 36px; flex-grow: 1; min-width: 120px; }
        .filter-controls button { padding: 8px 15px; background-color: var(--secondary-color); color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.9em; height: 36px; transition: background-color 0.2s ease; }
        .filter-controls button:hover { background-color: #444; }
        .message-box { padding: 12px 15px; margin-bottom: 20px; border-radius: 4px; border: 1px solid transparent; font-size: 0.95em; display: flex; justify-content: space-between; align-items: center; opacity: 1; transition: opacity 0.5s ease-out; }
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
        .modal-form input, .modal-form select { padding: 9px 12px; width: 100%; border: 1px solid #ccc; border-radius: 4px; font-size: 0.95rem; }
        .modal-form input:focus, .modal-form select:focus { border-color: var(--primary-color); outline: none; box-shadow: 0 0 0 2px rgba(0, 86, 63, 0.2); }
        .modal-form button { padding: 10px 20px; background-color: var(--primary-color); color: white; border: none; border-radius: 4px; cursor: pointer; margin-top: 10px; font-size: 0.95rem; float: right; }
        .modal-form button:hover { background-color: #00402e; }
    </style>
</head>
<body>
    <button class="menu-toggle" id="menuToggle">☰</button>

    <div class="sidebar" id="sidebar">
        <div class="logo">Expenzo</div>
        <div class="user-profile">
             <!-- Assuming user_id will be used to fetch actual user details later -->
            <div class="avatar" onclick="window.location.href='#'">U</div>
            <div class="user-name" onclick="window.location.href='#'">User <?php echo htmlspecialchars($user_id); ?></div>
        </div>
        <ul class="nav-menu">
            <li class="nav-item" onclick="window.location.href='Total_Expense.php'"> <span class="nav-icon">📊</span> Dashboard </li>
            <li class="nav-item" onclick="window.location.href='Add_Expense.php'"> <span class="nav-icon">💸</span> Expense </li>
            <li class="nav-item active"> <span class="nav-icon">💰</span> Income </li>
            <li class="nav-item" onclick="window.location.href='Budgets.php'"> <span class="nav-icon">💹</span> Budgets </li>
            <li class="nav-item" onclick="window.location.href='View_Transactions.php'"> <span class="nav-icon">📋</span> View Transactions </li>
        </ul>
    </div>

    <main class="main-content">
        <header class="header">
            <?php if (!empty($message)): ?>
            <div class="message-box <?php echo 'message-' . htmlspecialchars($message_type); ?>" id="statusMessage">
                <span><?php echo htmlspecialchars($message); ?></span>
                <button type="button" class="close-msg" onclick="this.parentElement.style.display='none'" aria-label="Close">×</button>
            </div>
            <?php endif; ?>
        </header>

        <div class="income-container">
            <div class="add-income section">
                <h2>Add New Income</h2>
                <form class="income-form" id="addIncomeForm" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <div class="form-group">
                        <label for="incomeSource">Source</label>
                        <select id="incomeSource" name="incomeSource" required>
                            <option value="">Select Source...</option>
                            <option value="Salary">Salary</option>
                            <option value="Business">Business</option>
                            <option value="Investment">Investment</option>
                            <option value="Freelance">Freelance</option>
                            <option value="Gifts">Gifts</option>
                            <option value="Bonus">Bonus</option>
                            <option value="Rental">Rental Income</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="incomeAmount">Amount (Rs.)</label>
                        <input type="number" id="incomeAmount" name="incomeAmount" placeholder="e.g., 50000.00" required min="0.01" step="0.01" />
                    </div>
                    <div class="form-group">
                        <label for="incomeDate">Date Received</label>
                        <input type="date" id="incomeDate" name="incomeDate" required value="<?php echo date('Y-m-d'); ?>" />
                    </div>
                    <button type="submit" name="add_income">Add Income</button>
                </form>
            </div>

            <div class="past-incomes section">
                <h2>Income History</h2>
                <form method="GET" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="filter-controls">
                    <select name="sourceFilter" id="sourceFilter" title="Filter by Source">
                        <option value="all" <?php echo ($source_filter === null) ? 'selected' : ''; ?>>All Sources</option>
                        <?php foreach ($income_sources as $src): ?>
                        <option value="<?php echo htmlspecialchars($src); ?>" <?php echo ($source_filter === $src) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($src); ?>
                        </option>
                        <?php endforeach; ?>
                        <?php if ($source_filter !== null && !in_array($source_filter, $income_sources)): ?>
                        <option value="<?php echo htmlspecialchars($source_filter); ?>" selected><?php echo htmlspecialchars($source_filter); ?> (No results)</option>
                        <?php endif; ?>
                    </select>
                    <select name="sortBy" id="sortBy" title="Sort By">
                        <option value="date-desc" <?php echo ($sort_key == 'date-desc') ? 'selected' : ''; ?>>Date (Newest)</option>
                        <option value="date-asc" <?php echo ($sort_key == 'date-asc') ? 'selected' : ''; ?>>Date (Oldest)</option>
                        <option value="amount-desc" <?php echo ($sort_key == 'amount-desc') ? 'selected' : ''; ?>>Amount (Highest)</option>
                        <option value="amount-asc" <?php echo ($sort_key == 'amount-asc') ? 'selected' : ''; ?>>Amount (Lowest)</option>
                        <option value="source-asc" <?php echo ($sort_key == 'source-asc') ? 'selected' : ''; ?>>Source (A-Z)</option>
                        <option value="source-desc" <?php echo ($sort_key == 'source-desc') ? 'selected' : ''; ?>>Source (Z-A)</option>
                    </select>
                    <button type="submit">Apply</button>
                </form>

                <ul id="incomesList">
                     <?php if (!empty($fetch_error)): ?>
                         <li><p style="color: red; text-align: center; padding: 20px;"><?php echo htmlspecialchars($fetch_error); ?></p></li>
                     <?php elseif (empty($incomes)): ?>
                         <li><p style="text-align: center; padding: 20px; color: var(--text-light);">
                             <?php echo ($source_filter) ? 'No income records match the selected filter.' : 'No income recorded yet.'; ?>
                         </p></li>
                     <?php else: ?>
                         <?php foreach ($incomes as $income): ?>
                             <li data-id="<?php echo $income['id']; ?>"
                                 data-source="<?php echo htmlspecialchars($income['source']); ?>"
                                 data-amount="<?php echo htmlspecialchars($income['amount']); ?>"
                                 data-date="<?php echo htmlspecialchars($income['date']); ?>">
                                 <div class="income-info">
                                     <span class="income-source"><?php echo htmlspecialchars($income['source']); ?></span>
                                     <span class="income-date"><?php echo date("M d, Y", strtotime($income['date'])); ?></span>
                                 </div>
                                 <div class="income-amount-actions">
                                     <span class="income-amount">Rs.<?php echo number_format($income['amount'], 2); ?></span>
                                     <span class="actions">
                                         <button type="button" class="edit-btn" aria-label="Edit Income" title="Edit Income">🖊️</button>
                                         <!-- DELETE LINK MODIFIED -->
                                         <a href="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>?action=delete&id=<?php echo $income['id']; ?>"
                                            class="delete-btn"
                                            aria-label="Delete Income"
                                            title="Delete Income"
                                            onclick="return confirm('Are you sure you want to permanently delete this income record?');">
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

    <div class="modal" id="editModal">
        <div class="modal-content">
            <button type="button" class="close-btn" id="closeModal" aria-label="Close Modal">×</button>
            <h3>Edit Income Record</h3>
            <form class="modal-form" id="editForm" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <input type="hidden" id="editIncomeId" name="editIncomeId">
                <div class="form-group">
                    <label for="editSource">Source</label>
                    <select id="editSource" name="editSource" required>
                        <option value="">Select Source...</option>
                        <option value="Salary">Salary</option>
                        <option value="Business">Business</option>
                        <option value="Investment">Investment</option>
                        <option value="Freelance">Freelance</option>
                        <option value="Gifts">Gifts</option>
                        <option value="Bonus">Bonus</option>
                        <option value="Rental">Rental Income</option>
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
                <button type="submit" name="update_income">Update Income</button>
            </form>
        </div>
    </div>

    <script>
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

            const modal = document.getElementById('editModal');
            const closeBtn = document.getElementById('closeModal');
            const incomesList = document.getElementById('incomesList');

            function openEditModal(incomeData) {
                if (!modal || !incomeData) { console.error("Modal or income data missing"); return; }
                document.getElementById('editIncomeId').value = incomeData.id || '';
                document.getElementById('editSource').value = incomeData.source || '';
                document.getElementById('editAmount').value = incomeData.amount || '';
                document.getElementById('editDate').value = incomeData.date || '';
                modal.style.display = 'block';
            }

            if (incomesList && modal) {
                 incomesList.addEventListener('click', function(event) {
                    const editButton = event.target.closest('.edit-btn');
                    if (editButton) {
                        event.preventDefault();
                        const listItem = editButton.closest('li[data-id]');
                        if (listItem && listItem.dataset) {
                            const incomeData = {
                                id: listItem.dataset.id,
                                source: listItem.dataset.source,
                                amount: listItem.dataset.amount,
                                date: listItem.dataset.date
                            };
                            openEditModal(incomeData);
                        }
                    }
                });
            }

            if (closeBtn && modal) {
                closeBtn.addEventListener('click', () => { modal.style.display = 'none'; });
            }
            window.addEventListener('click', (event) => {
                if (event.target === modal) { modal.style.display = 'none'; }
            });

            // Auto-hide status message
            const statusMessageBox = document.getElementById('statusMessage');
            if (statusMessageBox) {
                const closeMsgBtn = statusMessageBox.querySelector('.close-msg');
                if (closeMsgBtn) { // If manual close button exists, enable auto-hide
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