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
$action = isset($_GET['action']) ? $_GET['action'] : '';
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : '';

switch ($action) {
    case 'create_barang':
        $nama_barang = $_POST['nama_barang'];
        $barcode_barang = $_POST['barcode_barang'];
        $stok_barang = $_POST['stok_barang'];
        $kategori_id = $_POST['kategori_id'];
        $gambar = isset($_POST['gambar']) ? $_POST['gambar'] : null;
        $created_at = date('Y-m-d H:i:s');

        // Check if barcode exists
        $check_sql = "SELECT * FROM barang WHERE barcode_barang = '$barcode_barang'";
        $check_result = $conn->query($check_sql);

        if ($check_result->num_rows > 0) {
            echo json_encode(["status" => "error", "message" => "Barcode sudah tersedia"]);
        } else {
            $sql = "INSERT INTO barang (kategori_id, gambar, nama_barang, barcode_barang, stok_barang, created_at, updated_at)  
                    VALUES ('$kategori_id', '$gambar', '$nama_barang', '$barcode_barang', '$stok_barang', '$created_at', '$created_at')";
            
            if ($conn->query($sql) === TRUE) {
                $barang_id = $conn->insert_id;
                echo json_encode(["status" => "success", "id" => $barang_id]);
            } else {
                echo json_encode(["status" => "error", "message" => $conn->error]);
            }
        }
        break;

    case 'read_barang':
        $sql = "SELECT b.*, k.nama_kategori, h.harga_jual, h.harga_beli 
                FROM barang b 
                LEFT JOIN kategori k ON b.kategori_id = k.id
                LEFT JOIN harga h ON b.id = h.barang_id
                WHERE k.user_id = '$user_id'
                ORDER BY b.created_at DESC";
        $result = $conn->query($sql);
        
        $data = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        echo json_encode($data);
        break;

    case 'create_kategori':
        $nama_kategori = $_POST['nama_kategori'];
        $sql = "INSERT INTO kategori (nama_kategori) VALUES ('$nama_kategori')";
        
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

    case 'create_harga':
        $harga_jual = $_POST['harga_jual'];
        $harga_beli = $_POST['harga_beli'];
        $barang_id = $_POST['barang_id'];
        
        $sql = "INSERT INTO harga (barang_id, harga_beli, harga_jual, created_at, updated_at) 
                VALUES ('$barang_id', '$harga_beli', '$harga_jual', NOW(), NOW())";
        
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

    case 'update_barang':
        $id = $_POST['id'];
        $nama_barang = $_POST['nama_barang'];
        $barcode_barang = $_POST['barcode_barang'];
        $stok_barang = $_POST['stok_barang'];
        $kategori_id = $_POST['kategori_id'];
        $gambar = isset($_POST['gambar']) ? $_POST['gambar'] : null;
        $harga_jual = $_POST['harga_jual'];
        $harga_beli = $_POST['harga_beli'];
        $updated_at = date('Y-m-d H:i:s');

        $sql = "UPDATE barang 
                SET nama_barang = '$nama_barang', barcode_barang = '$barcode_barang', stok_barang = '$stok_barang', kategori_id = '$kategori_id', gambar = '$gambar', updated_at = '$updated_at' 
                WHERE id = '$id'";

        $sql_harga = "UPDATE harga 
                        SET harga_jual = '$harga_jual', harga_beli = '$harga_beli', updated_at = '$updated_at'
                        WHERE barang_id = '$id'";
        
        if ($conn->query($sql) === TRUE && $conn->query($sql_harga) === TRUE) {
            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "error", "message" => $conn->error]);
        }
        break;

        case 'delete_barang':
            $id = $_POST['id'];
    
            // Get the image filename
            $sql = "SELECT gambar FROM barang WHERE id = '$id'";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $image_path = 'uploads/' . $row['gambar'];
                if (file_exists($image_path)) {
                    unlink($image_path); // Delete the image file
                }
            }
    
            // Delete from harga table
            $sql_harga = "DELETE FROM harga WHERE barang_id = '$id'";
            $conn->query($sql_harga);
    
            // Delete from barang table
            $sql_barang = "DELETE FROM barang WHERE id = '$id'";
            if ($conn->query($sql_barang) === TRUE) {
                echo json_encode(["status" => "success"]);
            } else {
                echo json_encode(["status" => "error", "message" => $conn->error]);
            }
            break;

    default:
        echo json_encode(["status" => "error", "message" => "Invalid action"]);
}

$conn->close();
