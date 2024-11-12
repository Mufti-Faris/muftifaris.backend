<?php
include 'koneksi.php';

if (isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $koneksi->prepare("INSERT INTO user (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $hashed_password);

    if ($stmt->execute()) {
        echo '<script>
            alert("Berhasil Daftar Akun");
            window.location="index.html";
            </script>';
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $koneksi->close();
} else {
    echo "Error: Data tidak lengkap.";
}
