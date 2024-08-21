<?php
header('Content-Type: application/json');

// Array untuk menyimpan pesan log
$log = [];

// Detail koneksi database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kasirsql";

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Mengecek koneksi
if ($conn->connect_error) {
    $log[] = "Connection failed: " . $conn->connect_error;
    echo json_encode(["error" => "Connection failed", "log" => $log]);
    exit;
}

// Logging koneksi berhasil
$log[] = "Connected successfully to the database";

// Query untuk mengambil semua data dari tabel users
$sql = "SELECT id, nama, email, password, nama_toko, alamat, no_telepon, created_at, updated_at FROM user";
$result = $conn->query($sql);

if ($result === false) {
    $log[] = "Query error: " . $conn->error;
    echo json_encode(["error" => "Query error", "log" => $log]);
    exit;
}

// Logging jumlah hasil query
$log[] = "Number of rows returned: " . $result->num_rows;

if ($result->num_rows > 0) {
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = [
            "id" => $row["id"],
            "nama" => $row["nama"],
            "email" => $row["email"],
            "password" => $row["password"],
            "nama_toko" => $row["nama_toko"],
            "alamat" => $row["alamat"],
            "no_telepon" => $row["no_telepon"],
            "created_at" => $row["created_at"],
            "updated_at" => $row["updated_at"]
        ];
    }
    $log[] = "Data successfully returned";
    echo json_encode(["users" => $users, "log" => $log]);
} else {
    $log[] = "No data found in the users table";
    echo json_encode(["users" => [], "log" => $log]);
}

$conn->close();
$log[] = "Connection closed";
