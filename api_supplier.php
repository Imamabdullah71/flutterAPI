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

$action = isset($_GET['action']) ? $_GET['action'] : '';
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : '';

switch ($action) {
    case 'create_supplier':
        $nama_supplier = isset($_POST['nama_supplier']) ? $_POST['nama_supplier'] : '';
        $nama_toko_supplier = isset($_POST['nama_toko_supplier']) ? $_POST['nama_toko_supplier'] : '';
        $no_telepon = isset($_POST['no_telepon']) ? $_POST['no_telepon'] : '';
        $alamat = isset($_POST['alamat']) ? $_POST['alamat'] : '';
        $gambar = isset($_POST['gambar']) ? $_POST['gambar'] : null;
        $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : ''; // Terima user_id
        $created_at = date('Y-m-d H:i:s');
        
        // Debugging log
        error_log("Create supplier: $nama_supplier, $nama_toko_supplier, $no_telepon, $alamat, $gambar, $user_id, $created_at");
        
        // Perbaiki query SQL
        $sql = "INSERT INTO supplier (nama_supplier, nama_toko_supplier, no_telepon, alamat, gambar, user_id, created_at, updated_at) 
                VALUES ('$nama_supplier', '$nama_toko_supplier', '$no_telepon', '$alamat', '$gambar', '$user_id', '$created_at', '$created_at')";
        
        if ($conn->query($sql) === TRUE) {
            echo json_encode(["status" => "success"]);
        } else {
            // Tambahkan log error
            error_log("Error: " . $conn->error);
            echo json_encode(["status" => "error", "message" => $conn->error]);
        }
        break;

    case 'read_supplier':
        $sql = "SELECT * FROM supplier WHERE user_id = '$user_id'";
        $result = $conn->query($sql);
        
        $data = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        echo json_encode($data);
        break;

    case 'update_supplier':
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        $nama_supplier = isset($_POST['nama_supplier']) ? $_POST['nama_supplier'] : '';
        $nama_toko_supplier = isset($_POST['nama_toko_supplier']) ? $_POST['nama_toko_supplier'] : '';
        $no_telepon = isset($_POST['no_telepon']) ? $_POST['no_telepon'] : '';
        $alamat = isset($_POST['alamat']) ? $_POST['alamat'] : '';

        // Perbaiki query SQL
        $sql = "UPDATE supplier SET 
                    nama_supplier = '$nama_supplier', 
                    nama_toko_supplier = '$nama_toko_supplier', 
                    no_telepon = '$no_telepon', 
                    alamat = '$alamat', 
                    updated_at = NOW() 
                WHERE id = '$id'";

        if ($conn->query($sql) === TRUE) {
            echo json_encode(["status" => "success"]);
        } else {
            // Tambahkan log error
            error_log("Error: " . $conn->error);
            echo json_encode(["status" => "error", "message" => $conn->error]);
        }
        break;

    case 'delete_supplier':
        $id = $_POST['id'];
        $sql = "DELETE FROM supplier WHERE id = '$id'";
        
        if ($conn->query($sql) === TRUE) {
            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "error", "message" => $conn->error]);
        }
        break;

    case 'upload_image':
        if ($_FILES['image']['name']) {
            $filename = $_FILES['image']['name'];
            $location = "uploads/" . $filename;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $location)) {
                echo json_encode(["status" => "success", "path" => $filename]);
            } else {
                echo json_encode(["status" => "error", "message" => "Failed to upload image"]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "No image uploaded"]);
        }
        break;

    default:
        echo json_encode(["status" => "error", "message" => "Invalid action"]);
}

$conn->close();
