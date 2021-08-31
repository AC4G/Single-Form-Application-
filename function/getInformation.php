<?php 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require MAILER_DIR . '/src/Exception.php';
require MAILER_DIR . '/src/PHPMailer.php';
require MAILER_DIR . '/src/SMTP.php';

require_once FUNCTION_DIR . '/database.php';
require_once FUNCTION_DIR . '/utilities.php';


class FormInformation{
    public string $email = '';
    public string $visitDate = '';
    public string $visitTime = '';
    public string $firstName = '';
    public string $lastName = '';
    public string $mobileNumber = '';
    public string $persons = '';
    public string $note = '';
    private string $sendTime = '';

    private ?int $id = null;

    public bool $firstNameIsValid = true;
    public bool $lastNameIsValid = true;
    public bool $mobileNumberIsValid = true;
    public bool $emailIsValid = true;
    public bool $visitDateIsValid = true;
    public bool $visitTimeIsValid = true;
    public bool $personsIsValid = true;

    public $errors = [];

    private $csrfToken = '';
    private $csrfTokenExpire = '';

    private int $minPersons = 1;
    private int $maxPersons = 21;

    private string $sql = "INSERT INTO users (firstName, lastName, mobileNumber, email, visitDate, visitTime, persons, note, sendTime, securityKey) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    private $parameterForDeleteLink = [];

    private string $deleteLink = '';

    private string $securityKey = '';

    public function __construct(){
        $this->setCSRFValue();
        $this->csrfTokenRemoveExpired();
        $this->saveDataInDbAndSendEmail();
    }

    private function setCSRFValue(){
        $this->csrfToken = filter_input(INPUT_POST, 'csrfToken');
        $this->csrfTokenExpire = $_SESSION['csrfTokenExpire'];
    }

    private function saveDataInDbAndSendEmail(){
        try{
            $stmt = getDB()->prepare($this->sql);    
        
            $stmt->bindParam(1, $this->firstName, PDO::PARAM_STR);
            $stmt->bindParam(2, $this->lastName, PDO::PARAM_STR);
            $stmt->bindParam(3, $this->mobileNumber, PDO::PARAM_INT);
            $stmt->bindParam(4, $this->email, PDO::PARAM_STR);
            $stmt->bindParam(5, $this->visitDate, PDO::PARAM_STR);
            $stmt->bindParam(6, $this->visitTime, PDO::PARAM_STR);
            $stmt->bindParam(7, $this->persons, PDO::PARAM_INT);
            $stmt->bindParam(8, $this->note, PDO::PARAM_STR);
            $stmt->bindParam(9, $this->sendTime, PDO::PARAM_STR);
            $stmt->bindParam(10, $this->securityKey, PDO::PARAM_STR);

            $this->addNewValues($this->csrfToken, $stmt);

        }catch(PDOException $e){
            echo 'Failed to insert: ' . $e;
        }
    }

    private function addNewValues($csrfToken, $stmt){
        if(isPost()){
            if($csrfToken == $_SESSION['csrfToken']){
                $this->email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
                $this->visitDate = filter_input(INPUT_POST, 'visitDate');
                $this->visitTime = filter_input(INPUT_POST, 'visitTime');
                $this->persons = filter_input(INPUT_POST, 'persons', FILTER_VALIDATE_INT);
                $this->firstName = filter_input(INPUT_POST, 'firstName', FILTER_SANITIZE_STRING);
                $this->lastName = filter_input(INPUT_POST, 'lastName', FILTER_SANITIZE_STRING);
                $this->mobileNumber = filter_input(INPUT_POST, 'mobileNumber', FILTER_VALIDATE_INT);
                $this->note = filter_input(INPUT_POST, 'note');
                $this->sendTime = date('h:i a');
                $this->securityKey = hash('md5', random_bytes(16));

                $this->ifValuesEmpty();

                $this->executeAndSetValuesToDefault($stmt);

                $this->haveErrors();

            }else{
                throw new Exception('Invalided CSRF-Token!');
            }
        }
    }

    private function ifValuesEmpty(){
        if(empty($this->email)){
            $this->errors['email'] = 'Email ist leer';
            $this->emailIsValid = false;
        }
        if(empty($this->firstName)){
            $this->errors['firstName'] = 'Vorname ist leer';
            $this->firstNameIsValid = false;
        }
        if (preg_match('~[0-9]+~', $this->firstName)){
            $this->errors['firstName'] = 'Vorname ist kein String';
            $this->firstNameIsValid = false;
        }
        if(empty($this->lastName)){
            $this->errors['lastName'] = 'Nachname ist leer';
            $this->lastNameIsValid = false;
        }
        if (preg_match('~[0-9]+~', $this->lastName)){
            $this->errors['lastName'] = 'Nachname ist kein String';
            $this->lastNameIsValid = false;
        }
        if(empty($this->mobileNumber)){
            $this->errors['mobileNumber'] = 'Telefonnummer ist leer';
            $this->mobileNumberIsValid = false;
        }
        if(empty($this->persons)){
            $this->errors['persons'] = 'Personen nicht ausgewaehlt';
            $this->personsIsValid = false;
        }
        if(empty($this->visitDate)){
            $this->errors['date'] = 'Datum nicht ausgewaehlt';
            $this->visitDateIsValid = false;
        }
        if(empty($this->visitTime)){
            $this->errors['time'] = 'Uhrzeit nicht ausgewaehlt';
            $this->visitTimeIsValid = false;
        }
        if($this->minPersons <= $this->persons && $this->persons >= $this->maxPersons){
            $this->errors['personsNumber'] = 'Keine gueltige Anzahl an Personen!';
            $this->personsIsValid = false;
        }
    }

