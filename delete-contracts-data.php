<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "inventory_sys";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]));
}

$data = json_decode(file_get_contents('php://input'), true);
$ids = $data['ids'];

if (empty($ids)) {
    die(json_encode(['success' => false, 'message' => 'No IDs provided for deletion']));
}


$placeholders = implode(',', array_fill(0, count($ids), '?'));
$sql = "DELETE FROM contracts WHERE idno IN ($placeholders)";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die(json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]));
}


$types = str_repeat('i', count($ids));
$stmt->bind_param($types, ...$ids);


if ($stmt->execute()) {
    $affectedRows = $stmt->affected_rows;
    echo json_encode([
        'success' => true, 
        'message' => "Successfully deleted $affectedRows record(s)"
    ]);
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Execute failed: ' . $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>