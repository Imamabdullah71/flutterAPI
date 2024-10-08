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

// Handle different API requests
$action = isset($_GET['action']) ? $_GET['action'] : '';
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : '';

switch ($action) {
    case 'create_kategori':
        $nama_kategori = $_POST['nama_kategori'];
        $user_id = $_POST['user_id']; // Terima user_id
        $created_at = date('Y-m-d H:i:s');
        
        $sql = "INSERT INTO kategori (nama_kategori, user_id, created_at, updated_at) 
                VALUES ('$nama_kategori', '$user_id', '$created_at', '$created_at')";
        
        if ($conn->query($sql) === TRUE) {
            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "error", "message" => $conn->error]);
        }
        break;

    case 'read_kategori':
        $sql = "SELECT * FROM kategori WHERE user_id = '$user_id'";
        $result = $conn->query($sql);
        
        $data = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        echo json_encode($data);
        break;

    case 'delete_kategori':
        $id = $_POST['id'];
        $sql = "DELETE FROM kategori WHERE id = '$id'";
        
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
?>
