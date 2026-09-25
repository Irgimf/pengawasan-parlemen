<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Submission;
use Barryvdh\DomPDF\Facade\Pdf;

class FormController extends Controller
{
    public function index()
    {
        $kategoriForm = [
            'Rekap Koordinator / Team Leader' => [
                ['code' => 'RHP-00', 'name' => 'Rekap Harian Konsultan Pengawas'],
            ],
            'Kertas Kerja Harian (KKH)' => [
                ['code' => 'KKH-01-BANDARA', 'name' => 'Bandara Soekarno-Hatta'],
                ['code' => 'KKH-02-HOTEL', 'name' => 'Hotel - Foyer & Meeting Ballroom'],
                ['code' => 'KKH-03-DPRRI', 'name' => 'DPR RI - Pustakaloka/Nusantara'],
                ['code' => 'KKH-04-TRANSPORT', 'name' => 'Mobilitas / Transportasi Antar Lokasi'],
                ['code' => 'KKH-05-KUNKER', 'name' => 'Lokus Kunjungan Kerja'],
                ['code' => 'KKH-06-MUSEUM', 'name' => 'Museum Nasional / City Tour'],
            ],
            'Formulir Produksi (PRD)' => [
                ['code' => 'PRD-01A', 'name' => 'Produksi Fisik & Backdrop'],
                ['code' => 'PRD-01B', 'name' => 'Printing, Signage & Instalasi'],
                ['code' => 'PRD-02A', 'name' => 'Seragam Panitia & Peserta'],
                ['code' => 'PRD-02B', 'name' => 'Participant Kit & Souvenir'],
                ['code' => 'PRD-03', 'name' => 'Multimedia, Desain & Dokumentasi'],
            ],
            'Temuan & Tindak Lanjut' => [
                ['code' => 'RT-01', 'name' => 'Register Temuan dan Tindak Lanjut'],
                ['code' => 'FT-01', 'name' => 'Formulir Peringatan / Notifikasi Temuan'],
                ['code' => 'BA-TL-01', 'name' => 'BA Verifikasi Tindak Lanjut'],
            ]
        ];

        $query = Submission::query();

        if (request()->filled('search')) {
            $search = request()->search;
            $query->where(function($q) use ($search) {
                $q->where('form_code', 'like', "%{$search}%")
                  ->orWhere('supervisor_name', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        if (request()->filled('date')) {
            $query->whereDate('created_at', request()->date);
        }

        $submissions = $query->orderBy('created_at', 'desc')->get();
        return view('index', compact('kategoriForm', 'submissions'));
    }

    public function show($form_code)
    {
        // Memanggil pertanyaan dinamis berdasarkan kode form yang diklik user
        $questions = $this->getFormQuestions($form_code);
        return view('form', compact('form_code', 'questions'));
    }

    public function store(Request $request, $form_code)
    {
        $request->validate([
            'supervisor_name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'start_time' => 'required|date',
            'signature_supervisor' => 'required|string',
            'attachment' => 'nullable|file|mimes:jpeg,png,jpg,mp4,mov|max:10240', // Maks 10MB
        ]);

        $generalKeys = [
            '_token', 'supervisor_name', 'provider_name', 'committee_name', 
            'location', 'start_time', 'end_time', 'final_notes',
            'signature_supervisor', 'signature_provider', 'signature_committee', 'attachment'
        ];
        
        $formData = $request->except($generalKeys);

        // Logika Upload File
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            
            if (env('CLOUDINARY_URL')) {
                // Gunakan Cloudinary jika CLOUDINARY_URL tersedia di .env
                $uploadedFileUrl = cloudinary()->upload($file->getRealPath(), [
                    'folder' => 'dokumentasi',
                    'resource_type' => 'auto'
                ])->getSecurePath();
                $formData['attachment_path'] = $uploadedFileUrl;
            } else {
                // Fallback ke penyimpanan lokal
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('dokumentasi', $filename, 'public');
                $formData['attachment_path'] = 'storage/' . $path;
            }
        }

        Submission::create([
            'form_code' => $form_code,
            'supervisor_name' => $request->supervisor_name,
            'provider_name' => $request->provider_name,
            'committee_name' => $request->committee_name,
            'location' => $request->location,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'final_notes' => $request->final_notes,
            'signature_supervisor' => $request->signature_supervisor,
            'signature_provider' => $request->signature_provider,
            'signature_committee' => $request->signature_committee,
            'form_data' => $formData,
        ]);

        return redirect()->route('home')->with('success', "Formulir $form_code beserta dokumentasi berhasil disimpan!");
    }

        private function getFormQuestions($code)
    {
        $library = [
            // ... (Pertanyaan KKH-01 sampai PRD-03 sebelumnya tetap ada di sini) ...
            'KKH-01-BANDARA' => [
                'Airport service assistance/ground handling dilaksanakan pada area penjemputan standar di luar pintu exit.',
                'Kendaraan operasional bandara: Mini MPV min. tahun 2021, AC, GPS tracking, standar keamanan, dan standby 12 jam/hari; sudah termasuk supir, biaya tol, parkir, uang makan supir, dan bahan bakar; 1 unit.',
                'Bis penjemputan peserta: Fullday, tahun 2020, kapasitas 59 seater (termasuk supir), bahan bakar, biaya tol, dan parkir, GPS tracking dan alat pengamanan standar; Peruntukan penjemputan: Bandara – Hotel; 5 unit.',
                'Proses kedatangan/penjemputan peserta dan pergerakan Bandara-Hotel terlaksana sesuai daftar/jadwal panitia.',
                'Konsumsi petugas bandara tersedia sesuai kebutuhan kedatangan/kepulangan (25 pax; makan siang atau malam sesuai waktu).',
                'Koordinasi ground handling, pengemudi dan transportasi berjalan; kendala peserta/kendaraan segera ditindaklanjuti.',
                'Dokumentasi kondisi lapangan/kejadian penting tersedia sebagai bukti pengawasan.'
            ],
            'KKH-02-HOTEL' => [
                'Registration Counter di Foyer Ballroom siap; printer warna multifungsi (tipe Desk Jet, sudah termasuk isi ulang tinta asli) sebanyak 1 unit.',
                'Standing signage/penanda arah (dengan Tiang – Tripod dan Tatakan Foam Board, Cetak Stiker Warna/Cetak di atas Kertas HVS A0) tersedia dan terpasang sesuai kebutuhan area hotel.',
                'Ruang Meeting Ballroom disiapkan sesuai agenda: U Shape no table / Classroom / sesuai ruang sebagaimana jadwal.',
                'Area Main Stage: backdrop 7 m x 4 m dan bendera vandel/fraksi beserta tiang pataka terpasang baik.',
                'Area peserta: power plug sockets 5 lubang (10 unit) dan laptop sekretariat dengan spesifikasi I-core 5 / 7, OS Windows 2023, MS Office 2023, Software Standard PDF dilengkapi dengan antivirus yang tersedia serta berfungsi (1 unit).',
                'Konsumsi/air minum petugas di venue dan hotel tersedia sesuai kebutuhan hari.',
                'Koordinasi transportasi Hotel-DPR/Hotel-Museum/Hotel-Lokus berjalan sesuai agenda dan perubahan tercatat.',
                'Dokumentasi kegiatan dan catatan kondisi venue/temuan hari tersedia.'
            ],
            'KKH-03-DPRRI' => [
                'Ruang Pustakaloka, Nusantara 4 untuk Opening Ceremony disiapkan Theater Style sesuai kebutuhan agenda.',
                'Lighting system tersedia/berfungsi: Parled (12 unit), moving beam 350/400 (8 unit), tripod (2 unit), Fresnell (8 unit), dan mixer light (1 unit).',
                'Laptop notetaker dengan spesifikasi I-core 5 / 7, OS Windows 2023, MS Office 2023, Software Standard PDF dilengkapi dengan antivirus tersedia dan berfungsi (1 unit).',
                'Area group photo: Backdrop Photo dengan konstruksi kayu, uk. 11 m x 4 m, L. 30 cm (1 unit) dan lighting photo (1 pack) siap bila digunakan.',
                'Sekretariat PCO (Ruang Kantor/Tamu KK 2/BAMUS Gedung Nusantara): stationery berisi kertas A4, ballpoint, stabilo, map plastik bening, spidol warna merah, biru, dan hitam, stapler medium dan kecil beserta isi, gunting, klip kertas, klip binder beraneka ukuran (1 paket); dan power plug/extension minimal 6 plug (5 unit) tersedia.',
                'Kebersihan tambahan terjaga pada Gedung Nusantara, Gedung Nusantara II/public area – lobby dan ruang ibadah terkait.',
                'Signage venue/penunjuk arah/meeting room signage terpasang baik dan penempatan sesuai arahan panitia.',
                'HT/intercom/perangkat komunikasi petugas tersedia dan berfungsi pada area kegiatan.',
                'Dokumentasi foto/video serta catatan kejadian/ketidaksesuaian di area DPR RI tersedia.'
            ],
            'KKH-04-TRANSPORT' => [
                'Bis penjemputan/pengantaran/shuttle sesuai jenis penggunaan hari, kapasitas 59 seater dan jumlah unit sesuai rencana.',
                'Bis dilengkapi GPS tracking system dan alat pengamanan standar (pemecah kaca, fire extinguisher, dll.).',
                'Rute sesuai agenda: Bandara-Hotel; Hotel-Bandara; Hotel-DPR-Hotel; Hotel-Museum Nasional; atau Hotel-Lokus Kunker.',
                'Durasi kendaraan dipantau: maks. 12 jam untuk shuttle/operasional dan maks. 6 jam untuk pengantaran Hotel-Bandara.',
                'Kendaraan angkut Mini Bus 16 seater min. tahun 2021: AC dan GPS tracking; tersedia sesuai kebutuhan.',
                'Kendaraan operasional Mini MPV/VAN min. tahun 2021: AC, GPS tracking dan driver tersedia.',
                'Stiker kendaraan shuttle pada kaca depan terpasang bila digunakan sesuai kebutuhan.',
                'Waktu berangkat/tiba, perubahan rute, kendala kendaraan dan tindak lanjut dicatat.'
            ],
            'KKH-05-KUNKER' => [
                'Kedatangan peserta di lokus dan kepulangan ke hotel terkoordinasi sesuai agenda/daftar peserta panitia.',
                'Bis shuttle Hotel-Lokus Kunker memenuhi kapasitas, GPS tracking dan alat pengamanan standar.',
                'Ketersediaan kendaraan selama kunjungan dipantau dan perubahan operasional dicatat.',
                'Koordinasi petugas transportasi/EO/PIC lokus berjalan dan perubahan agenda diteruskan kepada pihak terkait.',
                'Pergerakan peserta pada titik turun/naik kendaraan berlangsung tertib dan kendala lapangan dicatat.',
                'Perangkat komunikasi lapangan tersedia/berfungsi sesuai kebutuhan koordinasi.',
                'Dokumentasi foto/video kegiatan dan bukti temuan pengawasan tersedia.'
            ],
            'KKH-06-MUSEUM' => [
                'Tiket masuk Museum Nasional dan Immersive Studio tersedia/digunakan sesuai kebutuhan peserta (210 pax).',
                'Kedatangan, masuk area, dan kepulangan peserta terkoordinasi sesuai agenda dan daftar peserta panitia.',
                'Bis shuttle Hotel-Museum Nasional memenuhi kapasitas 59 seater, GPS tracking dan alat pengamanan standar.',
                'Ketersediaan kendaraan dipantau; waktu tiba/berangkat serta kendala transportasi dicatat.',
                'Koordinasi EO/PIC dengan petugas transportasi dan peserta berjalan selama city tour.',
                'Perangkat komunikasi lapangan tersedia/berfungsi sesuai kebutuhan koordinasi.',
                'Dokumentasi foto/video kegiatan dan bukti temuan pengawasan tersedia.'
            ],
            'PRD-01A' => [
                'Backdrop Main Stage: Konstruksi kayu 7 m x 4 m; flexy indoor; cetak warna; termasuk pasang & bongkar; 1 unit.',
                'Bendera Vandel Fraksi: 80 x 120 cm; beludru/satin/drill; bordir + rumbai; tiang pataka 2,5 m; 8 unit.',
                'Backdrop Area Group Photo: Konstruksi kayu 11 m x 4 m; digital printing bolak-balik; 1 unit.',
                'Handheld Signage: A3 polyfoam + sticker vinyl; gagang min. 30 cm; 6 unit.',
                'T Banner Area DPR RI: 60 x 270 cm; vinyl outdoor full color + rangka; 35 unit/titik; termasuk pasang, bongkar, kontrol.'
            ],
            'PRD-01B' => [
                'Stiker Kendaraan Shuttle: A4 landscape; vinyl; ditempel di kaca depan; 6 pcs.',
                'Totem Nama Event: Konstruksi kayu ±244 x 60 x 12 cm; sticker depan-belakang; 1 unit.',
                'Signage Direction: Konstruksi kayu ±244 x 60 x 12 cm; sticker depan-belakang; 1 unit.',
                'Standing/Meeting Signage: Tripod + media cetak A0; periksa desain, wording, penempatan dan jumlah.',
                'Produksi lainnya: Periksa kuantitas dan spesifikasi (Item, Qty, Spesifikasi) sesuai aktual di lapangan.'
            ],
            'PRD-02A' => [
                'Jaket Panitia: Cotton Fleece PE; furing peles; kancing snap plastik; 160 pcs.',
                'Kaos Polo Panitia: Cotton Combed 24S; bordir logo; 160 pcs.',
                'Jaket Peserta: Parasut; kancing/resleting YKK; bordir warna; standar PARJA 2025; 200 pcs.',
                'Kaos Peserta: T-shirt katun; logo sablon laser; standar PARJA 2025; 200 pcs.',
                'Topi Peserta: Model baseball; bordir logo; desain/warna sesuai arahan panitia; 200 pcs.',
                'Celana Training: Bahan polar fleece; desain/warna sesuai arahan; standar PARJA 2025; 200 pcs.',
                'Jaket Peserta Terbaik/Terfavorit: Parasut; resleting; bordir depan dan sablon belakang; 6 pcs.'
            ],
            'PRD-02B' => [
                'Tas Conference: Ransel anti air untuk laptop 15 inci; desain/warna sesuai arahan; 360 pcs.',
                'Tumbler: Stainless steel 750 ml; grafir logo event; 210 pcs.',
                'Blocknote + Pen: Blocknote A5 50 lembar + pulpen metal grafir; 210 set.',
                'Participant Pin: Lapel pin magnet warna emas diameter 3 cm + box; 210 pcs.',
                'ID Card & Lanyard: PVC 18 x 12 cm; full color; kategori warna dikoordinasikan; 300 set.',
                'Sertifikat + Frame: Art Carton 210 gr + map/frame; 10 set.',
                'Bag Tag / Luggage Tag: Kulit sintetis + kantong kartu; 210 pcs.',
                'Plakat Peserta Terbaik: Akrilik + box beludru; 7 pcs.'
            ],
            'PRD-03' => [
                'Multimedia Bumper & Graphic: Konten audio/visual/audio-visual sesuai KV; bumper program/narasumber/lower third; 1 pkg.',
                'Desain Grafis & Sosial Media: Desain grafis 2D/layout; konten sesuai draft/arahan Humas; 1 pkg.',
                'Dokumentasi Foto: 2 photographer; output raw pada hardisk + edited via drive; 1 paket.',
                'Dokumentasi Video: Liputan kegiatan oleh 2 cameraman profesional; 1 paket.',
                'Video Highlight: Versi pendek 3-5 menit + full version; 2 paket.',
                'Sameday Edit / Daily Recap: Rekap kegiatan 1 hari penuh pada simulasi sidang, diedit hari yang sama; 1 paket.',
                'Final Report: Jilid soft cover Art Paper 150; isi HVS A4 80 gr; print color; 5 set.'
            ],
            
            // --- PENAMBAHAN FORM ADMINISTRATIF ---
            'RHP-00' => [
                'Bandara Soekarno-Hatta',
                'Hotel - Foyer & Ballroom',
                'DPR RI - Pustakaloka/Nusantara',
                'Mobilitas / Transportasi Antar Lokasi',
                'Lokus Kunjungan Kerja',
                'Museum Nasional / City Tour',
                'Produksi Fisik / Printing / Kit',
                'Multimedia / Dokumentasi'
            ],
            'RT-01' => [
                'Seluruh temuan (FT-01) hari ini telah diregistrasi dengan nomor, lokasi, dan kategori yang tepat.',
                'Target PIC dan batas waktu perbaikan untuk setiap temuan telah ditentukan dan disepakati.'
            ],
            'FT-01' => [
                'Uraian temuan/kondisi aktual telah dideskripsikan secara jelas dan spesifik.',
                'Kondisi yang dipersyaratkan (sesuai spesifikasi/dokumen) telah dicantumkan sebagai pembanding.',
                'Instruksi/tindakan perbaikan dan batas waktu telah dikomunikasikan kepada Penyedia/EO.'
            ],
            'BA-TL-01' => [
                'Tindakan perbaikan yang dilakukan Penyedia/EO telah diuraikan sesuai fakta lapangan.',
                'Verifikasi ulang telah dilakukan melalui inspeksi visual, ukur, uji fungsi, atau pemeriksaan dokumen.',
                'Status akhir temuan telah diputuskan (CLOSED / CLOSED DENGAN CATATAN / OPEN) dan bukti disertakan.'
            ]
        ];

        return $library[$code] ?? [];
    }

    public function exportPdf($id)
    {
        $submission = Submission::findOrFail($id);
        
        // Ambil kembali daftar pertanyaan asli sebagai referensi label
        $questions = $this->getFormQuestions($submission->form_code);
        
        // Generate PDF dari view 'pdf.blade.php'
        $pdf = Pdf::loadView('pdf', compact('submission', 'questions'));
        
        // Atur ukuran kertas ke A4 (Portrait)
        $pdf->setPaper('A4', 'portrait');
        
        // Gunakan stream() agar PDF terbuka di tab baru (bisa di-print dari sana).
        // Jika ingin langsung download, ganti menjadi download('nama_file.pdf')
        return $pdf->stream('Laporan_'.$submission->form_code.'_'.date('YmdHis').'.pdf');
    }

    public function destroy($id)
    {
        $submission = Submission::findOrFail($id);

        // Hapus file lampiran fisik jika ada di storage
        if (isset($submission->form_data['attachment_path'])) {
            $filePath = public_path('storage/' . str_replace('storage/', '', $submission->form_data['attachment_path']));
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }

        // Hapus data dari database SQLite
        $submission->delete();

        return redirect()->route('home')->with('success', 'Data formulir pengawasan berhasil dihapus.');
    }
}