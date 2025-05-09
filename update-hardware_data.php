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

$json = file_get_contents('php://input');
$data = json_decode($json, true);

$response = ["success" => false, "message" => "No data processed"];

if (is_array($data)) {
    $successCount = 0;
    $errorCount = 0;
    
    foreach ($data as $row) {
        if (empty($row['id'])) {
            $response[] = ["status" => "error", "message" => "Missing ID"];
            $errorCount++;
            continue;
        }

        $id = intval($row['id']);
        $status = $conn->real_escape_string($row['status'] ?? '');
        $item = $conn->real_escape_string($row['item'] ?? '');
        $serial = $conn->real_escape_string($row['serial_number'] ?? '');
        $host = $conn->real_escape_string($row['host'] ?? '');
        $model = $conn->real_escape_string($row['model_number'] ?? '');
        $brand = $conn->real_escape_string($row['brand'] ?? '');
        $assignee = $conn->real_escape_string($row['assignee_name'] ?? '');
        $group = $conn->real_escape_string($row['group'] ?? '');
        $department = $conn->real_escape_string($row['department'] ?? '');
        $division = $conn->real_escape_string($row['division'] ?? '');
        $position = $conn->real_escape_string($row['position'] ?? '');
        $date = $conn->real_escape_string($row['date'] ?? '');
        $mouse = $conn->real_escape_string($row['mouse'] ?? '');
        $adapter = $conn->real_escape_string($row['adapter'] ?? '');

        $sql = "UPDATE peripherals 
                SET Status='$status', Item='$item', Serial_num='$serial', Host='$host', 
                    Model_num='$model', Brand='$brand', Assignee_name='$assignee', 
                    Department='$department', Position='$position', Date='$date', 
                    Mouse_SN='$mouse', Adapter_SN='$adapter', Groups='$group', Division='$division'
                WHERE Idno='$id'";

        if ($conn->query($sql)) {
            $successCount++;
        } else {
            $errorCount++;
            $response['errors'][] = ["id" => $id, "message" => $conn->error];
        }
    }
    
    $response['success'] = $errorCount === 0;
    $response['message'] = "Records Updated" . ($errorCount > 0 ? ", failed $errorCount records" : "");
} else {
    $response = ["success" => false, "message" => "Invalid data format"];
}

echo json_encode($response);
$conn->close();
?>