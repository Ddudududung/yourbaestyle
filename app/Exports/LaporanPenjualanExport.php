<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class LaporanPenjualanExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $data;
    private $rowNumber = 0;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal & Waktu',
            'No. Pesanan / Kode TRX',
            'Platform',
            'Total Harga (Rp)',
            'HPP / Modal (Rp)',
            'Laba / Rugi (Rp)',
        ];
    }

    public function map($row): array
    {
        $this->rowNumber++;
        $harga = $row->total_harga ?? 0;
        $hpp = $row->total_hpp ?? 0;
        $laba = $harga - $hpp;
        $platform = ucfirst($row->platform ?? 'POS');

        return [
            $this->rowNumber,
            Carbon::parse($row->tanggal ?? now())->format('d/m/Y H:i'),
            $row->no_pesanan ?? $row->kode_transaksi ?? '-',
            $platform,
            $harga,
            $hpp,
            $laba,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2C3E50'],
                ],
            ],
        ];
    }
}
