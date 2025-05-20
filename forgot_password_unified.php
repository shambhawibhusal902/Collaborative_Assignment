<?php
session_start();

// Define steps
define('STEP_EMAIL', 'email');
define('STEP_CODE', 'code');
define('STEP_RESET', 'reset');
define('STEP_SUCCESS', 'success');

// --- Handle Actions (Start Over, Resend Code) ---
if (isset($_GET['action'])) {
    if ($_GET['action'] === 'reset_flow') {
        // Clear all session variables related to this flow
        unset($_SESSION['fp_step']);
        unset($_SESSION['fp_email_input']);
        unset($_SESSION['fp_email_for_code_display']);
        unset($_SESSION['fp_expected_code']);
        unset($_SESSION['fp_email_to_reset']);
        unset($_SESSION['fp_error_message']);
        unset($_SESSION['fp_success_message']);
        header('Location: forgot_password_unified.php'); // Redirect to clean URL
        exit();
    }
    if ($_GET['action'] === 'resend_code' && isset($_SESSION['fp_step']) && $_SESSION['fp_step'] === STEP_CODE && isset($_SESSION['fp_email_for_code_display'])) {
        // Simulate resending code (it's already set, or we can re-set it)
        $_SESSION['fp_expected_code'] = '123456'; // Ensure it's the hardcoded one
        $_SESSION['fp_success_message'] = "A new verification code has been (simulated) sent to " . htmlspecialchars($_SESSION['fp_email_for_code_display']) . ".";

        // Send the verification code to the user's email
        $to = $_SESSION['fp_email_for_code_display'];
        $subject = 'Verification Code';
        $message = 'Your verification code is: 123456';
        $headers = 'From: webmaster@example.com'; // Replace with your email address

        if (mail($to, $subject, $message, $headers)) {
            error_log("Verification code sent to $to");
        } else {
            error_log("Failed to send verification code to $to");
        }

        unset($_SESSION['fp_error_message']); // Clear any previous code error
        header('Location: forgot_password_unified.php'); // Redirect to re-render the code page
        exit();
    }
}

// --- Process Form Submissions ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['form_action'])) {
    $form_action = $_POST['form_action'];

    // Clear previous messages
    unset($_SESSION['fp_error_message']);
    unset($_SESSION['fp_success_message']);

    if ($form_action === 'send_code') {
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $_SESSION['fp_email_input'] = $email; // For repopulating field

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['fp_error_message'] = "Please enter a valid email address.";
            $_SESSION['fp_step'] = STEP_EMAIL;
        } else {
            // SIMULATE SENDING EMAIL & CODE GENERATION
            $_SESSION['fp_email_for_code_display'] = $email;
            $_SESSION['fp_expected_code'] = '123456'; // Hardcoded verification code

            // Send the verification code to the user's email
            $to = $email;
            $subject = 'Verification Code';
            $message = 'Your verification code is: 123456';
            $headers = 'From: webmaster@example.com'; // Replace with your email address

            if (mail($to, $subject, $message, $headers)) {
                error_log("Verification code sent to $to");
            } else {
                error_log("Failed to send verification code to $to");
            }

            $_SESSION['fp_success_message'] = "Verification code (simulated) sent to " . htmlspecialchars($email) . ".";
            $_SESSION['fp_step'] = STEP_CODE;
            unset($_SESSION['fp_email_input']); // No longer needed for input field
        }
    } elseif ($form_action === 'verify_code') {
        $entered_code = isset($_POST['verification_code']) ? trim($_POST['verification_code']) : '';

        if (!isset($_SESSION['fp_expected_code']) || !isset($_SESSION['fp_email_for_code_display'])) {
            $_SESSION['fp_error_message'] = "Session expired or invalid request. Please start over.";
            // Force reset to email step if session is broken
            $_SESSION['fp_step'] = STEP_EMAIL;
            unset($_SESSION['fp_email_for_code_display']);
            unset($_SESSION['fp_expected_code']);
        } elseif ($entered_code === $_SESSION['fp_expected_code']) {
            $_SESSION['fp_email_to_reset'] = $_SESSION['fp_email_for_code_display'];
            $_SESSION['fp_step'] = STEP_RESET;
            unset($_SESSION['fp_expected_code']);
            unset($_SESSION['fp_email_for_code_display']);
            $_SESSION['fp_success_message'] = "Email verified successfully. Please set your new password.";
        } else {
            $_SESSION['fp_error_message'] = "Invalid verification code. Please try again.";
            $_SESSION['fp_step'] = STEP_CODE; // Stay on code step
        }
    } elseif ($form_action === 'set_password') {
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if (!isset($_SESSION['fp_email_to_reset'])) {
            $_SESSION['fp_error_message'] = "Session expired or invalid request. Please start over.";
            $_SESSION['fp_step'] = STEP_EMAIL; // Force reset
        } elseif (empty($new_password) || empty($confirm_password)) {
            $_SESSION['fp_error_message'] = "Both password fields are required.";
            $_SESSION['fp_step'] = STEP_RESET;
        } elseif (strlen($new_password) < 8) {
            $_SESSION['fp_error_message'] = "Password must be at least 8 characters long.";
            $_SESSION['fp_step'] = STEP_RESET;
        } elseif ($new_password !== $confirm_password) {
            $_SESSION['fp_error_message'] = "Passwords do not match.";
            $_SESSION['fp_step'] = STEP_RESET;
        } else {
            // SIMULATE DATABASE PASSWORD UPDATE
            // error_log("SIMULATION: Password for {$_SESSION['fp_email_to_reset']} would be updated.");
            $_SESSION['fp_success_message'] = "Your password has been successfully updated!";
            $_SESSION['fp_step'] = STEP_SUCCESS;
            unset($_SESSION['fp_email_to_reset']); // Clear sensitive info
        }
    }

    // Redirect after POST to prevent re-submission on refresh
    header('Location: forgot_password_unified.php');
    exit();
}

