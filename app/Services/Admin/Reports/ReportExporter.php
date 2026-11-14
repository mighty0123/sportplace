<?php

namespace App\Services\Admin\Reports;

use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportExporter
{
    private const FORMAT = [
        'money' => '#,##0', 'int' => '#,##0', 'date' => 'dd/mm/yyyy', 'datetime' => 'dd/mm/yyyy hh:mm',
    ];

    // ------ Excel

    public function xlsx(ReportData $d, User $admin): StreamedResponse
    {
        $book = new Spreadsheet();
        $book->getProperties()->setCreator('SportPlace')->setTitle($d->title);

        // Lembar 1: ringkasan dan keterangan.
        $info = $book->getActiveSheet()->setTitle('Ringkasan');
        $row = 1;
        foreach ([['SportPlace', true], [$d->title, true]] as [$text, $bold]) {
            $this->put($info, 1, $row, $text, 'text');
            $info->getStyle("A{$row}")->getFont()->setBold($bold)->setSize($row === 1 ? 14 : 12);
            $row++;
        }
        foreach ($d->filterLines as $line) {
            $this->put($info, 1, $row++, $line, 'text');
        }
        $this->put($info, 1, $row++, 'Dicetak oleh ' . $admin->name . ', ' . $this->printedAt(), 'text');
        $this->put($info, 1, $row++, 'Jumlah baris data: ' . number_format($d->total, 0, ',', '.'), 'text');
        $row++;
        foreach ($d->summary as [$label, $value, $kind]) {
            $this->put($info, 1, $row, $label, 'text');
            $this->put($info, 2, $row, $value, $kind);
            if (isset(self::FORMAT[$kind])) {
                $info->getStyle("B{$row}")->getNumberFormat()->setFormatCode(self::FORMAT[$kind]);
            }
            $row++;
        }
        $info->getColumnDimension('A')->setWidth(46);
        $info->getColumnDimension('B')->setWidth(22);

        // Lembar 2: data.
        $sheet = $book->createSheet()->setTitle('Data');
        foreach ($d->columns as $i => $c) {
            $this->put($sheet, $i + 1, 1, $c['label'], 'text');
            $sheet->getColumnDimensionByColumn($i + 1)->setWidth($c['width']);
        }

        $r = 2;
        foreach ($d->rows as $item) {
            foreach ($d->columns as $i => $c) {
                $this->put($sheet, $i + 1, $r, $item[$c['key']] ?? null, $c['kind']);
            }
            $r++;
        }

        $last = max(1, $r - 1);
        $lastCol = Coordinate::stringFromColumnIndex(count($d->columns));
        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0A7660']],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
        ]);
        // Format angka dan tanggal per kolom, satu kali (bukan per sel supaya tetap cepat).
        foreach ($d->columns as $i => $c) {
            $letter = Coordinate::stringFromColumnIndex($i + 1);
            if (isset(self::FORMAT[$c['kind']]) && $last > 1) {
                $sheet->getStyle("{$letter}2:{$letter}{$last}")->getNumberFormat()->setFormatCode(self::FORMAT[$c['kind']]);
            }
        }
        $sheet->freezePane('A2');
        $sheet->setAutoFilter("A1:{$lastCol}{$last}");
        $book->setActiveSheetIndex(0);

        return response()->streamDownload(function () use ($book) {
            (new Xlsx($book))->save('php://output');
        }, $this->fileName($d, 'xlsx'), ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }

    /** Teks selalu ditulis sebagai teks murni: nilai seperti "=1+1" tidak pernah dijalankan sebagai rumus. */
    private function put(Worksheet $sheet, int $col, int $row, mixed $value, string $kind): void
    {
        $cell = $sheet->getCell(Coordinate::stringFromColumnIndex($col) . $row);

        switch ($kind) {
            case 'money':
            case 'int':
                $cell->setValueExplicit((float) ($value ?? 0), DataType::TYPE_NUMERIC);
                break;
            case 'date':
            case 'datetime':
                if ($value) {
                    $cell->setValueExplicit(Date::PHPToExcel($value), DataType::TYPE_NUMERIC);
                }
                break;
            case 'bool':
                $cell->setValueExplicit($value ? 'Ya' : 'Tidak', DataType::TYPE_STRING);
                break;
            default:
                $cell->setValueExplicit((string) ($value ?? ''), DataType::TYPE_STRING);
        }
    }

    // -------- PDF

    public function pdf(ReportData $d, User $admin)
    {
        $pdf = Pdf::loadView('reports.pdf', ['d' => $d, 'admin' => $admin->name, 'printedAt' => $this->printedAt()])
            ->setPaper('a4', 'landscape')
            ->setOption(['defaultFont' => 'Helvetica']);

        $pdf->output();
        $dompdf = $pdf->getDomPDF();
        $canvas = $dompdf->getCanvas();
        $font = $dompdf->getFontMetrics()->getFont('Helvetica', 'normal');
        $canvas->page_text(36, 566, 'SportPlace, ' . $d->title, $font, 8, [0.35, 0.42, 0.36]);
        $canvas->page_text(720, 566, 'Halaman {PAGE_NUM} dari {PAGE_COUNT}', $font, 8, [0.35, 0.42, 0.36]);

        return $pdf->download($this->fileName($d, 'pdf'));
    }

    // ---- helpers

    private function printedAt(): string
    {
        return now()->locale('id')->translatedFormat('j F Y, H.i') . ' ' . now()->format('T');
    }

    private function fileName(ReportData $d, string $ext): string
    {
        return Str::slug($d->title . ' ' . $d->period) . '-' . now()->format('Ymd-His') . '.' . $ext;
    }
}