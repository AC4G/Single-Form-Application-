<?php

require_once FUNCTION_DIR . '/database.php';
require_once FUNCTION_DIR . '/utilities.php';

class DeleteForm{

    private ?int $id = null;
    private string $securityKey = '';

    private $securityKeyByIDFromDB = [];

    public $errors = [];

    public function __construct(){
        $this->deleteDataFromDB();
    }

    private function deleteDataFromDB(){
        try{

            $this->id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
            $this->securityKey = filter_input(INPUT_GET, 'securityKey', FILTER_SANITIZE_STRING);

            $this->filter();

        }catch(Exception $e){
            echo 'Failed to delete: ' . $e;
        }
    }

    private function filter(){
        if($this->id == null){
            $this->errors['id'] = 'ID ist leer'; 
        }
        if(empty($this->securityKey)){
            $this->errors['securityKey'] = 'Security Key ist leer';
        }

        if(!empty($this->securityKeyByIDFromDB)){
            $this->errors['securityKeyByIDFromDB'] = 'Security Key wurde nicht gefunden';
        }

        $this->getSecurityKeyByID();
        var_dump($this->securityKeyByIDFromDB);

        if(0 === count($this->errors)){
            if($this->securityKeyByIDFromDB['securityKey'] == $this->securityKey){
                $sql = 'DELETE FROM users WHERE id=' . $this->id . '';
                getDB()->query($sql);
            }else{
                $this->errors['invalidKey'] = 'Invalid Key';
            }
        }
    }

    private function getSecurityKeyByID(){
        $query = 'SELECT securityKey FROM users WHERE id=' . $this->id . '';
        $sth = getDB()->query($query);

        $this->securityKeyByIDFromDB = $sth->fetch();
    }
}

$deleteRegistration = new DeleteForm();