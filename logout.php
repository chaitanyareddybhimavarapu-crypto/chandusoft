<?php
session_start();

// Optional flash message (if you'd like to show it on admin-leads.php)
$_SESSION['flash'] = '✅ Admin has been logged out.';

// Clear session
session_unset();
session_destroy();

// Redirect back to login.php (which will show login form)
header('Location: login.php');
exit;
