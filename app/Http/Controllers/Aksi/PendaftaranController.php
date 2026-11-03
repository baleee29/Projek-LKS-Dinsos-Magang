<?php

namespace App\Http\Controllers\Aksi;

use App\Http\Controllers\Controller;
use App\Models\BerkasPendaftaran;
use App\Models\Lembaga;
use App\Models\PendaftaranLks;
use App\Services\Pemberitahu;
use App\Services\PenyimpananBerkas;
use App\Support\Data\Kalender;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/** Kiriman formulir publik dan keputusan Admin. Hasilnya hanya diterima atau ditolak dan bersifat final. */
class PendaftaranController extends Controller
{
    public const CHOICES = [
        'lks-prioritas-pelayanan' => [
            'Kemiskinan',
            'Keterlantaran',
            'Disabilitas',
            'Keterpencilan',
            'Ketunaan sosial dan penyimpangan perilaku',
            'Korban bencana',
            'Korban tindak kekerasan, eksploitasi, dan diskriminasi',
        ],
        'lks-jenis-pelayanan' => [
            'Jaminan Sosial',
            'Rehabilitasi Sosial',
            'Pemberdayaan Sosial',
            'Perlindungan Sosial',
        ],
        'lks-sasaran-pelayanan' => [
            'Perseorangan',
            'Keluarga',
            'Kelompok',
            'Masyarakat',
        ],
        'lks-jenis-kegiatan' => [
            'Unit pelayanan sosial untuk anak',
            'Unit pelayanan sosial untuk lansia',
            'Unit pelayanan sosial untuk penyandang disabilitas',
            'Dukungan psikososial untuk anak dalam situasi bencana',
            'Pemberdayaan ekonomi kelompok miskin dan rentan',
        ],
        'lks-sifat-pelayanan' => [
            'Dalam LKS',
            'Luar LKS',
        ],
    ];

    /** Kunci berkas => batas ukuran dalam MB. */
    public const FILES = ['surat_permohonan' => 5, 'ad_art' => 2, 'akta_pendirian' => 2, 'domisili' => 2, 'npwp' => 2, 'kemenkumham' => 2, 'struktur_organisasi' => 2, 'data_umum' => 2, 'ktp_kontak_pengurus' => 2, 'bebas_narkoba' => 2, 'pernyataan_sengketa' => 2, 'kesanggupan_laporan' => 2, 'keabsahan_dana' => 2, 'persetujuan_tetangga' => 2, 'proposal' => 5, 'formulir_pendaftaran' => 5];

