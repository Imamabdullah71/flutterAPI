<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kasirsql";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action == 'create_transaksi') {
    $data = json_decode(file_get_contents('php://input'), true);

    $total_barang = $data['total_barang'];
    $total_harga = $data['total_harga'];
    $total_harga_beli = $data['total_harga_beli'];
    $bayar = $data['bayar'];
    $kembali = $data['kembali'];
    $user_id = $data['user_id'];
    $created_at = $data['created_at'];
    $updated_at = $data['updated_at'];

    $query = "INSERT INTO transaksi (total_barang, total_harga, total_harga_beli, bayar, kembali, user_id, created_at, updated_at) VALUES ('$total_barang', '$total_harga', '$total_harga_beli', '$bayar', '$kembali', '$user_id', '$created_at', '$updated_at')";

    if ($conn->query($query) === TRUE) {
        $transaksi_id = $conn->insert_id;
        echo json_encode(['status' => 'success', 'transaksi_id' => $transaksi_id]);
    } else {
        echo json_encode(['status' => 'error', 'message' => $conn->error]);
    }
} elseif ($action == 'create_detail_transaksi') {
    $data = json_decode(file_get_contents('php://input'), true);

    $transaksi_id = $data['transaksi_id'];
    $nama_barang = $data['nama_barang'];
    $jumlah_barang = $data['jumlah_barang'];
    $harga_barang = $data['harga_barang'];
    $jumlah_harga = $data['jumlah_harga'];
    $created_at = $data['created_at'];
    $updated_at = $data['updated_at'];

    $query = "INSERT INTO detail_transaksi (transaksi_id, nama_barang, jumlah_barang, harga_barang, jumlah_harga, created_at, updated_at) VALUES ('$transaksi_id', '$nama_barang', '$jumlah_barang', '$harga_barang', '$jumlah_harga', '$created_at', '$updated_at')";

    if ($conn->query($query) === TRUE) {
        // Kurangi stok barang di tabel barang
        $update_stok_query = "UPDATE barang SET stok_barang = stok_barang - $jumlah_barang WHERE nama_barang = '$nama_barang'";
        if ($conn->query($update_stok_query) !== TRUE) {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update stock: ' . $conn->error]);
        } else {
            echo json_encode(['status' => 'success']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => $conn->error]);
    }
} elseif ($action == 'upload_struk') {
    if (isset($_FILES['struk']) && isset($_POST['transaksi_id'])) {
        $transaksi_id = $_POST['transaksi_id'];
        $target_dir = "C:/xampp/htdocs/flutterapi/struk/";
        $file_name = basename($_FILES["struk"]["name"]);
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES["struk"]["tmp_name"], $target_file)) {
            $query = "UPDATE transaksi SET struk='$file_name' WHERE id='$transaksi_id'";
            if ($conn->query($query) === TRUE) {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'message' => $conn->error]);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to upload file']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
}

$conn->close();
?>
