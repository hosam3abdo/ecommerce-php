<?php
session_start();

include_once "app/database/models/User.php";

$userObject = new user;

$userObject->setStatus(1);

$userObject->setEmail($_GET['email']);

$result = $userObject->updateStatus();

if($result){
    $_SESSION['message'] = "<div class='alert alert-success'> Email Updated Successfully </div>";
}else{
    $_SESSION['message'] = "<div class='alert alert-danger'> Something Went Wrong </div>";
}

header('location:my-account.php');

// signed url