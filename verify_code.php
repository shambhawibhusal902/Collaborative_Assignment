<?php
session_start();

// Define steps
define('FP_STEP_CODE', 'code');
define('FP_STEP_RESET', 'reset');
define('FP_STEP_EMAIL', 'email'); // Forcing reset if session is broken

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['verification_code'])) {
    // Clear previous messages for this specific action
    unset($_SESSION['fp_error_message']);
    unset($_SESSION['fp_success_message']);

    if (!isset($_SESSION['fp_email_for_code_display']) || !isset($_SESSION['fp_expected_code'])) {
        $_SESSION['fp_step'] = FP_STEP_EMAIL; // Go to initial state if session is broken
        $_SESSION['fp_error_message'] = "Session expired or invalid request. Please start over.";
        // Clear sensitive session data if flow is broken
        unset($_SESSION['fp_email_for_code_display']);
        unset($_SESSION['fp_expected_code']);
    } else {
        $entered_code = trim($_POST['verification_code']);
        $expected_code = $_SESSION['fp_expected_code'];

        if ($entered_code === $expected_code) {
            $_SESSION['fp_email_to_reset'] = $_SESSION['fp_email_for_code_display'];
            $_SESSION['fp_step'] = FP_STEP_RESET;
            $_SESSION['fp_success_message'] = "Email verified successfully! Please set your new password.";

            // Clear session variables specific to code step
            unset($_SESSION['fp_email_for_code_display']);
            unset($_SESSION['fp_expected_code']);
        } else {
            $_SESSION['fp_step'] = FP_STEP_CODE; // Stay on code entry
            $_SESSION['fp_error_message'] = "Invalid verification code. Please try again.";
            // We keep 'fp_email_for_code_display' and 'fp_expected_code' for retries
        }
    }
} else {
    $_SESSION['fp_step'] = FP_STEP_EMAIL; // Default to email if accessed directly
    $_SESSION['fp_error_message'] = "Invalid access to verification process.";
}

header('Location: forgot-password.php');
exit();
?>