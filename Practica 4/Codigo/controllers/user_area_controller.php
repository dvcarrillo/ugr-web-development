<?php
/**
 * Created by PhpStorm.
 * User: dvcarrillo
 * Date: 13/5/18
 * Time: 20:53
 */

class UserAreaController
{
    public $loginItem = "";
    public $user;
    public $alertMsg = "";

    public function __construct() {
        require_once('models/user.php');
        $this->user = "";
    }

    public function setView($item) {
        $this->loginItem = $item;
    }

    public function setUser() {
        if (isset($_SESSION['user_email']) && isset($_SESSION['user_email'])) {
            $this->user = User::find($_SESSION['user_email'], $_SESSION['user_password']);
        }
    }

    public function show() {
        $this->setUser();
        $this->processActions();

        switch ($this->loginItem) {
            case 'login':
                require_once('views/user_area/loginForm.php');
                break;
            case 'registration':
                require_once('views/user_area/registrationForm.php');
                break;
            case 'userarea':   
                if (isset($_SESSION['user_name'])) {
                    require_once('views/user_area/userArea.php');
                    if($_GET['option']=="modify"){
                        $this->modifyUser();
                    }
                }
                else {
                    $success = $this->processLogin();
                    if ($success) {
                        require_once('views/user_area/userArea.php');
                    } else {
                        $this->alertMsg = "Usuario o clave incorrectos. Por favor introduzca de nuevo sus datos";
                        require_once('views/user_area/loginForm.php');
                    }
                }
                break;
            case 'registeruser':
                $success = $this->processUserRegistration();
                if ($success) {
                    $this->alertMsg = "Se ha registrado correctamente. Por favor, inicie sesion";
                    require_once("views/user_area/loginForm.php");
                }
                else {
                    require_once("views/user_area/registrationForm.php");
                }
                break;
            case 'modifydata':
                if(isset($_SESSION['user_name']))
                    require_once('views/user_area/modifyDataForm.php');
                else
                    require_once('views/pages/error.php');
                break;
            default:
                require_once('views/pages/error.php');
                break;
        }
    }

    private function processLogin() {
        if ((!isset($_POST['email'])) || (!isset($_POST['password']))) {
            return false;
        }

        $entered_user = User::find($_POST['email'], $_POST['password']);

        if(is_null($entered_user)) {
            return false;
        }

        // Set session variables
        $_SESSION['user_name'] = $entered_user->nombre;
        $_SESSION['user_surname'] = $entered_user->apellidos;
        $_SESSION['user_password'] = $entered_user->clave;
        $_SESSION['user_email'] = $entered_user->email;
        $_SESSION['user_type'] = $entered_user->tipo;
        $_SESSION['user_avatar'] = $entered_user->avatar;

        // Set user object
        $this->user = $entered_user;

        return true;
    }

    private function processUserRegistration() {
        if ((!isset($_POST['name'])) || (!isset($_POST['email'])) || (!isset($_POST['password']))
            || (!isset($_POST['confirm-password']))) {
            $this->alertMsg = "Por favor, complete todos los campos requeridos y vuelva a intentarlo";
            return false;
        }

        $entered_name = $_POST['name'];
        $entered_surname = $_POST['surname'];
        $entered_email = $_POST['email'];
        $entered_password = $_POST['password'];
        $entered_password_c = $_POST['confirm-password'];

        $default_type = "registrado";
        $default_avatar = "avatar.png";

        if ($entered_password !=  $entered_password_c) {
            $this->alertMsg = "Las claves no coinciden. Por favor, vuelva a intentarlo";
            return false;
        }

        // Check if the user already exists on the database
        $exists = User::exists($entered_email);
        if($exists) {
            $this->alertMsg = "El usuario especificado ya existe en el sistema";
            return false;
        }

        // If it does not exists, register the new user
        $success = User::addUser($entered_name, $entered_surname, $entered_email, $entered_password, $default_type,
            $default_avatar);

        if (!$success) {
            $this->alertMsg = "Ha habido un problema registrando su usuario";
            return false;
        }

        return true;
    }

    private function processActions(){
        if(isset($_GET['action'])) {
            $action = $_GET['action'];

            switch ($action) {
                case 'close':
                    // remove all session variables
                    session_unset();
                    // destroy the session
                    session_destroy();
                    $this->alertMsg = "Sesion cerrada, hasta pronto";
                    break;
                case 'modify':
                    $this->modifyUser();
                    break;
            }
        }
    }


    private function uploadAvatar(){
        $target_dir = "views/img/avatar/";//realpath(dirname(getcwd())) . "uploads/";
        $target_file = $target_dir . basename($_FILES["avatar-upload"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        // Check if image file is a actual image or fake image
        if(isset($_POST["submit"])) {
            $check = getimagesize($_FILES["avatar-upload"]["tmp_name"]);
            if($check !== false) {
                //echo "File is an image - " . $check["mime"] . ".";
                $uploadOk = 1;
            } else {
                //echo "File is not an image.";
                $uploadOk = 0;   
            }
        }
        // Check if file already exists
        if (file_exists($target_file)) {
            //echo "Sorry, file already exists.";
            $uploadOk = 0;
        }
        // Check file size
        if ($_FILES["avatar-upload"]["size"] > 500000) {
            //echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }
        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            //echo "Sorry, your file was not uploaded.";
            return false;
        // if everything is ok, try to upload file
        } else {
            echo(realpath(dirname(getcwd())));
            if (move_uploaded_file($_FILES["avatar-upload"]["tmp_name"], $target_file)) {
                //echo "The file ". basename( $_FILES["avatar-upload"]["name"]). " has been uploaded.";
                return true;
            } else {
                //echo "Sorry, there was an error uploading your file.";
                return false;
            }
        }
    }

    private function modifyUser() {
        if ((!isset($_POST['name'])) || (!isset($_POST['email'])) || (!isset($_POST['password']))
            || (!isset($_POST['confirm-password']))) {
            $this->alertMsg = "Por favor, complete todos los campos requeridos y vuelva a intentarlo";
            return false;
        }

        $modified_name = $_POST['name'];
        $modified_surname = $_POST['surname'];
        $modified_avatar = $_FILES["avatar-upload"]["name"];
        $modified_email = $_POST['email'];
        $modified_password = $_POST['password'];
        $modified_password_c = $_POST['confirm-password'];

        if ($modified_avatar == "" || !isset($_FILES["avatar-upload"]["name"])) {
            $modified_avatar = $this->user->avatar;
        }
        else {
            if($this->uploadAvatar()){
                $modified_avatar=$_FILES["avatar-upload"]["name"];
            }
            else
                $modified_avatar = "avatar.png";
        }

        $db = ConexionDB::getInstance();

        $stmt = $db->prepare("UPDATE usuarios SET nombre=:nombre, apellidos=:apellidos, email=:email, clave=:clave, avatar=:avatar WHERE usuarios.id = :id");

        $stmt->bindParam(':nombre',$modified_name);
        $stmt->bindParam(':apellidos',$modified_surname);
        $stmt->bindParam(':email',$modified_email);
        $stmt->bindParam(':clave',$modified_password);
        $stmt->bindParam(':avatar',$modified_avatar);
        $stmt->bindParam(':id',$this->user->id);

        $success = $stmt->execute();

        return $success;


    }
}