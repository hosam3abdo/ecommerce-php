<?php

session_start();

unset($_SESSION['user']);
setcookie('user','',time() -1 , '/');
header('location:login.php');

// namespace , autoload class