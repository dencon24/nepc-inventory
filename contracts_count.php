<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "inventory_sys";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

header('Content-Type: application/json');

    $result = [
        "issued" => 0,
    ];
    
$query = "SELECT COUNT(*) FROM contracts";
if ($stmt = $conn->prepare($query)) {
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    $result["issued"] = $count;
}
    

    $conn->close();
    echo json_encode($result);

?>
