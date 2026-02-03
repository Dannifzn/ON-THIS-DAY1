<?php
session_start();
include 'koneksi.php';

// Jika sudah login, lempar langsung ke admin
if (isset($_SESSION['status']) && $_SESSION['status'] == "login") {
    header("Location: admin.php");
    exit;
}

$pesan = "";

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Cek Username
    $result = mysqli_query($conn, "SELECT * FROM admin_users WHERE username = '$username'");

    // Jika username ada
    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        
        // Cek Password (Verify Hash)
        if (password_verify($password, $row['password'])) {
            // Password Benar! Buat Session
            $_SESSION['username'] = $username;
            $_SESSION['status'] = "login";
            
            header("Location: admin.php");
            exit;
        }
    }
    
    $pesan = "<div class='alert'>Username atau Password Salah!</div>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - On This Day</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f0f2f5; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-box { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 100%; max-width: 350px; text-align: center; }
        h2 { margin-bottom: 20px; color: #333; }
        input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; font-weight: bold; }
        button:hover { background-color: #0056b3; }
        .alert { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 14px; }
        .back-link { display: block; margin-top: 15px; text-decoration: none; color: #666; font-size: 14px; }
    </style>
</head>
<body>

<div class="login-box">
    <h2>🔒 Login Area</h2>
    <?php echo $pesan; ?>
    
    <form method="POST" action="">
        <input type="text" name="username" placeholder="Username" required autofocus>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="login">Masuk</button>
    </form>
    
    <a href="index.php" class="back-link">← Kembali ke Website Utama</a>
</div>

</body>
</html>