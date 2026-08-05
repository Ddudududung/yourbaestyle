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
                <div class="company-sub">Fashion Store</div>
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
</html><!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Penjualan - Yourbaestyle</title>
    <style>
        /* ============================================================
           PAGE LAYOUT & BASE STYLES
           ============================================================ */
        @page {
            size: A4;
            margin: 15mm 15mm 20mm 15mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10pt;
            color: #333333;
            line-height: 1.4;
            background: #ffffff;
        }

        /* ============================================================
           UTILITY CLASSES
           ============================================================ */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .font-normal { font-weight: normal; }
        .uppercase { text-transform: uppercase; }

        /* ============================================================
           HEADER SECTION
           ============================================================ */
        .header-wrapper {
            width: 100%;
            border-bottom: 2px solid #2C3E50;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header-left {
            width: 60%;
            display: inline-block;
            vertical-align: top;
        }

        .header-right {
            width: 40%;
            display: inline-block;
            vertical-align: top;
        }

        .company-name {
            font-size: 20pt;
            font-weight: bold;
            color: #2C3E50;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 3px;
        }

        .company-meta {
            font-size: 9pt;
            color: #7F8C8D;
            line-height: 1.6;
        }

        .report-title {
            text-align: right;
            font-size: 14pt;
            font-weight: bold;
            color: #2C3E50;
            margin-bottom: 8px;
        }

        .report-meta {
            text-align: right;
            font-size: 9pt;
            color: #555555;
            line-height: 1.6;
        }

        /* ============================================================
           SUMMARY / KPI BOXES
           ============================================================ */
        .summary-wrapper {
            width: 100%;
            margin-bottom: 20px;
            display: table;
            table-layout: fixed;
            border-spacing: 10px 0;
        }

        .summary-box {
            display: table-cell;
            width: 25%;
            background-color: #F8F9FA;
            border: 1px solid #E9ECEF;
            border-left: 4px solid #2C3E50;
            padding: 12px;
            text-align: center;
            border-radius: 3px;
        }

        .summary-box.color-blue { border-left-color: #0984E3; }
        .summary-box.color-purple { border-left-color: #6C5CE7; }
        .summary-box.color-red { border-left-color: #E17055; }
        .summary-box.color-green {
            border-left-color: #00B894;
            background-color: #EAFAF1;
        }

        .summary-label {
            font-size: 8pt;
            color: #6C757D;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .summary-box.color-green .summary-label {
            color: #00B894;
        }

        .summary-value {
            font-size: 12pt;
            font-weight: bold;
            color: #2C3E50;
        }

        .summary-box.color-green .summary-value {
            color: #00B894;
        }

        /* ============================================================
           DATA TABLE
           ============================================================ */
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
            padding: 10px 8px;
            border: 1px solid #2C3E50;
        }

        .data-table td {
            padding: 8px;
            border: 1px solid #DEE2E6;
        }

        .data-table tbody tr:nth-child(even) {
            background-color: #F8F9FA;
        }

        .data-table tbody tr:hover {
            background-color: #F0F0F0;
        }

        .data-table tfoot tr {
            background-color: #E2E8F0;
            font-weight: bold;
        }

        .data-table tfoot td {
            padding: 10px 8px;
            border: 1px solid #2C3E50;
        }

        /* ============================================================
           BADGES (Platform)
           ============================================================ */
        .badge {
            display: inline-block;
            padding: 4px 8px;
            font-size: 7.5pt;
            border-radius: 3px;
            text-transform: uppercase;
            font-weight: bold;
            border: 1px solid;
        }

        .badge.shopee {
            background-color: #FFE5E5;
            color: #D63031;
            border-color: #FFCCCC;
        }

        .badge.tiktok {
            background-color: #E2E8F0;
            color: #1A202C;
            border-color: #CBD5E1;
        }

        .badge.pos {
            background-color: #E3F2FD;
            color: #0984E3;
            border-color: #BBDEFB;
        }

        /* ============================================================
           PROFIT COLOR INDICATION
           ============================================================ */
        .profit-positive {
            color: #00B894;
            font-weight: bold;
        }

        .profit-negative {
            color: #D63031;
            font-weight: bold;
        }

        /* ============================================================
           EMPTY STATE
           ============================================================ */
        .empty-state {
            padding: 20px !important;
            text-align: center;
            color: #7F8C8D;
            font-style: italic;
        }
    </style>
</head>
<body>

    {{-- ============================================================
        SECTION 1: HEADER & BRANDING
        ============================================================ --}}
    <div class="header-wrapper">
        <div class="header-left">
            <div class="company-name">Yourbaestyle</div>
            <div class="company-meta">
                <div>Thrift & Rebranding Fashion Store</div>
                <div>Jl. Contoh Alamat No. 123, Bandung | Telp: 0812-3456-7890</div>
            </div>
        </div>

        <div class="header-right">
            <div class="report-title">LAPORAN PENJUALAN</div>
            <div class="report-meta">
                <div>Periode: <strong>{{ $tgl_mulai ?? '-' }}</strong> s/d <strong>{{ $tgl_selesai ?? '-' }}</strong></div>
                <div>Dicetak Pada: <strong>{{ date('d/m/Y H:i') }}</strong></div>
            </div>
        </div>
    </div>

    {{-- ============================================================
        SECTION 2: KPI SUMMARY BOXES
        ============================================================ --}}
    <div class="summary-wrapper">
        @php
            $totalTransaksi = count($data ?? []);
            $totalOmset = $total_omset ?? 0;
            $totalHpp = $total_hpp ?? 0;
            $totalLaba = $totalOmset - $totalHpp;
        @endphp

        <div class="summary-box color-blue">
            <div class="summary-label">📊 Total Transaksi</div>
            <div class="summary-value">{{ $totalTransaksi }}</div>
        </div>

        <div class="summary-box color-purple">
            <div class="summary-label">💰 Total Omset (Gross)</div>
            <div class="summary-value">Rp {{ number_format($totalOmset, 0, ',', '.') }}</div>
        </div>

        <div class="summary-box color-red">
            <div class="summary-label">📦 Total Modal (HPP)</div>
            <div class="summary-value">Rp {{ number_format($totalHpp, 0, ',', '.') }}</div>
        </div>

        <div class="summary-box color-green">
            <div class="summary-label">✓ Est. Laba Kotor</div>
            <div class="summary-value">Rp {{ number_format($totalLaba, 0, ',', '.') }}</div>
        </div>
    </div>

    {{-- ============================================================
        SECTION 3: DATA TABLE - TRANSACTIONS
        ============================================================ --}}
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;" class="text-center">No</th>
                <th style="width: 15%;">Tanggal & Waktu</th>
                <th style="width: 20%;">No. Pesanan / TRX</th>
                <th style="width: 12%;" class="text-center">Platform</th>
                <th style="width: 16%;" class="text-right">Total Harga</th>
                <th style="width: 16%;" class="text-right">HPP (Modal)</th>
                <th style="width: 16%;" class="text-right">Laba / Rugi</th>
            </tr>
        </thead>

        <tbody>
            @forelse(($data ?? []) as $index => $row)
                @php
                    $harga = $row->total_harga ?? 0;
                    $hpp = $row->total_hpp ?? 0;
                    $laba = $harga - $hpp;
                    $platform = $row->platform ?? 'offline';
                    $tanggal = \Carbon\Carbon::parse($row->tanggal ?? now())->format('d/m/Y H:i');
                @endphp

                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $tanggal }}</td>
                    <td class="font-bold">{{ $row->no_pesanan ?? $row->kode_transaksi ?? '-' }}</td>

                    <td class="text-center">
                        @if($platform === 'shopee')
                            <span class="badge shopee">Shopee</span>
                        @elseif($platform === 'tiktok')
                            <span class="badge tiktok">TikTok</span>
                        @else
                            <span class="badge pos">POS</span>
                        @endif
                    </td>

                    <td class="text-right">Rp {{ number_format($harga, 0, ',', '.') }}</td>
                    <td class="text-right" style="color: #666;">Rp {{ number_format($hpp, 0, ',', '.') }}</td>

                    <td class="text-right {{ $laba >= 0 ? 'profit-positive' : 'profit-negative' }}">
                        Rp {{ number_format($laba, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="empty-state">
                        ℹ️ Tidak ada data transaksi pada periode yang dipilih.
                    </td>
                </tr>
            @endforelse
        </tbody>

        {{-- FOOTER: Total Row --}}
        @if(count($data ?? []) > 0)
            <tfoot>
                <tr>
                    <td colspan="4" class="text-right">TOTAL KESELURUHAN:</td>
                    <td class="text-right">Rp {{ number_format($totalOmset, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($totalHpp, 0, ',', '.') }}</td>
                    <td class="text-right profit-positive">
                        Rp {{ number_format($totalLaba, 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        @endif
    </table>

</body>
</html>