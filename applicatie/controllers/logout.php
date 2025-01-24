<?php
require_once('../functions/security.php');

startSecureSession();
checkSessionTimeout();
session_start();
session_unset(); 
session_destroy(); 
header('Location: ../login.php'); 
exit();
?> 