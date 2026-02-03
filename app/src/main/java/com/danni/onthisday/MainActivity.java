package com.danni.onthisday;

import androidx.appcompat.app.AlertDialog;
import androidx.appcompat.app.AppCompatActivity;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;
import android.app.DatePickerDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.view.View;
import android.widget.Button;
import android.widget.DatePicker;
import android.widget.EditText;
import android.widget.ProgressBar;
import android.widget.TextView;
import android.widget.Toast;

import com.android.volley.DefaultRetryPolicy;
import com.android.volley.Request;
import com.android.volley.RequestQueue;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.StringRequest; // Ganti ke StringRequest agar lebih fleksibel handle error
import com.android.volley.toolbox.Volley;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.Calendar;
import java.util.List;

public class MainActivity extends AppCompatActivity {

    // Komponen UI
    Button btnDate, btnSaveKey;
    TextView tvTitle, tvRequestKey;
    EditText etApiKey;
    RecyclerView recyclerView;
    ProgressBar progressBar;

    // Data & Adapter
    List<HistoryModel> listData;
    HistoryAdapter adapter;

    // Penyimpanan Lokal (Untuk simpan API Key)
    SharedPreferences sharedPreferences;

    // === GANTI IP SESUAI KOMPUTER KAMU ===
    String BASE_URL = "https://danni.skill-issue.space/on-this-day/";
    String API_URL = BASE_URL + "history.php";
    String FORM_URL = BASE_URL + "request_key.php"; // Link Form Web

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        // 1. Inisialisasi View
        btnDate = findViewById(R.id.btnDate);
        btnSaveKey = findViewById(R.id.btnSaveKey);
        tvTitle = findViewById(R.id.tvTitle);
        tvRequestKey = findViewById(R.id.tvRequestKey);
        etApiKey = findViewById(R.id.etApiKey);
        recyclerView = findViewById(R.id.recyclerView);
        progressBar = findViewById(R.id.progressBar);

        // 2. Setup RecyclerView
        listData = new ArrayList<>();
        recyclerView.setLayoutManager(new LinearLayoutManager(this));
        adapter = new HistoryAdapter(this, listData);
        recyclerView.setAdapter(adapter);

        // 3. Setup SharedPreferences (Agar Key tersimpan di HP)
        sharedPreferences = getSharedPreferences("AppPrefs", Context.MODE_PRIVATE);

        // Muat Key yang tersimpan (kalau ada)
        String savedKey = sharedPreferences.getString("api_key", "");
        etApiKey.setText(savedKey);

        // 4. Tombol Simpan Key
        btnSaveKey.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                String inputKey = etApiKey.getText().toString().trim();

                // Simpan ke memori HP
                SharedPreferences.Editor editor = sharedPreferences.edit();
                editor.putString("api_key", inputKey);
                editor.apply();

                Toast.makeText(MainActivity.this, "Key Disimpan!", Toast.LENGTH_SHORT).show();

