<?php

namespace App\Http\Controllers;

use App\Models\MsWarna;
use App\Models\MsJenisPakaian;
use App\Models\MsModel;
use App\Models\Produk;
use Illuminate\Http\Request;

class MasterDataController extends Controller
{
    /**
     * Helper untuk membuat kode master otomatis (3 huruf unik)
     */
    private function generateKode(string $nama, string $modelClass): string
    {
        $clean = strtoupper(trim(preg_replace('/[^A-Za-z0-9\s]/', '', $nama)));
        $words = array_values(array_filter(explode(' ', $clean)));

        if (count($words) >= 3) {
            $baseKode = substr($words[0], 0, 1) . substr($words[1], 0, 1) . substr($words[2], 0, 1);
        } elseif (count($words) === 2) {
            if (strlen($words[0]) >= 2) {
                $baseKode = substr($words[0], 0, 2) . substr($words[1], 0, 1);
            } else {
                $baseKode = substr($words[0], 0, 1) . substr($words[1], 0, 2);
            }
        } else {
            $baseKode = substr($clean, 0, 3);
        }

        $baseKode = strtoupper(str_pad($baseKode, 3, 'X'));
        $kode = $baseKode;
        $counter = 1;

        while ($modelClass::where('kode', $kode)->exists()) {
            $counter++;
            $suffix = (string) $counter;
            $kode = substr($baseKode, 0, max(1, 3 - strlen($suffix))) . $suffix;
        }

        return $kode;
    }

    // ============================================================
    // WARNA
    // ============================================================
    public function storeWarna(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
        ]);

        $nama = trim($request->nama);
        $kode = $request->filled('kode') ? strtoupper(trim($request->kode)) : $this->generateKode($nama, MsWarna::class);

        $exists = MsWarna::where('nama', $nama)->orWhere('nama_warna', $nama)->exists();
        if ($exists) {
            return back()->with('error', "Warna '{$nama}' sudah ada di database.");
        }

        MsWarna::create([
            'nama'       => $nama,
            'nama_warna' => $nama,
            'kode'       => $kode,
        ]);

        return back()->with('success', "Warna '{$nama}' berhasil ditambahkan.");
    }

    public function updateWarna(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
        ]);

        $warna = MsWarna::findOrFail($id);
        $nama  = trim($request->nama);

        $warna->update([
            'nama'       => $nama,
            'nama_warna' => $nama,
        ]);

        return back()->with('success', "Warna '{$nama}' berhasil diperbarui.");
    }

    public function destroyWarna($id)
    {
        $warna = MsWarna::findOrFail($id);
        $jumlahProduk = Produk::where('id_warna', $id)->count();

        if ($jumlahProduk > 0) {
            return back()->with('error', "Gagal menghapus! Warna '{$warna->nama}' sedang digunakan oleh {$jumlahProduk} produk.");
        }

        $nama = $warna->nama;
        $warna->delete();

        return back()->with('success', "Warna '{$nama}' berhasil dihapus.");
    }

    // ============================================================
    // JENIS PAKAIAN
    // ============================================================
    public function storeJenis(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
        ]);

        $nama = trim($request->nama);
        $kode = $request->filled('kode') ? strtoupper(trim($request->kode)) : $this->generateKode($nama, MsJenisPakaian::class);

        $exists = MsJenisPakaian::where('nama', $nama)->orWhere('nama_jenis', $nama)->exists();
        if ($exists) {
            return back()->with('error', "Jenis pakaian '{$nama}' sudah ada di database.");
        }

        MsJenisPakaian::create([
            'nama'       => $nama,
            'nama_jenis' => $nama,
            'kode'       => $kode,
        ]);

        return back()->with('success', "Jenis pakaian '{$nama}' berhasil ditambahkan.");
    }

    public function updateJenis(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
        ]);

        $jenis = MsJenisPakaian::findOrFail($id);
        $nama  = trim($request->nama);

        $jenis->update([
            'nama'       => $nama,
            'nama_jenis' => $nama,
        ]);

        return back()->with('success', "Jenis pakaian '{$nama}' berhasil diperbarui.");
    }

    public function destroyJenis($id)
    {
        $jenis = MsJenisPakaian::findOrFail($id);
        $jumlahProduk = Produk::where('id_jenis_pakaian', $id)->count();

        if ($jumlahProduk > 0) {
            return back()->with('error', "Gagal menghapus! Jenis pakaian '{$jenis->nama}' sedang digunakan oleh {$jumlahProduk} produk.");
        }

        $nama = $jenis->nama;
        $jenis->delete();

        return back()->with('success', "Jenis pakaian '{$nama}' berhasil dihapus.");
    }

    // ============================================================
    // MODEL / MOTIF
    // ============================================================
    public function storeModel(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
        ]);

        $nama = trim($request->nama);
        $kode = $request->filled('kode') ? strtoupper(trim($request->kode)) : $this->generateKode($nama, MsModel::class);

        $exists = MsModel::where('nama', $nama)->orWhere('nama_model', $nama)->exists();
        if ($exists) {
            return back()->with('error', "Model '{$nama}' sudah ada di database.");
        }

        MsModel::create([
            'nama'       => $nama,
            'nama_model' => $nama,
            'kode'       => $kode,
        ]);

        return back()->with('success', "Model '{$nama}' berhasil ditambahkan.");
    }

    public function updateModel(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
        ]);

        $modelObj = MsModel::findOrFail($id);
        $nama     = trim($request->nama);

        $modelObj->update([
            'nama'       => $nama,
            'nama_model' => $nama,
        ]);

        return back()->with('success', "Model '{$nama}' berhasil diperbarui.");
    }

    public function destroyModel($id)
    {
        $modelObj = MsModel::findOrFail($id);
        $jumlahProduk = Produk::where('id_model', $id)->count();

        if ($jumlahProduk > 0) {
            return back()->with('error', "Gagal menghapus! Model '{$modelObj->nama}' sedang digunakan oleh {$jumlahProduk} produk.");
        }

        $nama = $modelObj->nama;
        $modelObj->delete();

        return back()->with('success', "Model '{$nama}' berhasil dihapus.");
    }
}
