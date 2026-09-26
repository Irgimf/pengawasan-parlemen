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
            'attachment' => 'nullable|file|mimes:jpeg,png,jpg,mp4,mov,pdf,zip|max:10240', // Maks 10MB
            'camera_attachment' => 'nullable|file|mimes:jpeg,png,jpg,mp4,mov|max:10240', // Maks 10MB
        ]);

        $generalKeys = [
            '_token', 'supervisor_name', 'provider_name', 'committee_name', 
            'location', 'start_time', 'end_time', 'final_notes',
            'signature_supervisor', 'signature_provider', 'signature_committee', 'attachment'
        ];
        
        $formData = $request->except($generalKeys);

        // Logika Upload File
        $file = null;
        if ($request->hasFile('camera_attachment')) {
            $file = $request->file('camera_attachment');
        } elseif ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
        }

        if ($file) {
            if (env('CLOUDINARY_URL')) {
                // Gunakan Cloudinary disk secara native (V3)
                $path = $file->store('dokumentasi', 'cloudinary');
                $uploadedFileUrl = \Illuminate\Support\Facades\Storage::disk('cloudinary')->url($path);
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
        [
            'title' => 'Airport service assistance/ground handling dilaksanakan pada area penjemputan standar di luar pintu exit.',
            'subitems' => [
            ]
        ],
        [
            'title' => 'Kendaraan operasional bandara:',
            'subitems' => [
                'Mini MPV min. tahun 2021',
                'AC',
                'GPS tracking',
                'Standar keamanan',
                'Standby 12 jam/hari',
                'Sudah termasuk supir, biaya tol, parkir, uang makan supir, dan bahan bakar',
                '1 unit',
            ]
        ],
        [
            'title' => 'Bis penjemputan peserta: Bandara – Hotel;',
            'subitems' => [
                'Fullday',
                'Tahun 2020',
                'Kapasitas 59 seater (termasuk supir)',
                'Bahan bakar, biaya tol, dan parkir',
                'GPS tracking dan alat pengamanan standar (pemecah kaca, fire extenguiser dlsb)',
                '5 unit',
            ]
        ],
        [
            'title' => 'Proses kedatangan/penjemputan peserta dan pergerakan Bandara-Hotel terlaksana sesuai daftar/jadwal panitia.',
            'subitems' => [
            ]
        ],
        [
            'title' => 'Konsumsi petugas bandara tersedia sesuai kebutuhan:',
            'subitems' => [
                'Kedatangan: 25 pax makan siang/malam sesuai waktu',
                'Kepulangan: 25 pax makan siang/malam sesuai waktu',
            ]
        ],
        [
            'title' => 'Koordinasi ground handling, pengemudi dan transportasi berjalan; kendala peserta/kendaraan segera ditindaklanjuti.',
            'subitems' => [
            ]
        ],
        [
            'title' => 'Dokumentasi kondisi lapangan/kejadian penting tersedia sebagai bukti pengawasan.',
            'subitems' => [
            ]
        ],
    ],
    'KKH-02-HOTEL' => [
        [
            'title' => 'Registration Counter di Foyer Ballroom:',
            'subitems' => [
                'Printer warna multifungsi 1 unit',
                'Tipe Desk Jet',
                'Isi ulang tinta asli',
            ]
        ],
        [
            'title' => 'Standing signage/penanda arah:',
            'subitems' => [
                '4 unit area hotel',
                'Tiang',
                'Tripod',
                'Tatakan Foam Board',
                'Cetak Stiker Warna/Cetak di atas Kertas HVS A0',
            ]
        ],
        [
            'title' => 'Ruang Meeting Ballroom disiapkan sesuai agenda: U Shape no table / Classroom / sesuai ruang sebagaimana jadwal.',
            'subitems' => [
            ]
        ],
        [
            'title' => 'Area Main Stage:',
            'subitems' => [
                'Backdrop 7 m x 4 m',
                'Bendera vandel/fraksi',
                'Tiang pataka',
            ]
        ],
        [
            'title' => 'Area peserta:',
            'subitems' => [
                'Power plug sockets 5 lubang (10 unit)',
                'Laptop sekretariat dengan spesifikasi I-core 5 / 7, OS Windows 2023, MS Office 2023, Software Standard PDF dilengkapi dengan antivirus yang tersedia serta berfungsi (1 unit)',
            ]
        ],
        [
            'title' => 'Konsumsi/air minum petugas di venue dan hotel tersedia sesuai kebutuhan hari.',
            'subitems' => [
            ]
        ],
        [
            'title' => 'Koordinasi transportasi berjalan sesuai agenda dan perubahan tercatat:',
            'subitems' => [
                'Hotel-DPR',
                'Hotel-Museum',
                'Hotel-Lokus',
            ]
        ],
        [
            'title' => 'Dokumentasi kegiatan dan catatan kondisi venue/temuan hari tersedia.',
            'subitems' => [
            ]
        ],
    ],
    'KKH-03-DPRRI' => [
        [
            'title' => 'Ruang Pustakaloka untuk Opening Ceremony disiapkan Theater Style sesuai kebutuhan agenda.',
            'subitems' => [
                'Ruang Nusantara 4 untuk Opening Ceremony disiapkan Theater Style sesuai kebutuhan agenda.',
            ]
        ],
        [
            'title' => 'Lighting system tersedia/berfungsi:',
            'subitems' => [
                'Parled (12 unit)',
                'moving beam 350/400 (8 unit)',
                'tripod (2 unit)',
                'Fresnell (8 unit)',
                'mixer light (1 unit)',
            ]
        ],
        [
            'title' => 'Laptop notetaker dengan spesifikasi:',
            'subitems' => [
                'I-core 5 / 7',
                'OS Windows 2023',
                'MS Office 2023',
                'Software Standard PDF dilengkapi dengan antivirus',
                '1 unit',
            ]
        ],
        [
            'title' => 'Area group photo:',
            'subitems' => [
                'Backdrop Photo dengan konstruksi kayu, uk. 11 m x 4 m, L. 30 cm (1 unit)',
                'lighting photo (1 pack)',
            ]
        ],
        [
            'title' => 'Sekretariat PCO (Ruang Kantor/Tamu KK 2/BAMUS Gedung Nusantara):',
            'subitems' => [
                'Stationery berisi kertas A4',
                'Ballpoint,',
                'Stabilo',
                'Map plastik bening',
                'Spidol warna merah, biru, dan hitam',
                'Stapler medium dan kecil beserta isi',
                'Gunting',
                'Klip kertas',
                'Power plug/extension minimal 6 plug (5 unit)',
            ]
        ],
        [
            'title' => 'Kebersihan tambahan terjaga pada Gedung Nusantara, Gedung Nusantara II/public area – lobby dan ruang ibadah terkait.',
            'subitems' => [
            ]
        ],
        [
            'title' => 'Signage venue/penunjuk arah/meeting room signage terpasang baik dan penempatan sesuai arahan panitia.',
            'subitems' => [
            ]
        ],
        [
            'title' => 'HT 20 unit /intercom 5 unit/perangkat komunikasi petugas tersedia dan berfungsi pada area kegiatan.',
            'subitems' => [
            ]
        ],
        [
            'title' => 'Dokumentasi foto/video serta catatan kejadian/ketidaksesuaian di area DPR RI tersedia.',
            'subitems' => [
            ]
        ],
    ],
    'KKH-04-TRANSPORT' => [
        [
            'title' => 'Bis penjemputan/pengantaran/shuttle sesuai jenis penggunaan hari:',
            'subitems' => [
                'kapasitas 59 seater',
                '4 unit sesuai rencana',
            ]
        ],
        [
            'title' => 'Bis dilengkapi GPS tracking system dan alat pengamanan standar (pemecah kaca, fire extinguisher, dll.).',
            'subitems' => [
            ]
        ],
        [
            'title' => 'Rute sesuai agenda:',
            'subitems' => [
                'Bandara-Hotel',
                'Hotel-Bandara',
                'Hotel-DPR-Hotel',
                'Hotel-Museum Nasional',
                'Hotel-Lokus Kunker',
            ]
        ],
        [
            'title' => 'Durasi kendaraan dipantau:',
            'subitems' => [
                'maks. 12 jam untuk shuttle/operasional',
                '6 jam untuk pengantaran Hotel-Bandara',
            ]
        ],
        [
            'title' => 'Kendaraan angkut Mini Bus:',
            'subitems' => [
                'Mini Bus 16 seater Min. tahun 2021',
                'AC dan alat GPS Tracking system',
                'Biaya sewa sudah termasuk bahan bakar, supir, biaya tol dan parkir, serta uang makan supir',
                'Biaya sewa max 12 jam',
            ]
        ],
        [
            'title' => 'Kendaraan operasional:',
            'subitems' => [
                'mini MPV atau VAN minimum tahun 2021 up',
                'AC',
                'Pengharum kendaraan',
                'Termasuk driver, tol dan bahan bakar',
                'Harga sewa per 12 jam',
                'Memiliki GPS tracking system',
            ]
        ],
        [
            'title' => 'Stiker kendaraan shuttle pada kaca depan terpasang bila digunakan sesuai kebutuhan.',
            'subitems' => [
            ]
        ],
        [
            'title' => 'Waktu berangkat/tiba, perubahan rute, kendala kendaraan dan tindak lanjut dicatat.',
            'subitems' => [
            ]
        ],
    ],
    'KKH-05-KUNKER' => [
        [
            'title' => 'Kedatangan peserta di lokus dan kepulangan ke hotel terkoordinasi sesuai agenda/daftar peserta panitia.',
            'subitems' => [
            ]
        ],
        [
            'title' => 'Bis shuttle Hotel-Lokus Kunker memenuhi kapasitas:',
            'subitems' => [
                'tahun 2020',
                '4 Unit',
                'kapasitas 59 seater (termasuk supir)',
                'bahan bakar, biaya tol, dan parkir,',
                'GPS tracking dan alat pengamanan standar (pemecah kaca, fire extenguiser dlsb)',
            ]
        ],
        [
            'title' => 'Ketersediaan kendaraan selama kunjungan dipantau dan perubahan operasional dicatat.',
            'subitems' => [
            ]
        ],
        [
            'title' => 'Koordinasi petugas transportasi/EO/PIC lokus berjalan dan perubahan agenda diteruskan kepada pihak terkait.',
            'subitems' => [
            ]
        ],
        [
            'title' => 'Pergerakan peserta pada titik turun/naik kendaraan berlangsung tertib dan kendala lapangan dicatat.',
            'subitems' => [
            ]
        ],
        [
            'title' => 'Perangkat komunikasi lapangan tersedia/berfungsi sesuai kebutuhan koordinasi.',
            'subitems' => [
            ]
        ],
        [
            'title' => 'Dokumentasi foto/video kegiatan dan bukti temuan pengawasan tersedia.',
            'subitems' => [
            ]
        ],
    ],
    'KKH-06-MUSEUM' => [
        [
            'title' => 'Tiket masuk Museum Nasional dan Immersive Studio tersedia/digunakan sesuai kebutuhan peserta (210 pax).',
            'subitems' => [
            ]
        ],
        [
            'title' => 'Kedatangan, masuk area, dan kepulangan peserta terkoordinasi sesuai agenda dan daftar peserta panitia.',
            'subitems' => [
            ]
        ],
        [
            'title' => 'Bis shuttle Hotel-Museum Nasional memenuhi kapasitas:',
            'subitems' => [
                'Bis tahun 2020',
                '4 Unit',
                'kapasitas 59 seater (termasuk supir)',
                'bahan bakar, biaya tol, dan parkir',
                'GPS tracking dan alat pengamanan standar (pemecah kaca, fire extenguiser dlsb)',
            ]
        ],
        [
            'title' => 'Ketersediaan kendaraan dipantau; waktu tiba/berangkat serta kendala transportasi dicatat.',
            'subitems' => [
            ]
        ],
        [
            'title' => 'Koordinasi EO/PIC dengan petugas transportasi dan peserta berjalan selama city tour.',
            'subitems' => [
            ]
        ],
        [
            'title' => 'Perangkat komunikasi lapangan tersedia/berfungsi sesuai kebutuhan koordinasi.',
            'subitems' => [
            ]
        ],
        [
            'title' => 'Dokumentasi foto/video kegiatan dan bukti temuan pengawasan tersedia.',
            'subitems' => [
            ]
        ],
    ],
    'PRD-01A' => [
        [
            'title' => 'Backdrop Main Stage:',
            'subitems' => [
                'Konstruksi kayu 7 m x 4 m',
                'flexy indoor',
                'cetak warna',
                '1 Unit',
            ]
        ],
        [
            'title' => 'Bendera Vandel Fraksi:',
            'subitems' => [
                '80 x 120 cm',
                'beludru/satin/drill',
                'bordir + rumbai',
                'tiang pataka 2,5 m',
                '8 Unit',
            ]
        ],
        [
            'title' => 'Backdrop Area Group Photo:',
            'subitems' => [
                'Konstruksi kayu 11 m x 4 m',
                'digital printing bolak-balik',
                '1 Unit',
            ]
        ],
        [
            'title' => 'Handheld Signage:',
            'subitems' => [
                'A3 polyfoam + sticker vinyl',
                'gagang min. 30 cm',
                '6 Unit',
            ]
        ],
        [
            'title' => 'T Banner Area DPR RI:',
            'subitems' => [
                '60 x 270 cm',
                'vinyl outdoor full color + rangka',
                '35 unit/titik',
                'termasuk pasang, bongkar, kontrol',
            ]
        ],
    ],
    'PRD-01B' => [
        [
            'title' => 'Stiker Kendaraan Shuttle:',
            'subitems' => [
                'A4 landscape',
                'Vinyl',
                'ditempel di kaca depan',
                '6 pcs',
            ]
        ],
        [
            'title' => 'Totem Nama Event:',
            'subitems' => [
                'Konstruksi kayu ±244 x 60 x 12 cm',
                'sticker depan-belakang',
                '1 Unit',
            ]
        ],
        [
            'title' => 'Signage Direction:',
            'subitems' => [
                'Konstruksi kayu ±244 x 60 x 12 cm',
                'sticker depan-belakang',
                '1 Unit',
            ]
        ],
        [
            'title' => 'Standing/Meeting Signage:',
            'subitems' => [
                'Tripod + media cetak A0',
                'periksa desain wording, penempatan',
                '4 Unit',
            ]
        ],
        [
            'title' => 'Produksi lainnya Item: ____________________ | Qty: ______ | Spesifikasi: ______________________________',
            'subitems' => [
            ]
        ],
    ],
    'PRD-02A' => [
        [
            'title' => 'Jaket Panitia:',
            'subitems' => [
                'Cotton Fleece PE',
                'furing peles',
                'kancing snap plastik',
                '160 pcs',
            ]
        ],
        [
            'title' => 'Kaos Polo Panitia:',
            'subitems' => [
                'Cotton Combed 24S',
                'bordir logo',
                '160 pcs',
            ]
        ],
        [
            'title' => 'Jaket Peserta:',
            'subitems' => [
                'Parasut;',
                'kancing/resleting YKK',
                'bordir warna',
                'standar PARJA 2025',
                '200 pcs',
            ]
        ],
        [
            'title' => 'Kaos Peserta:',
            'subitems' => [
                'T-shirt katun',
                'logo sablon laser',
                'standar PARJA 2025',
                '200 pcs',
            ]
        ],
        [
            'title' => 'Topi Peserta:',
            'subitems' => [
                'Model baseball',
                'bordir logo',
                'desain/warna sesuai arahan panitia',
                '200 pcs',
            ]
        ],
        [
            'title' => 'Celana Training:',
            'subitems' => [
                'Bahan polar fleece',
                'desain/warna sesuai arahan',
                'standar PARJA 2025',
                '200 pcs',
            ]
        ],
        [
            'title' => 'Jaket Peserta Terbaik/Terfavorit:',
            'subitems' => [
                'Parasut',
                'resleting',
                'bordir depan dan sablon belakang',
                '6 pcs',
            ]
        ],
    ],
    'PRD-02B' => [
        [
            'title' => 'Tas Conference:',
            'subitems' => [
                'Ransel anti air untuk laptop 15 inci',
                'desain/warna sesuai arahan',
                '360 pcs',
            ]
        ],
        [
            'title' => 'Tumbler:',
            'subitems' => [
                'Stainless steel 750 ml',
                'grafir logo event',
                '210 pcs',
            ]
        ],
        [
            'title' => 'Blocknote + Pen:',
            'subitems' => [
                'Blocknote A5 50 lembar',
                'pulpen metal grafir',
                '210 set',
            ]
        ],
        [
            'title' => 'Participant Pin:',
            'subitems' => [
                'Lapel pin magnet warna emas diameter 3 cm',
                'box',
                '210 pcs',
            ]
        ],
        [
            'title' => 'ID Card & Lanyard',
            'subitems' => [
                'PVC 18 x 12 cm',
                'full color',
                '300 set',
            ]
        ],
        [
            'title' => 'Sertifikat + Frame:',
            'subitems' => [
                'Art Carton 210 gr',
                'map/frame',
                '10 set',
            ]
        ],
        [
            'title' => 'Bag Tag / Luggage Tak:',
            'subitems' => [
                'Kulit sintetis',
                'kantong kartu',
                '210 pcs',
            ]
        ],
        [
            'title' => 'Plakat Peserta Terbaik:',
            'subitems' => [
                'Akrilik',
                'box beludru',
                '7 pcs',
            ]
        ],
    ],
    'PRD-03' => [
        [
            'title' => 'Multimedia Bumper & Graphic Konten audio/visual/audio-visual sesuai KV; bumper program/narasumber/lower third; 1 pkg',
            'subitems' => [
            ]
        ],
        [
            'title' => 'Desain Grafis & Sosial Media Desain grafis 2D/layout; konten sesuai draft/arahan Humas; 1 pkg',
            'subitems' => [
            ]
        ],
        [
            'title' => 'Dokumentasi Foto:',
            'subitems' => [
                '2 photographer',
                'output raw pada hardisk + edited via drive',
                '1 paket',
            ]
        ],
        [
            'title' => 'Dokumentasi Video Liputan kegiatan oleh 2 cameraman profesional; 1 paket',
            'subitems' => [
            ]
        ],
        [
            'title' => 'Video Highlight:',
            'subitems' => [
                'Versi pendek 3-5 menit',
                'full version',
                '2 paket',
            ]
        ],
        [
            'title' => 'Sameday Edit / Daily Recap Rekap kegiatan 1 hari penuh pada simulasi sidang, diedit hari yang sama; 1 paket',
            'subitems' => [
            ]
        ],
        [
            'title' => 'Final Report:',
            'subitems' => [
                'Jilid soft cover Art Paper 150',
                'isi HVS A4 80 gr',
                'print color',
                '5 set',
            ]
        ],
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