<?php
// 1. MULAI SESSION
session_start();

// 2. CEK LOGIN
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("Location: login.php");
    exit;
}

// 3. KONEKSI
include 'koneksi.php'; 

$message = "";

// --- LOGIKA SIMPAN ---
if (isset($_POST['submit'])) {
    $day     = $_POST['day'];
    $month   = $_POST['month'];
    $year    = mysqli_real_escape_string($conn, $_POST['year']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $link    = mysqli_real_escape_string($conn, $_POST['link']); 

    if (filter_var($link, FILTER_VALIDATE_URL) === FALSE) {
        $message = "<div class='alert error'>⚠️ Format Link Salah! Harus diawali https:// atau http://</div>";
    } else {
        $sql = "INSERT INTO custom_events (day, month, year, content, source, link) 
                VALUES ('$day', '$month', '$year', '$content', 'Admin', '$link')";

        if (mysqli_query($conn, $sql)) {
            $message = "<div class='alert success'>✅ Data Berhasil Disimpan!</div>";
        } else {
            $message = "<div class='alert error'>❌ Gagal Menyimpan: " . mysqli_error($conn) . "</div>";
        }
    }
}

// --- LOGIKA HAPUS ---
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM custom_events WHERE id='$id'");
    header("Location: admin.php"); 
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - On This Day</title>
    <style>
        /* CSS RESET */
        body { font-family: 'Segoe UI', sans-serif; background-color: #f4f6f9; margin: 0; padding: 20px; color: #495057; }
        
        .container { 
            max-width: 1000px; margin: 0 auto; background: #ffffff; 
            padding: 40px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); 
        }

        /* HEADER, USER INFO, & BUTTONS */
        .header { 
            display: flex; justify-content: space-between; align-items: center; 
            border-bottom: 2px solid #f1f3f5; padding-bottom: 20px; margin-bottom: 30px; 
        }
        h1 { margin: 0; color: #343a40; font-size: 1.8rem; }
        .user-info { font-size: 1rem; color: #666; display: block; margin-top: 5px; }

        .header-actions { display: flex; gap: 10px; }

        /* TOMBOL NAVIGASI HEADER */
        .btn-keys { 
            background-color: #0d6efd; color: white; padding: 8px 15px; 
            text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 0.9rem; transition: 0.3s;
        }
        .btn-keys:hover { background-color: #0b5ed7; }

        .btn-logout { 
            background-color: #dc3545; color: white; padding: 8px 15px; 
            text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 0.9rem; transition: 0.3s;
        }
        .btn-logout:hover { background-color: #c82333; }

        h2 { margin-top: 50px; color: #007bff; border-left: 5px solid #007bff; padding-left: 15px; }

        /* FORM */
        .form-row { display: flex; gap: 15px; flex-wrap: wrap; }
        .form-group { margin-bottom: 20px; flex: 1; min-width: 150px; }
        label { display: block; margin-bottom: 8px; font-weight: 600; color: #495057; }
        input, select, textarea { 
            width: 100%; padding: 12px; border: 1px solid #ced4da; 
            border-radius: 6px; box-sizing: border-box; font-size: 1rem; 
        }
        textarea { height: 100px; resize: vertical; }

        .btn-save { 
            background-color: #28a745; color: white; padding: 15px; border: none; 
            border-radius: 6px; cursor: pointer; font-size: 1.1rem; width: 100%; font-weight: bold; 
        }
        .btn-save:hover { background-color: #218838; }

        /* ALERT & TABLE */
        .alert { padding: 15px; margin-bottom: 25px; border-radius: 6px; text-align: center; font-weight: 500; }
        .success { background-color: #d1e7dd; color: #0f5132; }
        .error { background-color: #f8d7da; color: #842029; }

        .table-responsive { overflow-x: auto; margin-top: 20px; border-radius: 8px; border: 1px solid #dee2e6; }
        table { width: 100%; border-collapse: collapse; background: #fff; }
        th, td { padding: 15px; border-bottom: 1px solid #dee2e6; text-align: left; }
        th { background-color: #f8f9fa; font-weight: bold; font-size: 0.85rem; }
        
        .link-preview { color: #0d6efd; text-decoration: none; font-weight: 500; }
        .link-preview:hover { text-decoration: underline; }
        .btn-delete { color: #dc3545; font-weight: bold; text-decoration: none; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <div>
            <h1>⚙️ Admin Dashboard</h1>
            <span class="user-info">Halo, <b><?php echo htmlspecialchars($_SESSION['username']); ?></b>!</span>
        </div>
        
       <div class="header-actions">
    <a href="admin_requests.php" class="btn-keys" style="background-color: #6610f2;">📩 Cek Request</a>
    
    <a href="manage_keys.php" class="btn-keys">🔑 Kelola API Keys</a>
    <a href="logout.php" class="btn-logout">🚪 Keluar</a>
</div>
    </div>
    
    <?php echo $message; ?>

    <form method="POST" action="">
        <div class="form-row">
            <div class="form-group">
                <label>Tanggal</label>
                <select name="day" required>
                    <?php for($i=1; $i<=31; $i++) echo "<option value='$i'>$i</option>"; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Bulan</label>
                <select name="month" required>
                    <option value="1">Januari</option>
                    <option value="2">Februari</option>
                    <option value="3">Maret</option>
                    <option value="4">April</option>
                    <option value="5">Mei</option>
                    <option value="6">Juni</option>
                    <option value="7">Juli</option>
                    <option value="8">Agustus</option>
                    <option value="9">September</option>
                    <option value="10">Oktober</option>
                    <option value="11">November</option>
                    <option value="12">Desember</option>
                </select>
            </div>
            <div class="form-group">
                <label>Tahun</label>
                <input type="number" name="year" placeholder="1945" required>
            </div>
        </div>

        <div class="form-group">
            <label>Link Sumber</label>
            <input type="url" name="link" placeholder="https://..." required>
        </div>

        <div class="form-group">
            <label>Deskripsi Sejarah</label>
            <textarea name="content" placeholder="Detail peristiwa..." required></textarea>
        </div>

        <button type="submit" name="submit" class="btn-save">💾 Simpan Data</button>
    </form>

    <h2>📂 Arsip Data Lokal</h2>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th width="15%">Waktu</th>
                    <th width="10%">Tahun</th>
                    <th width="40%">Isi Kejadian</th>
                    <th width="20%">Link</th>
                    <th width="15%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $q = mysqli_query($conn, "SELECT * FROM custom_events ORDER BY id DESC");
                $bulanIndo = ["", "Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Ags", "Sep", "Okt", "Nov", "Des"];

                if (mysqli_num_rows($q) > 0) {
                    while ($row = mysqli_fetch_assoc($q)) {
                        $tgl = $row['day'] . " " . $bulanIndo[$row['month']];
                        echo "<tr>";
                        echo "<td><b>{$tgl}</b></td>";
                        echo "<td>{$row['year']}</td>";
                        echo "<td>{$row['content']}</td>";
                        echo "<td><a href='{$row['link']}' target='_blank' class='link-preview'>🔗 Buka</a></td>";
                        echo "<td><a href='admin.php?delete={$row['id']}' class='btn-delete' onclick='return confirm(\"Hapus?\")'>🗑️ Hapus</a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' style='text-align:center; padding: 30px; color:#999;'>Belum ada data.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>