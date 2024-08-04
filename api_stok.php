<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kasirsql";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set content type to JSON
header('Content-Type: application/json');

// Handle different API requests
$action = isset($_POST['action']) ? $_POST['action'] : '';

switch ($action) {
    case 'update_stok_barang':
        $id = $_POST['id'];
        $new_stok = $_POST['new_stok'];

        // Update stok_barang
        $sql = "UPDATE barang SET stok_barang = '$new_stok', updated_at = NOW() WHERE id = '$id'";

        if ($conn->query($sql) === TRUE) {
            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "error", "message" => $conn->error]);
        }
        break;

    default:
        echo json_encode(["status" => "error", "message" => "Invalid action"]);
}

$conn->close();