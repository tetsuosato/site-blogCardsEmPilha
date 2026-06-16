<?php
session_start(); 
include('../lib/config.php');
require_once('class/LoginAuthentication.php');

if(!isset($_SESSION['token'])){
    unset($_SESSION['login']);
    session_destroy();
    session_unset();
    header('Location: index');
    exit();
}else{
    unset($_SESSION['login']);
    session_destroy();
}

if(isset($_GET['logoff'])){
    unset($_SESSION['login']);
    session_destroy();
    session_unset();
    header('Location: index');
    exit();
}
?>
<h1>Your session has expired, please log in again</h1>
<br>
<a href="?logoff">Go to login</a>