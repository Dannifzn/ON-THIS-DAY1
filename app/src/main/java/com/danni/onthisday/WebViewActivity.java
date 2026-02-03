package com.danni.onthisday;

import android.graphics.Bitmap;
import android.os.Bundle;
import android.view.View;
import android.webkit.WebSettings;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.widget.ProgressBar;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;

public class WebViewActivity extends AppCompatActivity {

    WebView webView;
    ProgressBar progressBar;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_webview);

        // 1. Inisialisasi View
        webView = findViewById(R.id.webView);
        progressBar = findViewById(R.id.progressBar);

        // 2. Ambil URL yang dikirim dari Adapter
        String url = getIntent().getStringExtra("url_target");

        if (url == null || url.isEmpty()) {
            Toast.makeText(this, "Link tidak valid", Toast.LENGTH_SHORT).show();
            finish(); // Tutup halaman jika link rusak
            return;
        }

        // 3. Konfigurasi WebView (Agar behave seperti Browser asli)
        WebSettings settings = webView.getSettings();
        settings.setJavaScriptEnabled(true); // Wajib untuk Wikipedia/Web Modern
        settings.setDomStorageEnabled(true);

        // 4. PENTING: Pasang WebViewClient
        // Tanpa ini, Android akan tetap memaksa buka Chrome!
        webView.setWebViewClient(new WebViewClient() {
            @Override
            public void onPageStarted(WebView view, String url, Bitmap favicon) {
                super.onPageStarted(view, url, favicon);
                progressBar.setVisibility(View.VISIBLE); // Munculkan loading
            }

            @Override
            public void onPageFinished(WebView view, String url) {
                super.onPageFinished(view, url);
                progressBar.setVisibility(View.GONE); // Sembunyikan loading
            }
        });

        // 5. Buka URL
        webView.loadUrl(url);
    }

    // Biar kalau tekan tombol Back di HP, dia Back di Browser dulu (bukan tutup aplikasi)
    @Override
    public void onBackPressed() {
        if (webView.canGoBack()) {
            webView.goBack();
        } else {
            super.onBackPressed();
        }
    }
}