    public function store(Request $request, PenyimpananBerkas $penyimpanan): JsonResponse
    {
        $aturan = [
            'website' => ['nullable', 'prohibited'],
            'request_id' => ['nullable', 'uuid'],
            'lks-nama' => ['required', 'string', 'max:150'],
            'lks-ketua' => ['required', 'string', 'max:150'],
            'lks-whatsapp' => ['required', 'string', 'max:30'],
            'lks-izin' => ['nullable', 'string', 'max:150'],
            'lks-rekening' => ['nullable', 'string', 'max:150'],
            'lks-fokus' => ['required', 'string', 'max:2000'],
            'files' => ['required', 'array:'.implode(',', array_keys(self::FILES))],
            'file_labels' => ['nullable', 'array:'.implode(',', array_keys(self::FILES))],
            'file_labels.*' => ['nullable', 'string', 'max:255'],
        ];
        foreach (self::CHOICES as $grup => $pilihan) {
            $aturan[$grup] = ['required', 'array', 'min:1'];
            $aturan[$grup.'.*'] = ['required', 'string', 'distinct:strict', Rule::in($pilihan)];
        }
        foreach (self::FILES as $kunci => $mb) {
            $aturan['files.'.$kunci] = ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:'.($mb * 1024)];
        }
        $data = $request->validate($aturan);

        // Kiriman ulang dengan request_id yang sama tidak membuat baris baru.
        if (! empty($data['request_id']) && PendaftaranLks::where('permintaan_id', $data['request_id'])->exists()) {
            return response()->json(['message' => 'Pendaftaran sudah diterima sistem.', 'duplicate' => true]);
        }

        $pendaftaran = DB::transaction(function () use ($request, $data, $penyimpanan) {
            $pendaftaran = PendaftaranLks::create([
                'permintaan_id' => $data['request_id'] ?? null,
                'nama_lembaga' => $data['lks-nama'],
                'nama_ketua' => $data['lks-ketua'],
                'whatsapp' => $data['lks-whatsapp'],
                'nomor_izin' => $data['lks-izin'] ?? null,
                'rekening_bank' => $data['lks-rekening'] ?? null,
                'bidang_fokus' => $data['lks-fokus'],
                'prioritas_pelayanan' => array_values($data['lks-prioritas-pelayanan']),
                'jenis_pelayanan' => array_values($data['lks-jenis-pelayanan']),
                'sasaran_pelayanan' => array_values($data['lks-sasaran-pelayanan']),
                'jenis_kegiatan' => array_values($data['lks-jenis-kegiatan']),
                'sifat_pelayanan' => array_values($data['lks-sifat-pelayanan']),
            ]);
            foreach ($request->file('files') as $kunci => $berkas) {
                BerkasPendaftaran::create([
                    'pendaftaran_id' => $pendaftaran->id,
                    'kunci_berkas' => $kunci,
                    'label_berkas' => $data['file_labels'][$kunci] ?? ucwords(str_replace('_', ' ', $kunci)),
                    'berkas_id' => $penyimpanan->unggah($berkas, null, 'pendaftaran', $pendaftaran->id)->id,
                ]);
            }
            Pemberitahu::keAdmin([
                'title' => 'Pendaftaran LKS baru', 'message' => 'Ada pendaftaran LKS baru yang menunggu keputusan Admin.',
                'organizationTitle' => $pendaftaran->nama_lembaga, 'periodLabel' => 'Pendaftaran LKS', 'url' => '/admin/registration/'.$pendaftaran->id,
                'fields' => ['organization_name' => $pendaftaran->nama_lembaga, 'leader_name' => $pendaftaran->nama_ketua, 'status' => 'Menunggu Verifikasi'],
            ]);

            return $pendaftaran;
        });

        return response()->json(['message' => 'Pendaftaran diterima sistem.', 'submitted_at' => $pendaftaran->dibuat_pada->toIso8601String()], 201);
    }

    public function tinjau(Request $request, string $id): JsonResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in([PendaftaranLks::DISETUJUI, PendaftaranLks::DITOLAK])],
            'notes' => ['nullable', 'required_if:status,'.PendaftaranLks::DITOLAK, 'string', 'min:10', 'max:2000'],
        ], [
            'notes.required_if' => 'Alasan penolakan wajib diisi.',
            'notes.min' => 'Tuliskan alasan penolakan minimal 10 karakter.',
        ]);

        DB::transaction(function () use ($request, $id, $data) {
            $pendaftaran = PendaftaranLks::lockForUpdate()->findOrFail($id);
            abort_unless($pendaftaran->status === PendaftaranLks::MENUNGGU, 409, 'Pendaftaran ini sudah diputuskan.');
            $perubahan = [
                'status' => $data['status'], 'catatan' => $data['notes'] ?? null, 'ditinjau_oleh' => $request->user()->id,
                'ditinjau_pada' => now(), 'versi' => $pendaftaran->versi + 1,
            ];
            if ($data['status'] === PendaftaranLks::DISETUJUI) {
                // Baris lembaga dibuat saat diterima. Akun dibuat terpisah di menu Buat Akun LKS.
                $perubahan['lembaga_id'] = Lembaga::create([
                    'nama' => $pendaftaran->nama_lembaga, 'ketua' => $pendaftaran->nama_ketua, 'kontak' => $pendaftaran->whatsapp,
                    'legalitas' => $pendaftaran->nomor_izin, 'terdaftar_sejak' => Kalender::today(),
                ])->id;
            }
            $pendaftaran->update($perubahan);
        });
        $pesan = $data['status'] === PendaftaranLks::DISETUJUI ? 'Pendaftaran diterima.' : 'Pendaftaran ditolak.';
        $request->session()->flash('status', $pesan);

        return response()->json(['message' => $pesan]);
    }
}
