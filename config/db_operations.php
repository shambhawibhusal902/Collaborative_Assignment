<?php
require_once 'db_config.php'; 

// Function to execute a prepared statement
function executeQuery($sql, $params = []) {
    global $conn; 

    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        error_log("MySQL Prepare Error: " . mysqli_error($conn) . " - SQL: " . $sql);
        return false;
    }

    if (!empty($params)) {
        $types = "";
        if (stripos($sql, "INSERT INTO budgets") === 0 && count($params) == 5) {
            $types = "isdss"; // user_id (i), category (s), amount (d), description (s), date (s)
        }
        else if (stripos($sql, "DELETE FROM budgets") === 0 && count($params) == 1) {
             $types = "i"; 
        }
        else {
            $param_count = count($params);
            for ($i = 0; $i < $param_count; $i++) {
                if (is_int($params[$i])) {
                    $types .= 'i';
                } elseif (is_double($params[$i])) {
                    $types .= 'd';
                } elseif (is_string($params[$i])) {
                    $types .= 's';
                } else {
                    $types .= 'b'; 
                }
            }
        }
        
        // Ensure $types string length matches the number of parameters
        if (strlen($types) !== count($params)) {
            error_log("Parameter type string length mismatch. SQL: " . $sql . " - Expected " . count($params) . " params, got types: '" . $types . "'. Falling back to all 's'. Params: " . json_encode($params));
            $types = str_repeat('s', count($params)); // Fallback
        }

        if (!mysqli_stmt_bind_param($stmt, $types, ...$params)) {
            error_log("MySQL Bind Param Error: " . mysqli_stmt_error($stmt) . " - Types: " . $types . " - SQL: " . $sql . " Params: " . json_encode($params));
            mysqli_stmt_close($stmt);
            return false;
        }
    }

    $executionSuccess = mysqli_stmt_execute($stmt);

    if ($executionSuccess) {
        if (mysqli_stmt_field_count($stmt) > 0) { // Check if the query produces a result set (typically SELECT)
            $result = mysqli_stmt_get_result($stmt);
            mysqli_stmt_close($stmt);
            return $result; 
        } else {
            // For INSERT, UPDATE, DELETE
            $affected_rows = mysqli_stmt_affected_rows($stmt);
            mysqli_stmt_close($stmt);
            // For INSERT, we expect at least 1 row to be affected.
            if (stripos($sql, "INSERT INTO") === 0 || stripos($sql, "REPLACE INTO") === 0) {
                return $affected_rows > 0;
            }
            return true; 
        }
    } else {
        error_log("MySQL Execute Error: " . mysqli_stmt_error($stmt) . " - SQL: " . $sql . " Params: " . json_encode($params));
        mysqli_stmt_close($stmt);
        return false;
    }
}

function insertRecord($table, $data) {
    if (empty($data)) {
        error_log("No data provided for insertRecord on table $table.");
        return false;
    }
    $fields = implode(', ', array_keys($data));
    $placeholders = implode(', ', array_fill(0, count($data), '?'));
    $values = array_values($data);

    $sql = "INSERT INTO $table ($fields) VALUES ($placeholders)";
    
    // Determine types for insertRecord specifically
    $types = "";
    foreach($values as $value) {
        if (is_int($value)) $types .= "i";
        elseif (is_double($value)) $types .= "d";
        elseif (is_string($value)) $types .= "s";
        else $types .= "b"; // Default for other types (e.g., null, blob)
    }

    // Use mysqli_prepare, bind, execute directly for better control
    global $conn;
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        error_log("MySQL Prepare Error for insertRecord: " . mysqli_error($conn) . " - SQL: " . $sql);
        return false;
    }
    if (strlen($types) !== count($values)) { // Safety check
        error_log("Type string length mismatch in insertRecord. Types: $types, Values count: " . count($values));
        mysqli_stmt_close($stmt);
        return false;
    }
    if (!mysqli_stmt_bind_param($stmt, $types, ...$values)) {
        error_log("MySQL Bind Param Error for insertRecord: " . mysqli_stmt_error($stmt) . " - Types: " . $types);
        mysqli_stmt_close($stmt);
        return false;
    }
    if (mysqli_stmt_execute($stmt)) {
        $affected_rows = mysqli_stmt_affected_rows($stmt);
        mysqli_stmt_close($stmt);
        return $affected_rows > 0;
    } else {
        error_log("MySQL Execute Error for insertRecord: " . mysqli_stmt_error($stmt));
        mysqli_stmt_close($stmt);
        return false;
    }
}

