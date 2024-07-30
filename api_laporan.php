<?php
header('Content-Type: application/json');

// Detail koneksi database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kasirsql";

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Mengecek koneksi
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

$periode = $_POST['periode'];
$whereClause = "";

switch ($periode) {
    case 'Laporan hari ini':
        $whereClause = "WHERE DATE(created_at) = CURDATE()";
        break;
    case 'Laporan minggu ini':
        $whereClause = "WHERE YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)";
        break;
    case 'Laporan bulan ini':
        $whereClause = "WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())";
        break;
    case 'Laporan Keseluruhan':
        $whereClause = "";
        break;
    default:
        echo json_encode([]);
        exit();
}

$sql = "SELECT * FROM transaksi $whereClause";
$result = $conn->query($sql);

$transaksi = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $transaksi[] = $row;
    }
}

echo json_encode($transaksi);
$conn->close();