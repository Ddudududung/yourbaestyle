<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\PesananOnline;
use App\Models\DetailPesananOnline;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel; 
use App\Models\KatalogLive;
use App\Models\ReviewMappingManual; 

class PesananOnlineController extends Controller
{
    /**
     * 🆕 HELPER: Cari header column dengan flexible matching
     */
    private function findColumnKey(array $headers, array $aliases): ?string
    {
        foreach ($aliases as $alias) {
            foreach ($headers as $key) {
                if (strtolower(trim($key ?? '')) === strtolower(trim($alias ?? ''))) {
                    return $key;
                }
            }
        }
        return null;
    }

    /**
     * 🆕 HELPER: Get value dari data array dengan multiple alias support
     */
    private function getValueFromData(array $data, array $aliases, $default = null)
    {
        $key = $this->findColumnKey(array_keys($data), $aliases);
        return $key ? ($data[$key] ?? $default) : $default;
    }

    public function importForm()
    {
        return view('pesanan.import');
    }

    public function preview(Request $request)
    {
        $request->validate([
            'platform'   => 'required|in:shopee,tiktok',
            'format_csv' => 'required|in:shopee_standard,shopee_hemat_kargo,tiktok',
            'file_csv'   => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        $file      = $request->file('file_csv');
        $platform  = $request->platform;
        $formatCsv = $request->format_csv;

        try {
            $rows = Excel::toArray(new \stdClass(), $file)[0];
            
            if (empty($rows)) {
                return back()->with('error', 'File Excel kosong atau tidak terbaca.');
            }
            
            $header = array_shift($rows);

            $parsed = [];
            $parseErrors = [];
            
            foreach ($rows as $rowIdx => $row) {
                if (count($row) > count($header)) {
                    $row = array_slice($row, 0, count($header));
                } else {
                    $row = array_pad($row, count($header), null);
                }

                $data = array_combine($header, $row);

                $hasil = match ($formatCsv) {
                    'shopee_standard'    => $this->parseShopeeStandardRobust($data),
                    'shopee_hemat_kargo' => $this->parseShopeeHematKargoRobust($data),
                    'tiktok'             => $this->parseTiktokRobust($data),
                    default              => null,
                };

                if ($hasil) {
                    $parsed[] = $hasil;
                } else {
                    $parseErrors[] = $rowIdx + 2;
                }
            }

            if (empty($parsed)) {
                $errorMsg = 'Tidak ada baris yang berhasil di-parse. ';
                $errorMsg .= 'Periksa: 1) Header kolom sesuai format? ';
                $errorMsg .= '2) Ada kolom No. Pesanan/Order ID? ';
                $errorMsg .= '3) Data di kolom wajib lengkap?';
                
                if (!empty($parseErrors)) {
                    $errorMsg .= ' (Baris error: ' . implode(', ', array_slice($parseErrors, 0, 5)) . ')';
                }
                
                Log::error('Parse error - no rows parsed', [
                    'platform' => $platform,
                    'format' => $formatCsv,
                    'file' => $file->getClientOriginalName(),
                    'error_rows' => $parseErrors,
                ]);
                
                return back()->with('error', $errorMsg);
            }

            // Deteksi mapping otomatis
            $tanggalLiveDefault = date('Y-m-d');
            
            foreach ($parsed as &$item) {
                $variasiLive = $item['variasi'];
                
                if (!empty($variasiLive)) {
                    $katalog = KatalogLive::where('kode_live', $variasiLive)
                                          ->whereDate('tanggal_live', $tanggalLiveDefault)
                                          ->with('produk')
                                          ->first();
                    
                    if ($katalog && $katalog->produk && $katalog->produk->status === 'aktif') {
                        $item['mapping_status'] = 'auto_found';
                        $item['id_produk_auto'] = $katalog->produk->id;
                        $item['nama_produk_auto'] = $katalog->produk->nama_produk;
                    } else {
                        $item['mapping_status'] = 'not_found';
                        $item['id_produk_auto'] = null;
                    }
                } else {
                    $item['mapping_status'] = 'no_kode';
                    $item['id_produk_auto'] = null;
                }
            }
            
            $produkAktif = Produk::where('status', 'aktif')->orderBy('nama_produk', 'asc')->get();
            session(['csv_platform' => $platform, 'csv_format' => $formatCsv]);

            Log::info('Preview success', ['rows_parsed' => count($parsed), 'platform' => $platform]);

            return view('pesanan.preview', compact('parsed', 'platform', 'formatCsv', 'produkAktif'));
            
        } catch (\Exception $e) {
            Log::error('Preview error', [
                'error' => $e->getMessage(),
                'file' => $file->getClientOriginalName(),
                'line' => $e->getLine(),
            ]);
            
            return back()->with('error', 'Error membaca file: ' . $e->getMessage());
        }
    }

    /**
     * 🆕 ROBUST PARSER: Shopee Standard
     */
    private function parseShopeeStandardRobust(array $data): ?array
    {
        $noPesanan = trim((string)$this->getValueFromData($data, [
            'No. Pesanan', 'No.Pesanan', 'NoPesanan',
            'Order ID', 'OrderID', 'order_id',
            'Nomor Pesanan', 'ID Pesanan'
        ], ''));
        
        if (empty($noPesanan)) return null;

        return [
            'no_pesanan'   => $noPesanan,
            'nama_pembeli' => trim((string)$this->getValueFromData($data, [
                'Nama Penerima', 'NamaPenerima',
                'Recipient', 'Pembeli',
                'Username (Pembeli)', 'Buyer Name', 'buyer_name'
            ], '-')),
            'tanggal'      => $this->parseTanggal($this->getValueFromData($data, [
                'Waktu Pesanan Dibuat', 'WaktuPesananDibuat',
                'Tanggal Pesanan', 'TanggalPesanan',
                'Created Time', 'Order Date', 'Waktu'
            ])),
            'nama_produk'  => trim((string)$this->getValueFromData($data, [
                'Nama Produk', 'NamaProduk',
                'Product Name', 'Product', 'Produk'
            ], '')),
            'variasi'      => trim((string)$this->getValueFromData($data, [
                'Nama Variasi', 'NamaVariasi',
                'Variasi', 'SKU Name', 'SKU', 'Varian',
                'Variant', 'SKU Code'
            ], '')) ?: null,
            'qty'          => (int)($this->getValueFromData($data, [
                'Jumlah', 'Quantity', 'Qty',
                'Jumlah Produk', 'JumlahProduk'
            ], 1)),
            'harga_satuan' => $this->parseHarga($this->getValueFromData($data, [
                'Harga Setelah Diskon', 'HargaSetelahDiskon',
                'Unit Price', 'Price', 'Harga',
                'Harga per Item'
            ], 0)),
            'total_harga'  => $this->parseHarga($this->getValueFromData($data, [
                'Total Pembayaran', 'TotalPembayaran',
                'Total', 'Order Amount', 'Total Price',
                'Subtotal'
            ], 0)),
            'ongkir'       => $this->parseHarga($this->getValueFromData($data, [
                'Ongkos Kirim Dibayar oleh Pembeli', 'OngkosKirim',
                'Shipping Fee', 'Ongkir', 'Biaya Kirim',
                'Shipping Cost'
            ], 0)),
            'no_resi'      => trim((string)$this->getValueFromData($data, [
                'No. Resi', 'NoResi', 'Tracking Number',
                'Resi', 'Tracking Code'
            ], '')) ?: null,
        ];
    }

    private function parseShopeeHematKargoRobust(array $data): ?array
    {
        $noPesanan = trim((string)$this->getValueFromData($data, [
            'order_sn', 'Order SN', 'OrderSN',
            'No. Pesanan', 'Order ID'
        ], ''));
        
        if (empty($noPesanan)) return null;

        $info = (string)$this->getValueFromData($data, [
            'product_info', 'Product Info', 'ProductInfo'
        ], '');
        
        $namaProduk = $this->ekstrakField($info, 'Nama Produk');
        $variasi = $this->ekstrakField($info, 'Nama Variasi');
        $hargaRaw = $this->ekstrakField($info, 'Harga');
        $jumlahRaw = $this->ekstrakField($info, 'Jumlah');
        $harga = $this->parseHarga($hargaRaw);
        $qty = max(1, (int)($jumlahRaw ?: 1));

        return [
            'no_pesanan'   => $noPesanan,
            'nama_pembeli' => trim((string)$this->getValueFromData($data, [
                'order_receiver_name', 'buyer_user_name',
                'Nama Penerima', 'Pembeli'
            ], '-')),
            'tanggal'      => $this->parseTanggal($this->getValueFromData($data, [
                'order_creation_date', 'created_date',
                'Tanggal'
            ])),
            'nama_produk'  => $namaProduk ?? '',
            'variasi'      => $variasi,
            'qty'          => $qty,
            'harga_satuan' => $harga,
            'total_harga'  => $harga * $qty,
            'ongkir'       => 0,
            'no_resi'      => trim((string)$this->getValueFromData($data, [
                'tracking_number', 'Tracking Number'
            ], '')) ?: null,
        ];
    }

    private function parseTiktokRobust(array $data): ?array
    {
        $noPesanan = trim((string)$this->getValueFromData($data, [
            'Order ID', 'OrderID', 'order_id',
            'No. Pesanan', 'Order Number'
        ], ''));
        
        if (empty($noPesanan)) return null;

        return [
            'no_pesanan'   => $noPesanan,
            'nama_pembeli' => trim((string)$this->getValueFromData($data, [
                'Recipient', 'Buyer Name', 'buyer_name',
                'Pembeli', 'Customer'
            ], '-')),
            'tanggal'      => $this->parseTanggal($this->getValueFromData($data, [
                'Created Time', 'created_time',
                'Order Date', 'Tanggal'
            ])),
            'nama_produk'  => trim((string)$this->getValueFromData($data, [
                'Product Name', 'product_name',
                'Produk', 'Item'
            ], '')),
            'variasi'      => trim((string)$this->getValueFromData($data, [
                'SKU Name', 'sku_name', 'SKU',
                'Variasi', 'Variant'
            ], '')) ?: null,
            'qty'          => (int)($this->getValueFromData($data, [
                'Quantity', 'quantity', 'Qty', 'Jumlah'
            ], 1)),
            'harga_satuan' => $this->parseHarga($this->getValueFromData($data, [
                'Unit Price', 'unit_price', 'Price', 'Harga'
            ], 0)),
            'total_harga'  => $this->parseHarga($this->getValueFromData($data, [
                'Order Amount', 'order_amount', 'Total', 'Total Price'
            ], 0)),
            'ongkir'       => $this->parseHarga($this->getValueFromData($data, [
                'Shipping Fee', 'shipping_fee', 'Ongkir', 'Biaya Kirim'
            ], 0)),
            'no_resi'      => trim((string)$this->getValueFromData($data, [
                'Tracking Number', 'tracking_number',
                'Resi', 'Tracking Code'
            ], '')) ?: null,
        ];
    }

    /**
     * 🆕 DEBUG ENDPOINT
     */
    public function debugParse(Request $request)
    {
        $request->validate([
            'file_csv' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        $file = $request->file('file_csv');
        
        try {
            $allData = Excel::toArray(new \stdClass(), $file);
            
            return response()->json([
                'status' => 'success',
                'total_sheets' => count($allData),
                'sheet_0_info' => [
                    'total_rows' => count($allData[0] ?? []),
                    'header' => $allData[0][0] ?? [],
                    'first_data_row' => $allData[0][1] ?? null,
                    'second_data_row' => $allData[0][2] ?? null,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * 🆕 IMPORT PROSES - DENGAN VERBOSE ERROR HANDLING
     */
    public function importProses(Request $request)
    {
        Log::info('=== IMPORT PROSES START ===', [
            'user_id' => Auth::id(),
            'timestamp' => now(),
        ]);

        $pesananDataStr = $request->input('data_pesanan_mentah');
        
        if (empty($pesananDataStr)) {
            Log::warning('No data_pesanan_mentah provided');
            return redirect()->route('pesanan.import')
                ->with('error', 'Tidak ada data pesanan mentah yang diproses. Silakan upload file lagi.');
        }

        try {
            $pesananData = json_decode($pesananDataStr, true);
            
            if (!is_array($pesananData)) {
                throw new \Exception('Data pesanan bukan array yang valid');
            }
            
            Log::info('Data pesanan decoded', ['count' => count($pesananData)]);
            
        } catch (\Exception $e) {
            Log::error('JSON decode error', ['error' => $e->getMessage()]);
            return redirect()->route('pesanan.import')
                ->with('error', 'Error parsing data pesanan: ' . $e->getMessage());
        }

        $platform = session('csv_platform');
        $tanggalLive = $request->input('tanggal_live');
        
        if (empty($platform)) {
            Log::error('Platform not in session');
            return redirect()->route('pesanan.import')
                ->with('error', 'Session expired. Silakan upload file lagi.');
        }
        
        if (empty($tanggalLive)) {
            Log::warning('tanggal_live not provided');
            return back()->with('error', 'Tanggal Live wajib diisi!');
        }

        Log::info('Import parameters', [
            'platform' => $platform,
            'tanggal_live' => $tanggalLive,
            'total_pesanan' => count($pesananData),
        ]);

        $mappingDataStr = $request->input('mapping_data');
        $mappingData = $mappingDataStr ? json_decode($mappingDataStr, true) : [];

        DB::beginTransaction();
        try {
            $pesananBerhasil = 0;
            $pesananGagal = 0;
            $pesananSkip = 0;

            foreach ($pesananData as $idx => $row) {
                try {
                    $variasiLive = $row['variasi'];
                    $noPesanan = $row['no_pesanan'];
                    $produk = null;
                    
                    Log::debug("Processing pesanan {$noPesanan}", [
                        'kode_variasi' => $variasiLive,
                        'qty' => $row['qty'],
                    ]);

                    // 1. Cek mapping dari dropdown
                    if (isset($mappingData[$idx]) && !empty($mappingData[$idx])) {
                        $idProdukPilih = $mappingData[$idx];
                        
                        if ($idProdukPilih === 'skip') {
                            Log::info("Pesanan {$noPesanan} skipped by user");
                            $pesananSkip++;
                            continue;
                        }
                        
                        $produk = Produk::find($idProdukPilih);
                        if ($produk) {
                            Log::debug("Found from mapping dropdown: {$produk->nama_produk}");
                        }
                    } 
                    // 2. Cek dari auto_found
                    else if (isset($row['mapping_status']) && $row['mapping_status'] === 'auto_found') {
                        $produk = Produk::find($row['id_produk_auto'] ?? null);
                        if ($produk) {
                            Log::debug("Found from auto-detection: {$produk->nama_produk}");
                        }
                    }
                    // 3. Fallback ke katalog_live
                    else if (!empty($variasiLive)) {
                        $kamus = KatalogLive::where('kode_live', $variasiLive)
                                            ->whereDate('tanggal_live', \Carbon\Carbon::parse($tanggalLive)->toDateString())
                                            ->with('produk')
                                            ->first();
                        
                        if ($kamus && $kamus->produk) {
                            $produk = $kamus->produk;
                            Log::debug("Found from katalog_live: {$produk->nama_produk}");
                        }
                    }

                    // JIKA TETAP TIDAK ADA MAPPING
                    if (!$produk) {
                        Log::warning("No product found for pesanan {$noPesanan}, kode: {$variasiLive}");
                        $pesananGagal++;
                        
                        // Catat ke review_mapping_manual
                        ReviewMappingManual::create([
                            'tanggal_live'     => $tanggalLive,
                            'platform'         => $platform,
                            'kode_live_raw'    => $variasiLive,
                            'qty_order'        => $row['qty'],
                            'no_pesanan'       => $noPesanan,
                            'nama_pembeli'     => $row['nama_pembeli'],
                            'id_user_uploader' => Auth::id() ?? 1,
                            'status_resolusi'  => 'pending'
                        ]);

                        $pesanan = PesananOnline::create([
                            'no_pesanan'     => $noPesanan,
                            'id_user'        => Auth::id() ?? 1,
                            'platform'       => $platform,
                            'nama_pembeli'   => $row['nama_pembeli'],
                            'tanggal'        => $row['tanggal'],
                            'total_harga'    => $row['total_harga'],
                            'total_hpp'      => 0,
                            'status'         => 'hold_review',
                            'kode_live_raw'  => $variasiLive,
                            'status_mapping' => 'gagal_kode',
                            'pesan_mapping'  => 'Kode variasi [' . $variasiLive . '] tidak ter-map.'
                        ]);

                        DetailPesananOnline::create([
                            'id_pesanan'   => $pesanan->id,
                            'id_produk'    => null,
                            'qty'          => $row['qty'],
                            'harga_satuan' => $row['harga_satuan'],
                            'hpp_satuan'   => 0,
                            'variasi'      => $variasiLive,
                        ]);

                        continue;
                    }

                    // VALIDASI STOK
                    if ($produk->stok < $row['qty']) {
                        Log::error("Stok tidak cukup untuk {$produk->nama_produk}", [
                            'required' => $row['qty'],
                            'available' => $produk->stok,
                            'pesanan' => $noPesanan,
                        ]);
                        throw new \Exception("Stok untuk {$produk->nama_produk} (Kode: {$variasiLive}) tidak mencukupi! Sisa: {$produk->stok}, Diminta: {$row['qty']}");
                    }

                    // SIMPAN PESANAN (BERHASIL)
                    $pesanan = PesananOnline::create([
                        'no_pesanan'     => $noPesanan,
                        'id_user'        => Auth::id(),
                        'platform'       => $platform,
                        'nama_pembeli'   => $row['nama_pembeli'],
                        'tanggal'        => $row['tanggal'],
                        'total_harga'    => $row['total_harga'],
                        'total_hpp'      => $produk->hpp_otomatis * $row['qty'],
                        'status'         => 'diproses',
                        'status_mapping' => 'sukses',
                        'pesan_mapping'  => 'Berhasil dimapping otomatis',
                    ]);

                    DetailPesananOnline::create([
                        'id_pesanan'   => $pesanan->id,
                        'id_produk'    => $produk->id,
                        'qty'          => $row['qty'],
                        'harga_satuan' => $row['harga_satuan'],
                        'hpp_satuan'   => $produk->hpp_otomatis,
                        'variasi'      => $variasiLive,
                    ]);

                    // POTONG STOK
                    $produk->decrement('stok', $row['qty']);
                    
                    // SIMPAN KE KATALOG_LIVE
                    KatalogLive::firstOrCreate(
                        ['kode_live' => $variasiLive, 'tanggal_live' => date('Y-m-d', strtotime($tanggalLive))],
                        ['id_produk' => $produk->id, 'harga_live' => $produk->harga_jual]
                    );

                    $pesananBerhasil++;
                    Log::info("Pesanan {$noPesanan} berhasil disimpan");

                } catch (\Exception $e) {
                    Log::error("Error processing row {$idx}", [
                        'error' => $e->getMessage(),
                        'pesanan' => $row['no_pesanan'] ?? 'unknown',
                    ]);
                    throw $e; // Re-throw untuk rollback transaction
                }
            }

            DB::commit();

            $pesan = "✅ Import selesai! Berhasil: {$pesananBerhasil}, Gagal: {$pesananGagal}";
            if ($pesananSkip > 0) {
                $pesan .= ", Dilewati: {$pesananSkip}";
            }

            Log::info('Import completed successfully', [
                'berhasil' => $pesananBerhasil,
                'gagal' => $pesananGagal,
                'skip' => $pesananSkip,
            ]);

            session()->forget(['csv_platform', 'csv_format']);

            return redirect()->route('pesanan.index')->with('success', $pesan);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Import proses failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return back()->with('error', '❌ Gagal Menyimpan: ' . $e->getMessage());
        }
    }

    // ============================================================
    // HELPERS
    // ============================================================
    private function ekstrakField(string $text, string $key): ?string
    {
        $pattern = '/' . preg_quote($key, '/') . '\s*:\s*([^;]*)/i';
        return preg_match($pattern, $text, $m) ? trim($m[1]) : null;
    }

    private function parseHarga($value): float
    {
        if ($value === null) return 0;
        $clean = preg_replace('/[^0-9]/', '', (string)$value);
        return (float)($clean ?: 0);
    }

    private function parseTanggal($value): string
    {
        if (empty($value)) return now()->toDateTimeString();
        if (is_numeric($value)) {
            try {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)
                         ->format('Y-m-d H:i:s');
            } catch (\Exception $e) {}
        }
        try {
            return Carbon::parse($value)->toDateTimeString();
        } catch (\Exception $e) {
            return now()->toDateTimeString();
        }
    }

    // ============================================================
    // INDEX, SHOW, REVIEW MANUAL
    // ============================================================
    public function index(Request $request)
    {
        $query = PesananOnline::with('user');

        if ($request->filled('platform'))
            $query->where('platform', $request->platform);
        if ($request->filled('status'))
            $query->where('status', $request->status);
        if ($request->filled('tanggal'))
            $query->whereDate('tanggal', $request->tanggal);
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('no_pesanan', 'like', '%' . $request->search . '%')
                  ->orWhere('nama_pembeli', 'like', '%' . $request->search . '%');
            });
        }

        $pesanan = $query->orderBy('tanggal', 'desc')->paginate(15);
        return view('pesanan.index', compact('pesanan'));
    }

    public function show(string $id)
    {
        $pesanan = PesananOnline::with(['user', 'detail.produk'])->findOrFail($id);
        return view('pesanan.show', compact('pesanan'));
    }
    
    public function reviewManual()
    {
        $reviews = ReviewMappingManual::where('status_resolusi', 'pending')
                    ->orderBy('tanggal_live', 'desc')
                    ->paginate(10);

        $produks = Produk::where('status', 'aktif')->orderBy('nama_produk', 'asc')->get();
        return view('pesanan.review_manual', compact('reviews', 'produks'));
    }

    public function resolveManual(Request $request, $id)
    {
        $review = ReviewMappingManual::findOrFail($id);
        $action = $request->input('action');

        if ($action === 'mapped') {
            $request->validate([
                'id_produk_mapping' => 'required|exists:produk,id'
            ]);

            $produk = Produk::findOrFail($request->id_produk_mapping);

            if ($produk->stok < $review->qty_order) {
                return redirect()->back()->with('error', 'Stok produk tidak mencukupi!');
            }

            $review->update([
                'id_produk_mapping' => $produk->id,
                'status_resolusi'   => 'mapped',
                'id_user_resolver'  => Auth::id() ?? 1,
                'tanggal_resolusi'  => now(),
            ]);

            $pesanan = PesananOnline::where('no_pesanan', $review->no_pesanan)->first();
            if ($pesanan) {
                $pesanan->update([
                    'status_mapping' => 'manual',
                    'status'         => 'diproses',
                    'total_hpp'      => $produk->hpp_otomatis * $review->qty_order
                ]);

                DetailPesananOnline::where('id_pesanan', $pesanan->id)->update([
                    'id_produk'  => $produk->id,
                    'hpp_satuan' => $produk->hpp_otomatis
                ]);
            }

            $produk->decrement('stok', $review->qty_order);

            KatalogLive::firstOrCreate(
                ['kode_live' => $review->kode_live_raw, 'tanggal_live' => $review->tanggal_live],
                ['id_produk' => $produk->id, 'harga_live' => $produk->harga_jual]
            );

            Log::info("Manual mapping resolved for {$review->no_pesanan}");

            return redirect()->back()->with('success', 'Mapping berhasil!');
        }

        elseif ($action === 'skip') {
            $review->update([
                'status_resolusi'  => 'skip',
                'id_user_resolver' => Auth::id() ?? 1,
                'tanggal_resolusi' => now(),
            ]);
            return redirect()->back()->with('success', 'Dilewati.');
        }

        elseif ($action === 'reject') {
            $review->update([
                'status_resolusi'  => 'reject',
                'id_user_resolver' => Auth::id() ?? 1,
                'tanggal_resolusi' => now(),
            ]);
            PesananOnline::where('no_pesanan', $review->no_pesanan)->update(['status' => 'cancel']);
            return redirect()->back()->with('success', 'Ditolak.');
        }

        return redirect()->back();
    }
}