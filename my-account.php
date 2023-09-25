<?php
$title = "Profile";
include_once "layouts/header.php";
include_once "app/middleware/auth.php";
include_once "layouts/nav.php";
include_once "layouts/breadcrumb.php";
include_once "app/database/models/User.php";
include_once "app/services/media.php";
include_once "app/requests/RegisterRequest.php";
include_once "app/requests/LoginRequest.php";
include_once "app/mail/mail.php";




// get user information
$userObject = new user;
$userObject->setEmail($_SESSION['user']->email);




$errors = [];
$success = [];
// update user profile
if(isset($_POST['update-profile'])){
    // ahmed
    // print_r($_POST);
    // print_r($_FILES);die;
    if(empty($_POST['first_name']) || empty($_POST['last_name']) || empty($_POST['phone']) || empty($_POST['gender'])){
        $errors['update-profile']['all-feilds'] = "<div class='alert alert-danger'> All Fields Are Required </div>";
    }else{
        // no validation error
        // update on db
        if($_FILES['image']['error'] == 0){
            $media = new media;
            $imageResult = $media->setImage($_FILES['image'])
            ->validateOnSize(10**6)
            ->validateOnExtension(['png','jpg','jpeg'])
            ->upload('users');
            if(empty($imageResult->getErrors())){
                $userObject->setImage($imageResult->getNewImageName());
            }
            // validate on size
            // validate on extension
            // upload image
        }

        $result = $userObject->setFirst_name($_POST['first_name'])
        ->setLast_name($_POST['last_name'])
        ->setPhone($_POST['phone'])
        ->setGender($_POST['gender'])
        ->update();

        if($result){
            $_SESSION['user']->first_name = $_POST['first_name'];
            $_SESSION['user']->last_name = $_POST['last_name'];
            $_SESSION['user']->phone = $_POST['phone'];
            $_SESSION['user']->gender = $_POST['gender'];
            $success['update-profile']['success'] =  "<div class='alert alert-success'> Data Updated Successfully </div>";
        }else{
            $errors['update-profile']['something'] = "<div class='alert alert-danger'> Soemthing Went Wrong </div>";
        }
    }
}

// change password
if(isset($_POST['update-password'])){
  
    // old password => required , regex ==> loginRequest
    $oldPasswordValidation = new LoginRequest;
    $oldPasswordValidation->setPassword($_POST['old_password']);
    $oldPasswordValidationResult = $oldPasswordValidation->passwordValidation();

    // old password = database password
    if(empty($oldPasswordValidationResult)){
        // https://www.php.net/manual/en/function.password-verify.php
        if(sha1($_POST['old_password']) != $_SESSION['user']->password){
            $oldPasswordValidationResult['old-database'] = "<div class='alert alert-danger'> Wrong Password </div>";
        }
    }

    $passwordValidation = new RegisterRequest;
    $passwordValidation->setPassword($_POST['new_password']);
    $passwordValidation->setConfirmPassword($_POST['confirm_password']);
    $passwordValidationResult = $passwordValidation->passwordValidation();
    // new password => required , regex ==> registerRequest
    // new password = confirm
    // confirm = required

    if(empty($oldPasswordValidationResult) && empty($passwordValidationResult)){
        $userObject->setPassword($_POST['new_password']);
        $result = $userObject->updatePassword();
        if($result){
            $success['update-password']['success'] = "<div class='alert alert-success'> Password Updated Successfully </div>";
        }else{
            $errors['update-password']['something'] = "<div class='alert alert-danger'> Soemthing Went Wrong </div>";
        }
    }
}

