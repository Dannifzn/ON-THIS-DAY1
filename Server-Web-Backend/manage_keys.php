<?php
session_start();
// 1. CEK LOGIN (Proteksi)
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("Location: login.php");
    exit;
}

include 'koneksi.php';

$editMode = false;
$dataEdit = [
    'id' => '',
    'client_name' => '',
    'api_key' => ''
];

// --- A. LOGIKA UNTUK ISI FORM EDIT (Jika tombol Edit diklik) ---
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $q = mysqli_query($conn, "SELECT * FROM api_clients WHERE id='$id'");
    if (mysqli_num_rows($q) > 0) {
        $editMode = true;
        $dataEdit = mysqli_fetch_assoc($q);
    }
}

// --- B. LOGIKA SIMPAN (BARU / UPDATE) ---
if (isset($_POST['submit'])) {
    $clientName = mysqli_real_escape_string($conn, $_POST['client_name']);
    $apiKey     = mysqli_real_escape_string($conn, $_POST['api_key']);
    $idToUpdate = $_POST['id_client']; // Hidden Input

    // Validasi: Key tidak boleh kosong
    if (empty($apiKey)) {
        // Jika kosong, generate otomatis
        $apiKey = bin2hex(random_bytes(16));
    }

    if (!empty($idToUpdate)) {
        // === MODE UPDATE ===
        $sql = "UPDATE api_clients SET client_name='$clientName', api_key='$apiKey' WHERE id='$idToUpdate'";
        if(mysqli_query($conn, $sql)){
            header("Location: manage_keys.php"); // Refresh biar bersih
            exit;
        }
    } else {
        // === MODE INSERT BARU ===
        $sql = "INSERT INTO api_clients (client_name, api_key) VALUES ('$clientName', '$apiKey')";
        if(mysqli_query($conn, $sql)){
            header("Location: manage_keys.php");
            exit;
        }
    }
}

// --- C. LOGIKA HAPUS ---
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM api_clients WHERE id='$id'");
    header("Location: manage_keys.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola API Key</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f6f9; padding: 20px; color: #333; }
        .container { max-width: 850px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        
        h1 { margin-top: 0; color: #0d6efd; }
        
        /* FORM STYLE */
        .form-box { background: #e9ecef; padding: 20px; border-radius: 8px; margin-bottom: 30px; border: 1px solid #dee2e6; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"] { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        
        button { padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; color: white; transition: 0.3s; }
        .btn-save { background: #28a745; }
        .btn-save:hover { background: #218838; }
        .btn-cancel { background: #6c757d; text-decoration: none; display: inline-block; font-size: 14px; padding: 10px 20px; }
        
        /* TABLE STYLE */
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background: #f8f9fa; }
        
        .key-code { font-family: monospace; color: #d63384; background: #fff0f6; padding: 4px 8px; border-radius: 4px; border: 1px solid #fcc2d7; font-size: 1.1em; }
        
        .btn-edit { color: #fd7e14; font-weight: bold; text-decoration: none; margin-right: 10px; }
        .btn-delete { color: #dc3545; font-weight: bold; text-decoration: none; }
        .btn-back { display: inline-block; margin-bottom: 20px; text-decoration: none; color: #555; }
    </style>
</head>
<body>

<div class="container">
    <a href="admin.php" class="btn-back">← Kembali ke Dashboard</a>
    <h1>🔑 Manajemen API Key</h1>

    <div class="form-box">
        <h3><?php echo $editMode ? "✏️ Edit Client" : "➕ Tambah Client Baru"; ?></h3>
        
        <form method="POST" action="">
            <input type="hidden" name="id_client" value="<?php echo $dataEdit['id']; ?>">

            <div class="form-group">
                <label>Nama Client / Aplikasi</label>
                <input type="text" name="client_name" placeholder="Contoh: Web Kampus" 
                       value="<?php echo htmlspecialchars($dataEdit['client_name']); ?>" required>
            </div>

            <div class="form-group">
                <label>API Key</label>
                <input type="text" name="api_key" placeholder="Biarkan kosong untuk generate otomatis..." 
                       value="<?php echo htmlspecialchars($dataEdit['api_key']); ?>">
                <small style="color: #666;">*Anda bisa mengetik manual custom key atau kosongkan agar sistem yang membuat.</small>
            </div>

            <button type="submit" name="submit" class="btn-save">
                <?php echo $editMode ? "💾 Update Data" : "✨ Buat Key Baru"; ?>
            </button>

            <?php if($editMode): ?>
                <a href="manage_keys.php" class="btn-cancel">Batal Edit</a>
            <?php endif; ?>
        </form>
    </div>

    <h3>Daftar Client Aktif</h3>
    <table>
        <thead>
            <tr>
                <th width="30%">Nama Client</th>
                <th width="50%">API Key</th>
                <th width="20%">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $q = mysqli_query($conn, "SELECT * FROM api_clients ORDER BY id DESC");
            if (mysqli_num_rows($q) > 0) {
                while ($row = mysqli_fetch_assoc($q)) {
                    echo "<tr>";
                    echo "<td>{$row['client_name']}</td>";
                    echo "<td><span class='key-code'>{$row['api_key']}</span></td>";
                    echo "<td>
                            <a href='manage_keys.php?edit={$row['id']}' class='btn-edit'>Edit</a>
                            <a href='manage_keys.php?delete={$row['id']}' class='btn-delete' onclick='return confirm(\"Hapus akses client ini?\")'>Hapus</a>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='3' style='text-align:center;'>Belum ada data key.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>