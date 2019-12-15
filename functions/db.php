<?php

$conn = mysqli_connect("localhost","root","","registration_system");

function query($query){
    global $conn;

    return mysqli_query($conn,$query);
}

function confirm($result){
    global $conn;

    if(!$result){
        die("Query Field" . mysqli_query($conn));
    }
}
function fetch_array($result){
    return mysqli_fetch_array($result);
}

function escape($string){
    global $conn;

    return mysqli_real_escape_string($conn,$string);
}

function row_count($result){
        
    return mysqli_num_rows($result);
}

?>