if(isset($_POST['update-email'])){
    // required  , regex , 
    $emailValidation = new RegisterRequest;
    $emailValidation->setEmail($_POST['email']);
    $emailValidationResult = $emailValidation->emailValidation(); 
    //unique
    $emailExistsResult = $emailValidation->checkIfEmailExists(); 
    if(empty($emailValidationResult) && empty($emailExistsResult)){
        // update email in db
        $userObject->setEmail($_POST['email']);
        $userObject->setId($_SESSION['user']->id);
        $userObject->setStatus(0);
        $updateEmailResult = $userObject->updateEmail();
        if($updateEmailResult){
            // send mail
            $link = "http://localhost/nti/p13/ecommerce/change-email.php?email={$_POST['email']}";
            $body = "Hello {$_SESSION['user']->first_name}
            <p> Please Click the link below To verify Your Account</p>
            <div> <a href='$link'> Verify </a> </div>
            <p>Thank You</p>";
            $mail = new mail($_POST['email'],'Verify-Email-Address',$body);
            $result = $mail->send();
            if($result){
                $success['update-email']['success'] = "<div class='alert alert-success'> A fresh Email Has Been Sent Successfully , please check your email address  </div>";
                unset($_SESSION['user']);
                header('Refresh: 5; url=login.php');
            }else{
                $errors['update-email']['try-again'] = "<div class='alert alert-danger'> Please Try Agian Later </div>";
            }

        }else{
            $errors['update-email']['something'] = "<div class='alert alert-danger'> Something Went Wrong </div>";
        }
        
    }

}   

$result = $userObject->emailExists();
$user = $result->fetch_object(); // user => galal

