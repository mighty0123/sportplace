<?php

return [
    // Batas baris per format. Lebih dari itu, pengguna diminta mempersempit filter
    'max_rows' => [
        'xlsx' => (int) env('REPORT_MAX_ROWS_XLSX', 10000),
        'pdf'  => (int) env('REPORT_MAX_ROWS_PDF', 1500),
    ],

    // Jumlah baris pada pratinjau di layar
    'preview_rows' => 8,
];