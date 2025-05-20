<?php
session_start();

// This page is generally shown if someone bookmarks it or if a very old flow pointed here.
// The main success view is now part of forgot-password.php.

$status_message = $_SESSION['fp_success_message_final_page'] ?? "Your password action was completed."; // Generic message
if (isset($_SESSION['fp_success_message_final_page'])) {
    unset($_SESSION['fp_success_message_final_page']); // Clear after display
} elseif (isset($_SESSION['password_update_status'])) { // Compatibility with old session var
    $status_message = $_SESSION['password_update_status'];
    unset($_SESSION['password_update_status']);
}

// If the user ended up here without a specific message, guide them.
if (!isset($_SESSION['fp_step']) || $_SESSION['fp_step'] !== 'success') {
    // If not coming from the success step of the new flow, provide a generic message or redirect.
    // $status_message = "If you recently reset your password, you can now sign in.";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Action Complete</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }
        body { background-color: #f9f9f7; display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 20px; }
        .container { background-color: white; width: 100%; max-width: 400px; padding: 40px 30px; border-radius: 4px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); text-align: center; }
        .title { font-size: 20px; font-weight: bold; margin-bottom: 15px; color: #28a745; }
        .message { font-size: 16px; color: #333; margin-bottom: 30px; }
        .signin-button { display: inline-block; width: 100%; padding: 12px; background-color: #000; color: white; text-decoration: none; border: none; border-radius: 2px; font-size: 14px; cursor: pointer; }
        .extra-info { margin-top: 20px; font-size: 14px; }
        .extra-info a { color: #000; text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <div class="title">ACTION COMPLETE!</div>
        <p class="message"><?php echo htmlspecialchars($status_message); ?></p>
        <a href="Login.php" class="signin-button">Sign In</a>
         <div class="extra-info">
            If you need to reset your password again, <a href="forgot-password.php?action=reset_flow">click here</a>.
        </div>
    </div>
</body>
</html>