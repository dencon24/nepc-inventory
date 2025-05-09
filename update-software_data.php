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

$response = [];

if (is_array($data)) {
    foreach ($data as $row) {
        $id = intval($row['id']);
        $serial = $conn->real_escape_string($row['serial_number']);
        $assignee = $conn->real_escape_string($row['an']);
        $expiry = $conn->real_escape_string($row['expiry']);
        $license = $conn->real_escape_string($row['license']);
        $supplier = $conn->real_escape_string($row['suppliers']);
        $receiver = $conn->real_escape_string($row['receivers']);
        $software = $conn->real_escape_string($row['software']);
        $group = $conn->real_escape_string($row['group']);
        $department = $conn->real_escape_string($row['department']);
        $division = $conn->real_escape_string($row['division']);
        $position = $conn->real_escape_string($row['position']);
        $date = $conn->real_escape_string($row['date']);

        // Update query
        $sql = "UPDATE software 
                SET  Serial_num='$serial', software='$software', 
                    expiry='$expiry', license='$license', Assignee_name='$assignee', 
                    department='$department', position='$position', date='$date', 
                    suppliers='$supplier', receivers='$receiver', groups = '$group', division = '$division'
                WHERE idno='$id'";

        if ($conn->query($sql)) {
            $response[] = ["id" => $id, "status" => "success"];
        } else {
            $response[] = ["id" => $id, "status" => "error", "message" => $conn->error];
        }
    }
} else {
    $response = ["error" => "Invalid data format"];
}

echo json_encode($response);
$conn->close();
?>
