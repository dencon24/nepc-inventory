<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "inventory_sys";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$pn = mysqli_real_escape_string($conn, $_POST['pn']);
$desc = mysqli_real_escape_string($conn, $_POST['desc']);
$vendor = mysqli_real_escape_string($conn, $_POST['vendor']);
$edate = mysqli_real_escape_string($conn, $_POST['enddate']);
$department = mysqli_real_escape_string($conn, $_POST['department']);
$cdate = mysqli_real_escape_string($conn, $_POST['date']);
$division = mysqli_real_escape_string($conn, $_POST['division']);
$group = mysqli_real_escape_string($conn, $_POST['group']);

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

$sql = "INSERT INTO contracts (File, ProjectName, Description, Groups, Department, Vendor, ContractDate, EndDate, Division) 
        VALUES ('$filePath', '$pn', '$desc', '$group','$department','$vendor', '$cdate', '$edate','$division')";

if ($conn->query($sql) === TRUE) {
    header("Location: contracts.php?uploadsuccess");
    exit();
} else {
    echo "Error: " . $conn->error;
}
$conn->close();
?>