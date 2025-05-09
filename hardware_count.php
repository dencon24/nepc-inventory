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

if (isset($_POST['category'])) {
    $category = $_POST['category'];

    $result = [
        "issued" => 0,
        "borrowed" => 0,
        "on_site" => 0,
        "returned" => 0
    ];

    $query = "SELECT COUNT(*) FROM peripherals WHERE Status = ?";
    $statuses = ["Issued", "Borrowed", "On-Site", "Returned"];
    
    foreach ($statuses as $status) {
        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param("s", $status);
            $stmt->execute();
            $stmt->bind_result($count);
            $stmt->fetch();
            $stmt->close();

  
            switch ($status) {
                case "Issued": $result["issued"] = $count; break;
                case "Borrowed": $result["borrowed"] = $count; break;
                case "On-Site": $result["on_site"] = $count; break;
                case "Returned": $result["returned"] = $count; break;
            }
        }
    }

    $conn->close();
    echo json_encode($result);
}
?>
