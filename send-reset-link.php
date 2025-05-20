<?php
session_start();

// Define steps (consistency)
define('FP_STEP_EMAIL', 'email');
define('FP_STEP_CODE', 'code');

$is_resend = (isset($_GET['resend']) && $_GET['resend'] == 'true');
$email_from_get = null;

if ($is_resend && isset($_GET['email'])) {
    $email_from_get = filter_var($_GET['email'], FILTER_SANITIZE_EMAIL);
}

if (($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) || $is_resend) {

    $email = '';
    if ($is_resend && $email_from_get) {
        $email = $email_from_get;
    } elseif (isset($_POST['email'])) {
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    }

    // Clear previous error/success to avoid confusion if user navigates back/forth
    unset($_SESSION['fp_error_message']);
    unset($_SESSION['fp_success_message']);
    unset($_SESSION['fp_email_input']); // Clear repopulation attempt

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['fp_step'] = FP_STEP_EMAIL;
        $_SESSION['fp_error_message'] = "Invalid email address provided.";
        if (isset($_POST['email'])) {
             $_SESSION['fp_email_input'] = $_POST['email']; // Keep original potentially invalid input for user to see
        }
    } else {
        // --- SIMULATED EMAIL SENDING AND CODE GENERATION ---
        $verification_code = '123456'; // Hardcoded as requested

        $_SESSION['fp_expected_code'] = $verification_code;
        $_SESSION['fp_email_for_code_display'] = $email;
        $_SESSION['fp_step'] = FP_STEP_CODE;

        if ($is_resend) {
            $_SESSION['fp_success_message'] = "A new verification code (123456) has been (simulated) sent to " . htmlspecialchars($email) . ".";
        } else {
            $_SESSION['fp_success_message'] = "Verification code (123456) (simulated) sent to " . htmlspecialchars($email) . ".";
        }
        // error_log("Password reset code for $email: $verification_code");
    }
} else {
    // Not a POST with email, and not a valid resend request
    $_SESSION['fp_step'] = FP_STEP_EMAIL;
    $_SESSION['fp_error_message'] = "Invalid request.";
}

header('Location: forgot-password.php');
exit();
?>