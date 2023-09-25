<?php
$title = "Verify Email";
include_once 'layouts/header.php';
include_once "app/requests/RegisterRequest.php";
include_once "app/database/models/User.php";
include_once "app/mail/mail.php";

if($_POST){
   $emailValidation = new RegisterRequest;
   $emailValidation->setEmail($_POST['email']);
   $emailValidationResult = $emailValidation->emailValidation();

   if(empty($emailValidationResult)){
       $userObject = new user;
       $userObject->setEmail($_POST['email']);
       $emailExistsResult = $userObject->emailExists();
       if($emailExistsResult){
            $user = $emailExistsResult->fetch_object();
            $code = rand(10000,99999);
            $userObject->setCode($code);
            $result = $userObject->updateCode();
            if($result){
                $body = "<p> Hello {$user->first_name}</p><p> Your Verification Code is:<b style='color:blue;'>$code</b> </p><p> Thank you.</p>";
                $mail = new mail($_POST['email'],"Forget Password",$body);
                $mailResult = $mail->send();
                if($mailResult){
                    $_SESSION['checkcode-email'] = $_POST['email'];
                    header('location:check-code.php?page=verify-email');die;
                }
            }
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
                                    <form  method="post">
                                        <input type="email" name="email" placeholder="Email">
                                        <?php 
                                            if(!empty($emailValidationResult)){
                                                foreach ($emailValidationResult as $key => $value) {
                                                    echo $value;
                                                }
                                            }

                                            if(isset($emailExistsResult) && empty($emailExistsResult)){
                                                echo "<div class='alert alert-danger'> Email Dosen't Match Our Records </div>";
                                            }
                                        ?>
                                            <button type="submit" class="btn btn-success"><span>Check</span></button>
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