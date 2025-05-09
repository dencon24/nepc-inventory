<?php
header('Content-Type: application/json');
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "inventory_sys";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $serialNum = $_POST["search"] ?? '';

    $sql = "SELECT * FROM contracts WHERE ProjectName LIKE ? OR Description LIKE? OR Groups LIKE ? OR Department LIKE? OR Division LIKE? OR Vendor LIKE? OR ContractDate LIKE? OR EndDate LIKE? ";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        die(json_encode(["error" => "Prepare failed: " . $conn->error]));
    }
    
    $searchTerm = "%" . $serialNum . "%";
    $stmt->bind_param("ssssssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
    
    if (!$stmt->execute()) {
        die(json_encode(["error" => "Execute failed: " . $stmt->error]));
    }
    
    $result = $stmt->get_result();
    $data = [];
    
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'id' => $row['idno'],
            'pn' => $row['ProjectName'],
            'desc' => $row['Description'],
            'vendor' => $row['Vendor'],
            'group' => $row['Groups'],
            'department' => $row['Department'],
            'division' => $row['Division'],
            'cdate' => $row['ContractDate'],
            'edate' => $row['EndDate'],
            'file_path' => !empty($row['File']) ? $row['File'] : null
        ];
    }
    
    echo json_encode($data);
} else {
    echo json_encode(["error" => "Invalid request method"]);
}

$conn->close();
?>