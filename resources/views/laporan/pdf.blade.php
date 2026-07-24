<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan - Yourbaestyle</title>
    <style>
        @page {
            size: A4;
            margin: 15mm 15mm 20mm 15mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10pt;
            color: #333333;
            line-height: 1.4;
        }
        /* Header & Branding */
        .header-table {
            width: 100%;
            border-bottom: 2px solid #2C3E50;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .company-name {
            font-size: 20pt;
            font-weight: bold;
            color: #2C3E50;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .company-sub {
            font-size: 9pt;
            color: #7F8C8D;
        }
        .report-title {
            text-align: right;
            font-size: 14pt;
            font-weight: bold;
            color: #2C3E50;
        }
        .report-meta {
            text-align: right;
            font-size: 9pt;
            color: #555555;
        }
        /* Summary Cards / KPI Table */
        .summary-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: separate;
            border-spacing: 10px 0;
        }
        .summary-box {
            background-color: #F8F9FA;
            border: 1px solid #E9ECEF;
            border-left: 4px solid #2C3E50;
            padding: 10px;
            text-align: center;
            border-radius: 3px;
        }
        .summary-label {
            font-size: 8pt;
            color: #6C757D;
            text-transform: uppercase;
        }
        .summary-value {
            font-size: 12pt;
            font-weight: bold;
            color: #2C3E50;
            margin-top: 5px;
        }
        /* Main Data Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 9pt;
        }
        .data-table th {
            background-color: #2C3E50;
            color: #FFFFFF;
            font-weight: bold;
            text-align: left;
            padding: 8px;
            border: 1px solid #2C3E50;
        }
        .data-table td {
            padding: 8px;
            border: 1px solid #DEE2E6;
        }
        .data-table tr:nth-child(even) {
            background-color: #F8F9FA;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        
        /* Badges */
        .badge {
            padding: 3px 6px;
            font-size: 7.5pt;
            border-radius: 3px;
            text-transform: uppercase;
            font-weight: bold;
        }
        .bg-shopee { background-color: #FFE5E5; color: #D63031; border: 1px solid #FFCCCC; }
        .bg-tiktok { background-color: #E2E8F0; color: #1A202C; border: 1px solid #CBD5E1; }
        .bg-pos { background-color: #E3F2FD; color: #0984E3; border: 1px solid #BBDEFB; }

        /* Footer & Signatures */
        .footer-table {
            width: 100%;
            margin-top: 30px;
            page-break-inside: avoid;
        }
        .signature-area {
            text-align: center;
            width: 30%;
            float: right;
        }
        .signature-space {
            height: 60px;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <table class="header-table">
        <tr>
            <td style="width: 60%;">
                <div class="company-name">Yourbaestyle</div>
                <div class="company-sub">Thrift & Rebranding Fashion Store</div>
                <div class="company-sub">Jl. Contoh Alamat No. 123, Bandung | Telp: 0812-3456-7890</div>
            </td>
            <td style="width: 40%;">
                <div class="report-title">LAPORAN PENJUALAN</div>
                <div class="report-meta">Periode: {{ $tgl_mulai ?? '-' }} s/d {{ $tgl_selesai ?? '-' }}</div>
                <div class="report-meta">Dicetak Pada: {{ date('d/m/Y H:i') }}</div>
            </td>
        </tr>
    </table>

    <!-- Ringkasan KPI (Total Pendapatan, HPP, & Laba) -->
    <table class="summary-table">
        <tr>
            <td class="summary-box" style="border-left-color: #0984E3;">
                <div class="summary-label">Total Transaksi</div>
                <div class="summary-value">{{ count($data ?? []) }} Pesanan</div>
            </td>
            <td class="summary-box" style="border-left-color: #6C5CE7;">
                <div class="summary-label">Total Omset (Gross)</div>
                <div class="summary-value">Rp {{ number_format($total_omset ?? 0, 0, ',', '.') }}</div>
            </td>
            <td class="summary-box" style="border-left-color: #E17055;">
                <div class="summary-label">Total Modal (HPP)</div>
                <div class="summary-value">Rp {{ number_format($total_hpp ?? 0, 0, ',', '.') }}</div>
            </td>
            <td class="summary-box" style="border-left-color: #00B894; background-color: #EAFAF1;">
                <div class="summary-label" style="color: #00B894; font-weight: bold;">Est. Laba Kotor (Profit)</div>
                <div class="summary-value" style="color: #00B894;">
                    Rp {{ number_format(($total_omset ?? 0) - ($total_hpp ?? 0), 0, ',', '.') }}
                </div>
            </td>
        </tr>
    </table>

    <!-- Tabel Data Transaksi -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;" class="text-center">No</th>
                <th style="width: 15%;">Tanggal</th>
                <th style="width: 22%;">No. Pesanan / TRX</th>
                <th style="width: 13%;" class="text-center">Platform</th>
                <th style="width: 15%;" class="text-right">Total Harga</th>
                <th style="width: 15%;" class="text-right">HPP (Modal)</th>
                <th style="width: 15%;" class="text-right">Laba (Profit)</th>
            </tr>
        </thead>
        <tbody>
            @forelse(($data ?? []) as $index => $row)
            @php
                $harga = $row->total_harga ?? 0;
                $hpp = $row->total_hpp ?? 0;
                $laba = $harga - $hpp;
            @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y H:i') }}</td>
                <td class="font-bold">{{ $row->no_pesanan ?? $row->kode_transaksi }}</td>
                <td class="text-center">
                    @if(($row->platform ?? '') == 'shopee')
                        <span class="badge bg-shopee">Shopee</span>
                    @elseif(($row->platform ?? '') == 'tiktok')
                        <span class="badge bg-tiktok">TikTok</span>
                    @else
                        <span class="badge bg-pos">POS / Kasir</span>
                    @endif
                </td>
                <td class="text-right">Rp {{ number_format($harga, 0, ',', '.') }}</td>
                <td class="text-right" style="color: #666;">Rp {{ number_format($hpp, 0, ',', '.') }}</td>
                <td class="text-right font-bold" style="color: {{ $laba >= 0 ? '#00B894' : '#D63031' }};">
                    Rp {{ number_format($laba, 0, ',', '.') }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center" style="padding: 20px; color: #7F8C8D;">
                    Tidak ada data transaksi pada periode yang dipilih.
                </td>
            </tr>
            @endforelse
        </tbody>
        @if(count($data ?? []) > 0)
        <tfoot>
            <tr style="background-color: #E2E8F0; font-weight: bold;">
                <td colspan="4" class="text-right" style="padding: 10px;">TOTAL KESELURUHAN :</td>
                <td class="text-right" style="padding: 10px;">Rp {{ number_format($total_omset ?? 0, 0, ',', '.') }}</td>
                <td class="text-right" style="padding: 10px;">Rp {{ number_format($total_hpp ?? 0, 0, ',', '.') }}</td>
                <td class="text-right" style="padding: 10px; color: #00B894;">
                    Rp {{ number_format(($total_omset ?? 0) - ($total_hpp ?? 0), 0, ',', '.') }}
                </td>
            </tr>
        </tfoot>
        @endif
    </table>

</body>
</html>