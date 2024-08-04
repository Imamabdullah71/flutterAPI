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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    if ($action == 'register') {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $nama_toko = $_POST['nama_toko'];
        $alamat = $_POST['alamat'];
        $no_telepon = $_POST['no_telepon'];
        
        // Cek apakah email sudah ada di database
        $check_sql = "SELECT id FROM user WHERE email = '$email'";
        $check_result = $conn->query($check_sql);
        if ($check_result->num_rows > 0) {
            echo json_encode(["status" => "error", "message" => "Email sudah terdaftar"]);
            return;
        }
        
        $sql = "INSERT INTO user (nama, email, password, nama_toko, alamat, no_telepon) VALUES ('$name', '$email', '$password', '$nama_toko', '$alamat', '$no_telepon')";
        
        if ($conn->query($sql) === TRUE) {
            echo json_encode(["status" => "success", "message" => "User registered successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error: " . $sql . "<br>" . $conn->error]);
        }
    }

    if ($action == 'login') {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $sql = "SELECT * FROM user WHERE email = '$email'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                echo json_encode(["status" => "success", "message" => "Logged in successfully", "user" => $user]);
            } else {
                echo json_encode(["status" => "error", "message" => "Invalid password"]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "User not found"]);
        }
    }
}

$conn->close();