// Determine current step and messages
$current_step = $_SESSION['fp_step'] ?? STEP_EMAIL;
$error_message = $_SESSION['fp_error_message'] ?? null;
$success_message = $_SESSION['fp_success_message'] ?? null;

// Clear messages after retrieving them so they don't show again on refresh without new action
unset($_SESSION['fp_error_message']);
unset($_SESSION['fp_success_message']);

// Page titles for each step
$page_titles = [
    STEP_EMAIL => 'Forgot Password',
    STEP_CODE => 'Enter Verification Code',
    STEP_RESET => 'Reset Your Password',
    STEP_SUCCESS => 'Password Updated',
];
$page_title = $page_titles[$current_step] ?? 'Forgot Password';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }
        body { background-color: #f9f9f7; display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 20px; position: relative; }
        .back-button { position: absolute; top: 20px; left: 20px; background: none; border: none; cursor: pointer; }
        .back-button svg { width: 24px; height: 24px; }
        .container { background-color: white; width: 100%; max-width: 400px; padding: 30px; border-radius: 4px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        .title { font-size: 18px; font-weight: bold; margin-bottom: 5px; text-align: center; }
        .subtitle { font-size: 14px; color: #555; margin-bottom: 20px; text-align: center; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-size: 14px; }
        input[type="email"], input[type="text"], input[type="password"] { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 2px; font-size: 14px; }
        .submit-button { width: 100%; padding: 12px; background-color: #000; color: white; border: none; border-radius: 2px; font-size: 14px; cursor: pointer; margin-top: 10px; }
        .message-box { margin-bottom: 15px; padding: 10px; border-radius: 4px; text-align: center; font-size: 14px; }
        .error-box { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .success-box { background-color: #e6ffed; color: #28a745; border: 1px solid #a3d8b3; }
        .info-text { text-align: center; font-size: 13px; line-height: 1.6; margin-top: 15px; }
        .info-text a { color: #000; text-decoration: underline; }
    </style>
</head>
<body>
    <button class="back-button" onclick="window.location.href='Login.php'" title="Back to Login">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M19 12H5M12 19l-7-7 7-7" />
        </svg>
    </button>

    <div class="container">
        <?php if ($success_message): ?>
            <div class="message-box success-box"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <div class="message-box error-box"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <?php if ($current_step === STEP_EMAIL): ?>
            <div class="title">FORGOT PASSWORD</div>
            <div class="subtitle">Enter your email to receive a verification code.</div>
            <form method="POST" action="forgot_password_unified.php">
                <input type="hidden" name="form_action" value="send_code">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($_SESSION['fp_email_input'] ?? ''); ?>">
                </div>
                <button type="submit" class="submit-button">Send Verification Code</button>
            </form>
            <div class="info-text">Remember your password? <a href="Login.php">Sign in</a></div>

        <?php elseif ($current_step === STEP_CODE && isset($_SESSION['fp_email_for_code_display'])): ?>
            <div class="title">VERIFY YOUR EMAIL</div>
            <div class="subtitle">
                A 6-digit verification code was (simulated as <strong>123456</strong>) sent to <strong><?php echo htmlspecialchars($_SESSION['fp_email_for_code_display']); ?></strong>.
                Please enter the code below.
            </div>
            <form method="POST" action="forgot_password_unified.php">
                <input type="hidden" name="form_action" value="verify_code">
                <div class="form-group">
                    <label for="verification_code">Verification Code</label>
                    <input type="text" id="verification_code" name="verification_code" required pattern="\d{6}" title="Enter the 6-digit code" autocomplete="off" maxlength="6" autofocus>
                </div>
                <button type="submit" class="submit-button">Verify Code</button>
            </form>
            <div class="info-text">
                Didn't receive the code?
                <a href="forgot_password_unified.php?action=resend_code">Resend Code</a>
                <br>
                Entered the wrong email? <a href="forgot_password_unified.php?action=reset_flow">Start Over</a>
            </div>

        <?php elseif ($current_step === STEP_RESET && isset($_SESSION['fp_email_to_reset'])): ?>
            <div class="title">RESET YOUR PASSWORD</div>
            <div class="subtitle">Create a new password for <strong><?php echo htmlspecialchars($_SESSION['fp_email_to_reset']); ?></strong>.</div>
            <form method="POST" action="forgot_password_unified.php" id="resetPasswordForm">
                <input type="hidden" name="form_action" value="set_password">
                <div class="form-group">
                    <label for="new_password">New Password (min. 8 characters)</label>
                    <input type="password" id="new_password" name="new_password" required minlength="8">
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required minlength="8">
                </div>
                 <p class="message-box error-box" id="js-error-message" style="display:none; margin-top:0; margin-bottom:10px;"></p>
                <button type="submit" class="submit-button">Set New Password</button>
            </form>
             <div class="info-text">
                Need to start over? <a href="forgot_password_unified.php?action=reset_flow">Click here</a>
            </div>
            <script>
                // Client-side validation for password reset form (optional enhancement)
                const resetForm = document.getElementById('resetPasswordForm');
                if (resetForm) {
                    const newPassInput = document.getElementById('new_password');
                    const confirmPassInput = document.getElementById('confirm_password');
                    const jsErrorMessage = document.getElementById('js-error-message');

                    resetForm.addEventListener('submit', function(event) {
                        const newPass = newPassInput.value;
                        const confirmPass = confirmPassInput.value;
                        let clientError = '';
                        jsErrorMessage.style.display = 'none';

                        if (newPass.length < 8) {
                            clientError = "Password must be at least 8 characters long.";
                        } else if (newPass !== confirmPass) {
                            clientError = "Passwords do not match.";
                        }

                        if (clientError) {
                            event.preventDefault();
                            jsErrorMessage.textContent = clientError;
                            jsErrorMessage.style.display = 'block';
                        }
                    });
                }
            </script>

        <?php elseif ($current_step === STEP_SUCCESS): ?>
            <div class="title" style="color: #28a745;">SUCCESS!</div>
            <!-- The success message is already displayed at the top -->
            <a href="Login.php" class="submit-button" style="text-decoration: none; text-align:center; display:block;">Sign In</a>
            <div class="info-text">
                <a href="forgot_password_unified.php?action=reset_flow">Start another password reset</a>
            </div>

        <?php else: ?>
             <div class="title">ERROR</div>
             <div class="subtitle">An unexpected error occurred or your session is invalid.</div>
             <div class="info-text">
                <a href="forgot_password_unified.php?action=reset_flow">Please Start Over</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
