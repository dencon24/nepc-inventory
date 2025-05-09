<?php
$conn = new mysqli ("localhost","root", "","inventory_sys");
if($conn->connect_error){
    die("".$conn->connect_error);
}

$username = mysqli_real_escape_string($conn, $_POST['un']);
$password = mysqli_real_escape_string($conn, $_POST['pw']);
$cpassword = mysqli_real_escape_string($conn,$_POST['cpass']);
$role = mysqli_real_escape_string($conn,$_POST['role']);;

if($cpassword == $password){
    $sql = "INSERT INTO details (username, password, Role) VALUES ('$username','$password','$role')";
    if($conn->query($sql)===TRUE){
        header("Location: login.html?uploadsuccess");
        exit();
    }
}
else{
    header("Location: signup_1.html?error=1");
    exit();
}


?>