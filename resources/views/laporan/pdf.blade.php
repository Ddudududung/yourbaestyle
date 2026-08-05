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
            color: #4D3D43;
            line-height: 1.4;
            background: #ffffff;
        }
        
        /* ============================================================
           Header & Branding
           ============================================================ */
        .header-table {
            width: 100%;
            border-bottom: 2px solid #EC95A8;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .company-name {
            font-size: 22pt;
            font-weight: bold;
            color: #EC95A8;
            letter-spacing: 1px;
        }
        .company-sub {
            font-size: 9.5pt;
            color: #8C7B80;
            margin-top: 3px;
        }
        .report-title {
            text-align: right;
            font-size: 15pt;
            font-weight: bold;
            color: #4D3D43;
            margin-bottom: 5px;
        }
        .report-meta {
            text-align: right;
            font-size: 9pt;
            color: #8C7B80;
        }

        /* ============================================================
           Summary Cards / KPI Table
           ============================================================ */
        .summary-table {
            width: 100%;
            margin-bottom: 25px;
            border-collapse: separate;
            border-spacing: 12px 0;
            table-layout: fixed;
        }
        .summary-box {
            background-color: #FFFFFF;
            border: 1px solid #F7E5EA;
            border-top: 4px solid #EC95A8;
            padding: 12px;
            text-align: center;
            border-radius: 6px;
        }
        .profit-box {
            background-color: #FFFFFF;
            border: 1px solid #E3F2E9;
            border-top: 4px solid #52976D;
        }
        .summary-label {
            font-size: 8pt;
            color: #8C7B80;
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }
        .summary-value {
            font-size: 13pt;
            font-weight: bold;
            color: #4D3D43;
        }
        .profit-label { color: #52976D; }
        .profit-value { color: #37754E; }

        /* ============================================================
           Main Data Table
           ============================================================ */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
            font-size: 9pt;
        }
        .data-table th {
            background-color: #FFFFFF;
            color: #7A686D;
            font-weight: bold;
            text-align: left;
            padding: 10px 8px;
            border: 1px solid #F7E5EA;
            border-bottom: 2px solid #EC95A8;
            text-transform: uppercase;
            font-size: 8.5pt;
        }
        .data-table td {
            padding: 10px 8px;
            border: 1px solid #F7E5EA;
            border-bottom: 1px dashed #F7E5EA;
            color: #4D3D43;
        }
        .data-table tbody tr:nth-child(even) {
            background-color: #FFFFFF;
        }
        .data-table tfoot td {
            background-color: #FFFFFF;
            font-weight: bold;
            color: #4D3D43;
            padding: 10px 8px;
            border-top: 2px solid #EC95A8;
            border-bottom: 2px solid #EC95A8;
        }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .text-success { color: #52976D; font-weight: bold; }
        .text-danger { color: #D63031; font-weight: bold; }
        
        /* ============================================================
           Badges
           ============================================================ */
        .badge {
            padding: 4px 8px;
            font-size: 7.5pt;
            border-radius: 4px;
            font-weight: bold;
            text-transform: uppercase;
            display: inline-block;
        }
        .bg-shopee { background-color: #FFFFFF; color: #EC95A8; border: 1px solid #F7E5EA; }
        .bg-tiktok { background-color: #FFFFFF; color: #4D3D43; border: 1px solid #E9ECEF; }
        .bg-pos { background-color: #FFFFFF; color: #52976D; border: 1px solid #E3F2E9; }

        /* ============================================================
           Footer & Signatures
           ============================================================ */
        .footer-table {
            width: 100%;
            margin-top: 40px;
            page-break-inside: avoid;
        }
        .signature-area {
            text-align: center;
            width: 35%;
            float: right;
            color: #4D3D43;
        }
        .signature-space {
            height: 70px;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <table class="header-table">
        <tr>
            <td style="width: 60%;">
                <div class="company-name">Yourbaestyle</div>
                <div class="company-sub">Fashion Store</div>
                <div class="company-sub">l. Aster No.61, Rancamanyar, Kec. Baleendah, Kabupaten Bandung, Jawa Barat 40375 | Telp: 0812-3456-7890</div>
            </td>
            <td style="width: 40%;">
                <div class="report-title">LAPORAN PENJUALAN</div>
                <div class="report-meta">Periode: <strong>{{ $tgl_mulai ?? '-' }}</strong> s/d <strong>{{ $tgl_selesai ?? '-' }}</strong></div>
                <div class="report-meta">Dicetak Pada: <strong>{{ date('d/m/Y H:i') }}</strong></div>
            </td>
        </tr>
    </table>

    <!-- Ringkasan KPI (Total Pendapatan, HPP, & Laba) -->
    @php
        $totalTransaksi = count($data ?? []);
        $totalOmset = $total_omset ?? 0;
        $totalHpp = $total_hpp ?? 0;
        $totalLaba = $totalOmset - $totalHpp;
    @endphp

    <table class="summary-table">
        <tr>
            <td class="summary-box">
                <div class="summary-label">TOTAL TRANSAKSI</div>
                <div class="summary-value">{{ $totalTransaksi }}</div>
            </td>
            <td class="summary-box">
                <div class="summary-label">TOTAL OMSET (GROSS)</div>
                <div class="summary-value">Rp {{ number_format($totalOmset, 0, ',', '.') }}</div>
            </td>
            <td class="summary-box">
                <div class="summary-label">TOTAL MODAL (HPP)</div>
                <div class="summary-value">Rp {{ number_format($totalHpp, 0, ',', '.') }}</div>
            </td>
            <td class="summary-box profit-box">
                <div class="summary-label profit-label">EST. LABA KOTOR</div>
                <div class="summary-value profit-value">
                    Rp {{ number_format($totalLaba, 0, ',', '.') }}
                </div>
            </td>
        </tr>
    </table>

    <!-- Tabel Data Transaksi -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;" class="text-center">No</th>
                <th style="width: 15%;">Tgl & Waktu</th>
                <th style="width: 22%;">No. Pesanan / TRX</th>
                <th style="width: 13%;" class="text-center">Platform</th>
                <th style="width: 15%;" class="text-right">Total Harga</th>
                <th style="width: 15%;" class="text-right">HPP (Modal)</th>
                <th style="width: 15%;" class="text-right">Laba / Rugi</th>
            </tr>
        </thead>
        <tbody>
            @forelse(($data ?? []) as $index => $row)
            @php
                $harga = $row->total_harga ?? 0;
                $hpp = $row->total_hpp ?? 0;
                $laba = $harga - $hpp;
                $platform = strtolower($row->platform ?? '');
            @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($row->tanggal ?? now())->format('d/m/Y H:i') }}</td>
                <td class="font-bold">{{ $row->no_pesanan ?? $row->kode_transaksi ?? '-' }}</td>
                <td class="text-center">
                    @if($platform == 'shopee')
                        <span class="badge bg-shopee">Shopee</span>
                    @elseif($platform == 'tiktok')
                        <span class="badge bg-tiktok">TikTok</span>
                    @else
                        <span class="badge bg-pos">POS Kasir</span>
                    @endif
                </td>
                <td class="text-right">Rp {{ number_format($harga, 0, ',', '.') }}</td>
                <td class="text-right" style="color: #8C7B80;">Rp {{ number_format($hpp, 0, ',', '.') }}</td>
                <td class="text-right {{ $laba >= 0 ? 'text-success' : 'text-danger' }}">
                    Rp {{ number_format($laba, 0, ',', '.') }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center" style="padding: 20px; color: #8C7B80; font-style: italic;">
                    Tidak ada data transaksi pada periode yang dipilih.
                </td>
            </tr>
            @endforelse
        </tbody>
        
        @if($totalTransaksi > 0)
        <tfoot>
            <tr>
                <td colspan="4" class="text-right">TOTAL KESELURUHAN :</td>
                <td class="text-right">Rp {{ number_format($totalOmset, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($totalHpp, 0, ',', '.') }}</td>
                <td class="text-right text-success">
                    Rp {{ number_format($totalLaba, 0, ',', '.') }}
                </td>
            </tr>
        </tfoot>
        @endif
    </table>

</body>
</html>