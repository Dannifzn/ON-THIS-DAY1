<?php
include 'koneksi.php';
$message = "";

if (isset($_POST['submit'])) {
    $name = mysqli_real_escape_string($conn, $_POST['client_name']);
    $wa   = mysqli_real_escape_string($conn, $_POST['whatsapp']);

    // Validasi sederhana
    if (!empty($name) && !empty($wa)) {
        $sql = "INSERT INTO api_requests (client_name, whatsapp) VALUES ('$name', '$wa')";
        if (mysqli_query($conn, $sql)) {
            $message = "<div class='alert success'>✅ Permintaan Terkirim! Tunggu Admin menghubungi Anda via WhatsApp.</div>";
        } else {
            $message = "<div class='alert error'>❌ Gagal mengirim: " . mysqli_error($conn) . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request API Key</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f6f9; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .box { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        h2 { text-align: center; color: #333; margin-bottom: 20px; }
        input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background-color: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; font-size: 16px; margin-top: 10px; }
        button:hover { background-color: #218838; }
        .back-link { display: block; text-align: center; margin-top: 20px; text-decoration: none; color: #666; }
        .alert { padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center; font-size: 14px; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>

<div class="box">
    <h2>🔑 Request API Key</h2>
    <p style="text-align: center; color: #666; font-size: 0.9rem;">Admin akan mengirimkan Key ke WhatsApp Anda.</p>
    
    <?php echo $message; ?>

    <form method="POST">
        <label>Nama Anda / Aplikasi</label>
        <input type="text" name="client_name" placeholder="Contoh: Budi (Aplikasi Kampus)" required>

        <label>Nomor WhatsApp</label>
        <input type="number" name="whatsapp" placeholder="Contoh: 08123456789" required>

        <button type="submit" name="submit">Kirim Permintaan 🚀</button>
    </form>

    <a href="index.php" class="back-link">← Kembali ke Halaman Utama</a>
</div>

</body>
</html>