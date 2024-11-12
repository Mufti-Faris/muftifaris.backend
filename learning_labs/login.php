<?php
session_start();
include 'koneksi.php';

$username = $_POST['username'];
$password = $_POST['password'];

if (empty($username) || empty($password)) {
    echo '<script>alert("Username dan Password harus diisi!"); window.location.href = "index.html";</script>';
    exit;
}

$sql = "SELECT * FROM user WHERE username = ?";
$stmt = $koneksi->prepare($sql);

if (!$stmt) {
    echo 'Error preparing statement: ' . $koneksi->error;
    exit;
}

$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    if (password_verify($password, $user['password'])) {
        $_SESSION['username'] = $username;
        $_SESSION['user_id'] = $user['id']; // Store user_id in session
        header("Location: home/todolist.php");
        exit;
    } else {
        echo '<script>alert("Oops! Password salah. Silakan coba lagi."); window.location.href = "index.html";</script>';
    }
} else {
    echo '<script>alert("Oops! Username tidak ditemukan. Silakan coba lagi."); window.location.href = "index.html";</script>';
}

$stmt->close();
$koneksi->close();
?>
