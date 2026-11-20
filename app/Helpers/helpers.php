<?php

if (! function_exists('rupiah')) {
    /**
     * Format angka menjadi rupiah, contoh: 150000 => Rp 150.000
     */
    function rupiah($angka): string
    {
        return 'Rp ' . number_format((float) $angka, 0, ',', '.');
    }
}

if (! function_exists('tanggal_id')) {
    /**
     * Format tanggal ke bahasa Indonesia, contoh: 2024-01-31 => 31 Januari 2024
     */
    function tanggal_id($tanggal): string
    {
        if (! $tanggal) {
            return '-';
        }

        $bulan = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
        ];

        $tanggal = $tanggal instanceof \Carbon\Carbon ? $tanggal : \Carbon\Carbon::parse($tanggal);

        return $tanggal->day . ' ' . $bulan[(int) $tanggal->month] . ' ' . $tanggal->year;
    }
}
