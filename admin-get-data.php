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
    $serialNum = $_POST["sni"];

    $sql = "SELECT * FROM peripherals WHERE Serial_num LIKE ? OR Assignee_name LIKE ? OR Host LIKE? OR Brand LIKE? OR Groups LIKE? OR Department LIKE? OR Division LIKE? OR Position LIKE? OR Date LIKE? OR Status LIKE? OR Item LIKE? OR Mouse_SN LIKE? OR Adapter_SN LIKE?";
    $stmt = $conn->prepare($sql);
    $searchTerm = "%" . $serialNum . "%";
    $stmt->bind_param("sssssssssssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'id' => $row['Idno'],
            'status' => $row['Status'],
            'item' => $row['Item'],
            'serial_number' => $row['Serial_num'],
            'host' => $row['Host'],
            'model_number' => $row['Model_num'],
            'brand' => $row['Brand'],
            'assignee_name' => $row['Assignee_name'],
            'department' => $row['Department'],
            'position' => $row['Position'],
            'date' => $row['Date'],
            'group' => $row['Groups'],
            'division' => $row['Division'],
            'mouse' => $row['Mouse_SN'],
            'adapter' => $row['Adapter_SN'],
            'file_path' => !empty($row['File']) && file_exists($row['File']) ? $row['File'] : null
        ];
    }

    echo json_encode($data);
} else {
    echo json_encode(["error" => "Invalid request method"]);
}

$conn->close();
?>