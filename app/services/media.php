<?php 

class media {
    private $image;
    private $errors = [];
    private $extension;
    private $newImageName = "";

    /**
     * Set the value of image
     *
     * @return  self
     */ 
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get the value of errors
     */ 
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Get the value of newImageName
     */ 
    public function getNewImageName()
    {
        return $this->newImageName;
    }


    public function validateOnSize(int $maxSize)
    {
        if($this->image['size'] > $maxSize){
            $this->errors['size'] = "Too large File , Max Size Is $maxSize Bytes";
        }
        return $this;
    }

    public function validateOnExtension(array $availableExtensions)
    {
        $this->extension = pathinfo($this->image['name'] , PATHINFO_EXTENSION);
        if(!in_array($this->extension , $availableExtensions)){
            $this->errors['extension'] = "Sorry , available extensions is " . implode(' , ', $availableExtensions );
        }
        return $this;
    }

    public function upload( string $uploadedDir) // users
    {
        if(empty($this->errors)){
            $this->newImageName = time() . '-' . $_SESSION['user']->id . '.' . $this->extension; // 45132532-1.png
            $path = "assets/img/$uploadedDir/$this->newImageName"; // assests/img/users/45132532-1.png
            move_uploaded_file($this->image['tmp_name'],$path);
        }
        return $this;
    }

    

    

   
}