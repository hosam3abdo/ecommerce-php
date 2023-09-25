<?php
$title = "Register";
include_once 'layouts/header.php';
include_once "app/middleware/guest.php";
include_once 'layouts/nav.php';
include_once 'layouts/breadcrumb.php';
include_once "app/requests/RegisterRequest.php";
include_once "app/database/models/User.php";
include_once "app/mail/mail.php";
if($_POST){
    $validation = new RegisterRequest;
    $validation->setEmail($_POST['email']);
    $emailValidationResult = $validation->emailValidation();

    //  email must be unique in db
    $emailExistsResult = $validation->checkIfEmailExists();
    $validation->setPhone($_POST['phone']);
    $emailPhoneResult = $validation->checkIfPhoneExists();

    $validation->setPassword($_POST['password']);
    $validation->setConfirmPassword($_POST['confirm_password']);
    $passwordValidationResult = $validation->passwordValidation();


    if(empty($emailValidationResult) && empty($passwordValidationResult) && empty($emailExistsResult) && empty($emailPhoneResult)){
        //insert user into database
        $code = rand(10000,99999);
        $userObject = new user;
        $userObject->setFirst_name($_POST['first_name']);
        $userObject->setLast_name($_POST['last_name']);
        $userObject->setEmail($_POST['email']);
        $userObject->setPhone($_POST['phone']);
        $userObject->setPassword($_POST['password']);
        $userObject->setGender($_POST['gender']);
        $userObject->setCode($code);
        $result = $userObject->create();
        if($result){
            // send mail with code
            $subject = "Ecommerce-Verification-Code";
            $body = "<p> Hello {$_POST['first_name']}</p><p> Your Verification Code is:<b style='color:blue;'>$code</b> </p><p> Thank you.</p>";
            $mail = new mail($_POST['email'],$subject,$body);
            $mailResult = $mail->send();
            if($mailResult){
                // header location => check-code.php
                $_SESSION['checkcode-email'] = $_POST['email'];
                header('location:check-code.php?page=register');die;
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
                        <a class="active" data-toggle="tab" href="#lg2">
                            <h4> register </h4>
                        </a>
                    </div>
                    <div class="tab-content">
                        <div id="lg2" class="tab-pane active">
                            <div class="login-form-container">
                                <div class="login-register-form">
                                    <?php 
                                        if(isset($result) && !$result){
                                            echo "<div class='alert alert-danger'> Try Agian Later </div>";
                                        }

                                        if(isset($mailResult) && ! $mailResult){
                                            // header location => check-code.php
                                            echo "<div class='alert alert-danger'> Something Went Wrong </div>";
                                        }
                                    ?>
                                    <form method="post">
                                        <input type="text" name="first_name" placeholder="First Name" value="<?php if(isset($_POST['first_name'])){echo $_POST['first_name'];} ?>">
                                        <input type="text" name="last_name" placeholder="Last Name" value="<?php if(isset($_POST['last_name'])){echo $_POST['last_name'];} ?>">
                                        <input type="email" name="email" placeholder="Email" value="<?php if(isset($_POST['email'])){echo $_POST['email'];} ?>">
                                        <?php 
                                            if(!empty($emailValidationResult)){
                                                foreach ($emailValidationResult as $index => $error) {
                                                   echo $error;
                                                }
                                            }
                                            if(!empty($emailExistsResult)){
                                                foreach ($emailExistsResult as $index => $error) {
                                                   echo $error;
                                                }
                                            }
                                        ?>
                                        <input type="tel" name="phone" placeholder="Phone" value="<?php if(isset($_POST['phone'])){echo $_POST['phone'];} ?>">
                                        <?php 
                                            if(!empty($emailPhoneResult)){
                                                foreach ($emailPhoneResult as $index => $error) {
                                                echo $error;
                                                }
                                            }
                                        ?>
                                        <input type="password" name="password" placeholder="Password">
                                            <?php 
                                                if(isset($passwordValidationResult['password-required'])){
                                                    echo $passwordValidationResult['password-required'];
                                                }
                                                if(isset($passwordValidationResult['password-invalid'])){
                                                    echo $passwordValidationResult['password-invalid'];
                                                }
                                            ?>
                                        <input type="password" name="confirm_password" placeholder="Confirm Password">
                                            <?php 
                                                if(isset($passwordValidationResult['confrim-required'])){
                                                    echo $passwordValidationResult['confrim-required'];
                                                }
                                                if(isset($passwordValidationResult['password-notmatched'])){
                                                    echo $passwordValidationResult['password-notmatched'];
                                                }
                                            ?>
                                        <select name="gender" class="form-control" id="">
                                            <option <?= (isset($_POST['gender']) && $_POST['gender'] == 'm') ? 'selected' : '' ?> value="m">Male</option>
                                            <option  <?= (isset($_POST['gender']) && $_POST['gender'] == 'f') ? 'selected' : '' ?> value="f">Female</option>
                                        </select>
                                        <div class="button-box mt-5">
                                            <button type="submit"><span>Register</span></button>
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
include_once 'layouts/footer.php';
include_once "layouts/footer-scripts.php";
?>