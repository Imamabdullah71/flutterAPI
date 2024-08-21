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

// Menangani input periode atau rentang tanggal
$periode = $_POST['periode'] ?? null;
$startDate = $_POST['start_date'] ?? null;
$endDate = $_POST['end_date'] ?? null;

$whereClause = "";
$infaqWhereClause = "";  // Clause tambahan untuk infaq

if ($periode) {
    switch ($periode) {
        case 'Laporan hari ini':
            $whereClause = "WHERE DATE(created_at) = CURDATE()";
            $infaqWhereClause = "WHERE DATE(tanggal_infaq) = CURDATE()";
            break;
        case 'Laporan minggu ini':
            $whereClause = "WHERE YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)";
            $infaqWhereClause = "WHERE YEARWEEK(tanggal_infaq, 1) = YEARWEEK(CURDATE(), 1)";
            break;
        case 'Laporan bulan ini':
            $whereClause = "WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())";
            $infaqWhereClause = "WHERE MONTH(tanggal_infaq) = MONTH(CURDATE()) AND YEAR(tanggal_infaq) = YEAR(CURDATE())";
            break;
        case 'Laporan Keseluruhan':
            $whereClause = "";
            $infaqWhereClause = "";  // Tidak ada filter untuk keseluruhan
            break;
        default:
            echo json_encode([]);
            exit();
    }
} elseif ($startDate && $endDate) {
    // Jika date range diberikan, buat where clause berdasarkan rentang tanggal
    $whereClause = "WHERE DATE(created_at) BETWEEN '$startDate' AND '$endDate'";
    $infaqWhereClause = "WHERE DATE(tanggal_infaq) BETWEEN '$startDate' AND '$endDate'";
} else {
    echo json_encode([]);
    exit();
}

// Query untuk transaksi
$sql = "SELECT * FROM transaksi $whereClause";
$result = $conn->query($sql);

$transaksi = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $transaksi[] = $row;
    }
}

// Query untuk menghitung total infaq
$sqlInfaq = "SELECT SUM(jumlah_infaq) as total_infaq FROM infaq $infaqWhereClause";
$resultInfaq = $conn->query($sqlInfaq);

$totalInfaq = 0;
if ($resultInfaq->num_rows > 0) {
    $rowInfaq = $resultInfaq->fetch_assoc();
    $totalInfaq = $rowInfaq['total_infaq'] ?? 0;
}

// Menggabungkan hasil transaksi dan total infaq
$response = [
    'transaksi' => $transaksi,
    'total_infaq' => $totalInfaq
];

echo json_encode($response);
$conn->close();
