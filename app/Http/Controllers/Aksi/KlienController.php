<?php

namespace App\Http\Controllers\Aksi;

use App\Http\Controllers\Controller;
use App\Models\KlienLks;
use App\Models\Lembaga;
use App\Services\Pemberitahu;
use App\Services\PenyimpananBerkas;
use App\Support\Data\DataKlien;
use App\Support\Data\Kalender;
use App\Support\Data\SkemaKlien;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/** Klien disimpan langsung oleh lembaga pemilik dan langsung berstatus Terdaftar. Admin hanya membaca. */
class KlienController extends Controller
{
    private function aturan(string $lembagaId, ?string $id): array
    {
        $aturan = [];
        foreach (SkemaKlien::fields() as $isian => $bidang) {
            $tipe = $bidang['type'] ?? 'text';
            $baris = [! empty($bidang['required']) ? 'required' : 'nullable'];
            if ($tipe === 'checkbox') {
                $aturan[$isian] = ['nullable', 'boolean'];

                continue;
            }
            match ($tipe) {
                'date' => array_push($baris, 'date', 'before_or_equal:'.Kalender::today()),
                'number' => array_push($baris, 'integer', 'min:'.($bidang['min'] ?? 0), 'max:'.($bidang['max'] ?? 125)),
                default => array_push($baris, 'string', 'max:'.($bidang['maxLength'] ?? 255)),
            };
            if (! empty($bidang['options'])) {
                $baris[] = Rule::in($bidang['options']);
            }
            $aturan[$isian] = $baris;
        }
        // Wajib bersyarat, sama dengan isRequired() di client-form.js.
        $request = request();
        $aturan['birthDate'][] = Rule::requiredIf(! $request->boolean('birthDateUnknown'));
        $aturan['approximateAge'][] = Rule::requiredIf($request->boolean('birthDateUnknown'));
        $aturan['educationStatus'][] = Rule::requiredIf(function () use ($request) {
            $lahir = $request->boolean('birthDateUnknown') ? null : strtotime((string) $request->input('birthDate'));

            return $request->input('category') === 'Anak' || ($lahir && $lahir > strtotime('-18 years'));
        });
        foreach (['exitDate', 'exitReason'] as $isian) {
            $aturan[$isian][] = 'required_if:residenceStatus,Sudah keluar';
        }
        $aturan['exitDate'][] = 'after_or_equal:admissionDate';
        foreach (['guardianName', 'guardianRelationship'] as $isian) {
            $aturan[$isian][] = 'required_if:guardianStatus,Keluarga/wali diketahui';
        }
        foreach (['schoolName', 'educationLevel'] as $isian) {
            $aturan[$isian][] = 'required_if:educationStatus,Sedang sekolah';
        }
        $unik = fn (string $kolom) => Rule::unique('klien_lks', $kolom)->where('lembaga_id', $lembagaId)->ignore($id);
        $aturan['code'][] = $unik('kode');
        $aturan['nik'] = ['nullable', 'digits:16', $unik('nik')];
        $aturan['familyCardNumber'] = ['nullable', 'digits:16'];
        $aturan['healthCoverageNumber'] = ['nullable', 'digits:13'];
        $aturan['photo'] = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'];
        $aturan['removePhoto'] = ['nullable', 'boolean'];

        return $aturan;
    }

    private function pesan(): array
    {
        return [
            'code.unique' => 'Kode klien sudah dipakai di lembaga ini.',
            'nik.unique' => 'NIK sudah terdaftar di lembaga ini.',
            'exitDate.after_or_equal' => 'Tanggal keluar tidak boleh sebelum tanggal mulai layanan.',
        ];
    }

