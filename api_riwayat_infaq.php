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

$user_id = $_POST['user_id'];

if (empty($user_id)) {
    die(json_encode(["error" => "User ID tidak boleh kosong"]));
}

$sql = "SELECT * FROM infaq WHERE user_id = '$user_id' ORDER BY tanggal_infaq DESC";
$result = $conn->query($sql);

$infaq = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $infaq[] = $row;
    }
}

echo json_encode($infaq);
$conn->close();