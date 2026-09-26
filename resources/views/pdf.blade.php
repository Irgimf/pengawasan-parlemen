<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $submission->form_code }} - Parlemen Remaja 2026</title>
    <style>
        body { 
            font-family: Helvetica, Arial, sans-serif; 
            font-size: 10px; 
            color: #000; 
            margin: 0; 
            padding: 0; 
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 10px; 
        }
        th, td { 
            border: 1px solid #000 !important; 
            padding: 6px 8px; 
            vertical-align: middle; 
        }
        .no-border, .no-border th, .no-border td { border: none !important; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-bold { font-weight: bold; }
        .bg-gray { background-color: #f3f4f6; }
        .bg-dark-gray { background-color: #2d2d2d; color: #ffffff; }
        .bg-dark-red { background-color: #8B1A24; color: #ffffff; }

        /* Spesifik ukuran kolom tabel utama KKH */
        .col-no { width: 4%; text-align: center; }
        .col-objek { width: 46%; }
        .col-status { width: 20%; text-align: center; }
        .col-catatan { width: 30%; }

        .signature-img { max-height: 40px; width: auto; display: block; margin: 0 auto; }
        .sig-box { height: 45px; text-align: center; vertical-align: bottom; }
        
        /* Make brackets monospaced for checkboxes */
        .check-box { font-family: monospace; font-size: 11px; font-weight: bold; }
    </style>
</head>
<body>

    @if($submission->form_code == 'RHP-00')
        <!-- ================================================================= -->
        <!-- 1. TEMPLATE KHUSUS RHP-00 (REKAP HARIAN KONSULTAN PENGAWAS)       -->
        <!-- ================================================================= -->
        
        <!-- HEADER BLOCK RHP-00 -->
        <table>
            <tr>
                <td colspan="2" rowspan="2" class="text-center text-bold bg-dark-red" style="font-size: 14px; width: 20%;">PARLEMEN<br>REMAJA 2026</td>
                <td colspan="4" class="text-center" style="font-size: 13px;"><span class="text-bold">REKAP HARIAN KONSULTAN PENGAWAS</span><br><span style="font-size: 10px; color: #555;">Rekap Koordinator / Team Leader | Pelaksanaan Parlemen Remaja Tahun 2026</span></td>
                <td colspan="2" class="text-center bg-gray" style="width: 15%;">Kode<br><span class="text-bold">RHP-00</span></td>
            </tr>
            <tr>
                <td colspan="4" class="text-center" style="border-top: none;"></td>
                <td colspan="2" class="text-center bg-gray" style="border-top: none;">Revisi<br><span class="text-bold">01</span></td>
            </tr>
            <tr>
                <td style="width: 12%;" class="text-bold bg-gray">Hari/Tanggal</td>
                <td colspan="2">{{ \Carbon\Carbon::parse($submission->start_time)->format('l, d F Y') }}</td>
                <td style="width: 10%;" class="text-bold bg-gray">Waktu</td>
                <td>{{ \Carbon\Carbon::parse($submission->start_time)->format('H:i') }} s.d. {{ $submission->end_time ? \Carbon\Carbon::parse($submission->end_time)->format('H:i') : '____ : ____' }}</td>
                <td style="width: 12%;" class="text-bold bg-gray">Lokasi/Pos</td>
                <td colspan="2">Seluruh pos pengawasan</td>
            </tr>
            <tr>
                <td class="text-bold bg-gray">Petugas<br>Pengawas</td>
                <td colspan="2">{{ $submission->supervisor_name }}</td>
                <td class="text-bold bg-gray">Penyedia/EO/PIC</td>
                <td>{{ $submission->provider_name ?? '' }}</td>
                <td class="text-bold bg-gray">Agenda/Tahap</td>
                <td colspan="2">{{ $submission->form_data['agenda'] ?? '' }}</td>
            </tr>
        </table>

        <p style="margin: 3px 0 8px 0; font-size: 9px; font-style: italic; color: #555;">Diisi oleh Koordinator/Team Leader berdasarkan seluruh Kertas Kerja Harian (KKH), formulir produksi (PRD), serta register temuan.</p>

        <!-- TABEL UTAMA RHP-00 -->
        <table>
            <tr class="bg-dark-gray text-center text-bold">
                <th style="width: 4%;">No.</th>
                <th style="width: 31%;">Pos / Objek Rekap</th>
                <th style="width: 15%;">Status Umum</th>
                <th style="width: 8%;">Open</th>
                <th style="width: 8%;">Closed</th>
                <th style="width: 34%;">Temuan Prioritas / No. FT / Tindak Lanjut</th>
            </tr>
            @if(isset($questions) && count($questions) > 0)
                @foreach($questions as $index => $item)
                    @php
                        $is_array = is_array($item);
                        $title = $is_array ? $item['title'] : $item;
                        $subitems = $is_array ? $item['subitems'] : [];
                    @endphp
                    @if(empty($subitems))
                        @php
                            $aktual = $submission->form_data['q_'.$index.'_aktual'] ?? '';
                            $status = $submission->form_data['q_'.$index.'_status'] ?? '';
                            $catatan = $submission->form_data['q_'.$index.'_catatan'] ?? '';
                        @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{!! nl2br(e($title)) !!}</td>
                            <td class="text-center">{{ $aktual }}</td>
                            <td class="text-center check-box" style="font-weight: normal;">
                                [{{ $status == 'S' ? 'X' : ' ' }}] S <br>
                                [{{ $status == 'TS' ? 'X' : ' ' }}] TS <br>
                                [{{ $status == 'N/A' ? 'X' : ' ' }}] N/A
                            </td>
                            <td>{{ $catatan }}</td>
                        </tr>
                    @else
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td colspan="4" class="text-bold">{!! nl2br(e($title)) !!}</td>
                        </tr>
                        @foreach($subitems as $subIndex => $subitem)
                            @php
                                $aktual = $submission->form_data['q_'.$index.'_sub_'.$subIndex.'_aktual'] ?? '';
                                $status = $submission->form_data['q_'.$index.'_sub_'.$subIndex.'_status'] ?? '';
                                $catatan = $submission->form_data['q_'.$index.'_sub_'.$subIndex.'_catatan'] ?? '';
                            @endphp
                            <tr>
                                <td></td>
                                <td>- {!! nl2br(e($subitem)) !!}</td>
                                <td class="text-center">{{ $aktual }}</td>
                                <td class="text-center check-box" style="font-weight: normal;">
                                    [{{ $status == 'S' ? 'X' : ' ' }}] S <br>
                                    [{{ $status == 'TS' ? 'X' : ' ' }}] TS <br>
                                    [{{ $status == 'N/A' ? 'X' : ' ' }}] N/A
                                </td>
                                <td>{{ $catatan }}</td>
                            </tr>
                        @endforeach
                    @endif
                @endforeach
            @else
                <tr><td colspan="6" class="text-center"><em>Tidak ada data rekap pos yang ditemukan.</em></td></tr>
            @endif
        </table>

        <!-- TEMUAN PRIORITAS / KEPUTUSAN KOORDINASI RHP-00 -->
        <table>
            <tr class="bg-dark-red">
                <th colspan="6" class="text-left text-bold">TEMUAN PRIORITAS / KEPUTUSAN KOORDINASI</th>
            </tr>
            <tr class="bg-gray text-center text-bold">
                <td style="width: 5%;">No.</td>
                <td style="width: 15%;">No. FT</td>
                <td style="width: 30%;">Temuan</td>
                <td style="width: 25%;">Instruksi / Keputusan</td>
                <td style="width: 15%;">PIC / Target</td>
                <td style="width: 10%;">Status</td>
            </tr>
            @for ($i = 0; $i < 3; $i++)
                <tr>
                    <td class="text-center">&nbsp;</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            @endfor
        </table>

        <!-- RINGKASAN HARIAN -->
        <table>
            <tr class="bg-dark-red">
                <th colspan="2" class="text-left text-bold">RINGKASAN HARIAN</th>
            </tr>
            <tr>
                <td style="width: 25%; font-weight: bold;" class="bg-gray">Kejadian penting / kendala</td>
                <td style="height: 40px; vertical-align: top;">{{ $submission->form_data['kendala'] ?? '' }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;" class="bg-gray">Keputusan / koordinasi</td>
                <td style="height: 40px; vertical-align: top;">{{ $submission->form_data['keputusan'] ?? '' }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;" class="bg-gray">Rencana tindak lanjut<br>berikutnya</td>
                <td style="height: 40px; vertical-align: top;">{{ $submission->form_data['rtl'] ?? '' }}</td>
            </tr>
        </table>

        <!-- SIGNATURE BLOCK RHP-00 -->
        <table class="no-border" style="margin-top: 15px;">
            <tr>
                <td class="no-border text-center" style="width: 33%;">
                    Koordinator/Team Leader<br>
                    <div class="sig-box">
                        @if($submission->signature_supervisor)
                            <img src="{{ $submission->signature_supervisor }}" class="signature-img">
                        @endif
                    </div>
                    <span style="text-decoration: underline;">{{ $submission->supervisor_name }}</span>
                </td>
                <td class="no-border text-center" style="width: 34%;">
                    Perwakilan Penyedia/EO<br>
                    <div class="sig-box">
                        @if($submission->signature_provider)
                            <img src="{{ $submission->signature_provider }}" class="signature-img">
                        @endif
                    </div>
                    <span style="text-decoration: underline;">{{ $submission->provider_name ?? '........................' }}</span>
                </td>
                <td class="no-border text-center" style="width: 33%;">
                    Mengetahui Panitia*<br>
                    <div class="sig-box">
                        @if($submission->signature_committee)
                            <img src="{{ $submission->signature_committee }}" class="signature-img">
                        @endif
                    </div>
                    <span style="text-decoration: underline;">{{ $submission->committee_name ?? '........................' }}</span>
                </td>
            </tr>
        </table>

    @elseif(str_starts_with($submission->form_code, 'PRD'))
        <!-- ================================================================= -->
        <!-- 2. TEMPLATE KHUSUS FORMULIR PRODUKSI (PRD)                       -->
        <!-- ================================================================= -->
        
        <table style="border-collapse: collapse; width: 100%;">
            <tr>
                <td colspan="2" rowspan="2" class="text-center text-bold bg-dark-red" style="font-size: 14px; width: 20%; border: 1px solid #000;">PARLEMEN<br>REMAJA 2026</td>
                <td colspan="5" class="text-center text-bold" style="font-size: 13px; border: 1px solid #000;">
                    FORMULIR PENGAWASAN PRODUKSI KEGIATAN
                </td>
                <td class="text-center bg-gray" style="width: 15%; border: 1px solid #000;">Kode<br><span class="text-bold">{{ $submission->form_code }}</span></td>
            </tr>
            <tr>
                <td colspan="5" class="text-center" style="border: 1px solid #000; font-size: 10px; color: #555;">
                    @if($submission->form_code == 'PRD-01A')
                        A1. Produksi Fisik &amp; Backdrop | Parlemen Remaja Tahun 2026
                    @elseif($submission->form_code == 'PRD-01B')
                        A2. Printing, Signage &amp; Instalasi | Parlemen Remaja Tahun 2026
                    @elseif($submission->form_code == 'PRD-02A')
                        B1. Seragam Panitia &amp; Peserta | Parlemen Remaja Tahun 2026
                    @elseif($submission->form_code == 'PRD-02B')
                        B2. Participant Kit &amp; Souvenir | Parlemen Remaja Tahun 2026
                    @else
                        C. Multimedia, Desain &amp; Dokumentasi | Parlemen Remaja Tahun 2026
                    @endif
                </td>
                <td class="text-center bg-gray" style="border: 1px solid #000;">Revisi<br><span class="text-bold">01</span></td>
            </tr>
            <tr>
                <td style="width: 12%; border: 1px solid #000;" class="text-bold bg-gray">Hari/Tanggal</td>
                <td colspan="2" style="border: 1px solid #000;">{{ \Carbon\Carbon::parse($submission->start_time)->format('l, d F Y') }}</td>
                <td style="width: 10%; border: 1px solid #000;" class="text-bold bg-gray">Waktu</td>
                <td style="border: 1px solid #000;">{{ \Carbon\Carbon::parse($submission->start_time)->format('H:i') }} s.d. {{ $submission->end_time ? \Carbon\Carbon::parse($submission->end_time)->format('H:i') : '____ : ____' }}</td>
                <td style="width: 12%; border: 1px solid #000;" class="text-bold bg-gray">Lokasi/Pos</td>
                <td colspan="2" style="border: 1px solid #000;">{{ $submission->location }}</td>
            </tr>
            <tr>
                <td class="text-bold bg-gray" style="border: 1px solid #000;">Petugas<br>Pengawas</td>
                <td colspan="2" style="border: 1px solid #000;">{{ $submission->supervisor_name }}</td>
                <td class="text-bold bg-gray" style="border: 1px solid #000;">Penyedia/EO/PIC</td>
                <td style="border: 1px solid #000;">{{ $submission->provider_name ?? '' }}</td>
                <td class="text-bold bg-gray" style="border: 1px solid #000;">Agenda/Tahap</td>
                <td colspan="2" style="border: 1px solid #000;">{{ $submission->form_data['agenda'] ?? '' }}</td>
            </tr>
        </table>

        <p style="margin: 3px 0 8px 0; font-size: 9px; font-style: italic; color: #555;">Petunjuk: isi hasil aktual/qty yang diperiksa. Status: [ ] S, [ ] TS, [ ] N/A. Setiap TS harus dirujuk ke No. Temuan FT-01.</p>

        <!-- TABEL UTAMA ITEM PRODUKSI & QTY -->
        <table>
            <tr class="bg-dark-gray text-center text-bold">
                <th style="width: 4%;">No.</th>
                <th style="width: 41%;">Item Produksi & Acuan / Target</th>
                <th style="width: 18%;">Hasil Aktual / Qty</th>
                <th style="width: 14%;">Status</th>
                <th style="width: 23%;">Catatan / No. FT</th>
            </tr>
            @if(isset($questions) && count($questions) > 0)
                @foreach($questions as $index => $item)
                    @php
                        $aktual = $submission->form_data['q_'.$index.'_aktual'] ?? '';
                        $status = $submission->form_data['q_'.$index.'_status'] ?? '';
                        $catatan = $submission->form_data['q_'.$index.'_catatan'] ?? '';
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{!! nl2br(e($item)) !!}</td>
                        <td class="text-center">{{ $aktual }}</td>
                        <td class="text-center check-box" style="font-weight: normal;">
                            [{{ $status == 'S' ? 'X' : ' ' }}] S <br>
                            [{{ $status == 'TS' ? 'X' : ' ' }}] TS <br>
                            [{{ $status == 'N/A' ? 'X' : ' ' }}] N/A
                        </td>
                        <td>{{ $catatan }}</td>
                    </tr>
                @endforeach
            @else
                <tr><td colspan="5" class="text-center"><em>Tidak ada item produksi yang ditemukan.</em></td></tr>
            @endif
        </table>

        <!-- BLOK CHECKPOINT PENERIMAAN / MUTU -->
        <table>
            <tr class="bg-dark-red">
                <th colspan="3" class="text-left text-bold">CHECKPOINT PENERIMAAN / MUTU</th>
            </tr>
            @php
                $chk = $submission->form_data['checkpoint'] ?? [];
            @endphp
            @if(in_array($submission->form_code, ['PRD-02A', 'PRD-02B']))
                {{-- Checkpoint khusus untuk Seragam & Souvenir --}}
                <tr>
                    <td style="width: 33%;" class="check-box">[ {{ in_array('sample', $chk) ? 'X' : ' ' }}] <span style="font-family: Helvetica, Arial, sans-serif; font-weight: normal;">Sample/prototype telah disetujui</span></td>
                    <td style="width: 33%;" class="check-box">[ {{ in_array('material', $chk) ? 'X' : ' ' }}] <span style="font-family: Helvetica, Arial, sans-serif; font-weight: normal;">Bahan/material &amp; ukuran sesuai</span></td>
                    <td style="width: 34%;" class="check-box">[ {{ in_array('logo', $chk) ? 'X' : ' ' }}] <span style="font-family: Helvetica, Arial, sans-serif; font-weight: normal;">Logo/desain/warna/finishing sesuai</span></td>
                </tr>
                <tr>
                    <td class="check-box">[ {{ in_array('jumlah', $chk) ? 'X' : ' ' }}] <span style="font-family: Helvetica, Arial, sans-serif; font-weight: normal;">Jumlah sesuai kontrak/BoQ</span></td>
                    <td class="check-box">[ {{ in_array('cacat', $chk) ? 'X' : ' ' }}] <span style="font-family: Helvetica, Arial, sans-serif; font-weight: normal;">Tidak cacat/rusak/salah cetak</span></td>
                    <td class="check-box">[ {{ in_array('packing', $chk) ? 'X' : ' ' }}] <span style="font-family: Helvetica, Arial, sans-serif; font-weight: normal;">Packing/distribusi &amp; bukti penerimaan tersedia</span></td>
                </tr>
            @elseif($submission->form_code == 'PRD-03')
                {{-- Checkpoint khusus untuk Multimedia --}}
                <tr>
                    <td style="width: 33%;" class="check-box">[ {{ in_array('file', $chk) ? 'X' : ' ' }}] <span style="font-family: Helvetica, Arial, sans-serif; font-weight: normal;">File/output dapat dibuka dan lengkap</span></td>
                    <td style="width: 33%;" class="check-box">[ {{ in_array('konten', $chk) ? 'X' : ' ' }}] <span style="font-family: Helvetica, Arial, sans-serif; font-weight: normal;">Konten sesuai KV/arahan Humas</span></td>
                    <td style="width: 34%;" class="check-box">[ {{ in_array('nama', $chk) ? 'X' : ' ' }}] <span style="font-family: Helvetica, Arial, sans-serif; font-weight: normal;">Nama/judul/lower third akurat</span></td>
                </tr>
                <tr>
                    <td class="check-box">[ {{ in_array('audio', $chk) ? 'X' : ' ' }}] <span style="font-family: Helvetica, Arial, sans-serif; font-weight: normal;">Audio &amp; visual berfungsi baik</span></td>
                    <td class="check-box">[ {{ in_array('durasi', $chk) ? 'X' : ' ' }}] <span style="font-family: Helvetica, Arial, sans-serif; font-weight: normal;">Durasi/format/output sesuai</span></td>
                    <td class="check-box">[ {{ in_array('serahterima', $chk) ? 'X' : ' ' }}] <span style="font-family: Helvetica, Arial, sans-serif; font-weight: normal;">Serah terima &amp; penyimpanan file terdokumentasi</span></td>
                </tr>
            @else
                {{-- Checkpoint default untuk PRD-01A / PRD-01B (Produksi Fisik & Printing) --}}
                <tr>
                    <td style="width: 33%;" class="check-box">[ {{ in_array('desain', $chk) ? 'X' : ' ' }}] <span style="font-family: Helvetica, Arial, sans-serif; font-weight: normal;">Desain/warna/wording disetujui</span></td>
                    <td style="width: 33%;" class="check-box">[ {{ in_array('material', $chk) ? 'X' : ' ' }}] <span style="font-family: Helvetica, Arial, sans-serif; font-weight: normal;">Material &amp; ukuran sesuai</span></td>
                    <td style="width: 34%;" class="check-box">[ {{ in_array('jumlah', $chk) ? 'X' : ' ' }}] <span style="font-family: Helvetica, Arial, sans-serif; font-weight: normal;">Jumlah/qty sesuai</span></td>
                </tr>
                <tr>
                    <td class="check-box">[ {{ in_array('finishing', $chk) ? 'X' : ' ' }}] <span style="font-family: Helvetica, Arial, sans-serif; font-weight: normal;">Cetak/finishing rapi &amp; tidak cacat</span></td>
                    <td class="check-box">[ {{ in_array('pemasangan', $chk) ? 'X' : ' ' }}] <span style="font-family: Helvetica, Arial, sans-serif; font-weight: normal;">Pemasangan/penempatan aman</span></td>
                    <td class="check-box">[ {{ in_array('pemeliharaan', $chk) ? 'X' : ' ' }}] <span style="font-family: Helvetica, Arial, sans-serif; font-weight: normal;">Pemeliharaan/pembongkaran dilakukan</span></td>
                </tr>
            @endif
        </table>

        <!-- KESIMPULAN & TINDAK LANJUT PRODUKSI -->
        <table style="margin-top: -10px; border-top: none;">
            <tr>
                <td style="width: 15%; font-weight: bold; border-top: none;">Kesimpulan</td>
                <td style="width: 40%; border-top: none;" class="check-box">
                    <span style="font-weight: normal;">[{{ ($submission->form_data['kesimpulan'] ?? '') == 'Diterima' ? 'X' : ' ' }}] <span style="font-family: Helvetica, Arial, sans-serif;">Diterima</span> &nbsp;</span>
                    <span style="font-weight: normal;">[{{ ($submission->form_data['kesimpulan'] ?? '') == 'Perbaikan' ? 'X' : ' ' }}] <span style="font-family: Helvetica, Arial, sans-serif;">Diterima dengan perbaikan</span> &nbsp;</span>
                    <span style="font-weight: normal;">[{{ ($submission->form_data['kesimpulan'] ?? '') == 'Ditolak' ? 'X' : ' ' }}] <span style="font-family: Helvetica, Arial, sans-serif;">Ditolak/harus diganti</span></span>
                </td>
                <td style="width: 15%; font-weight: bold; border-top: none;">Bukti</td>
                <td style="width: 30%; border-top: none;">Foto/File No.: {{ $submission->form_data['bukti_no'] ?? '____________________' }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Tindak lanjut</td>
                <td>
                    No. FT: {{ $submission->form_data['ft_no'] ?? '________' }} &nbsp;| Uraian singkat: {{ $submission->form_data['ft_uraian'] ?? '____________________' }}
                </td>
                <td style="font-weight: bold;">PIC / Target</td>
                <td>{{ $submission->form_data['ft_pic'] ?? '__________  /  __________' }}</td>
            </tr>
        </table>

        <!-- TANDA TANGAN PRD -->
        <table class="no-border" style="margin-top: 15px;">
            <tr>
                <td class="no-border text-center" style="width: 33%;">
                    Konsultan Pengawas<br>
                    <div class="sig-box">
                        @if($submission->signature_supervisor)
                            <img src="{{ $submission->signature_supervisor }}" class="signature-img">
                        @endif
                    </div>
                    <span style="text-decoration: underline;">{{ $submission->supervisor_name }}</span>
                </td>
                <td class="no-border text-center" style="width: 34%;">
                    PIC Penyedia/EO<br>
                    <div class="sig-box">
                        @if($submission->signature_provider)
                            <img src="{{ $submission->signature_provider }}" class="signature-img">
                        @endif
                    </div>
                    <span style="text-decoration: underline;">{{ $submission->provider_name ?? '........................' }}</span>
                </td>
                <td class="no-border text-center" style="width: 33%;">
                    Mengetahui Panitia*<br>
                    <div class="sig-box">
                        @if($submission->signature_committee)
                            <img src="{{ $submission->signature_committee }}" class="signature-img">
                        @endif
                    </div>
                    <span style="text-decoration: underline;">{{ $submission->committee_name ?? '........................' }}</span>
                </td>
            </tr>
        </table>

    @elseif(!in_array($submission->form_code, ['RT-01', 'FT-01', 'BA-TL-01']))
        <!-- ================================================================= -->
        <!-- 3. TEMPLATE STANDAR KKH (KERTAS KERJA HARIAN PENGAWAS)            -->
        <!-- ================================================================= -->
        
        <!-- HEADER BLOCK KKH -->
        <table>
            <tr>
                <td colspan="2" rowspan="2" class="text-center text-bold bg-dark-red" style="font-size: 14px; width: 20%;">PARLEMEN<br>REMAJA 2026</td>
                <td colspan="5" class="text-center text-bold" style="font-size: 13px;">KERTAS KERJA HARIAN PENGAWAS<br><span style="font-weight:normal; font-size: 10px; color: #555;">Lokasi: {{ $submission->location }} | Pelaksanaan Parlemen Remaja Tahun 2026</span></td>
                <td colspan="2" class="text-center bg-gray" style="width: 15%;">Kode<br><span class="text-bold">{{ $submission->form_code }}</span></td>
            </tr>
            <tr>
                <td colspan="5" class="text-center" style="border-top: none;"></td>
                <td colspan="2" class="text-center bg-gray" style="border-top: none;">Revisi<br><span class="text-bold">01</span></td>
            </tr>
            <tr>
                <td style="width: 12%;" class="text-bold bg-gray">Hari/Tanggal</td>
                <td colspan="3">{{ \Carbon\Carbon::parse($submission->start_time)->format('l, d F Y') }}</td>
                <td style="width: 8%;" class="text-bold bg-gray">Waktu</td>
                <td colspan="2">{{ \Carbon\Carbon::parse($submission->start_time)->format('H:i') }} s.d. {{ $submission->end_time ? \Carbon\Carbon::parse($submission->end_time)->format('H:i') : '____ : ____' }}</td>
                <td style="width: 12%;" class="text-bold bg-gray">Lokasi/Pos</td>
                <td>{{ $submission->location }}</td>
            </tr>
            <tr>
                <td class="text-bold bg-gray">Petugas<br>Pengawas</td>
                <td colspan="3">{{ $submission->supervisor_name }}</td>
                <td class="text-bold bg-gray" colspan="2">Penyedia/EO/PIC</td>
                <td>{{ $submission->provider_name ?? '' }}</td>
                <td class="text-bold bg-gray">Agenda/Tahap</td>
                <td>{{ $submission->form_data['agenda'] ?? '' }}</td>
            </tr>
        </table>

        <p style="margin: 3px 0 8px 0; font-size: 9px; font-style: italic; color: #555;">Petunjuk: beri tanda pada status [X] S (Sesuai), [X] TS (Tidak Sesuai), atau [X] N/A. Jika TS, cantumkan No. Temuan FT-01 dan tindakan awal pada kolom catatan.</p>

        <!-- MAIN CHECKLIST BLOCK KKH -->
        <table>
            <tr class="bg-dark-gray text-bold">
                <th class="col-no text-center">No.</th>
                <th class="col-objek text-center">Objek / Kriteria Pengawasan</th>
                <th class="col-status text-center">Status</th>
                <th class="col-catatan text-center">Catatan / No. Temuan</th>
            </tr>
            @if(isset($questions) && count($questions) > 0)
                @foreach($questions as $index => $question)
                    @php
                        $is_array = is_array($question);
                        $title = $is_array ? $question['title'] : $question;
                        $subitems = $is_array ? $question['subitems'] : [];
                    @endphp
                    
                    @if(empty($subitems))
                        @php
                            $status = $submission->form_data['q_'.$index.'_status'] ?? '';
                            $note = $submission->form_data['q_'.$index.'_note'] ?? '';
                        @endphp
                        <tr>
                            <td class="col-no text-center">{{ $index + 1 }}</td>
                            <td class="col-objek">{!! nl2br(e($title)) !!}</td>
                            <td class="col-status text-center check-box" style="font-weight: normal; white-space: nowrap;">
                                [{{ $status == 'S' ? 'X' : ' ' }}] S &nbsp; [{{ $status == 'TS' ? 'X' : ' ' }}] TS &nbsp; [{{ $status == 'N/A' ? 'X' : ' ' }}] N/A
                            </td>
                            <td class="col-catatan">{{ $note }}</td>
                        </tr>
                    @else
                        <tr>
                            <td class="col-no text-center">{{ $index + 1 }}</td>
                            <td class="col-objek text-bold" colspan="3">{!! nl2br(e($title)) !!}</td>
                        </tr>
                        @foreach($subitems as $subIndex => $subitem)
                            @php
                                $status = $submission->form_data['q_'.$index.'_sub_'.$subIndex.'_status'] ?? '';
                                $note = $submission->form_data['q_'.$index.'_sub_'.$subIndex.'_note'] ?? '';
                            @endphp
                            <tr>
                                <td></td>
                                <td class="col-objek">- {!! nl2br(e($subitem)) !!}</td>
                                <td class="col-status text-center check-box" style="font-weight: normal; white-space: nowrap;">
                                    [{{ $status == 'S' ? 'X' : ' ' }}] S &nbsp; [{{ $status == 'TS' ? 'X' : ' ' }}] TS &nbsp; [{{ $status == 'N/A' ? 'X' : ' ' }}] N/A
                                </td>
                                <td class="col-catatan">{{ $note }}</td>
                            </tr>
                        @endforeach
                    @endif
                @endforeach
            @else
                <tr><td colspan="4" class="text-center"><em>Tidak ada kriteria ceklis yang ditemukan.</em></td></tr>
            @endif
        </table>

        <!-- TEMUAN PRIORITAS DAN TINDAK LANJUT BLOCK KKH -->
        <table>
            <tr class="bg-dark-red">
                <th colspan="5" class="text-left text-bold">TEMUAN PRIORITAS DAN TINDAK LANJUT</th>
            </tr>
            <tr class="bg-gray text-center text-bold">
                <th style="width: 15%;">No. FT</th>
                <th style="width: 30%;">Temuan / Ketidaksesuaian</th>
                <th style="width: 25%;">Tindakan yang Diminta</th>
                <th style="width: 15%;">PIC / Target</th>
                <th style="width: 15%;">Status</th>
            </tr>
            @php
                $temuans = $submission->form_data['temuan'] ?? [];
            @endphp

            @if(count($temuans) > 0)
                @foreach($temuans as $t)
                    <tr>
                        <td class="text-center">{{ $t['no_ft'] ?? '-' }}</td>
                        <td>{{ $t['deskripsi'] ?? '-' }}</td>
                        <td>{{ $t['tindakan'] ?? '-' }}</td>
                        <td class="text-center">{{ $t['pic'] ?? '-' }}</td>
                        <td class="text-center">{{ $t['status'] ?? 'Open' }}</td>
                    </tr>
                @endforeach
            @else
                <tr><td class="text-center">&nbsp;</td><td></td><td></td><td></td><td></td></tr>
                <tr><td class="text-center">&nbsp;</td><td></td><td></td><td></td><td></td></tr>
            @endif
        </table>

        <!-- KESIMPULAN HARIAN BLOCK KKH -->
        <table>
            <tr class="bg-dark-red">
                <th colspan="4" class="text-left text-bold">KESIMPULAN HARIAN</th>
            </tr>
            <tr>
                <td style="width: 15%; font-weight: bold;" class="bg-gray">Kondisi</td>
                <td style="width: 45%;" class="check-box">
                    <span style="font-weight: normal;">[{{ ($submission->form_data['kondisi'] ?? '') == 'Sesuai' ? 'X' : ' ' }}] <span style="font-family: Helvetica, Arial, sans-serif;">Sesuai</span> &nbsp;</span>
                    <span style="font-weight: normal;">[{{ ($submission->form_data['kondisi'] ?? '') == 'Catatan' ? 'X' : ' ' }}] <span style="font-family: Helvetica, Arial, sans-serif;">Sesuai dgn catatan</span> &nbsp;</span>
                    <span style="font-weight: normal;">[{{ ($submission->form_data['kondisi'] ?? '') == 'Perbaikan' ? 'X' : ' ' }}] <span style="font-family: Helvetica, Arial, sans-serif;">Perlu perbaikan segera</span></span>
                </td>
                <td style="width: 15%; font-weight: bold;" class="bg-gray">Dokumentasi</td>
                <td style="width: 25%;">Foto/Video/File No.: {{ $submission->form_data['dokumentasi'] ?? '____________________' }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;" class="bg-gray">Catatan akhir</td>
                <td colspan="3" style="height: 30px; vertical-align: top;">{{ $submission->form_data['final_notes'] ?? '' }}</td>
            </tr>
        </table>

        <!-- SIGNATURE BLOCK KKH -->
        <table class="no-border" style="margin-top: 15px;">
            <tr>
                <td class="no-border text-center" style="width: 33%;">
                    Konsultan Pengawas<br>
                    <div class="sig-box">
                        @if($submission->signature_supervisor)
                            <img src="{{ $submission->signature_supervisor }}" class="signature-img">
                        @endif
                    </div>
                    <span style="text-decoration: underline;">{{ $submission->supervisor_name }}</span>
                </td>
                <td class="no-border text-center" style="width: 34%;">
                    PIC Penyedia/EO<br>
                    <div class="sig-box">
                        @if($submission->signature_provider)
                            <img src="{{ $submission->signature_provider }}" class="signature-img">
                        @endif
                    </div>
                    <span style="text-decoration: underline;">{{ $submission->provider_name ?? '...........................' }}</span>
                </td>
                <td class="no-border text-center" style="width: 33%;">
                    Koordinator/Team Leader<br>
                    <div class="sig-box">
                        @if($submission->signature_committee)
                            <img src="{{ $submission->signature_committee }}" class="signature-img">
                        @endif
                    </div>
                    <span style="text-decoration: underline;">{{ $submission->committee_name ?? '...........................' }}</span>
                </td>
            </tr>
        </table>
    @elseif($submission->form_code == 'RT-01')
        <!-- ================================================================= -->
        <!-- 4. TEMPLATE KHUSUS RT-01 (REGISTER TEMUAN DAN TINDAK LANJUT)      -->
        <!-- ================================================================= -->
        
        <!-- HEADER BLOCK RT-01 -->
        <table>
            <tr>
                <td rowspan="2" class="text-center text-bold bg-dark-red" style="font-size: 14px; width: 15%;">PARLEMEN<br>REMAJA 2026</td>
                <td class="text-center text-bold" style="font-size: 14px;">REGISTER TEMUAN DAN TINDAK LANJUT<br><span style="font-weight:normal; font-size: 10px; color: #555;">Daftar kendali temuan, perbaikan, verifikasi dan penutupan</span></td>
                <td class="text-center bg-gray" style="width: 15%;">Kode<br><span class="text-bold">RT-01</span></td>
            </tr>
            <tr>
                <td class="text-center" style="border-top: none;"></td>
                <td class="text-center bg-gray" style="border-top: none;">Revisi<br><span class="text-bold">01</span></td>
            </tr>
        </table>
        
        <p style="margin: 3px 0 8px 0; font-size: 9px; font-style: italic; color: #555;">Gunakan satu baris untuk setiap FT-01. Status diperbarui sampai temuan diverifikasi melalui BA-TL-01 atau dinyatakan masih Open.</p>

        <!-- MAIN TABLE RT-01 -->
        <table>
            <tr class="bg-dark-gray text-center text-bold">
                <th style="width: 4%;">No.</th>
                <th style="width: 10%;">No. FT</th>
                <th style="width: 10%;">Tanggal</th>
                <th style="width: 15%;">Lokasi / Item</th>
                <th style="width: 10%;">Kategori</th>
                <th style="width: 21%;">Ringkasan Temuan</th>
                <th style="width: 10%;">PIC</th>
                <th style="width: 10%;">Target</th>
                <th style="width: 10%;">Status / Ref. BA</th>
            </tr>
            @php
                $rt_temuans = $submission->form_data['rt_temuan'] ?? [];
            @endphp

            @if(count($rt_temuans) > 0)
                @foreach($rt_temuans as $index => $rt)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">{{ $rt['no_ft'] ?? '-' }}</td>
                        <td class="text-center">{{ !empty($rt['tanggal']) ? \Carbon\Carbon::parse($rt['tanggal'])->format('d/m/Y') : '-' }}</td>
                        <td>{{ $rt['lokasi'] ?? '-' }}</td>
                        <td class="text-center">{{ $rt['kategori'] ?? '-' }}</td>
                        <td>{{ $rt['ringkasan'] ?? '-' }}</td>
                        <td class="text-center">{{ $rt['pic'] ?? '-' }}</td>
                        <td class="text-center">{{ $rt['target'] ?? '-' }}</td>
                        <td class="text-center">{{ $rt['status'] ?? '-' }}</td>
                    </tr>
                @endforeach
            @else
                @for ($i = 1; $i <= 11; $i++)
                    <tr>
                        <td class="text-center" style="height: 25px;">{{ $i }}</td>
                        <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                    </tr>
                @endfor
            @endif
        </table>

        <!-- REKAP STATUS RT-01 -->
        <table>
            <tr class="bg-dark-red">
                <th colspan="7" class="text-left text-bold">REKAP STATUS</th>
            </tr>
            <tr class="bg-gray text-center text-bold">
                <th style="width: 10%;">Total FT</th>
                <th style="width: 10%;">Open</th>
                <th style="width: 10%;">Closed</th>
                <th style="width: 10%;">Minor</th>
                <th style="width: 10%;">Mayor</th>
                <th style="width: 10%;">Kritis</th>
                <th style="width: 40%;">Catatan Koordinator</th>
            </tr>
            <tr>
                <td class="text-center" style="height: 30px;">{{ $submission->form_data['rekap']['total'] ?? '' }}</td>
                <td class="text-center">{{ $submission->form_data['rekap']['open'] ?? '' }}</td>
                <td class="text-center">{{ $submission->form_data['rekap']['closed'] ?? '' }}</td>
                <td class="text-center">{{ $submission->form_data['rekap']['minor'] ?? '' }}</td>
                <td class="text-center">{{ $submission->form_data['rekap']['mayor'] ?? '' }}</td>
                <td class="text-center">{{ $submission->form_data['rekap']['kritis'] ?? '' }}</td>
                <td>{{ $submission->form_data['rekap']['catatan'] ?? '' }}</td>
            </tr>
        </table>
        
        <div class="text-center" style="font-size: 8px; color: #555; margin-bottom: 20px;">
            Paket Formulir Pengawasan - Parlemen Remaja 2026 &nbsp;|&nbsp; 22
        </div>

        <!-- TANDA TANGAN RT-01 -->
        <table class="no-border" style="margin-top: 15px;">
            <tr>
                <td class="no-border text-center" style="width: 50%;">
                    Koordinator/Team Leader<br>
                    <div class="sig-box">
                        @if($submission->signature_supervisor)
                            <img src="{{ $submission->signature_supervisor }}" class="signature-img">
                        @endif
                    </div>
                    <span style="text-decoration: underline;">{{ $submission->supervisor_name ?? '...........................' }}</span>
                </td>
                <td class="no-border text-center" style="width: 50%;">
                    Mengetahui Panitia*<br>
                    <div class="sig-box">
                        @if($submission->signature_committee)
                            <img src="{{ $submission->signature_committee }}" class="signature-img">
                        @endif
                    </div>
                    <span style="text-decoration: underline;">{{ $submission->committee_name ?? '...........................' }}</span>
                </td>
            </tr>
        </table>
    @elseif($submission->form_code == 'FT-01')
        <!-- ================================================================= -->
        <!-- 5. TEMPLATE KHUSUS FT-01 (FORMULIR PERINGATAN TEMUAN)             -->
        <!-- ================================================================= -->
        @php
            $ft = $submission->form_data['ft'] ?? [];
            $acuan = $ft['acuan'] ?? [];
            $bukti = $ft['bukti'] ?? [];
            $distribusi = $ft['distribusi'] ?? [];
        @endphp
        
        <!-- HEADER DAN METADATA FT-01 -->
        <table style="margin-bottom: 5px; table-layout: fixed;">
            <tr>
                <td rowspan="2" class="text-center text-bold bg-dark-red" style="font-size: 14px; width: 22%; color: white;">PARLEMEN<br>REMAJA 2026</td>
                <td rowspan="2" colspan="2" class="text-center text-bold" style="font-size: 14px; width: 50%;">FORMULIR PERINGATAN / NOTIFIKASI TEMUAN<br><br><span style="font-weight:normal; font-size: 10px; color: #555;">Ketidaksesuaian mutu, jumlah, waktu, pemasangan, output atau spesifikasi pekerjaan</span></td>
                <td class="text-center bg-gray" style="width: 28%;">Kode<br><span class="text-bold">FT-01</span></td>
            </tr>
            <tr>
                <td class="text-center bg-gray">Revisi<br><span class="text-bold">01</span></td>
            </tr>
            <tr>
                <td class="text-bold bg-gray">No. Temuan</td>
                <td>{{ $ft['no'] ?? 'FT-___/___/2026' }}</td>
                <td class="text-bold bg-gray" style="width: 18%;">Peringatan Ke</td>
                <td class="check-box">
                    [{{ ($ft['peringkat'] ?? '') == 'I' ? 'X' : ' ' }}] I &nbsp;&nbsp; 
                    [{{ ($ft['peringkat'] ?? '') == 'II' ? 'X' : ' ' }}] II &nbsp;&nbsp; 
                    [{{ ($ft['peringkat'] ?? '') == 'III' ? 'X' : ' ' }}] III
                </td>
            </tr>
            <tr>
                <td class="text-bold bg-gray">Tanggal/Waktu</td>
                <td>{{ \Carbon\Carbon::parse($submission->start_time)->format('d/m/Y H:i') }}</td>
                <td class="text-bold bg-gray">Lokasi</td>
                <td>{{ $submission->location }}</td>
            </tr>
            <tr>
                <td class="text-bold bg-gray">Petugas Pengawas</td>
                <td>{{ $submission->supervisor_name }}</td>
                <td class="text-bold bg-gray">PIC Penyedia/EO</td>
                <td>{{ $submission->provider_name }}</td>
            </tr>
            <tr>
                <td class="text-bold bg-gray">Item/Pekerjaan</td>
                <td>{{ $ft['item'] ?? '' }}</td>
                <td class="text-bold bg-gray">Kategori</td>
                <td class="check-box">
                    [{{ ($ft['kategori'] ?? '') == 'Minor' ? 'X' : ' ' }}] Minor &nbsp; 
                    [{{ ($ft['kategori'] ?? '') == 'Mayor' ? 'X' : ' ' }}] Mayor &nbsp; 
                    [{{ ($ft['kategori'] ?? '') == 'Kritis' ? 'X' : ' ' }}] Kritis
                </td>
            </tr>
        </table>
        
        <p style="margin: 0 0 5px 0; font-size: 10px; font-style: italic; color: #555;" class="check-box">
            Acuan: 
            [{{ in_array('Spesifikasi Teknis', $acuan) ? 'X' : ' ' }}] Spesifikasi Teknis &nbsp; 
            [{{ in_array('BoQ', $acuan) ? 'X' : ' ' }}] BoQ &nbsp; 
            [{{ in_array('Aanwijzing/Addendum', $acuan) ? 'X' : ' ' }}] Aanwijzing/Addendum &nbsp; 
            [{{ in_array('Arahan Panitia', $acuan) ? 'X' : ' ' }}] Arahan Panitia &nbsp; 
            [{{ in_array('Lainnya', $acuan) ? 'X' : ' ' }}] Lainnya: <span style="text-decoration: underline;">{{ $ft['acuan_lainnya'] ?? '_____________________' }}</span>
        </p>

        <!-- BODY DAN TANDA TANGAN FT-01 -->
        <table style="table-layout: fixed; margin-bottom: 2px;">
            <tr class="bg-dark-red text-left text-bold">
                <td colspan="4" style="color: white; padding: 6px;">URAIAN TEMUAN / KONDISI AKTUAL</td>
            </tr>
            <tr>
                <td colspan="4" style="height: 60px; vertical-align: top;">{{ $ft['uraian'] ?? '' }}</td>
            </tr>
            <tr class="bg-dark-red text-left text-bold">
                <td colspan="4" style="color: white; padding: 6px;">KONDISI YANG DIPERSYARATKAN / SEHARUSNYA</td>
            </tr>
            <tr>
                <td colspan="4" style="height: 60px; vertical-align: top;">{{ $ft['kondisi'] ?? '' }}</td>
            </tr>
            <tr class="bg-dark-red text-left text-bold">
                <td colspan="4" style="color: white; padding: 6px;">INSTRUKSI / TINDAKAN PERBAIKAN</td>
            </tr>
            <tr>
                <td colspan="4" style="height: 60px; vertical-align: top;">{{ $ft['instruksi'] ?? '' }}</td>
            </tr>
            <tr>
                <td class="text-bold bg-gray" style="width: 22%;">Batas Waktu</td>
                <td style="width: 32%;" class="check-box">
                    [{{ ($ft['batas_waktu'] ?? '') == 'Segera' ? 'X' : ' ' }}] Segera &nbsp; 
                    [{{ ($ft['batas_waktu'] ?? '') == 'Hari ini' ? 'X' : ' ' }}] Hari ini &nbsp;<br>
                    [{{ ($ft['batas_waktu'] ?? '') == 'Tgl/Jam' ? 'X' : ' ' }}] Tgl/Jam: <span style="text-decoration: underline;">{{ $ft['batas_waktu_tgl'] ?? '________' }}</span>
                </td>
                <td class="text-bold bg-gray" style="width: 18%;">Status Awal</td>
                <td style="width: 28%;">{{ $ft['status_awal'] ?? 'OPEN' }}</td>
            </tr>
            <tr>
                <td class="text-bold bg-gray">Tindakan segera</td>
                <td>{{ $ft['tindakan_segera'] ?? '' }}</td>
                <td class="text-bold bg-gray">Bukti Temuan</td>
                <td class="check-box">
                    [{{ in_array('Foto', $bukti) ? 'X' : ' ' }}] Foto &nbsp; 
                    [{{ in_array('Video', $bukti) ? 'X' : ' ' }}] Video &nbsp;<br>
                    [{{ in_array('File', $bukti) ? 'X' : ' ' }}] File &nbsp; 
                    [{{ in_array('Sampel', $bukti) ? 'X' : ' ' }}] Sampel
                </td>
            </tr>
            <tr>
                <td class="text-bold bg-gray">Distribusi</td>
                <td class="check-box">
                    [{{ in_array('Pengawas', $distribusi) ? 'X' : ' ' }}] Pengawas &nbsp; 
                    [{{ in_array('Penyedia/EO', $distribusi) ? 'X' : ' ' }}] Penyedia/EO
                </td>
                <td class="text-bold bg-gray">No. Lampiran</td>
                <td>{{ $ft['no_lampiran'] ?? '__________________' }}</td>
            </tr>
            <tr>
                <td colspan="2" class="text-center" style="vertical-align: top; padding-top: 15px; height: 90px;">
                    Konsultan Pengawas<br><br>
                    <div class="sig-box" style="height: 45px;">
                        @if($submission->signature_supervisor)
                            <img src="{{ $submission->signature_supervisor }}" class="signature-img">
                        @endif
                    </div>
                    <span style="text-decoration: underline;">{{ $submission->supervisor_name ?? '...........................' }}</span>
                </td>
                <td colspan="2" class="text-center" style="vertical-align: top; padding-top: 15px;">
                    PIC Penyedia/EO (menerima notifikasi)<br><br>
                    <div class="sig-box" style="height: 45px;">
                        @if($submission->signature_provider)
                            <img src="{{ $submission->signature_provider }}" class="signature-img">
                        @endif
                    </div>
                    <span style="text-decoration: underline;">{{ $submission->provider_name ?? '...........................' }}</span>
                </td>
            </tr>
        </table>
        <p style="margin: 0; font-size: 8px; font-style: italic; color: #777;">Catatan: tanda tangan PIC Penyedia/EO menyatakan penerimaan notifikasi untuk ditindaklanjuti.</p>

    @elseif($submission->form_code == 'BA-TL-01')
        <!-- ================================================================= -->
        <!-- 6. TEMPLATE KHUSUS BA-TL-01 (BERITA ACARA VERIFIKASI TINDAK LANJUT) -->
        <!-- ================================================================= -->
        @php
            $ba = $submission->form_data['ba'] ?? [];
            $metode = $ba['metode'] ?? [];
            $bukti = $ba['bukti'] ?? [];
        @endphp

        <!-- HEADER DAN METADATA BA-TL-01 -->
        <table style="margin-bottom: 5px; table-layout: fixed;">
            <tr>
                <td rowspan="2" class="text-center text-bold bg-dark-red" style="font-size: 14px; width: 22%; color: white;">PARLEMEN<br>REMAJA 2026</td>
                <td rowspan="2" colspan="2" class="text-center text-bold" style="font-size: 13px; width: 56%;">BERITA ACARA VERIFIKASI TINDAK LANJUT / PERBAIKAN TEMUAN<br><br><span style="font-weight:normal; font-size: 10px; color: #555;">Pemeriksaan ulang setelah tindakan koreksi/perbaikan</span></td>
                <td class="text-center bg-gray" style="width: 22%;">Kode<br><span class="text-bold">BA-TL-01</span></td>
            </tr>
            <tr>
                <td class="text-center bg-gray">Revisi<br><span class="text-bold">01</span></td>
            </tr>
            <tr>
                <td class="text-bold bg-gray" style="width: 22%;">No. Berita Acara</td>
                <td style="width: 28%;">{{ $ba['no'] ?? 'BA-TL-___/___/2026' }}</td>
                <td class="text-bold bg-gray" style="width: 22%;">Ref. No. Temuan</td>
                <td style="width: 28%;">{{ $ba['ref_ft'] ?? 'FT-___/___/2026' }}</td>
            </tr>
            <tr>
                <td class="text-bold bg-gray">Tanggal Temuan</td>
                <td>{{ !empty($ba['tgl_temuan']) ? \Carbon\Carbon::parse($ba['tgl_temuan'])->format('d/m/Y') : '_________' }}</td>
                <td class="text-bold bg-gray">Tanggal Verifikasi</td>
                <td>{{ !empty($ba['tgl_verifikasi']) ? \Carbon\Carbon::parse($ba['tgl_verifikasi'])->format('d/m/Y') : '_________' }}</td>
            </tr>
            <tr>
                <td class="text-bold bg-gray">Lokasi</td>
                <td>{{ $ba['lokasi'] ?? '' }}</td>
                <td class="text-bold bg-gray">Item/Pekerjaan</td>
                <td>{{ $ba['item'] ?? '' }}</td>
            </tr>
            <tr>
                <td class="text-bold bg-gray">Petugas Pengawas</td>
                <td>{{ $submission->supervisor_name }}</td>
                <td class="text-bold bg-gray">PIC Penyedia/EO</td>
                <td>{{ $submission->provider_name }}</td>
            </tr>
        </table>

        <!-- BODY BA-TL-01 -->
        <table style="table-layout: fixed; margin-bottom: 0;">
            <tr class="bg-dark-red text-left text-bold">
                <td colspan="4" style="color: white; padding: 6px;">RINGKASAN TEMUAN AWAL</td>
            </tr>
            <tr>
                <td colspan="4" style="height: 60px; vertical-align: top;">{{ $ba['ringkasan_awal'] ?? '' }}</td>
            </tr>
            <tr class="bg-dark-red text-left text-bold">
                <td colspan="4" style="color: white; padding: 6px;">TINDAKAN PERBAIKAN YANG TELAH DILAKUKAN PENYEDIA / EO</td>
            </tr>
            <tr>
                <td colspan="4" style="height: 60px; vertical-align: top;">{{ $ba['tindakan_perbaikan'] ?? '' }}</td>
            </tr>
            <tr>
                <td class="text-bold bg-gray" style="width: 22%;">Metode Verifikasi</td>
                <td colspan="3" class="check-box" style="width: 78%;">
                    [{{ in_array('Visual', $metode) ? 'X' : ' ' }}] Visual &nbsp;&nbsp; 
                    [{{ in_array('Ukur', $metode) ? 'X' : ' ' }}] Ukur &nbsp;&nbsp; 
                    [{{ in_array('Hitung', $metode) ? 'X' : ' ' }}] Hitung &nbsp;&nbsp; 
                    [{{ in_array('Uji fungsi', $metode) ? 'X' : ' ' }}] Uji fungsi &nbsp;&nbsp; 
                    [{{ in_array('Dokumen/File', $metode) ? 'X' : ' ' }}] Dokumen/File &nbsp;&nbsp; 
                    [{{ in_array('Lainnya', $metode) ? 'X' : ' ' }}] Lainnya: <span style="text-decoration: underline;">{{ $ba['metode_lainnya'] ?? '_______________' }}</span>
                </td>
            </tr>
            <tr class="bg-dark-red text-left text-bold">
                <td colspan="4" style="color: white; padding: 6px;">HASIL PEMERIKSAAN ULANG / VERIFIKASI</td>
            </tr>
            <tr>
                <td colspan="4" style="height: 60px; vertical-align: top;">{{ $ba['hasil_verifikasi'] ?? '' }}</td>
            </tr>
            <tr>
                <td class="text-bold bg-gray" style="width: 22%;">Hasil Verifikasi</td>
                <td class="check-box" style="width: 38%;">
                    [{{ ($ba['hasil_akhir'] ?? '') == 'CLOSED' ? 'X' : ' ' }}] CLOSED &nbsp; 
                    [{{ ($ba['hasil_akhir'] ?? '') == 'CLOSED DENGAN CATATAN' ? 'X' : ' ' }}] CLOSED DGN CATATAN &nbsp;
                    [{{ ($ba['hasil_akhir'] ?? '') == 'OPEN' ? 'X' : ' ' }}] OPEN
                </td>
                <td class="text-bold bg-gray" style="width: 18%;">Tanggal Closed</td>
                <td style="width: 22%;">{{ !empty($ba['tgl_closed']) ? \Carbon\Carbon::parse($ba['tgl_closed'])->format('d/m/Y') : '___________' }}</td>
            </tr>
            <tr>
                <td class="text-bold bg-gray">Bukti Perbaikan</td>
                <td class="check-box">
                    [{{ in_array('Foto sebelum-sesudah', $bukti) ? 'X' : ' ' }}] Foto sblm-ssdh &nbsp; 
                    [{{ in_array('Video', $bukti) ? 'X' : ' ' }}] Video &nbsp;
                    [{{ in_array('File revisi', $bukti) ? 'X' : ' ' }}] File
                </td>
                <td class="text-bold bg-gray">No. Lampiran</td>
                <td>{{ $ba['no_lampiran'] ?? '___________' }}</td>
            </tr>
            <tr>
                <td class="text-bold bg-gray">Catatan akhir</td>
                <td>{{ $ba['catatan_akhir'] ?? '' }}</td>
                <td class="text-bold bg-gray">Tindak lanjut sisa</td>
                <td>{{ $ba['tindak_lanjut_sisa'] ?? '' }}</td>
            </tr>
        </table>
        <table style="table-layout: fixed; margin-top: -1px; border-top: none;">
            <tr>
                <td class="text-center" style="vertical-align: top; padding-top: 15px; height: 90px; width: 33.33%; border-top: none;">
                    Konsultan Pengawas<br><br>
                    <div class="sig-box" style="height: 45px;">
                        @if($submission->signature_supervisor)
                            <img src="{{ $submission->signature_supervisor }}" class="signature-img">
                        @endif
                    </div>
                    <span style="text-decoration: underline;">{{ $submission->supervisor_name ?? '...........................' }}</span>
                </td>
                <td class="text-center" style="vertical-align: top; padding-top: 15px; width: 33.33%; border-top: none;">
                    PIC Penyedia/EO<br><br>
                    <div class="sig-box" style="height: 45px;">
                        @if($submission->signature_provider)
                            <img src="{{ $submission->signature_provider }}" class="signature-img">
                        @endif
                    </div>
                    <span style="text-decoration: underline;">{{ $submission->provider_name ?? '...........................' }}</span>
                </td>
                <td class="text-center" style="vertical-align: top; padding-top: 15px; width: 33.33%; border-top: none;">
                    Mengetahui Panitia/PPK*<br><br>
                    <div class="sig-box" style="height: 45px;">
                        @if($submission->signature_committee)
                            <img src="{{ $submission->signature_committee }}" class="signature-img">
                        @endif
                    </div>
                    <span style="text-decoration: underline;">{{ $submission->committee_name ?? '...........................' }}</span>
                </td>
            </tr>
        </table>
        <p style="margin: 0; font-size: 8px; font-style: italic; color: #777;">*Kolom Panitia/PPK digunakan bila diperlukan sesuai mekanisme administrasi kegiatan.</p>
    @endif

</body>
</html>