    private function simpan(Request $request, KlienLks $klien, array $data, string $catatan, PenyimpananBerkas $penyimpanan): void
    {
        $nilai = [];
        foreach (DataKlien::KOLOM as $isian => $kolom) {
            $nilai[$isian] = in_array($isian, DataKlien::BOOLEAN, true) ? ! empty($data[$isian]) : (($data[$isian] ?? '') === '' ? null : $data[$isian]);
        }
        foreach (DataKlien::KOLOM as $isian => $kolom) {
            // Isian bersyarat yang tidak berlaku dikosongkan, supaya tidak tersimpan nilai usang.
            $berlaku = $isian === 'birthDateUnknown' || SkemaKlien::applicable($isian, $nilai);
            $klien->{$kolom} = $berlaku ? $nilai[$isian] : (in_array($isian, DataKlien::BOOLEAN, true) ? false : null);
        }
        $klien->pengguna_id = $request->user()->id;
        $klien->status = 'Terdaftar';
        $klien->riwayat = [...($klien->riwayat ?? []), ['status' => 'Terdaftar', 'at' => now()->toIso8601String(), 'actor' => $request->user()->nama, 'note' => $catatan]];
        $klien->save();

        $fotoLama = $klien->foto_berkas_id;
        if ($request->hasFile('photo')) {
            $klien->foto_berkas_id = $penyimpanan->unggah($request->file('photo'), $klien->lembaga_id, 'klien', $klien->id)->id;
        } elseif (! empty($data['removePhoto'])) {
            $klien->foto_berkas_id = null;
        }
        if ($fotoLama !== $klien->foto_berkas_id) {
            $klien->save();
            $penyimpanan->hapus($fotoLama);
        }
    }

    public function store(Request $request, PenyimpananBerkas $penyimpanan): JsonResponse
    {
        $lembagaId = $request->user()->lembaga_id;
        $data = $request->validate($this->aturan($lembagaId, null), $this->pesan());
        $id = DB::transaction(function () use ($request, $lembagaId, $data, $penyimpanan) {
            $klien = new KlienLks(['lembaga_id' => $lembagaId]);
            $this->simpan($request, $klien, $data, 'Biodata disimpan.', $penyimpanan);
            $nama = Lembaga::whereKey($lembagaId)->value('nama');
            Pemberitahu::keAdmin([
                'title' => 'Klien baru tercatat', 'message' => "{$nama} menambahkan biodata klien.", 'organizationTitle' => $nama,
                'periodLabel' => 'Pembaruan data', 'url' => '/admin/resident/'.$klien->id,
            ]);

            return $klien->id;
        });
        $request->session()->flash('status', 'Biodata klien tersimpan.');

        return response()->json(['message' => 'Biodata klien tersimpan.', 'redirect' => '/lks/resident/'.$id]);
    }

    public function update(Request $request, string $id, PenyimpananBerkas $penyimpanan): JsonResponse
    {
        $lembagaId = $request->user()->lembaga_id;
        $data = $request->validate($this->aturan($lembagaId, $id) + ['version' => ['required', 'integer']], $this->pesan());
        DB::transaction(function () use ($request, $id, $lembagaId, $data, $penyimpanan) {
            $klien = KlienLks::where('lembaga_id', $lembagaId)->lockForUpdate()->findOrFail($id);
            abort_unless($klien->versi === (int) $data['version'], 409, 'Data klien berubah. Muat ulang sebelum menyimpan.');
            $klien->versi++;
            $this->simpan($request, $klien, $data, 'Biodata diperbarui.', $penyimpanan);
        });
        $request->session()->flash('status', 'Biodata klien tersimpan.');

        return response()->json(['message' => 'Biodata klien tersimpan.', 'redirect' => '/lks/resident/'.$id]);
    }

    public function destroy(Request $request, string $id, PenyimpananBerkas $penyimpanan): JsonResponse
    {
        DB::transaction(function () use ($request, $id, $penyimpanan) {
            $klien = KlienLks::where('lembaga_id', $request->user()->lembaga_id)->findOrFail($id);
            $foto = $klien->foto_berkas_id;
            $klien->delete();
            $penyimpanan->hapus($foto);
        });
        $request->session()->flash('status', 'Klien dihapus.');

        return response()->json(['message' => 'Klien dihapus.', 'redirect' => '/lks/occupancy']);
    }
}