    private function executeAndSetValuesToDefault($stmt){
        if (0 === count($this->errors)){
            $this->visitDate = date('d-m-Y', strtotime($this->visitDate));
            $this->visitTime = date('h:i A', strtotime($this->visitTime));
        
            $stmt->execute();

            $this->getLatestID();

            $this->createDeleteLink();
        
            $this->mailer($this->email);
        
            $this->email = '';
            $this->visitDate = '';
            $this->visitTime = '';
            $this->firstName = '';
            $this->lastName = '';
            $this->mobileNumber = '';
            $this->persons = '';
            $this->note = '';
            $this->errors = [];
        
            $_SESSION['noErrors'] = true;
        
            header('Location: index.php');
        }
    }

    private function haveErrors(){
        if(count($this->errors) > 0){
            $_SESSION['noErrors'] = false;
        }
    }

    private function csrfTokenRemoveExpired(){
        $csrfToken = filter_input(INPUT_POST, 'csrfToken') ?? ''; 
        $csrfTokenExpire = $_SESSION['csrfTokenExpire'] ?? '';

        if(!empty($csrfToken)){
            if(time() >= $csrfTokenExpire){
                unset($_SESSION['csrfToken']);
                unset($_SESSION['csrfTokenExpire']);
            }
        }
    }

    function mailer($email){
        $mail = new PHPMailer();
    
        try{   
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'vost6000@gmail.com';
            $mail->Password = '***********************';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; 
            $mail->Port       = 465;     
    
            $mail->setFrom('vost6000@gmail.com', 'Bansai Reservierungsbestaetigung');
            $mail->addAddress($email);
    
            $mail->isHTML(true);
            $mail->Subject = 'Reservierungsbestaetigung';
            $mail->Body = '
            <!doctype html>
            <html lang="de">
                <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width,initial-scale=1">
                    <meta name="x-apple-disable-message-reformatting">
                    <title>Restaurant Bansai</title>
                    <style>
                        table, td, div, h1, p, h6, h3 {
                            font-family: Arial, sans-serif;
                        }
                        body{
                            background-color: #222;
                        }
                    </style>
                </head>
                <body style="margin: 0; background-color: #222;">
                    <div class="nav" aria-roledescription="email" lang="en" style="text-size-adjust:100%;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%; height: 120px;background-image: url(https://ru-static.z-dn.net/files/dfe/262f6d4d8330ce596695d8134c8cb0d1.jpg); background-position: center; background-repeat: no-repeat; border-bottom: 3px solid #de5c9d;">
                        <table role="presentation" style="width:100%;border:none;border-spacing:0;">
                            <tr>
                                <td style="padding:0; text-align: center; top: 20px; position: relative;">
                                    <a href="http://178.12.20.185/Single-Form-Application-Copy" style="text-decoration: none;">
                                        <h1 style="color: black; text-decoration: none;">Restaurant Bansai</h1>
                                        <h3 style="color: black; text-decoration: none;">Japanische Kueche</h6>
                                    </a>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div style="height: 400px; max-width: 500px; margin-left: auto; margin-right: auto; padding-left: 10px; padding-right: 10px; text-align: center;">
                        <h3 style="color: white; margin-top: 40px;">Ihre Reservierung ist bei uns angekommen!</h3>
                        <p style="color:white;">Ihr Besuch findet statt:</p>
                        <p style="color: white;">Am: ' . $this->visitDate . ' Um: ' . $this->visitTime . '</p>
                        <p style="color: white;">Mit: ' . $this->persons . ' Personen</p>
                        <h3 style="color: white; margin-top: 50px;">Moechtest du deine Reservierung stornieren? Dann folge dem Link:</h3>
                        <a href="' . $this->deleteLink .'" style="color: #00BFFF;">'. $this->deleteLink .'</a>
                        <img src="https://i.postimg.cc/PqCJvGfF/frame-1.png" style="border-radius: 20px; width: 200px; margin-top: 20px;">
                    </div>
                </body>
            </html>';
    
            $mail->send();
        }catch(Exception $e){
            echo "Reservierungsbestaetigung konnte nicht gesendet werden: {$mail->ErrorInfo}";
        }
    }

    private function getLatestID(){
        $this->id = getDB()->lastInsertId();
    }

    private function createDeleteLink(){
        $this->parameterForDeleteLink = [
            'page'=>'delete',
            'id'=>$this->id,
            'securityKey'=>$this->securityKey
        ];

        $this->deleteLink = 'http://178.12.20.185/Single-Form-Application-Copy/index.php?' . http_build_query($this->parameterForDeleteLink);
    }

    public function selected($persons, $amountOfPersons){
        if($persons == $amountOfPersons){
            return ' selected';
        }
    }
}

$processingInformation = new FormInformation();
