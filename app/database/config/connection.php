<?php
// mysqli
class connection {
    private $hostname = 'localhost';
    private $username = 'root';
    private $password = '';
    private $database = 'nti_ecommerce';
    private $con;

    public function __construct() {
       $this->con = new mysqli($this->hostname,$this->username,$this->password,$this->database);
        // if ($con->connect_error) {
        //     die("Connection failed: " . $con->connect_error);
        // }
        // echo "Connected successfully";
    }
    // insert - update - delete 
    public function runDML($query)
    {
        $result = $this->con->query($query);
        if($result){
            return true;
        }else{
            return false;
        }
    }
    // select
    public function runDQL($query)
    {
        $result = $this->con->query($query); 
        if($result->num_rows > 0){
            return $result;
        }else{
            return [];
        }
    }
}
