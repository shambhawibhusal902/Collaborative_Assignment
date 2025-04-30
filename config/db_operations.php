<?php
require_once 'db_config.php';

function executeQuery($sql, $params = []) {
    global $conn;
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($params) {
        $types = str_repeat('s', count($params));
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt);
        return $result;
    }
    
    mysqli_stmt_close($stmt);
    return false;
}

function insertRecord($table, $data) {
    $fields = array_keys($data);
    $values = array_values($data);
    $placeholders = str_repeat('?,', count($fields) - 1) . '?';
    
    $sql = sprintf(
        "INSERT INTO %s (%s) VALUES (%s)",
        $table,
        implode(', ', $fields),
        $placeholders
    );
    
    return executeQuery($sql, $values);
}

function getRecordById($table, $id) {
    $sql = "SELECT * FROM $table WHERE id = ?";
    $result = executeQuery($sql, [$id]);
    return mysqli_fetch_assoc($result);
}

function updateRecord($table, $id, $data) {
    $fields = array_keys($data);
    $values = array_values($data);
    $set = implode(' = ?, ', $fields) . ' = ?';
    
    $sql = sprintf(
        "UPDATE %s SET %s WHERE id = ?",
        $table,
        $set
    );
    
    $values[] = $id;
    return executeQuery($sql, $values);
}

function deleteRecord($table, $id) {
    $sql = "DELETE FROM $table WHERE id = ?";
    return executeQuery($sql, [$id]);
}

function getAllRecords($table, $conditions = null) {
    $sql = "SELECT * FROM $table";
    if ($conditions) {
        $sql .= " WHERE " . $conditions;
    }
    $result = executeQuery($sql);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}
?>