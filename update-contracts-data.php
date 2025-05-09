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
        $pn = $conn->real_escape_string($row['pn']);
        $desc = $conn->real_escape_string($row['desc']);
        $vendor = $conn->real_escape_string($row['vendor']);
        $group= $conn->real_escape_string($row['group']);
        $department = $conn->real_escape_string($row['department']);
        $division = $conn->real_escape_string($row['division']);
        $cdate = $conn->real_escape_string($row['cdate']);
        $edate = $conn->real_escape_string($row['edate']);


        $sql = "UPDATE contracts 
                SET ProjectName='$pn', Description='$desc', Groups='$group', Department='$department', 
                    Division='$division', ContractDate='$cdate', EndDate ='$edate', Vendor ='$vendor'

                WHERE idno='$id'";

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