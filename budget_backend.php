<?php
// Include database configuration and operations
require_once 'config/db_config.php';
require_once 'config/db_operations.php';
header('Content-Type: application/json');
$user_id = 1; 

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $budgets_from_db = getAllRecords('budgets', "user_id = $user_id"); // Renamed to avoid conflict
        $responseBudgets = [];

        if ($budgets_from_db === false) { // Checking if getAllRecords failed
            error_log("Failed to fetch budgets for user_id: $user_id from database.");
            echo json_encode(['success' => false, 'message' => 'Error fetching budgets.']);
            break;
        }
        
        foreach ($budgets_from_db as $budget) {
            $budget_date_obj = null;
            if (!empty($budget['date'])) {
                try {
                    $budget_date_obj = new DateTime($budget['date']);
                } catch (Exception $e) {
                    error_log("Invalid date format for budget ID {$budget['id']}: {$budget['date']}");
                    // Handles invalid date
                    continue; 
                }
            } else {
                error_log("Missing date for budget ID {$budget['id']}");
                continue;
            }

            $month_start = $budget_date_obj->format('Y-m-01');
            $month_end = $budget_date_obj->format('Y-m-t');

            $sql_spent = "SELECT SUM(amount) as total_spent FROM expenses WHERE user_id = ? AND category = ? AND date BETWEEN ? AND ?";
            
            global $conn;
            $stmt_spent = mysqli_prepare($conn, $sql_spent);

            if ($stmt_spent) {
                mysqli_stmt_bind_param($stmt_spent, 'isss', $user_id, $budget['category'], $month_start, $month_end);
                mysqli_stmt_execute($stmt_spent);
                $result_spent = mysqli_stmt_get_result($stmt_spent);
                $spent_data = mysqli_fetch_assoc($result_spent);
                mysqli_stmt_close($stmt_spent);
                $spent_amount = $spent_data['total_spent'] ? (float)$spent_data['total_spent'] : 0;
            } else {
                error_log("Failed to prepare statement for spent amount calculation for budget ID {$budget['id']}: " . mysqli_error($conn));
                $spent_amount = 0; 
            }
            
            $remaining_amount = (float)$budget['amount'] - $spent_amount;

            $responseBudgets[] = [
                'id' => (int)$budget['id'],
                'name' => ($budget['category'] ?? 'Unknown') . ' Budget',
                'total' => (float)($budget['amount'] ?? 0),
                'spent' => $spent_amount,
                'remaining' => $remaining_amount,
                'status' => 'Monthly (Active)',
                'description' => $budget['description'] ?? '',
                'date' => $budget['date'] 
            ];
        }
        echo json_encode($responseBudgets);
        break;

    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);

        // Validate required fields
        if (!isset($input['category']) || !isset($input['amount']) || !isset($input['date']) ||
            empty(trim($input['category'])) || !is_numeric($input['amount']) || empty(trim($input['date']))) {
            echo json_encode(['success' => false, 'message' => 'Category, Amount, and Date are required and cannot be empty.']);
            break;
        }
        
        $amount = (float)$input['amount'];
        if ($amount <= 0) {
            echo json_encode(['success' => false, 'message' => 'Budget amount must be a positive number.']);
            break;
        }

        $data = [
            'user_id' => $user_id,
            'category' => trim($input['category']),
            'amount' => $amount,
            'description' => isset($input['description']) ? trim($input['description']) : null,
            'date' => trim($input['date']) // Frontend sends startDate as 'date'
        ];

        if (insertRecord('budgets', $data)) {
            // Optionally, fetch the newly created record to return it with ID
            // For simplicity, just returning success now.
            echo json_encode(['success' => true, 'message' => 'Budget added successfully!']);
        } else {
            error_log("Failed to insert budget for user_id: $user_id. Data: " . json_encode($data));
            echo json_encode(['success' => false, 'message' => 'Failed to add budget to the database.']);
        }
        break;

    case 'PUT': // Handles budget updates
        $input = json_decode(file_get_contents('php://input'), true);
        $budget_id = $input['id'] ?? null;

        if (!$budget_id || !is_numeric($budget_id)) {
            echo json_encode(['success' => false, 'message' => 'Valid Budget ID is required for update.']);
            break;
        }
        $budget_id = (int)$budget_id;

        // Fetch existing budget to verify ownership and get current values
        $existing_budget = getRecordById('budgets', $budget_id);

        if (!$existing_budget) {
            echo json_encode(['success' => false, 'message' => 'Budget not found.']);
            break;
        }

        if ($existing_budget['user_id'] != $user_id) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized to update this budget.']);
            break;
        }

        $dataToUpdate = [];
        $current_total_amount = (float)$existing_budget['amount'];

        // Category update
        if (isset($input['category']) && !empty(trim($input['category']))) {
            $dataToUpdate['category'] = trim($input['category']);
        }

        // Description update
        if (isset($input['description'])) { // Allow empty string for description
            $dataToUpdate['description'] = trim($input['description']);
        }

        // Date update (corresponds to startDate from frontend)
        if (isset($input['date']) && !empty(trim($input['date']))) {
            $dataToUpdate['date'] = trim($input['date']);
        }

        // Amount adjustment (if amountAdjustment is provided by frontend)
        if (isset($input['amountAdjustment']) && is_numeric($input['amountAdjustment'])) {
            $amount_adjustment = (float)$input['amountAdjustment'];
            $new_total_amount = $current_total_amount + $amount_adjustment;
            $dataToUpdate['amount'] = max(0, $new_total_amount); // Ensure amount doesn't go below 0
        }
        

        if (empty($dataToUpdate)) {
            echo json_encode(['success' => true, 'message' => 'No changes provided to update. Budget remains the same.']);
            break;
        }

        if (updateRecord('budgets', $budget_id, $dataToUpdate)) {
            echo json_encode(['success' => true, 'message' => 'Budget updated successfully!']);
        } else {
            error_log("Failed to update budget ID: $budget_id for user_id: $user_id. Data: " . json_encode($dataToUpdate));
            echo json_encode(['success' => false, 'message' => 'Failed to update budget in the database.']);
        }
        break;


    case 'DELETE':
        $input = json_decode(file_get_contents('php://input'), true);
        $budget_id_del = $input['id'] ?? null; // Use a different variable name

        if (!$budget_id_del || !is_numeric($budget_id_del)) {
            echo json_encode(['success' => false, 'message' => 'Valid Budget ID is required for deletion.']);
            break;
        }
        $budget_id_del = (int)$budget_id_del;
        
        // Check ownership before deleting
        $budget_to_delete = getRecordById('budgets', $budget_id_del);
        if (!$budget_to_delete) {
            echo json_encode(['success' => false, 'message' => 'Budget not found for deletion.']);
            break;
        }
        if ($budget_to_delete['user_id'] != $user_id) {
             echo json_encode(['success' => false, 'message' => 'Unauthorized to delete this budget.']);
            break;
        }

        if (deleteRecord('budgets', $budget_id_del)) {
            echo json_encode(['success' => true, 'message' => 'Budget deleted successfully!']);
        } else {
            error_log("Failed to delete budget ID: $budget_id_del for user_id: $user_id.");
            echo json_encode(['success' => false, 'message' => 'Failed to delete budget from the database.']);
        }
        break;

    default:
        http_response_code(405); // Method Not Allowed
        echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
        break;
}
?>