                // Refresh data dengan key baru
                Calendar now = Calendar.getInstance();
                fetchHistoryData(now.get(Calendar.DAY_OF_MONTH), now.get(Calendar.MONTH) + 1);
            }
        });

        // 5. Link Request Key (Buka Form Web di WebView)
        tvRequestKey.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Intent intent = new Intent(MainActivity.this, WebViewActivity.class);
                intent.putExtra("url_target", FORM_URL);
                startActivity(intent);
            }
        });

        // 6. Tombol Pilih Tanggal
        btnDate.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                showDatePicker();
            }
        });

        // Load Data Awal (Hari Ini)
        Calendar now = Calendar.getInstance();
        fetchHistoryData(now.get(Calendar.DAY_OF_MONTH), now.get(Calendar.MONTH) + 1);
    }

    private void showDatePicker() {
        Calendar calendar = Calendar.getInstance();
        DatePickerDialog dpd = new DatePickerDialog(this, new DatePickerDialog.OnDateSetListener() {
            @Override
            public void onDateSet(DatePicker view, int year, int month, int dayOfMonth) {
                int selectedMonth = month + 1;
                String dateStr = dayOfMonth + "/" + selectedMonth + "/" + year;
                btnDate.setText("Tanggal: " + dateStr);
                fetchHistoryData(dayOfMonth, selectedMonth);
            }
        }, calendar.get(Calendar.YEAR), calendar.get(Calendar.MONTH), calendar.get(Calendar.DAY_OF_MONTH));
        dpd.show();
    }

    private void fetchHistoryData(int day, int month) {
        progressBar.setVisibility(View.VISIBLE);
        listData.clear();
        adapter.notifyDataSetChanged();

        // Ambil Key dari EditText (Bisa kosong)
        String currentKey = etApiKey.getText().toString().trim();

        // Susun URL
        String url = API_URL + "?day=" + day + "&month=" + month;
        if (!currentKey.isEmpty()) {
            url += "&key=" + currentKey;
        }

        RequestQueue queue = Volley.newRequestQueue(this);

        // Ganti jadi StringRequest agar bisa handle Error JSON dari PHP
        StringRequest request = new StringRequest(Request.Method.GET, url,
                new Response.Listener<String>() {
                    @Override
                    public void onResponse(String response) {
                        progressBar.setVisibility(View.GONE);
                        try {
                            // Cek apakah responnya Error Object atau Array Data?
                            // Jika diawali '{', berarti Object (kemungkinan Error)
                            if (response.trim().startsWith("{")) {
                                JSONObject obj = new JSONObject(response);
                                if (obj.has("status") && obj.getString("status").equals("error")) {
                                    String msg = obj.getString("message");

                                    // Tampilkan Alert jika Kuota Habis
                                    showErrorDialog(msg);
                                    return;
                                }
                            }

                            // Jika diawali '[', berarti Array (Data Sukses)
                            JSONArray jsonArray = new JSONArray(response);
                            if (jsonArray.length() == 0) {
                                Toast.makeText(MainActivity.this, "Tidak ada data.", Toast.LENGTH_SHORT).show();
                                return;
                            }

                            for (int i = 0; i < jsonArray.length(); i++) {
                                JSONObject obj = jsonArray.getJSONObject(i);
                                String year = obj.getString("year");
                                String text = obj.getString("text");
                                String link = "";
                                if (obj.has("link")) link = obj.getString("link");

                                listData.add(new HistoryModel(year, text, link));
                            }
                            adapter.notifyDataSetChanged();

                        } catch (JSONException e) {
                            e.printStackTrace();
                            Toast.makeText(MainActivity.this, "Error Parsing Data", Toast.LENGTH_SHORT).show();
                        }
                    }
                },
                new Response.ErrorListener() {
                    @Override
                    public void onErrorResponse(VolleyError error) {
                        progressBar.setVisibility(View.GONE);
                        // Handle Error 403 (Limit Habis / Key Salah) dari Volley
                        if (error.networkResponse != null && error.networkResponse.statusCode == 403) {
                            // Coba baca body errornya
                            String body = new String(error.networkResponse.data);
                            try {
                                JSONObject json = new JSONObject(body);
                                showErrorDialog(json.getString("message"));
                            } catch (Exception e) {
                                showErrorDialog("Akses Ditolak. Cek API Key Anda.");
                            }
                        } else {
                            Toast.makeText(MainActivity.this, "Gagal Koneksi ke Server", Toast.LENGTH_SHORT).show();
                        }
                    }
                });

        request.setRetryPolicy(new DefaultRetryPolicy(
                30000,
                DefaultRetryPolicy.DEFAULT_MAX_RETRIES,
                DefaultRetryPolicy.DEFAULT_BACKOFF_MULT));

        queue.add(request);
    }

    // Fungsi menampilkan Dialog Cantik saat Kuota Habis
    private void showErrorDialog(String message) {
        new AlertDialog.Builder(this)
                .setTitle("⚠️ Akses Dibatasi")
                .setMessage(message + "\n\nSilakan minta API Key agar bisa akses tanpa batas.")
                .setPositiveButton("Minta Key Sekarang", new DialogInterface.OnClickListener() {
                    @Override
                    public void onClick(DialogInterface dialog, int which) {
                        // Buka Halaman Request
                        Intent intent = new Intent(MainActivity.this, WebViewActivity.class);
                        intent.putExtra("url_target", FORM_URL);
                        startActivity(intent);
                    }
                })
                .setNegativeButton("Tutup", null)
                .setIcon(android.R.drawable.ic_dialog_alert)
                .show();
    }
}