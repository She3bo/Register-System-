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
        $errors[]= $_SESSION['message'];

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
        if(strlen($password) < $min){
            $errors[]= "Your Password name cannot be less than {$min} character";
        }
        if(strlen($password) > $max){
            $errors[]= "Your Password name cannot be more than {$max} character";
        }
        if($password !== $confirm_password){
            $errors [] = "Your Password faild do not match";
        }
         
    }
    if(!empty($errors)){
        foreach($errors as $error){
            echo validation_error($error);
        }
    }
}
