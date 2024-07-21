<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kasirsql";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_GET['action'])) {
    $action = $_GET['action'];

    if ($action == 'create_transaksi') {
        $data = json_decode(file_get_contents('php://input'), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            echo json_encode(array('status' => 'error', 'message' => 'JSON decoding error: ' . json_last_error_msg()));
            $conn->close();
            exit();
        }

        $detail_barang = json_encode($data['detailBarang']);
        $total_barang = $data['totalBarang'];
        $total_harga = $data['totalHarga'];
        $total_harga_beli = $data['totalHargaBeli'];
        $bayar = $data['bayar'];
        $kembali = $data['kembali'];
        $user_id = $data['userId'];
        $created_at = $data['createdAt'];
        $updated_at = $data['updatedAt'];

        $sql = "INSERT INTO transaksi (detail_barang, total_barang, total_harga, total_harga_beli, bayar, kembali, user_id, created_at, updated_at)
                VALUES ('$detail_barang', $total_barang, $total_harga, $total_harga_beli, $bayar, $kembali, $user_id, '$created_at', '$updated_at')";

        if ($conn->query($sql) === TRUE) {
            $transaksi_id = $conn->insert_id;
            $response = array('status' => 'success', 'message' => 'Transaksi berhasil disimpan', 'transaksi_id' => $transaksi_id);
        } else {
            $response = array('status' => 'error', 'message' => 'SQL Error: ' . $conn->error);
        }

        echo json_encode($response);

    } elseif ($action == 'get_transaksi') {
        $result = $conn->query("SELECT * FROM transaksi");

        $transaksi = array();
        while ($row = $result->fetch_assoc()) {
            $transaksi[] = $row;
        }

        echo json_encode($transaksi);
    } elseif ($action == 'upload_struk') {
        $transaksi_id = $_POST['transaksi_id'];

        if (isset($_FILES['struk'])) {
            $target_dir = "struk/";
            $target_file = $target_dir . basename($_FILES["struk"]["name"]);
            if (move_uploaded_file($_FILES["struk"]["tmp_name"], $target_file)) {
                $struk_name = basename($_FILES["struk"]["name"]);
                $sql = "UPDATE transaksi SET struk='$struk_name' WHERE id=$transaksi_id";
                if ($conn->query($sql) === TRUE) {
                    echo json_encode(array('status' => 'success', 'message' => 'Struk uploaded successfully'));
                } else {
                    echo json_encode(array('status' => 'error', 'message' => 'Failed to update struk: ' . $conn->error));
                }
            } else {
                echo json_encode(array('status' => 'error', 'message' => 'Failed to move uploaded file'));
            }
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'No file uploaded'));
        }
    }
}

$conn->close();