?>
<!-- my account start -->
<div class="checkout-area pb-80 pt-100">
    <div class="container">
        <div class="row">
            <div class="ml-auto mr-auto col-lg-9">
                <div class="checkout-wrapper">
                    <div id="faq" class="panel-group">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h5 class="panel-title"><span>1</span> <a data-toggle="collapse" data-parent="#faq" href="#my-account-1">Edit your account information </a></h5>
                            </div>
                            <div id="my-account-1" class="panel-collapse collapse <?php if(isset($_POST['update-profile'])){echo 'show';} ?>">
                                <div class="panel-body">
                                    <div class="billing-information-wrapper">
                                        <div class="account-info-wrapper">
                                            <h4>My Account Information</h4>
                                            <h5>Your Personal Details</h5>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <?php 
                                                    if(isset($errors['update-profile'])){
                                                        foreach ($errors['update-profile'] as $key => $error) {
                                                            echo $error;
                                                        }
                                                    }
                                                    if(isset($imageResult) && !empty($imageResult->getErrors())){
                                                        foreach ($imageResult->getErrors() as $key => $error) {
                                                            echo $error;
                                                        }
                                                    }
                                                    if(isset($success['update-profile']['success'])){
                                                        echo $success['update-profile']['success'];
                                                    }   
                                                ?>
                                            </div>
                                        </div>
                                        <form  method="post" enctype="multipart/form-data">
                                            <div class="row">
                                                <div class="col-12 my-5">
                                                    <div class="row">
                                                        <div class="col-4 offset-4">
                                                            <img src="assets/img/users/<?= $user->image ?>" alt="" class="w-100 rounded-circle">
                                                            <input type="file" name="image" class="form-control" id="">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-6">
                                                    <div class="billing-info">
                                                        <label>First Name</label>
                                                        <input type="text" name="first_name" value="<?= $user->first_name ?>">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-6">
                                                    <div class="billing-info">
                                                        <label>Last Name</label>
                                                        <input type="text" name="last_name" value="<?= $user->last_name ?>">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-6">
                                                    <div class="billing-info">
                                                        <label>Phone</label>
                                                        <input type="number" name="phone" value="<?= $user->phone ?>">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-6">
                                                    <div class="billing-info">
                                                        <label>Gender</label>
                                                        <select name="gender" class="form-control"  id="">
                                                            <option <?= $user->gender == 'm' ? 'selected' : '' ?> value="m">male</option>
                                                            <option <?= $user->gender == 'f' ? 'selected' : '' ?> value="f">female</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="billing-back-btn">
                                                <div class="billing-btn">
                                                    <button type="submit" name="update-profile">update profile</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h5 class="panel-title"><span>2</span> <a data-toggle="collapse" data-parent="#faq" href="#my-account-2">Change your password </a></h5>
                            </div>
                            <div id="my-account-2" class="panel-collapse collapse <?php if(isset($_POST['update-password'])){echo 'show';} ?>">
                                <div class="panel-body">
                                    <div class="billing-information-wrapper">
                                        <div class="account-info-wrapper">
                                            <h4>Change Password</h4>
                                            <h5>Your Password</h5>
                                        </div>
                                        <form  method="post">
                                            <div class="row">
                                                <div class="col-12">
                                                    <?php  
                                                        if(isset($success['update-password']['success'])){
                                                            echo $success['update-password']['success'];
                                                        }  
                                                        if(isset($errors['update-password']['something'])){
                                                            echo $errors['update-password']['something'];
                                                        }   
                                                    ?>
                                                </div>
                                                <div class="col-lg-12 col-md-12">
                                                    <div class="billing-info">
                                                        <label>Old Password</label>
                                                        <input type="password" name="old_password">
                                                        <?php 
                                                        if(!empty($oldPasswordValidationResult)){
                                                            foreach ($oldPasswordValidationResult as $key => $error) {
                                                                echo $error;
                                                            }
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 col-md-12">
                                                    <div class="billing-info">
                                                        <label>New Password</label>
                                                        <input type="password" name="new_password">
                                                        <?php 
                                                            if(isset($passwordValidationResult['password-required'])){
                                                                echo $passwordValidationResult['password-required'];
                                                            }
                                                            if(isset($passwordValidationResult['password-invalid'])){
                                                                echo $passwordValidationResult['password-invalid'];
                                                            }
                                                        ?>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 col-md-12">
                                                    <div class="billing-info">
                                                        <label>Password Confirm</label>
                                                        <input type="password" name="confirm_password">
                                                        <?php 
                                                            if(isset($passwordValidationResult['confrim-required'])){
                                                                echo $passwordValidationResult['confrim-required'];
                                                            }
                                                            if(isset($passwordValidationResult['password-notmatched'])){
                                                                echo $passwordValidationResult['password-notmatched'];
                                                            }
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="billing-back-btn">
                                                <div class="billing-btn">
                                                    <button type="submit" name="update-password">update password</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h5 class="panel-title"><span>3</span> <a data-toggle="collapse" data-parent="#faq" href="#my-account-3">Change your Email </a></h5>
                            </div>
                            <div id="my-account-3" class="panel-collapse collapse <?php if(isset($_POST['update-email']) || isset($_SESSION['message'])){echo 'show';} ?>">
                                <div class="panel-body">
                                    <div class="billing-information-wrapper">
                                        <div class="account-info-wrapper">
                                            <h4>Change Email</h4>
                                            <h5>Your Email</h5>
                                        </div>
                                        <form  method="post">
                                            <div class="row">
                                                <div class="col-12">
                                                    <?php 
                                                        if(isset($success['update-email']['success'])){
                                                            echo $success['update-email']['success'];
                                                        } 
                                                        if(isset($errors['update-email']['try-agian'])){
                                                            echo $errors['update-email']['try-agian'];
                                                        } 
                                                        if(isset($_SESSION['message'])){
                                                            echo $_SESSION['message'];
                                                            unset($_SESSION['message']);
                                                        } 
                                                    ?>
                                                </div>
                                                <div class="col-lg-12 col-md-12">
                                                    <div class="billing-info">
                                                        <label>Email</label>
                                                        <input type="email" name="email" value="<?= $user->email ?>">
                                                        <?php  
                                                            if(!empty($emailValidationResult)){
                                                                foreach ($emailValidationResult as $key => $error) {
                                                                    echo $error;
                                                                }
                                                            }
                                                            if(!empty($emailExistsResult)){
                                                                foreach ($emailExistsResult as $key => $error) {
                                                                    echo $error;
                                                                }
                                                            }
                                                        ?> 
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="billing-back-btn">
                                                <div class="billing-btn">
                                                    <button type="submit" name="update-email">update Email</button>
                                                </div>
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
</div>
<!-- my account end -->
<?php
include_once "layouts/footer.php";
include_once "layouts/footer-scripts.php";
?>