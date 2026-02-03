<?php
session_start();
// Cek Login Admin
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("Location: login.php");
    exit;
}

include 'koneksi.php';

// Hapus Request (Jika sudah diproses)
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM api_requests WHERE id='$id'");
    header("Location: admin_requests.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Permintaan Key</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f6f9; padding: 20px; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        h1 { margin-top: 0; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background: #f8f9fa; }
        .btn-wa { background: #25D366; color: white; padding: 6px 12px; text-decoration: none; border-radius: 4px; font-weight: bold; font-size: 14px; }
        .btn-wa:hover { background: #128C7E; }
        .btn-delete { color: #dc3545; text-decoration: none; font-weight: bold; margin-left: 10px; font-size: 14px; }
        .btn-back { display: inline-block; margin-bottom: 20px; text-decoration: none; color: #555; }
        .date { font-size: 12px; color: #888; }
    </style>
</head>
<body>

<div class="container">
    <a href="admin.php" class="btn-back">← Kembali ke Dashboard</a>
    <h1>📩 Permintaan Masuk</h1>
    <p>Daftar user yang meminta API Key via form web.</p>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Nama Client</th>
                <th>Nomor WhatsApp</th>
                <th>Aksi Admin</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $q = mysqli_query($conn, "SELECT * FROM api_requests ORDER BY id DESC");
            if (mysqli_num_rows($q) > 0) {
                while ($row = mysqli_fetch_assoc($q)) {
                    $nama = htmlspecialchars($row['client_name']);
                    $waOriginal = $row['whatsapp'];
                    
                    // --- LOGIKA UBAH 08xx JADI 628xx ---
                    // Agar link WA berfungsi, format harus 628... bukan 08...
                    $waLink = $waOriginal;
                    if (substr($waLink, 0, 1) == '0') {
                        $waLink = '62' . substr($waLink, 1);
                    }

                    // Pesan otomatis saat admin klik tombol WA
                    $pesan = "Halo $nama, saya Admin OnThisDay. Permintaan API Key Anda sudah kami terima...Berikut kode api-key:";
                    $linkWA = "https://wa.me/$waLink?text=" . urlencode($pesan);

                    echo "<tr>";
                    echo "<td class='date'>{$row['created_at']}</td>";
                    echo "<td><b>$nama</b></td>";
                    echo "<td>$waOriginal</td>";
                    echo "<td>
                            <a href='$linkWA' target='_blank' class='btn-wa'>💬 Chat WA</a>
                            <a href='admin_requests.php?delete={$row['id']}' class='btn-delete' onclick='return confirm(\"Hapus permintaan ini?\")'>Hapus</a>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4' style='text-align:center; padding: 20px;'>Belum ada permintaan baru.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>