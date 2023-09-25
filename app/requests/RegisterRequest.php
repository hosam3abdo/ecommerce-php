<?php
// autoload class
include_once __DIR__ . "\..\database\models\User.php";
class RegisterRequest
{
    private $password;
    private $email;
    private $phone;
    private $confirmPassword;
    /**
     * Get the value of password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set the value of password
     *
     * @return  self
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get the value of email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the value of email
     *
     * @return  self
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of phone
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set the value of phone
     *
     * @return  self
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get the value of confirmPassword
     */ 
    public function getConfirmPassword()
    {
        return $this->confirmPassword;
    }

    /**
     * Set the value of confirmPassword
     *
     * @return  self
     */ 
    public function setConfirmPassword($confirmPassword)
    {
        $this->confirmPassword = $confirmPassword;

        return $this;
    }
    
    // email
    // array
    public function emailValidation()
    {
        // required , 
        $errors = [];
        if (empty($this->email)) {
            $errors['email-required'] = "<div class='alert alert-danger'> Email Is Required </div>";
        } else {
            // has specific pattern
            // regular expression
            $pattern = '/^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/';
            if (!preg_match($pattern, $this->email)) {
                $errors['email-pattern'] = "<div class='alert alert-danger'> Email Is Invalid </div>";
            }
        }

        return $errors;
    }
    // password , confirm 
    // array
    public function passwordValidation()
    {

        // layer 1 => password , confirm => required
        // layer 2 => password = confirm 
        // layer 3 => password = pattern
         
        // password required
        $errors = [];
        $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,20}$/';
        if (empty($this->password)) {
            $errors['password-required'] = "<div class='alert alert-danger'> Password Is Required </div>";
        }
        // confrim required
        if (empty($this->confirmPassword)) {
            $errors['confrim-required'] = "<div class='alert alert-danger'> Confrim Password Is Required </div>";
        }
        
        if(empty($errors)){
            // password = confrim
            if($this->password != $this->confirmPassword){
                $errors['password-notmatched'] = "<div class='alert alert-danger'> Password Not Matched </div>";
            }
            if(empty($errors)){
                 // pattern 
                if(! preg_match($pattern , $this->password)){
                    $errors['password-invalid'] = "<div class='alert alert-danger'> Minimum eight and maximum 20 characters, at least one uppercase letter, one lowercase letter, one number and one special character </div>";
                }
            }
        }
        return $errors;
    }

    public function checkIfEmailExists()
    {
        $errors = [];
        $userObject = new user;
        $userObject->setEmail($this->email);
        $result = $userObject->emailExists();
        if($result){
            $errors['email-alreadyExists'] = "<div class='alert alert-danger'> Email ALready Exists </div>";
        }
        return $errors;
    }
    public function checkIfPhoneExists()
    {
        $errors = [];
        $userObject = new user;
        $userObject->setPhone($this->phone);
        $result = $userObject->phoneExists();
        if($result){
            $errors['phone-alreadyExists'] = "<div class='alert alert-danger'> Phone ALready Exists </div>";
        }
        return $errors;
    }
    
}
