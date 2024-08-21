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

// Mengambil data infaq dari request
$user_id = $_POST['user_id'];
$jumlah_infaq = $_POST['jumlah_infaq'];

if (empty($user_id) || empty($jumlah_infaq)) {
    die(json_encode(["error" => "User ID dan Jumlah Infaq tidak boleh kosong"]));
}

// Menyisipkan data infaq ke database
$sql = "INSERT INTO infaq (user_id, jumlah_infaq) VALUES ('$user_id', '$jumlah_infaq')";

if ($conn->query($sql) === TRUE) {
    echo json_encode(["success" => "Infaq berhasil ditambahkan"]);
} else {
    echo json_encode(["error" => "Gagal menambahkan infaq: " . $conn->error]);
}

$conn->close();