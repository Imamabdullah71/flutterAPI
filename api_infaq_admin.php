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

// Query untuk mendapatkan semua data riwayat infaq
$sql = "SELECT id, user_id, jumlah_infaq, tanggal_infaq FROM infaq";
$result = $conn->query($sql);

// Mengecek apakah ada data yang ditemukan
if ($result->num_rows > 0) {
    $infaqData = [];
    while ($row = $result->fetch_assoc()) {
        $infaqData[] = [
            "id" => $row["id"],
            "user_id" => $row["user_id"],
            "jumlah_infaq" => $row["jumlah_infaq"],
            "tanggal_infaq" => $row["tanggal_infaq"]
        ];
    }
    echo json_encode($infaqData);
} else {
    echo json_encode([]);
}

// Menutup koneksi
$conn->close();