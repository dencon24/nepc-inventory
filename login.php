<?php  
session_start();


$Servername = "localhost";
$Username = "root";
$Password = "";
$dbname = "inventory_sys";

$conn = new mysqli($Servername, $Username, $Password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = mysqli_real_escape_string($conn, $_POST['username']);
$password = mysqli_real_escape_string($conn, $_POST['password']);

$sql = "SELECT Role FROM details WHERE username = ? AND password = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $username, $password);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $_SESSION['username'] = $username;
    $_SESSION['role'] = $row['Role'];
    if ($row['Role'] == "admin") {
         echo "admin";
         header("Location: dashboard.php");

    } else if ($row['Role'] == "user") {
        echo "user";
      
    }
    header("Location: dashboard.php");

} else {
    header("Location: login.html?error=1");
    exit();
}

$stmt->close();
$conn->close();
?>