function getRecordById($table, $id) {
    $sql = "SELECT * FROM $table WHERE id = ?";
    global $conn;
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        error_log("MySQL Prepare Error for getRecordById: " . mysqli_error($conn));
        return null;
    }
    mysqli_stmt_bind_param($stmt, "i", $id); // ID is an integer
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt);
        if ($result) {
            return mysqli_fetch_assoc($result);
        }
    } else {
        error_log("MySQL Execute Error for getRecordById: " . mysqli_stmt_error($stmt));
        mysqli_stmt_close($stmt);
    }
    return null;
}


function updateRecord($table, $id, $data) {
    if (empty($data)) {
        error_log("No data provided for updateRecord on table $table for id $id.");
        return false; 
    }
    $setClauses = [];
    $values = []; 
    $types = "";  

    foreach ($data as $field => $value) {
        $setClauses[] = "$field = ?";
        $values[] = $value;
        if (is_int($value)) $types .= "i";
        elseif (is_double($value)) $types .= "d";
        elseif (is_string($value)) $types .= "s";
        else $types .= "b"; 
    }
    
    $sql = "UPDATE $table SET " . implode(', ', $setClauses) . " WHERE id = ?";
    
    $values[] = $id; 
    $types .= "i"; 
    global $conn;
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        error_log("MySQL Prepare Error for updateRecord: " . mysqli_error($conn) . " - SQL: " . $sql);
        return false;
    }

    if (strlen($types) !== count($values)) { // Safety check
        error_log("Type string length mismatch in updateRecord. Types: $types, Values count: " . count($values));
        mysqli_stmt_close($stmt);
        return false;
    }
    if (!mysqli_stmt_bind_param($stmt, $types, ...$values)) {
        error_log("MySQL Bind Param Error for updateRecord: " . mysqli_stmt_error($stmt) . " - Types: " . $types . " - SQL: " . $sql . " Values: " . json_encode($values));
        mysqli_stmt_close($stmt);
        return false;
    }

    if (mysqli_stmt_execute($stmt)) {
        // $affected_rows = mysqli_stmt_affected_rows($stmt); // Not strictly needed to check for update success
        mysqli_stmt_close($stmt);
        return true; // Update is successful if it executes
    } else {
        error_log("MySQL Execute Error for updateRecord: " . mysqli_stmt_error($stmt) . " - SQL: " . $sql . " Values: " . json_encode($values));
        mysqli_stmt_close($stmt);
        return false;
    }
}


function deleteRecord($table, $id) {
    $sql = "DELETE FROM $table WHERE id = ?";
    global $conn;
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        error_log("MySQL Prepare Error for deleteRecord: " . mysqli_error($conn));
        return false;
    }
    mysqli_stmt_bind_param($stmt, "i", $id); // ID is an integer
    if (mysqli_stmt_execute($stmt)) {
        $affected_rows = mysqli_stmt_affected_rows($stmt);
        mysqli_stmt_close($stmt);
        return $affected_rows > 0; // Return true if a row was actually deleted
    } else {
        error_log("MySQL Execute Error for deleteRecord: " . mysqli_stmt_error($stmt));
        mysqli_stmt_close($stmt);
        return false;
    }
}

function getAllRecords($table, $conditions = null) {
    $sql = "SELECT * FROM $table";
    $params = []; 
    
    if ($conditions) {
        // This assumes $conditions is a simple string like "user_id = 1"
        $sql .= " WHERE " . $conditions;
    }
    $result = executeQuery($sql, $params); 
    
    if ($result && $result instanceof mysqli_result) { 
        if (mysqli_num_rows($result) > 0) {
            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        } else {
            return []; // No records found but query was successful
        }
    }
    return false;
}
?>