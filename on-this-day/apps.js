document.addEventListener("DOMContentLoaded", () => {
    const selDate = document.getElementById("selDate");
    const selMonth = document.getElementById("selMonth");
    const apiKeyInput = document.getElementById("apiKeyInput");
    const btnSearch = document.getElementById("btnSearch");
    const resultsContainer = document.getElementById("resultsContainer");
    const loader = document.getElementById("loader");

    if (!btnSearch || !resultsContainer) return;

    // =========================================
    // 1. INISIALISASI TANGGAL & BULAN
    // =========================================
    function initApp() {
        // Isi Dropdown Tanggal 1-31
        for (let i = 1; i <= 31; i++) {
            let opt = document.createElement("option");
            opt.value = i;
            opt.textContent = i;
            selDate.appendChild(opt);
        }

        // Isi Dropdown Bulan
        const months = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        months.forEach((name, index) => {
            let opt = document.createElement("option");
            opt.value = index + 1;
            opt.textContent = name;
            selMonth.appendChild(opt);
        });

        // Set Default ke Hari Ini
        const today = new Date();
        selDate.value = today.getDate();
        selMonth.value = today.getMonth() + 1;
    }
    initApp();

    // =========================================
    // 2. LOGIKA PENCARIAN (CORE)
    // =========================================
    btnSearch.onclick = async () => {
        const d = selDate.value;
        const m = selMonth.value;
        const userKey = apiKeyInput.value.trim();

        // Bersihkan hasil lama & Munculkan Loading
        resultsContainer.innerHTML = "";
        if (loader) loader.style.display = "block";

        try {
            // --- KONSTRUKSI URL ---
            // Gunakan path relatif './' agar aman
            let url = `./history.php?day=${d}&month=${m}`;
            
            // Logika Freemium:
            // Jika user isi key, kita kirim. Jika kosong, biarkan (biar server yang cek kuota IP).
            if (userKey) {
                url += `&key=${userKey}`;
            }

            console.log("Request ke:", url); // Debugging di Console Browser

            // --- EKSEKUSI FETCH ---
            const res = await fetch(url);
            
            // BACA TEXT DULU (Jangan langsung JSON, buat jaga-jaga kalau PHP Error)
            const textData = await res.text();
            
            let data;
            try {
                data = JSON.parse(textData);
            } catch (e) {
                // Jika gagal parsing JSON, berarti ada Error PHP (Syntax/Database Error)
                throw new Error(`<b>Server Error (Bukan JSON):</b><br>Server mengirim respon aneh. Cek kodingan PHP.<br><pre style="background:#eee; padding:5px; font-size:11px; margin-top:5px;">${textData.substring(0, 200)}...</pre>`);
            }

            // --- CEK STATUS RESPON ---
            if (!res.ok || data.status === "error") {
                const msg = data.message || "Gagal mengambil data.";

                // Deteksi Pesan Kuota Habis (Dari PHP)
                // Pastikan kata-kata ini cocok dengan yang ada di history.php
                if (msg.includes("Jatah gratis") || msg.includes("5x")) {
                    throw new Error(`
                        <b>🔒 Limit Gratis Harian Habis!</b><br>
                        Anda sudah mencoba 5 kali hari ini tanpa kunci.<br><br>
                        Silakan minta kunci akses agar bisa lanjut:<br>
                        <a href="request_key.php" target="_blank" style="display:inline-block; margin-top:10px; background:#28a745; color:white; padding:8px 12px; text-decoration:none; border-radius:5px; font-weight:bold;">👉 Minta API Key Disini</a>
                        <br><br><small>Setelah dapat, masukkan kuncinya di kolom atas.</small>
                    `);
                } else {
                    // Error biasa (misal Key Salah)
                    throw new Error(msg);
                }
            }

            // Jika Sukses
            renderList(data);

        } catch (error) {
            console.error(error);
            // Tampilkan Error Cantik di Layar
            resultsContainer.innerHTML = `
                <div style="text-align:center; padding: 25px; border: 1px solid #ffcccc; background: #fff5f5; color: #cc0000; border-radius: 8px;">
                    ❌ <b>Terjadi Masalah:</b><br>
                    <div style="margin-top:10px; color:#333;">${error.message}</div>
                </div>`;
        } finally {
            if (loader) loader.style.display = "none";
        }
    };

    // =========================================
    // 3. FUNGSI RENDER TAMPILAN
    // =========================================
    function renderList(eventsArray) {
        if (!eventsArray || eventsArray.length === 0) {
            resultsContainer.innerHTML = "<p style='text-align:center; color:#666;'>Tidak ada catatan sejarah ditemukan untuk tanggal ini.</p>";
            return;
        }

        let htmlContent = "";
        
        eventsArray.forEach(item => {
            // Logika Tahun (Handle minus untuk SM)
            let yearDisplay = item.year;
            if (parseInt(item.year) < 0) {
                yearDisplay = Math.abs(item.year) + " SM";
            }

            // Pastikan Link Ada
            let linkUrl = item.link;
            if (!linkUrl || linkUrl === "") {
                linkUrl = `https://www.google.com/search?q=${encodeURIComponent(item.text)}`;
            }

            // Render Item
            htmlContent += `
                <a href="${linkUrl}" target="_blank" class="journal-item" title="Klik untuk baca sumber">
                    <span class="year-label">Tahun ${yearDisplay}</span>
                    <div class="content-text">${item.text}</div>
                    <div class="click-hint">🔗 Baca Sumber Lengkap &rarr;</div>
                </a>
            `;
        });

        resultsContainer.innerHTML = htmlContent;
    }
});