<?php
session_start();

// Destroy all session data
$_SESSION = array();
session_destroy();

// Redirect to landing page
header("Location: index.php");
exit();
?>
