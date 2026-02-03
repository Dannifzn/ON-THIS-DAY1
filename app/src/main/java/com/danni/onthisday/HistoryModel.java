package com.danni.onthisday;

public class HistoryModel {
    String year;
    String text;
    String link; // <-- Tambahan

    public HistoryModel(String year, String text, String link) {
        this.year = year;
        this.text = text;
        this.link = link;
    }

    public String getYear() { return year; }
    public String getText() { return text; }
    public String getLink() { return link; } // <-- Tambahan Getter
}