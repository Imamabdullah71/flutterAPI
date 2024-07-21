// case 'delete_barang':
    //     $id = $_POST['id'];

    //     // Get the image file path
    //     $result = $conn->query("SELECT gambar FROM barang WHERE id = '$id'");
    //     $row = $result->fetch_assoc();
    //     $gambar = $row['gambar'];

    //     // Delete the barang entry
    //     $sql_barang = "DELETE FROM barang WHERE id = '$id'";

    //     // Delete the harga entry
    //     $sql_harga = "DELETE FROM harga WHERE barang_id = '$id'";

    //     if ($conn->query($sql_barang) === TRUE && $conn->query($sql_harga) === TRUE) {
    //         // Delete the image file from the server
    //         if ($gambar && file_exists("uploads/" . $gambar)) {
    //             unlink("uploads/" . $gambar);
    //         }
    //         echo json_encode(["status" => "success"]);
    //     } else {
    //         echo json_encode(["status" => "error", "message" => $conn->error]);
    //     }
    //     break;