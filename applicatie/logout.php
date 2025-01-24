<?php
require_once('../applicatie/functions/security.php');

startSecureSession();
checkSessionTimeout();
session_start();
session_unset(); 
session_destroy(); 
header('Location: login.php'); 
exit();
?> 