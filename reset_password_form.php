<?php
session_start();
// This page is obsolete in the new flow. Redirect to the main forgot password page.
$_SESSION['fp_error_message'] = "This page is no longer in use. Please start the password reset process again.";
$_SESSION['fp_step'] = 'email'; // Reset to the beginning
header('Location: forgot-password.php?action=reset_flow'); // reset_flow will clear other fp_ session vars
exit();
?>