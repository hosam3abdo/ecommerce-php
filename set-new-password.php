<?php
$title = "Set New Password";
include_once 'layouts/header.php';
include_once "app/requests/RegisterRequest.php";
include_once "app/database/models/User.php";

if ($_POST) {
    $passwordValidation = new RegisterRequest;
    $passwordValidation->setPassword($_POST['password']);
    $passwordValidation->setConfirmPassword($_POST['confirm_password']);
    $passwordValidationResult = $passwordValidation->passwordValidation();

    if(empty($passwordValidationResult)){
        $userObject = new user;
        $userObject->setPassword($_POST['password']);
        $userObject->setEmail($_SESSION['checkcode-email']);
        $result = $userObject->updatePassword();
        if($result){
            // send mail => message
            header('location:login.php');
        }
    }
}
?>
<div class="login-register-area ptb-100">
    <div class="container">
        <div class="row">
            <div class="col-lg-7 col-md-12 ml-auto mr-auto">
                <div class="login-register-wrapper">
                    <div class="login-register-tab-list nav">
                        <a class="active" data-toggle="tab" href="#lg1">
                            <h4> <?= $title; ?> </h4>
                        </a>
                    </div>
                    <div class="tab-content">
                        <div id="lg1" class="tab-pane active">
                            <div class="login-form-container">
                                <div class="login-register-form">
                                    <?php 
                                        if(isset($result) && !$result){
                                            echo "<div class='alert alert-danger'> SomeThing Went Wrong </div>";
                                        }
                                    ?>
                                    <form method="post">
                                        <input type="password" name="password" placeholder="Password">
                                        <?php
                                        if (isset($passwordValidationResult['password-required'])) {
                                            echo $passwordValidationResult['password-required'];
                                        }
                                        if (isset($passwordValidationResult['password-invalid'])) {
                                            echo $passwordValidationResult['password-invalid'];
                                        }
                                        ?>
                                        <input type="password" name="confirm_password" placeholder="Confirm Password">
                                        <?php
                                        if (isset($passwordValidationResult['confrim-required'])) {
                                            echo $passwordValidationResult['confrim-required'];
                                        }
                                        if (isset($passwordValidationResult['password-notmatched'])) {
                                            echo $passwordValidationResult['password-notmatched'];
                                        }
                                        ?>
                                        <button type="submit" class="btn btn-success"><span>Update</span></button>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<?php
include_once "layouts/footer-scripts.php";
?>