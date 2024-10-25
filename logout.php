<?php
session_start();
// Destroy all sessions
session_destroy();
// Redirect to login page (index.php in this case)
header("Location: index.php");
exit();
?>
