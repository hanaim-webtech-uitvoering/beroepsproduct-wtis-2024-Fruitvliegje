<?php

function startSecureSession() {
    session_regenerate_id(true); 
}

function checkSessionTimeout() {
    if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
        session_unset(); 
        session_destroy(); 
        header("Location: login.php"); 
        exit();
    }
    $_SESSION['LAST_ACTIVITY'] = time(); 
}


function checkUserLoggedIn() {
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit();
    }
}

function checkIfPersonnel() {
    if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Personnel') {
        header('Location: login.php');
        exit();
    }
}

?>