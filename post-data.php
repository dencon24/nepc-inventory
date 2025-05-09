<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "inventory_sys";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$status = mysqli_real_escape_string($conn, $_POST['status']);
$item = mysqli_real_escape_string($conn, $_POST['item']);
$sn = mysqli_real_escape_string($conn, $_POST['sn']);
$host = mysqli_real_escape_string($conn, $_POST['host']);
$mn = mysqli_real_escape_string($conn, $_POST['mn']);
$brand = mysqli_real_escape_string($conn, $_POST['brand']);
$an = mysqli_real_escape_string($conn, $_POST['an']);
$department = mysqli_real_escape_string($conn, $_POST['department']);
$position = mysqli_real_escape_string($conn, $_POST['position']);
$date = mysqli_real_escape_string($conn, $_POST['date']);
$mouse = mysqli_real_escape_string($conn, $_POST['mouse']);
$adapter = mysqli_real_escape_string($conn, $_POST['adapter']);
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

$sql = "INSERT INTO peripherals (File, Status, Serial_num, Host, Brand, Model_num, Assignee_name, Department,Position, Date, Item, Mouse_SN, Adapter_SN, Division, Groups) 
        VALUES ('$filePath', '$status', '$sn', '$host', '$brand', '$mn', '$an', '$department','$position', '$date', '$item', '$mouse', '$adapter', '$division','$group')";

if ($conn->query($sql) === TRUE) {
    header("Location: hardware.php?uploadsuccess");
    exit();
} else {
    echo "Error: " . $conn->error;
}
$conn->close();
?>