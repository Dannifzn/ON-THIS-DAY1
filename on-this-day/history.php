<?php
// history.php - FREEMIUM VERSION (5x Free/Day or API Key)
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

include 'koneksi.php';

// ==========================================
// LOGIKA KEAMANAN HYBRID (IP vs KEY)
// ==========================================

$isAllowed = false; // Default: Ditolak
$pesanError = "";

// SKENARIO A: User Membawa API Key
if (isset($_GET['key']) && !empty($_GET['key'])) {
    $clientKey = mysqli_real_escape_string($conn, $_GET['key']);
    $cekKey = mysqli_query($conn, "SELECT * FROM api_clients WHERE api_key = '$clientKey'");
    
    if (mysqli_num_rows($cekKey) > 0) {
        $isAllowed = true; // Key Valid! Lolos.
    } else {
        $pesanError = "API Key yang Anda masukkan Salah.";
    }
} 
// SKENARIO B: User Gratisan (Tanpa Key)
else {
    // 1. Ambil IP Address User
    $userIP = $_SERVER['REMOTE_ADDR'];
    
    // 2. Hitung jumlah akses IP ini HARI INI
    // Kita pakai CURDATE() agar limitnya reset setiap ganti hari.
    // Kalau mau limit seumur hidup, hapus bagian "AND DATE(access_time)..."
    $sqlCount = "SELECT COUNT(*) as jumlah FROM access_logs 
                 WHERE ip_address = '$userIP' AND DATE(access_time) = CURDATE()";
    $qCount = mysqli_query($conn, $sqlCount);
    $data = mysqli_fetch_assoc($qCount);
    $jumlahAkses = $data['jumlah'];
    
    // 3. Cek Batas Limit (Maksimal 5)
    if ($jumlahAkses < 5) {
        // Catat akses ini ke database
        mysqli_query($conn, "INSERT INTO access_logs (ip_address) VALUES ('$userIP')");
        $isAllowed = true; // Masih punya jatah! Lolos.
    } else {
        $pesanError = "Jatah gratis 5x Anda hari ini habis! Silakan minta API Key untuk lanjut.";
    }
}

// === EKSEKUSI: JIKA DITOLAK ===
if (!$isAllowed) {
    http_response_code(403);
    echo json_encode(["status" => "error", "message" => $pesanError]);
    exit; // Stop proses
}

// ==========================================
// JIKA LOLOS, LANJUT KE PROSES DATA (Sama seperti sebelumnya)
// ==========================================

$day   = isset($_GET['day']) ? $_GET['day'] : '1';
$month = isset($_GET['month']) ? $_GET['month'] : '1';
$mm = str_pad($month, 2, '0', STR_PAD_LEFT);
$dd = str_pad($day, 2, '0', STR_PAD_LEFT);

$finalEvents = [];

// 1. DATA ADMIN
$sql = "SELECT year, content, link FROM custom_events WHERE day = '$day' AND month = '$month'";
$result = mysqli_query($conn, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $finalEvents[] = [
            "year" => $row['year'],
            "text" => $row['content'] . " (Sumber: Lokal)",
            "link" => $row['link']
        ];
    }
}

// 2. DATA WIKIPEDIA
$url = "https://en.wikipedia.org/api/rest_v1/feed/onthisday/events/{$mm}/{$dd}";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERAGENT, 'SchoolProject/1.0');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$resp = curl_exec($ch);
curl_close($ch);

if ($resp) {
    $data = json_decode($resp, true);
    if (isset($data['events'])) {
        $limit = 7; $count = 0;
        foreach ($data['events'] as $event) {
            if ($count >= $limit) break;
            $indoText = googleTranslate($event['text']);
            $wikiLink = isset($event['pages'][0]['content_urls']['mobile']['page']) ? 
                        $event['pages'][0]['content_urls']['mobile']['page'] : 
                        "https://en.wikipedia.org/wiki/" . $event['year'];
            
            $finalEvents[] = ["year" => (string)$event['year'], "text" => $indoText, "link" => $wikiLink];
            $count++;
        }
    }
}

if (!empty($finalEvents)) {
    usort($finalEvents, function($a, $b) { return $a['year'] - $b['year']; });
    echo json_encode($finalEvents);
} else {
    echo json_encode([]);
}

function googleTranslate($text) {
    $encodedText = urlencode($text);
    $url = "https://translate.googleapis.com/translate_a/single?client=gtx&sl=en&tl=id&dt=t&q={$encodedText}";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
    $response = curl_exec($ch);
    curl_close($ch);
    if ($response) {
        $data = json_decode($response, true);
        if (isset($data[0][0][0])) {
            $translatedText = "";
            foreach ($data[0] as $part) { $translatedText .= $part[0]; }
            return $translatedText;
        }
    }
    return $text;
}
?>