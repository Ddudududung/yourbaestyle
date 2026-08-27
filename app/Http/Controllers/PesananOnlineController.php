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
    private function findColumnKey(array $headers, array $aliases): ?string
{
    foreach ($aliases as $alias) {
        foreach ($headers as $key) {
            // Case-insensitive matching
            if (strtolower(trim($key ?? '')) === strtolower(trim($alias ?? ''))) {
                return $key;
            }
        }
    }
    return null;
}

    /** Helper: Ambil value dari data array dengan multiple alias support*/
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
    // Validasi input
    $request->validate([
        'platform'   => 'required|in:shopee,tiktok',
        'format_csv' => 'required|in:shopee_standard,shopee_hemat_kargo,tiktok',
        'file_csv'   => 'required|file|mimes:xlsx,xls,csv|max:5120',
    ]);

    $file      = $request->file('file_csv');
    $platform  = $request->platform;
    $formatCsv = $request->format_csv;

    try {
        // Parse file Excel
        $rows = Excel::toArray(new \stdClass(), $file)[0];
        
        if (empty($rows)) {
            return back()->with('error', 'File Excel kosong atau tidak terbaca.');
        }
        
        // Pisahkan header dari data
        $header = array_shift($rows);

        $parsed = [];
        $parseErrors = [];
        
        // Parse setiap baris data
        foreach ($rows as $rowIdx => $row) {
            if (count($row) > count($header)) {
                $row = array_slice($row, 0, count($header));
            } else {
                $row = array_pad($row, count($header), null);
            }

            $data = array_combine($header, $row);

            // Pilih parser sesuai format platform
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

        // Cek kelengkapan data hasil parse
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

        // Deteksi mapping otomatis berdasarkan katalog live
        $tanggalLiveDefault = date('Y-m-d');
        
        foreach ($parsed as &$item) {
            $noPesananCheck = $item['no_pesanan'] ?? null;
            $item['is_duplicate'] = !empty($noPesananCheck) && PesananOnline::where('no_pesanan', $noPesananCheck)->exists();

            $variasiLive = $item['variasi'];
            
            if (!empty($variasiLive)) {
                // 1. Match persis kode_live pada tanggal hari ini
                $katalog = KatalogLive::where('kode_live', $variasiLive)
                                      ->whereDate('tanggal_live', $tanggalLiveDefault)
                                      ->with('produk')
                                      ->first();

                // 2. Fallback: Match persis kode_live tanpa batasan tanggal hari ini (live sebelumnya)
                if (!$katalog) {
                    $katalog = KatalogLive::where('kode_live', $variasiLive)
                                          ->orderBy('created_at', 'desc')
                                          ->with('produk')
                                          ->first();
                }

                // 3. Fallback: Ekstrak nomor dari "IYB 3" -> "3" atau sebaliknya
                if (!$katalog && preg_match('/(\d+)/', $variasiLive, $m)) {
                    $numCode = $m[1];
                    $katalog = KatalogLive::where('kode_live', $numCode)
                                          ->orWhere('kode_live', 'IYB ' . $numCode)
                                          ->orWhere('kode_live', 'IYB-' . $numCode)
                                          ->orderBy('created_at', 'desc')
                                          ->with('produk')
                                          ->first();
                }
                
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
        
        // Ambil data produk aktif untuk manual mapping
        $produkAktif = Produk::where('status', 'aktif')
                             ->orderBy('nama_produk', 'asc')
                             ->get();
        
        // Simpan info session platform
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

    /** Helper: Cek apakah baris merupakan header/deskripsi dummy */
    private function isDummyHeaderRow(string $noPesanan, ?string $variasi = null): bool
    {
        $no = strtolower(trim($noPesanan));
        $var = strtolower(trim($variasi ?? ''));

        if (empty($no)) return true;

        if (str_contains($no, 'platform') || 
            str_contains($no, 'unique order') || 
            str_contains($no, 'field to explain') || 
            str_contains($no, 'filed to explain') ||
            str_contains($no, 'order status') ||
            str_contains($var, 'sku variation') ||
            str_contains($var, 'platform sku')) {
            return true;
        }

        return false;
    }

    /** Parser: Shopee Standard / Reguler */
    private function parseShopeeStandardRobust(array $data): ?array
    {
        // Cek jika kolom memakai format product_info (seperti order_sn & product_info dari ekspor Shopee Reguler)
        $orderSnCheck = trim((string)$this->getValueFromData($data, ['order_sn', 'Order SN', 'OrderSN'], ''));
        $productInfoCheck = (string)$this->getValueFromData($data, ['product_info', 'Product Info', 'ProductInfo'], '');

        if (!empty($orderSnCheck) || !empty($productInfoCheck)) {
            return $this->parseShopeeHematKargoRobust($data);
        }

        // Extract No. Pesanan
        $noPesanan = trim((string)$this->getValueFromData($data, [
            'No. Pesanan', 'No.Pesanan', 'NoPesanan',
            'Order ID', 'OrderID', 'order_id',
            'Nomor Pesanan', 'ID Pesanan'
        ], ''));
        
        $rawVariasi = trim((string)$this->getValueFromData($data, [
            'Nama Variasi', 'NamaVariasi',
            'Variasi', 'SKU Name', 'SKU', 'Varian',
            'Variant', 'SKU Code', 'Option'
        ], ''));

        // Abaikan baris header / penjelasan dummy
        if ($this->isDummyHeaderRow($noPesanan, $rawVariasi)) {
            return null;
        }

        // Jika variasi panjang (misal "54, NO RETUR..."), ambil kode awalnya
        if (!empty($rawVariasi) && str_contains($rawVariasi, ',')) {
            $parts = explode(',', $rawVariasi);
            $rawVariasi = trim($parts[0]);
        }

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
            'variasi'      => $rawVariasi ?: null,
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
        ];
    }

    private function parseShopeeHematKargoRobust(array $data): ?array
    {
        $noPesanan = trim((string)$this->getValueFromData($data, [
            'order_sn', 'Order SN', 'OrderSN',
            'No. Pesanan', 'Order ID'
        ], ''));
        
        $info = (string)$this->getValueFromData($data, [
            'product_info', 'Product Info', 'ProductInfo'
        ], '');

        $variasi = $this->ekstrakField($info, 'Nama Variasi');

        if ($this->isDummyHeaderRow($noPesanan, $variasi)) return null;
        
        $namaProduk = $this->ekstrakField($info, 'Nama Produk');
        if (!empty($variasi) && str_contains($variasi, ',')) {
            $parts = explode(',', $variasi);
            $variasi = trim($parts[0]);
        }

        $hargaRaw = $this->ekstrakField($info, 'Harga');
        $jumlahRaw = $this->ekstrakField($info, 'Jumlah');
        $harga = $this->parseHarga($hargaRaw);
        $qty = max(1, (int)($jumlahRaw ?: 1));

        return [
            'no_pesanan'   => $noPesanan,
            'nama_pembeli' => trim((string)$this->getValueFromData($data, [
                'order_receiver_name', 'buyer_user_name',
                'Nama Penerima', 'Pembeli', 'Buyer Name'
            ], '-')),
            'tanggal'      => $this->parseTanggal($this->getValueFromData($data, [
                'order_creation_date', 'created_date',
                'Tanggal'
            ])),
            'nama_produk'  => $namaProduk ?? '',
            'variasi'      => $variasi ?: null,
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

        $rawVariasi = trim((string)$this->getValueFromData($data, [
            'Variation', 'variation', 'SKU Name', 'sku_name', 'SKU',
            'Variasi', 'Variant', 'Seller SKU', 'SellerSKU', 'Platform SKU'
        ], ''));
        
        // Abaikan baris penjelasan header TikTok ("Platform unique order ID.")
        if ($this->isDummyHeaderRow($noPesanan, $rawVariasi)) {
            return null;
        }

        // Jika variasi TikTok berupa "IYB 3, NO RETUR, MEMBELI ARTINYA AGREE", ambil "IYB 3"
        if (!empty($rawVariasi) && str_contains($rawVariasi, ',')) {
            $parts = explode(',', $rawVariasi);
            $rawVariasi = trim($parts[0]);
        }

        return [
            'no_pesanan'   => $noPesanan,
            'nama_pembeli' => trim((string)$this->getValueFromData($data, [
                'Buyer Username', 'buyer_username', 'Recipient',
                'Buyer Name', 'buyer_name', 'Pembeli', 'Customer'
            ], '-')),
            'tanggal'      => $this->parseTanggal($this->getValueFromData($data, [
                'Created Time', 'created_time', 'Paid Time',
                'Order Date', 'Tanggal'
            ])),
            'nama_produk'  => trim((string)$this->getValueFromData($data, [
                'Product Name', 'product_name',
                'Produk', 'Item'
            ], '')),
            'variasi'      => $rawVariasi ?: null,
            'qty'          => max(1, (int)($this->getValueFromData($data, [
                'Quantity', 'quantity', 'Qty', 'Jumlah'
            ], 1))),
            'harga_satuan' => $this->parseHarga($this->getValueFromData($data, [
                'SKU Unit Price', 'Unit Price', 'unit_price', 'Price', 'Harga'
            ], 0)),
            'total_harga'  => $this->parseHarga($this->getValueFromData($data, [
                'Order Amount', 'order_amount', 'SKU Subtotal After Discount', 'Total', 'Total Price'
            ], 0)),
            'ongkir'       => $this->parseHarga($this->getValueFromData($data, [
                'Shipping Fee After Discount', 'Shipping Fee', 'shipping_fee', 'Ongkir', 'Biaya Kirim'
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
        $platform = session('csv_platform') ?? 'shopee';
        $tanggalLiveReq = $request->input('tanggal_live', date('Y-m-d H:i'));

        // Handle both data_pesanan_mentah (JSON string/array) & pesanan (array)
        $dataPesanan = [];
        if ($request->filled('data_pesanan_mentah')) {
            $raw = $request->input('data_pesanan_mentah');
            $dataPesanan = is_string($raw) ? json_decode($raw, true) : $raw;
        } elseif ($request->has('pesanan')) {
            $dataPesanan = $request->input('pesanan');
        }

        if (empty($dataPesanan) || !is_array($dataPesanan)) {
            return back()->with('error', 'Gagal menyimpan: Data pesanan tidak ditemukan atau kosong.');
        }

        $mappingData = [];
        if ($request->filled('mapping_data')) {
            $rawMap = $request->input('mapping_data');
            $mappingData = is_string($rawMap) ? json_decode($rawMap, true) : $rawMap;
        }

        DB::beginTransaction();
        
        try {
            $pesananBerhasil = 0;
            $pesananGagal = 0;
            $pesananSkip = 0;
            $processedOrders = [];

            foreach ($dataPesanan as $idx => $row) {
                $noPesanan = trim((string)($row['no_pesanan'] ?? ''));
                $variasiLive = trim((string)($row['variasi'] ?? ''));
                $tanggalRow = !empty($row['tanggal']) ? $row['tanggal'] : $tanggalLiveReq;

                if (empty($noPesanan) || $this->isDummyHeaderRow($noPesanan, $variasiLive)) {
                    continue;
                }

                $qty = max(1, (int)($row['qty'] ?? 1));
                $hargaSatuan = $this->parseHarga($row['harga_satuan'] ?? 0);
                $totalHarga = $this->parseHarga($row['total_harga'] ?? ($hargaSatuan * $qty));
                $ongkir = $this->parseHarga($row['ongkir'] ?? 0);
                $namaPembeli = trim((string)($row['nama_pembeli'] ?? '-'));
                $noResi = trim((string)($row['no_resi'] ?? '')) ?: null;

                // Tentukan produk yang di-map
                $idProdukMapped = null;
                $userSelection = $mappingData[$idx] ?? null;

                if ($userSelection === 'skip') {
                    $idProdukMapped = null;
                } elseif (!empty($userSelection) && is_numeric($userSelection)) {
                    $idProdukMapped = (int)$userSelection;
                } elseif (!empty($row['id_produk_auto'])) {
                    $idProdukMapped = (int)$row['id_produk_auto'];
                }

                // Fallback pencarian katalog jika belum terpilih
                if (!$idProdukMapped && !empty($variasiLive)) {
                    $katalog = KatalogLive::where('kode_live', $variasiLive)
                                          ->whereDate('tanggal_live', date('Y-m-d', strtotime($tanggalRow)))
                                          ->with('produk')
                                          ->first();

                    if (!$katalog) {
                        $katalog = KatalogLive::where('kode_live', $variasiLive)
                                              ->orderBy('created_at', 'desc')
                                              ->with('produk')
                                              ->first();
                    }

                    if (!$katalog && preg_match('/(\d+)/', $variasiLive, $m)) {
                        $numCode = $m[1];
                        $katalog = KatalogLive::where('kode_live', $numCode)
                                              ->orWhere('kode_live', 'IYB ' . $numCode)
                                              ->orWhere('kode_live', 'IYB-' . $numCode)
                                              ->orderBy('created_at', 'desc')
                                              ->with('produk')
                                              ->first();
                    }

                    if ($katalog && $katalog->produk && $katalog->produk->status === 'aktif') {
                        $idProdukMapped = $katalog->produk->id;
                    }
                }

                $produkObj = $idProdukMapped ? Produk::where('id', $idProdukMapped)->lockForUpdate()->first() : null;

                // Buat atau perbarui record PesananOnline
                $pesanan = PesananOnline::where('no_pesanan', $noPesanan)->first();

                if ($produkObj) {
                    $hppSatuan = $produkObj->hpp_aktif;
                    $totalHpp = $hppSatuan * $qty;

                    $isFirstTimeInBatch = false;

                    if (!$pesanan) {
                        $pesanan = PesananOnline::create([
                            'no_pesanan'     => $noPesanan,
                            'id_user'        => Auth::id(),
                            'platform'       => $platform,
                            'no_resi'        => $noResi,
                            'nama_pembeli'   => $namaPembeli,
                            'tanggal'        => Carbon::parse($tanggalRow),
                            'total_harga'    => $totalHarga,
                            'total_hpp'      => $totalHpp,
                            'ongkir'         => $ongkir,
                            'status'         => 'diproses',
                            'status_mapping' => 'sukses',
                            'pesan_mapping'  => 'Berhasil dimapping',
                        ]);
                        $isFirstTimeInBatch = true;
                    } else {
                        if (!in_array($pesanan->id, $processedOrders)) {
                            // First time encounter in this import batch -> reset totals & details
                            $pesanan->update([
                                'id_user'        => Auth::id() ?? $pesanan->id_user,
                                'platform'       => $platform,
                                'no_resi'        => $noResi ?: $pesanan->no_resi,
                                'nama_pembeli'   => $namaPembeli,
                                'tanggal'        => Carbon::parse($tanggalRow),
                                'total_harga'    => $totalHarga,
                                'total_hpp'      => $totalHpp,
                                'ongkir'         => $ongkir ?: $pesanan->ongkir,
                                'status'         => 'diproses',
                                'status_mapping' => 'sukses',
                                'pesan_mapping'  => 'Berhasil dimapping',
                            ]);
                            DetailPesananOnline::where('id_pesanan', $pesanan->id)->delete();
                            $isFirstTimeInBatch = true;
                        } else {
                            // Subsequent item in the same import batch for this order
                            $pesanan->update([
                                'total_harga'    => $pesanan->total_harga + $totalHarga,
                                'total_hpp'      => $pesanan->total_hpp + $totalHpp,
                            ]);
                        }
                    }

                    if (!in_array($pesanan->id, $processedOrders)) {
                        $processedOrders[] = $pesanan->id;
                    }

                    DetailPesananOnline::create([
                        'id_pesanan'   => $pesanan->id,
                        'id_produk'    => $produkObj->id,
                        'qty'          => $qty,
                        'harga_satuan' => $hargaSatuan > 0 ? $hargaSatuan : ($totalHarga / $qty),
                        'hpp_satuan'   => $hppSatuan,
                        'variasi'      => $variasiLive,
                    ]);

                    // Potong stok produk
                    $produkObj->decrement('stok', $qty);

                    // Update / Buat KatalogLive jika ada kode variasi
                    if (!empty($variasiLive)) {
                        KatalogLive::firstOrCreate(
                            ['kode_live' => $variasiLive, 'tanggal_live' => date('Y-m-d', strtotime($tanggalRow))],
                            ['id_produk' => $produkObj->id, 'harga_live' => $produkObj->harga_jual]
                        );
                    }

                    $pesananBerhasil++;
                    Log::info("Pesanan {$noPesanan} berhasil disimpan");

                } else {
                    // Jika produk tidak ditemukan / di-skip
                    if (!$pesanan) {
                        PesananOnline::create([
                            'no_pesanan'     => $noPesanan,
                            'id_user'        => Auth::id(),
                            'platform'       => $platform,
                            'no_resi'        => $noResi,
                            'nama_pembeli'   => $namaPembeli,
                            'tanggal'        => Carbon::parse($tanggalRow),
                            'total_harga'    => $totalHarga,
                            'total_hpp'      => 0,
                            'ongkir'         => $ongkir,
                            'status'         => 'draft',
                            'status_mapping' => 'manual',
                            'pesan_mapping'  => 'Memerlukan mapping manual',
                        ]);
                    }

                    ReviewMappingManual::create([
                        'no_pesanan'      => $noPesanan,
                        'kode_live_raw'  => $variasiLive,
                        'tanggal_live'    => $tanggalRow,
                        'qty_order'       => $qty,
                        'status_resolusi' => 'pending',
                    ]);

                    $pesananSkip++;
                }
            }

            DB::commit();

            $pesan = "Import Selesai! Berhasil: {$pesananBerhasil} pesanan tersimpan ke riwayat.";
            if ($pesananSkip > 0) {
                $pesan .= " ({$pesananSkip} pesanan memerlukan review manual/dilewati)";
            }

            session()->forget(['csv_platform', 'csv_format']);

            return redirect()->route('pesanan.index')->with('success', $pesan);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Import proses failed', [
                'error' => $e->getMessage(),
                'line'  => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return back()->with('error', 'Gagal Menyimpan: ' . $e->getMessage());
        }
    }
    // Helpers
    // ✅ Ekstrak field dari text (untuk TikTok format yang text-based)
private function ekstrakField(string $text, string $key): ?string
{
    $pattern = '/' . preg_quote($key, '/') . '\s*:\s*([^;]*)/i';
    return preg_match($pattern, $text, $m) ? trim($m[1]) : null;
}

// ✅ Parse harga (handle "Rp 100.000" → 100000)
private function parseHarga($value): float
{
    if ($value === null) return 0;
    $clean = preg_replace('/[^0-9]/', '', (string)$value);  // Hapus non-digit
    return (float)($clean ?: 0);
}

// ✅ Parse tanggal (handle Excel date format)
private function parseTanggal($value): string
{
    if (empty($value)) return now()->toDateTimeString();
    
    if (is_numeric($value)) {
        try {
            // Excel serial date → convert
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

   public function index(Request $request)
{
    $query = PesananOnline::with('user');

    // Filter by platform
    if ($request->filled('platform'))
        $query->where('platform', $request->platform);
    
    // Filter by status
    if ($request->filled('status'))
        $query->where('status', $request->status);
    
    // Filter by date
    if ($request->filled('tanggal'))
        $query->whereDate('tanggal', $request->tanggal);
    
    // Search by nomor pesanan atau nama pembeli
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
    // Tampilkan semua pesanan yang belum di-map (kode tidak ketemu)
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
        // ✅ User pilih produk untuk di-map
        $request->validate(['id_produk_mapping' => 'required|exists:produk,id']);
        
        $produk = Produk::findOrFail($request->id_produk_mapping);
        
        if ($produk->stok < $review->qty_order) {
            return redirect()->back()->with('error', 'Stok produk tidak mencukupi!');
        }

        // Update review record
        $review->update([
            'id_produk_mapping' => $produk->id,
            'status_resolusi'   => 'mapped',
            'id_user_resolver'  => Auth::id() ?? 1,
            'tanggal_resolusi'  => now(),
        ]);

        // Update pesanan
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

        Log::info("Manual mapping resolved for {$review->no_pesanan}");
        return redirect()->back()->with('success', 'Mapping berhasil!');
    }
    
    elseif ($action === 'skip') {
        // Skip pesanan ini
        $review->update([
            'status_resolusi'  => 'skip',
            'id_user_resolver' => Auth::id() ?? 1,
            'tanggal_resolusi' => now(),
        ]);
        return redirect()->back()->with('success', 'Dilewati.');
    }
    
    elseif ($action === 'reject') {
        // Tolak pesanan
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