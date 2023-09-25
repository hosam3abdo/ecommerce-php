<?php
$title = "Check Code";
include_once 'layouts/header.php';
include_once "app/requests/CheckcodeRequest.php";
include_once "app/database/models/User.php";

if($_GET){
    if(isset($_GET['page'])){
        $pages = ['login','register','verify-email'];
        if(!in_array($_GET['page'],$pages)){
            header('location:errors/404.php');die;
        }
    }else{
        header('location:errors/404.php');die;
    }
}else{
    header('location:errors/404.php');die;
}

if($_POST){
    $validation = new CheckcodeRequest;
    $validation->setCode($_POST['code']);
    $validationResult = $validation->codeValidation();
    
    if(empty($validationResult)){
        // search on code,email in db
        $userObject = new user;
        $userObject->setCode($_POST['code']);
        $userObject->setEmail($_SESSION['checkcode-email']);
        $checkCodeResult = $userObject->checkCode();
        if($checkCodeResult){
            // true => status = 1 => header to login page
            $userObject->setStatus(1);
            $result = $userObject->updateStatus();
            if($result){
                switch ($_GET['page']) {
                    case 'login':
                    case 'register' :
                        header('location:login.php');die;
                    default:
                        header('location:set-new-password.php');die;
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
                            <h4> Check Code </h4>
                        </a>
                    </div>
                    <div class="tab-content">
                        <div id="lg1" class="tab-pane active">
                            <div class="login-form-container">
                                <div class="login-register-form">
                                    <form  method="post">
                                        <input type="number" name="code" placeholder="Code">
                                        <?php 
                                            if(!empty($validationResult)){
                                                foreach ($validationResult as $key => $error) {
                                                    echo $error;
                                                }
                                            }
                                            if(isset($checkCodeResult) && empty($checkCodeResult)){
                                                echo "<div class='alert alert-danger'> Wrong Code </div>";
                                            }

                                            if(isset($result) && ! $result){
                                                echo "<div class='alert alert-danger'> Something Went Wrong </div>";
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