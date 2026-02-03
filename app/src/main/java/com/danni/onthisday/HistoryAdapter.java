package com.danni.onthisday; // Pastikan package ini sesuai dengan punyamu

import android.content.Context;
import android.content.Intent;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;

import java.util.List;

public class HistoryAdapter extends RecyclerView.Adapter<HistoryAdapter.ViewHolder> {

    private List<HistoryModel> historyList;
    private Context context;

    public HistoryAdapter(Context context, List<HistoryModel> historyList) {
        this.context = context;
        this.historyList = historyList;
    }

    @NonNull
    @Override
    public ViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(parent.getContext()).inflate(R.layout.item_history, parent, false);
        return new ViewHolder(view);
    }

    @Override
    public void onBindViewHolder(@NonNull ViewHolder holder, int position) {
        HistoryModel item = historyList.get(position);

        holder.tvYear.setText("Tahun " + item.getYear());
        holder.tvText.setText(item.getText());

        // === BAGIAN INI YANG MENENTUKAN CHROME ATAU WEBVIEW ===
        holder.itemView.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                String url = item.getLink();

                if (url != null && !url.isEmpty()) {

                    // --- KODE LAMA (HAPUS INI) ---
                    // Intent intent = new Intent(Intent.ACTION_VIEW);
                    // intent.setData(Uri.parse(url));

                    // --- KODE BARU (PAKAI INI) ---
                    // Kita panggil WebViewActivity.class agar bukanya di dalam aplikasi
                    Intent intent = new Intent(context, WebViewActivity.class);

                    // Kita kirim URL-nya sebagai "paket" bernama "url_target"
                    intent.putExtra("url_target", url);

                    context.startActivity(intent);

                } else {
                    Toast.makeText(context, "Link sumber tidak tersedia", Toast.LENGTH_SHORT).show();
                }
            }
        });
    }

    @Override
    public int getItemCount() {
        return historyList.size();
    }

    public static class ViewHolder extends RecyclerView.ViewHolder {
        TextView tvYear, tvText;

        public ViewHolder(@NonNull View itemView) {
            super(itemView);
            tvYear = itemView.findViewById(R.id.tvYear);
            tvText = itemView.findViewById(R.id.tvText);
        }
    }
}