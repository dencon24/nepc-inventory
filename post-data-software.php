<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "inventory_sys";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sn = mysqli_real_escape_string($conn, $_POST['sn']);
$an = mysqli_real_escape_string($conn, $_POST['an']);
$expiry = mysqli_real_escape_string($conn, $_POST['expiry']);
$license = mysqli_real_escape_string($conn, $_POST['license']);
$supplier = mysqli_real_escape_string($conn, $_POST['supplier']);
$receiver = mysqli_real_escape_string($conn, $_POST['receiver']);
$software = mysqli_real_escape_string($conn, $_POST['software']);
$department = mysqli_real_escape_string($conn, $_POST['department']);
$position = mysqli_real_escape_string($conn, $_POST['position']);
$date = mysqli_real_escape_string($conn, $_POST['date']);
$group = mysqli_real_escape_string($conn, $_POST['group']);
$division = mysqli_real_escape_string($conn, $_POST['division']);

$filePath = ""; 

if (isset($_FILES['files']) && $_FILES['files']['error'] === 0) {
    $targetDir = "uploads/";  
    $fileName = basename($_FILES["files"]["name"]);
    $fileTmpName = $_FILES["files"]["tmp_name"];
    $fileSize = $_FILES["files"]["size"];
    $fileError = $_FILES["files"]["error"];

    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowed = array('jpg', 'jpeg', 'png', 'pdf');

    if (in_array($fileExt, $allowed)) {
        if ($fileSize < 2000000) {

            $newFileName = uniqid("file_", true) . "." . $fileExt;
            $filePath = $targetDir . $newFileName; 

            if (move_uploaded_file($fileTmpName, $filePath)) {
                echo "File uploaded successfully.";
            } else {
                echo "Error uploading file.";
                exit();
            }
        } else {
            echo "Your file is too big!";
            exit();
        }
    } else {
        echo "You cannot upload this type of file.";
        exit();
    }
}

$sql = "INSERT INTO software (File, Serial_num, expiry, license,receivers,software, suppliers, Assignee_name, department, date, position, division, groups) 
        VALUES ('$filePath', '$sn', '$expiry', '$license', '$receiver', '$software', '$supplier', '$an','$department', '$date', '$position','$division' ,'$group')";

if ($conn->query($sql) === TRUE) {
    header("Location: software.php?uploadsuccess");
    exit();
} else {
    echo "Error: " . $conn->error ;
}
$conn->close();
?>