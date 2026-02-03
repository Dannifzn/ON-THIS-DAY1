<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>On This Day - Sejarah Dunia</title>
    <style>
        /* === RESET & BASIC STYLE === */
        body {
            background-color: #f4f1ea;
            color: #3e3b36;
            font-family: 'Georgia', 'Times New Roman', serif;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            min-height: 100vh;
        }

        .container { width: 100%; max-width: 850px; }

        h1 {
            text-align: center;
            font-size: 2.5rem;
            border-bottom: 3px double #3e3b36;
            padding-bottom: 10px;
            margin-bottom: 5px;
            color: #2c3e50;
        }

        .subtitle { text-align: center; font-style: italic; color: #666; margin-bottom: 30px; }

        /* === CONTROLS === */
        .controls {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #dcdcdc;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            display: flex;
            gap: 10px;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 30px;
        }

        select, button, input {
            padding: 12px 15px;
            font-size: 1rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            background: #fff;
        }

        /* Styling Input API Key */
        #apiKeyInput {
            flex: 1; /* Agar lebar menyesuaikan */
            min-width: 200px;
            background-color: #f9f9f9;
            border: 1px dashed #aaa;
            color: #333;
        }
        #apiKeyInput:focus { border-color: #8b0000; outline: none; background: #fff; }

        button {
            background-color: #8b0000;
            color: #fff;
            cursor: pointer;
            font-weight: bold;
            transition: 0.3s;
            border: none;
            min-width: 150px;
        }
        button:hover { background-color: #a52a2a; transform: scale(1.05); }

        .loading { text-align: center; font-style: italic; display: none; margin: 20px 0; color: #888; }

        /* === LIST JOURNAL ITEM === */
        .journal-list { display: flex; flex-direction: column; gap: 20px; }

        .journal-item {
            display: block;
            text-decoration: none;
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            border-left: 6px solid #c9b09a;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            position: relative;
            color: #3e3b36;
        }
        .journal-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.15);
            border-left-color: #8b0000;
            background-color: #fffaf0;
        }
        .year-label { font-family: 'Arial', sans-serif; font-weight: bold; color: #8b0000; font-size: 1.3rem; margin-bottom: 8px; display: block; }
        .content-text { font-size: 1.1rem; line-height: 1.6; margin-bottom: 15px; }
        .click-hint { font-size: 0.85rem; color: #007bff; font-family: sans-serif; font-weight: bold; display: flex; align-items: center; gap: 5px; }

        @media (max-width: 600px) {
            .controls { flex-direction: column; }
            select, button, input { width: 100%; box-sizing: border-box; }
            h1 { font-size: 2rem; }
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>On This Day 📜</h1>
        <p class="subtitle">"Masukkan Tanggal & API Key untuk membuka arsip sejarah."</p>

        <div class="controls">
            <select id="selDate"></select>
            <select id="selMonth"></select>
            
            <input type="text" id="apiKeyInput" placeholder="Tempel API Key disini..." autocomplete="off">
<button id="btnSearch">📖 Buka Sejarah</button>

<div style="width: 100%; text-align: center; margin-top: 10px;">
    <small>Belum punya kunci akses? <a href="request_key.php" style="color: #007bff; text-decoration: none; font-weight: bold;">Minta API Key Disini &rarr;</a></small>
</div>
        </div>

        <div id="loader" class="loading">⏳ Sedang memverifikasi kunci & mengambil data...</div>

        <div id="resultsContainer" class="journal-list"></div>
    </div>

    <script src="apps.js"></script>
</body>
</html>