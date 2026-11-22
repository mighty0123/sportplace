<?php

namespace App\Services\Admin\Reports;

use Illuminate\Support\Collection;

/** Hasil satu laporan: judul, kolom, baris, dan ringkasan. Dipakai bersama oleh layar, Excel, dan PDF */
final class ReportData
{
    /**
     * @param array<int, array{key:string,label:string,kind:string,pdf:bool,preview:bool,width:int}> $columns
     * @param Collection<int, array<string, mixed>> $rows
     * @param array<int, array{0:string,1:mixed,2:string}> $summary [label, nilai, jenis]
     * @param string[] $filterLines
     */
    public function __construct(
        public readonly string $type,
        public readonly string $title,
        public readonly string $period,
        public readonly array $filterLines,
        public readonly array $columns,
        public readonly Collection $rows,
        public readonly int $total,
        public readonly array $summary,
        public readonly ?string $rangeError = null,
    ) {
    }
}