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
    $serialNum = $_POST["sni"] ?? '';

    $sql = "SELECT * FROM software WHERE Serial_num LIKE ? OR Assignee_name LIKE ? OR license LIKE? OR expiry LIKE? OR suppliers LIKE? OR receivers LIKE? OR software LIKE? OR groups LIKE? OR department LIKE? OR division LIKE? OR position LIKE?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        die(json_encode(["error" => "Prepare failed: " . $conn->error]));
    }
    
    $searchTerm = "%" . $serialNum . "%";
    $stmt->bind_param("sssssssssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
    
    if (!$stmt->execute()) {
        die(json_encode(["error" => "Execute failed: " . $stmt->error]));
    }
    
    $result = $stmt->get_result();
    $data = [];
    
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'id' => $row['idno'],
            'an' => $row['Assignee_name'],
            'serial_number' => $row['Serial_num'],
            'expiry' => $row['expiry'],
            'license' => $row['license'],
            'suppliers' => $row['suppliers'],
            'receivers' => $row['receivers'],
            'software' => $row['software'],
            'group' => $row['groups'],
            'department' => $row['department'],
            'division' => $row['division'],
            'position' => $row['position'],
            'date' => $row['date'],
            'file_path' => !empty($row['File']) ? $row['File'] : null
        ];
    }
    
    echo json_encode($data);
} else {
    echo json_encode(["error" => "Invalid request method"]);
}

$conn->close();
?>