<?php
$title = "Login";
include_once 'layouts/header.php';
include_once "app/middleware/guest.php";
include_once 'layouts/nav.php';
include_once 'layouts/breadcrumb.php';
include_once "app/requests/RegisterRequest.php";
include_once "app/requests/LoginRequest.php";
include_once "app/database/models/User.php";
if($_POST)
{
    // print_r($_POST);die;
    // validation => 
    // email => required , regex
    $emailValidation = new RegisterRequest;
    $emailValidation->setEmail($_POST['email']);
    $emailValidationResult = $emailValidation->emailValidation();
    // password => reqired , regex
    $passwordValidation = new LoginRequest;
    $passwordValidation->setPassword($_POST['password']);
    $passwordValidationResult = $passwordValidation->passwordValidation();

    if(empty($emailValidationResult) && empty($passwordValidationResult)){
        // get user from db
        $userObject = new user;
        $userObject->setPassword($_POST['password']);
        $userObject->setEmail($_POST['email']);
        $result = $userObject->login();
        if($result){
          $user =  $result->fetch_object();
            // user => exists => check status 
            switch ($user->status) {
                case '1':
                    $_SESSION['user'] = $user;
                    if(isset($_POST['remember_me'])){
                        setcookie('user', $_POST['email'] , time() + (86400 * 30) , '/');
                    }
                   header('location:index.php');die;
                case '0':
                    $_SESSION['checkcode-email'] = $_POST['email'];
                    header('location:check-code.php?page=login');die;
                default:
                    $message = "<div class='alert alert-danger'> Sorry , Your Account Has been Blocked </div>";
                    break;
            }
        }else{
              // user => not exists => error message
              $message = "<div class='alert alert-danger'> Falid Attempt </div>";
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
                            <h4> login </h4>
                        </a>
                    </div>
                    <div class="tab-content">
                        <div id="lg1" class="tab-pane active">
                            <div class="login-form-container">
                                <div class="login-register-form">

                                    <form  method="post">
                                        <input type="email" name="email" placeholder="Email" value="<?php if(isset($_SESSION['checkcode-email'])){ echo $_SESSION['checkcode-email']; unset($_SESSION['checkcode-email']); } ?>">
                                        <?php 
                                            if(!empty($emailValidationResult)){
                                                foreach ($emailValidationResult as $key => $error) {
                                                   echo $error;
                                                }
                                            }
                                        ?>
                                        <input type="password" name="password" placeholder="Password">
                                        <?php 
                                            if(!empty($passwordValidationResult)){
                                                foreach ($passwordValidationResult as $key => $error) {
                                                   echo $error;
                                                }
                                            }
                                            if(isset($message)){
                                                echo $message;
                                            }
                                        ?>
                                        <div class="button-box">
                                            <div class="login-toggle-btn">
                                                <input type="checkbox" name="remember_me">
                                                <label>Remember me</label>
                                                <a href="verify-email.php">Forgot Password?</a>
                                            </div>
                                            <button type="submit"><span>Login</span></button>
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