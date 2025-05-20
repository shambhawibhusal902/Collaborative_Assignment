<?php
session_start();

// Define steps
define('FP_STEP_RESET', 'reset');
define('FP_STEP_SUCCESS', 'success');
define('FP_STEP_EMAIL', 'email'); // Forcing reset if session is broken

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Clear previous messages for this specific action
    unset($_SESSION['fp_error_message']);
    unset($_SESSION['fp_success_message']);

    if (!isset($_SESSION['fp_email_to_reset']) || !isset($_POST['new_password']) || !isset($_POST['confirm_password'])) {
        $_SESSION['fp_step'] = FP_STEP_EMAIL;
        $_SESSION['fp_error_message'] = "Invalid request or session expired. Please start over.";
        unset($_SESSION['fp_email_to_reset']); // Clear sensitive info
    } else {
        $email_to_update = $_SESSION['fp_email_to_reset'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if (empty($new_password) || empty($confirm_password)) {
            $_SESSION['fp_error_message'] = "Both password fields are required.";
            $_SESSION['fp_step'] = FP_STEP_RESET; // Stay on reset step
        } elseif (strlen($new_password) < 8) {
            $_SESSION['fp_error_message'] = "Password must be at least 8 characters long.";
            $_SESSION['fp_step'] = FP_STEP_RESET;
        } elseif ($new_password !== $confirm_password) {
            $_SESSION['fp_error_message'] = "Passwords do not match.";
            $_SESSION['fp_step'] = FP_STEP_RESET;
        } else {
            // --- SIMULATED DATABASE UPDATE ---
            // error_log("SIMULATION: Password for $email_to_update would be updated in DB.");
            $password_updated_successfully = true; // Assume success

            if ($password_updated_successfully) {
                $_SESSION['fp_step'] = FP_STEP_SUCCESS;
                $_SESSION['fp_success_message'] = "Your password has been successfully updated!";
                unset($_SESSION['fp_email_to_reset']); // Clear after successful update
            } else {
                $_SESSION['fp_error_message'] = "An error occurred while updating your password. Please try again.";
                $_SESSION['fp_step'] = FP_STEP_RESET; // Stay on reset step
            }
        }
    }
} else {
    $_SESSION['fp_step'] = FP_STEP_EMAIL; // Default to email if accessed directly
    $_SESSION['fp_error_message'] = "Invalid access to password update process.";
}

header('Location: forgot-password.php');
exit();
?>