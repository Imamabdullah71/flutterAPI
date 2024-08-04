<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kasirsql";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(array("status" => "error", "message" => "Connection failed: " . $conn->connect_error)));
}

$action = $_GET['action'];

if ($action == 'get_hutang') {
    $status = $_GET['status'];
    $sql = "SELECT * FROM hutang WHERE status='$status'";
    $result = $conn->query($sql);

    $hutang = array();
    while($row = $result->fetch_assoc()) {
        $hutang[] = $row;
    }
    echo json_encode(array("status" => "success", "data" => $hutang));
} elseif ($action == 'update_hutang') {
    $id = $_POST['id'];
    $inputBayar = $_POST['input_bayar'];
    $sisaHutangSebelum = $_POST['sisa_hutang_sebelum'];
    $transaksiId = $_POST['transaksi_id'];

    $sisaHutangSekarang = $sisaHutangSebelum + $inputBayar;

    $conn->begin_transaction();

    try {
        if ($inputBayar >= abs($sisaHutangSebelum)) {
            $sisaHutangSekarang = 0;
            $sql = "UPDATE hutang SET sisa_hutang=0, status='lunas', tanggal_selesai=NOW() WHERE id='$id'";
            $conn->query($sql);

            $sql = "UPDATE transaksi SET kembali=0 WHERE id='$transaksiId'";
            $conn->query($sql);
        } else {
            $sql = "UPDATE hutang SET sisa_hutang='$sisaHutangSekarang' WHERE id='$id'";
            $conn->query($sql);
        }

        $sql = "INSERT INTO riwayat_pembayaran (hutang_id, sisa_hutang_sebelum, sisa_hutang_sekarang, jumlah_bayar, tanggal_bayar)
                VALUES ('$id', '$sisaHutangSebelum', '$sisaHutangSekarang', '$inputBayar', NOW())";
        $conn->query($sql);

        $conn->commit();
        echo json_encode(array("status" => "success"));
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(array("status" => "error", "message" => $conn->error));
    }
} elseif ($action == 'update_status_hutang') {
    $id = $_POST['id'];
    $status = $_POST['status'];
    $sql = "UPDATE hutang SET status='$status'";
    if ($status == 'lunas') {
        $sql .= ", tanggal_selesai=NOW()";
    }
    $sql .= " WHERE id='$id'";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(array("status" => "success"));
    } else {
        echo json_encode(array("status" => "error", "message" => $conn->error));
    }
} elseif ($action == 'get_riwayat_pembayaran') {
    $hutangId = $_GET['hutang_id'];
    $sql = "SELECT * FROM riwayat_pembayaran WHERE hutang_id='$hutangId'";
    $result = $conn->query($sql);

    $riwayat = array();
    while($row = $result->fetch_assoc()) {
        $riwayat[] = $row;
    }
    echo json_encode(array("status" => "success", "data" => $riwayat));
} else {
    echo json_encode(array("status" => "error", "message" => "Invalid action"));
}

$conn->close();
