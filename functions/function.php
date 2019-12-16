<?php

/* ********************  Helper Function ********************************** */


function clean($str){
    return htmlentities($str);
}

function redirect($loc){
    return header("Location:$loc");
}
function set_message($message){
    if(!empty($message)){
        $_SESSION['message'] = $message;
    }else{
        $message ="";
    }
}
function dispaly_message(){
    if(isset($_SESSION['message'])){
        echo $_SESSION['message'];
        unset($_SESSION['message']);
    }
}
function token_generator(){
    $token = $_SESSION['token'] = md5(uniqid(mt_rand(),true));
    return $token;
}
function validation_error($error_message){
    $error_message = '
    <div class="alert alert-danger alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <strong>Warning!</strong>' . $error_message . '
    </div>
    ';;
    return $error_message;
}
function username_exit($username){
    $sql = "select id from users where username = '$username'";

    $res = query($sql);

    if(row_count($res) == 1){
        return true;
    }else{
        return false;
    }
}
function email_exit($email){
    $sql = "select id from users where email = '$email'";

    $res = query($sql);

    if(row_count($res) == 1){
        return true;
    }else{
        return false;
    }
}
function send_mail($to,$subject,$message,$header){
   return mail($to,$subject,$message,$header);
}
/* ********************  Validation Function ********************************** */
function validate_user_registration(){

    $min = 3;
    $max = 20;
    $errors = [];
    if($_SERVER['REQUEST_METHOD']=='POST'){

        $firstname                   = clean($_POST['first_name']);
        $lastname                    = clean($_POST['last_name']);
        $username                    = clean($_POST['username']);
        $email                       = clean($_POST['email']);
        $password                    = clean($_POST['password']);
        $confirm_password            = clean($_POST['confirm_password']);


        if(strlen($firstname) < $min){
            $errors[]= "Your First name cannot be less than {$min} character";
        }
        if(strlen($firstname) > $max){
            $errors[]= "Your First name cannot be more than {$max} character";
        }
        if(strlen($lastname) < $min){
            $errors[]= "Your Last name cannot be less than {$min} character";
        }
        if(strlen($lastname) > $max){
            $errors[]= "Your Last name cannot be more than {$max} character";
        }
        if(strlen($username) < $min){
            $errors[]= "Your User name cannot be less than {$min} character";
        }
        if(strlen($username) > $max){
            $errors[]= "Your User name cannot be more than {$max} character";
        }
        if(username_exit($username)){
            $errors[]= "Sorry this Username is already is taken";
        }
        if(email_exit($email)){
            $errors[]= "Sorry this email is already is register";
        }        
        if(strlen($password) > $max){
            $errors[]= "Your Password name cannot be more than {$max} character";
        }
        if($password !== $confirm_password){
            $errors [] = "Your Password faild do not match";
        }
         
        if(!empty($errors)){
            foreach($errors as $error){
                echo validation_error($error);
            }
        }
        else{
            if(register_user($firstname,$lastname,$username,$email,$password)){
                set_message("<p>Regisrter Succssfully! check your email for activation </p>");
                redirect("index.php");
            }else{
                set_message("<p class='bg-danger'>Sorry!Registration not completed. please register again </p>");
            }
        }
    }
}
/* ********************  register user Function ********************************** */

function register_user($firstname,$lastname,$username,$email,$password){
    // 
    $firstname    = escape($firstname);
    $lastname     = escape($lastname);
    $username     = escape($username);
    $email        = escape($email);
    $password     = escape($password);

    // encrypt the password using md5
    $password = md5($password);

    // generate and encrypt the confirm_code using md5 based on username 
    $confirm_code = md5((int)$username + microtime());
    
    // query to insert user to database 
    $sql = "insert into users(first_name,last_name,username,email,password,confirm_code,active)
            values('$firstname','$lastname','$username','$email','$password','$confirm_code','0')";
    
    $result = query($sql);
    confirm($result);

    // email parametters to send activation code to user  
    $subject = "Active Account";

    $message = "please click the link below to active your account 
                http://localhost/Register-System-/active.php/?email=$email&code=$confirm_code";
   
    $header  = "From: norply@yourwebsit.com";


    send_mail($email,$subject,$mesg,$header);

    return true;
}

/* ********************  active user Function ********************************** */

function activate_user(){

    if($_SERVER['REQUST_METHOD']='GET'){
        if(isset($_GET['email'])){

            $email = clean($_GET['email']);
            $code = clean($_GET['code']);

            $sql = "select id from users where email = '".escape($email)."' and confirm_code = '".escape($code)."' ";
            $result = query($sql);
            confirm($result);
            
            if(row_count($result) == 1){
                $sql2 = "update users set active = 1 , confirm_code = 0 where email = '".escape($email)."' and confirm_code = '".escape($code)."' "; 
                $result2 = query($sql2);
                confirm($result2);
                set_message("<p class='bg-success'>Your account has been activated please login </p>");
                redirect("login.php");
            }else{
                set_message("<p class='bg-danger'>Sorry! there's a problem Your account could not be activated </p>");
                redirect("login.php");
            }
        }
    }
}

/* ********************  Validation login Function ********************************** */

function validate_user_login(){

    $min = 3;
    $max = 20;
    $errors = [];


    if($_SERVER['REQUEST_METHOD'] == 'POST'){

        $email       = clean($_POST['email']);
        $password    = clean($_POST['password']);

        if(empty($email)){
            $errors[]= "Email cannot be empty";
        }
        if(empty($password)){
            $errors[]= "Password cannot be empty";
        }

        if(!empty($errors)){
            foreach($errors as $error){
                echo validation_error($error);
            }
        }
        else{
            if(login_user($email,$password)){
                set_message("<p>Login Succssfully! Welecom</p>");
                redirect("admin.php");
            }else{
                set_message("<p class='bg-danger'>Sorry! email or password are wrong  </p>");
            }
        }
    }
}

/* ********************  login Function ********************************** */

function login_user($email,$password){

    $pass = md5($password);
    $sql = "select password,id from users where email='".escape($email)."' and password = '".escape($pass)."' ";
    $result = query($sql);
    confirm($result);
    if(row_count($result) == 1){
        $_SESSION['email'] = $email; 
        return true;
    }
    
}
function logedin(){
    if(isset($_SESSION['email'])){
        return true;
    }
}