@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto bg-white p-8 rounded-lg shadow-md border border-gray-200">
    <h2 class="text-3xl font-bold text-gray-800 mb-2 border-b pb-4">Pengisian Form: {{ $form_code }}</h2>

    <form id="pengawasanForm" method="POST" action="{{ route('form.store', $form_code) }}" enctype="multipart/form-data">
        @csrf
        
        <!-- BLOK 1: METADATA & PIHAK TERLIBAT -->
        <div class="bg-gray-50 p-6 rounded-lg border border-gray-200 mb-8">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Informasi Umum</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nama Petugas Pengawas <span class="text-red-500">*</span></label>
                    <input type="text" name="supervisor_name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black border p-2">
                </div>
                @if($form_code != 'RT-01')
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nama Penyedia/EO/PIC</label>
                    <input type="text" name="provider_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black border p-2">
                </div>
                @endif
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nama Koordinator/Panitia</label>
                    <input type="text" name="committee_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black border p-2">
                </div>
                @if($form_code != 'RT-01')
                <div>
                    <label class="block text-sm font-medium text-gray-700">Lokasi/Pos <span class="text-red-500">*</span></label>
                    <input type="text" name="location" value="{{ $form_code == 'RHP-00' ? 'Seluruh pos pengawasan' : '' }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black border p-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Agenda / Tahap</label>
                    <input type="text" name="agenda" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black border p-2">
                </div>
                @endif
                <div>
                    <label class="block text-sm font-medium text-gray-700">Waktu Mulai <span class="text-red-500">*</span></label>
                    <input type="datetime-local" name="start_time" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black border p-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Waktu Selesai</label>
                    <input type="datetime-local" name="end_time" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black border p-2">
                </div>
            </div>
        </div>

        <!-- BLOK 2: KRITERIA PENGAWASAN / ITEM PRODUKSI DINAMIS -->
        @if(!empty($questions) && !in_array($form_code, ['RT-01', 'FT-01', 'BA-TL-01']))
            <div class="mb-8">
                <h3 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">
                    {{ $form_code == 'RHP-00' ? 'Matriks Rekap Pos Pengawasan' : (str_starts_with($form_code, 'PRD') ? 'Daftar Item & Verifikasi Produksi' : 'Kriteria Pengawasan') }}
                </h3>
                
                @foreach($questions as $index =>$question)
                    @php
                        $is_array = is_array($question);
                        $title = $is_array ? $question['title'] : $question;
                        $subitems = $is_array ? $question['subitems'] : [];
                    @endphp
                    <div class="mb-5 p-5 border border-gray-200 rounded-lg bg-white shadow-sm hover:border-blue-300 transition">
                        <p class="text-sm font-bold text-gray-800 mb-3">{{ $index + 1 }}. {{$title}}</p>
                        
                        @if($form_code == 'RHP-00')
                            <!-- RHP-00 selalu tidak punya subitems (is_array = false) -->
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-center">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Status Umum</label>
                                    <div class="flex space-x-3">
                                        <label class="inline-flex items-center cursor-pointer"><input type="radio" name="q_{{ $index }}_status" value="S" class="w-4 h-4 text-black form-radio border-gray-300 focus:ring-black" required> <span class="ml-1 text-xs font-bold text-black">S</span></label>
                                        <label class="inline-flex items-center cursor-pointer"><input type="radio" name="q_{{ $index }}_status" value="TS" class="w-4 h-4 text-gray-800 form-radio border-gray-300 focus:ring-black"> <span class="ml-1 text-xs font-bold text-gray-800">TS</span></label>
                                        <label class="inline-flex items-center cursor-pointer"><input type="radio" name="q_{{ $index }}_status" value="N/A" class="w-4 h-4 text-gray-500 form-radio border-gray-300 focus:ring-gray-500"> <span class="ml-1 text-xs font-bold text-gray-600">N/A</span></label>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Open</label>
                                    <div class="pt-1">
                                        <label class="inline-flex items-center cursor-pointer"><input type="checkbox" name="q_{{ $index }}_open" value="1" class="w-4 h-4 text-black form-checkbox border-gray-300 focus:ring-black"> <span class="ml-2 text-xs text-gray-600">Pilih</span></label>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Closed</label>
                                    <div class="pt-1">
                                        <label class="inline-flex items-center cursor-pointer"><input type="checkbox" name="q_{{ $index }}_closed" value="1" class="w-4 h-4 text-black form-checkbox border-gray-300 focus:ring-black"> <span class="ml-2 text-xs text-gray-600">Pilih</span></label>
                                    </div>
                                </div>
                                <div class="md:col-span-4">
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Temuan Prioritas / No. FT / Tindak Lanjut</label>
                                    <input type="text" name="q_{{ $index }}_tindak" placeholder="Keterangan temuan / tindak lanjut..." class="w-full border rounded-md p-2 text-xs bg-gray-50 border-gray-300 focus:border-black focus:ring-black">
                                </div>
                            </div>
                        @else
                            @if(empty($subitems))
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-center">
                                    @if(str_starts_with($form_code, 'PRD'))
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-600 mb-1">Hasil Aktual / Qty</label>
                                            <input type="text" name="q_{{ $index }}_aktual" placeholder="Cth: 1 Unit / Sesuai" class="w-full border rounded-md p-1.5 text-xs bg-gray-50 border-gray-300 focus:border-black focus:ring-black">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-600 mb-1">Status Mutu</label>
                                            <div class="flex space-x-2 text-xs">
                                                <label class="inline-flex items-center cursor-pointer"><input type="radio" name="q_{{ $index }}_status" value="S" class="w-4 h-4 text-black form-radio border-gray-300 focus:ring-black" required> <span class="ml-1 font-bold text-black">S</span></label>
                                                <label class="inline-flex items-center cursor-pointer"><input type="radio" name="q_{{ $index }}_status" value="TS" class="w-4 h-4 text-gray-800 form-radio border-gray-300 focus:ring-black"> <span class="ml-1 font-bold text-gray-800">TS</span></label>
                                                <label class="inline-flex items-center cursor-pointer"><input type="radio" name="q_{{ $index }}_status" value="N/A" class="w-4 h-4 text-gray-500 form-radio border-gray-300 focus:ring-gray-500"> <span class="ml-1 font-bold text-gray-600">N/A</span></label>
                                            </div>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-xs font-semibold text-gray-600 mb-1">Catatan / No. FT</label>
                                            <input type="text" name="q_{{ $index }}_catatan" placeholder="Catatan atau rujukan FT-01..." class="w-full border rounded-md p-1.5 text-xs bg-gray-50 border-gray-300 focus:border-black focus:ring-black">
                                        </div>
                                    @else
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-600 mb-1">Status Umum</label>
                                            <div class="flex space-x-3">
                                                <label class="inline-flex items-center cursor-pointer"><input type="radio" name="q_{{ $index }}_status" value="S" class="w-4 h-4 text-black form-radio border-gray-300 focus:ring-black" required> <span class="ml-1 text-xs font-bold text-black">S</span></label>
                                                <label class="inline-flex items-center cursor-pointer"><input type="radio" name="q_{{ $index }}_status" value="TS" class="w-4 h-4 text-gray-800 form-radio border-gray-300 focus:ring-black"> <span class="ml-1 text-xs font-bold text-gray-800">TS</span></label>
                                                <label class="inline-flex items-center cursor-pointer"><input type="radio" name="q_{{ $index }}_status" value="N/A" class="w-4 h-4 text-gray-500 form-radio border-gray-300 focus:ring-gray-500"> <span class="ml-1 text-xs font-bold text-gray-600">N/A</span></label>
                                            </div>
                                        </div>
                                        <div class="md:col-span-3">
                                            <label class="block text-xs font-semibold text-gray-600 mb-1">Catatan / No. Temuan</label>
                                            <input type="text" name="q_{{ $index }}_note" placeholder="Catatan / No. Temuan FT-01..." class="w-full border rounded-md p-2 text-sm bg-gray-50 border-gray-300 focus:border-black focus:ring-black">
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="space-y-4">
                                    @foreach($subitems as $subIndex => $subitem)
                                        <div class="border-t pt-3 grid grid-cols-1 md:grid-cols-4 gap-4 items-center">
                                            <div class="md:col-span-4">
                                                <p class="text-sm text-gray-700">- {{ $subitem }}</p>
                                            </div>
                                            @if(str_starts_with($form_code, 'PRD'))
                                                <div>
                                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Hasil Aktual / Qty</label>
                                                    <input type="text" name="q_{{ $index }}_sub_{{ $subIndex }}_aktual" placeholder="Cth: 1 Unit / Sesuai" class="w-full border rounded-md p-1.5 text-xs bg-gray-50 border-gray-300 focus:border-black focus:ring-black">
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Status Mutu</label>
                                                    <div class="flex space-x-2 text-xs">
                                                        <label class="inline-flex items-center cursor-pointer"><input type="radio" name="q_{{ $index }}_sub_{{ $subIndex }}_status" value="S" class="w-4 h-4 text-black form-radio border-gray-300 focus:ring-black" required> <span class="ml-1 font-bold text-black">S</span></label>
                                                        <label class="inline-flex items-center cursor-pointer"><input type="radio" name="q_{{ $index }}_sub_{{ $subIndex }}_status" value="TS" class="w-4 h-4 text-gray-800 form-radio border-gray-300 focus:ring-black"> <span class="ml-1 font-bold text-gray-800">TS</span></label>
                                                        <label class="inline-flex items-center cursor-pointer"><input type="radio" name="q_{{ $index }}_sub_{{ $subIndex }}_status" value="N/A" class="w-4 h-4 text-gray-500 form-radio border-gray-300 focus:ring-gray-500"> <span class="ml-1 font-bold text-gray-600">N/A</span></label>
                                                    </div>
                                                </div>
                                                <div class="md:col-span-2">
                                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Catatan / No. FT</label>
                                                    <input type="text" name="q_{{ $index }}_sub_{{ $subIndex }}_catatan" placeholder="Catatan atau rujukan FT-01..." class="w-full border rounded-md p-1.5 text-xs bg-gray-50 border-gray-300 focus:border-black focus:ring-black">
                                                </div>
                                            @else
                                                <div class="md:col-span-1">
                                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Status Umum</label>
                                                    <div class="flex space-x-3">
                                                        <label class="inline-flex items-center cursor-pointer"><input type="radio" name="q_{{ $index }}_sub_{{ $subIndex }}_status" value="S" class="w-4 h-4 text-black form-radio border-gray-300 focus:ring-black" required> <span class="ml-1 text-xs font-bold text-black">S</span></label>
                                                        <label class="inline-flex items-center cursor-pointer"><input type="radio" name="q_{{ $index }}_sub_{{ $subIndex }}_status" value="TS" class="w-4 h-4 text-gray-800 form-radio border-gray-300 focus:ring-black"> <span class="ml-1 text-xs font-bold text-gray-800">TS</span></label>
                                                        <label class="inline-flex items-center cursor-pointer"><input type="radio" name="q_{{ $index }}_sub_{{ $subIndex }}_status" value="N/A" class="w-4 h-4 text-gray-500 form-radio border-gray-300 focus:ring-gray-500"> <span class="ml-1 text-xs font-bold text-gray-600">N/A</span></label>
                                                    </div>
                                                </div>
                                                <div class="md:col-span-3">
                                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Catatan / No. Temuan</label>
                                                    <input type="text" name="q_{{ $index }}_sub_{{ $subIndex }}_note" placeholder="Catatan / No. Temuan FT-01..." class="w-full border rounded-md p-2 text-sm bg-gray-50 border-gray-300 focus:border-black focus:ring-black">
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @endif
                    </div>
                @endforeach            </div>
        @elseif(!in_array($form_code, ['RT-01', 'FT-01', 'BA-TL-01']))
            <div class="mb-6 p-4 bg-yellow-50 rounded-md border border-yellow-200">
                <p class="text-sm text-yellow-800">Daftar pertanyaan/pos/item untuk form ini belum dikonfigurasi.</p>
            </div>
        @endif

        <!-- BLOK TAMBAHAN KHUSUS SPESIFIK TIPE FORM -->
        @if($form_code == 'RHP-00')
            <!-- BLOK KONTEN RHP-00 -->
            <div class="bg-gray-50 p-6 rounded-lg border border-gray-200 mb-8">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Ringkasan Harian & Keputusan Koordinasi</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Kejadian penting / kendala</label>
                        <textarea name="kendala" rows="2" class="mt-1 w-full border rounded-md p-2 text-sm border-gray-300 focus:border-black focus:ring-black"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Keputusan / koordinasi</label>
                        <textarea name="keputusan" rows="2" class="mt-1 w-full border rounded-md p-2 text-sm border-gray-300 focus:border-black focus:ring-black"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Rencana tindak lanjut berikutnya</label>
                        <textarea name="rtl" rows="2" class="mt-1 w-full border rounded-md p-2 text-sm border-gray-300 focus:border-black focus:ring-black"></textarea>
                    </div>
                </div>
            </div>

        @elseif(str_starts_with($form_code, 'PRD'))
            <!-- BLOK KONTEN PRD -->
            <div class="bg-gray-50 p-6 rounded-lg border border-gray-200 mb-8 space-y-6">
                <h3 class="text-lg font-bold text-gray-800 border-b pb-2">Checkpoint Penerimaan & Mutu</h3>
                @if(in_array($form_code, ['PRD-02A', 'PRD-02B']))
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <label class="flex items-center space-x-2 cursor-pointer"><input type="checkbox" name="checkpoint[]" value="sample" class="rounded border-gray-300 text-black focus:ring-black"> <span>Sample/prototype telah disetujui</span></label>
                    <label class="flex items-center space-x-2 cursor-pointer"><input type="checkbox" name="checkpoint[]" value="material" class="rounded border-gray-300 text-black focus:ring-black"> <span>Bahan/material & ukuran sesuai</span></label>
                    <label class="flex items-center space-x-2 cursor-pointer"><input type="checkbox" name="checkpoint[]" value="logo" class="rounded border-gray-300 text-black focus:ring-black"> <span>Logo/desain/warna/finishing sesuai</span></label>
                    <label class="flex items-center space-x-2 cursor-pointer"><input type="checkbox" name="checkpoint[]" value="jumlah" class="rounded border-gray-300 text-black focus:ring-black"> <span>Jumlah sesuai kontrak/BoQ</span></label>
                    <label class="flex items-center space-x-2 cursor-pointer"><input type="checkbox" name="checkpoint[]" value="cacat" class="rounded border-gray-300 text-black focus:ring-black"> <span>Tidak cacat/rusak/salah cetak</span></label>
                    <label class="flex items-center space-x-2 cursor-pointer"><input type="checkbox" name="checkpoint[]" value="packing" class="rounded border-gray-300 text-black focus:ring-black"> <span>Packing/distribusi & bukti penerimaan tersedia</span></label>
                </div>
                @elseif($form_code == 'PRD-03')
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <label class="flex items-center space-x-2 cursor-pointer"><input type="checkbox" name="checkpoint[]" value="file" class="rounded border-gray-300 text-black focus:ring-black"> <span>File/output dapat dibuka dan lengkap</span></label>
                    <label class="flex items-center space-x-2 cursor-pointer"><input type="checkbox" name="checkpoint[]" value="konten" class="rounded border-gray-300 text-black focus:ring-black"> <span>Konten sesuai KV/arahan Humas</span></label>
                    <label class="flex items-center space-x-2 cursor-pointer"><input type="checkbox" name="checkpoint[]" value="nama" class="rounded border-gray-300 text-black focus:ring-black"> <span>Nama/judul/lower third akurat</span></label>
                    <label class="flex items-center space-x-2 cursor-pointer"><input type="checkbox" name="checkpoint[]" value="audio" class="rounded border-gray-300 text-black focus:ring-black"> <span>Audio & visual berfungsi baik</span></label>
                    <label class="flex items-center space-x-2 cursor-pointer"><input type="checkbox" name="checkpoint[]" value="durasi" class="rounded border-gray-300 text-black focus:ring-black"> <span>Durasi/format/output sesuai</span></label>
                    <label class="flex items-center space-x-2 cursor-pointer"><input type="checkbox" name="checkpoint[]" value="serahterima" class="rounded border-gray-300 text-black focus:ring-black"> <span>Serah terima & penyimpanan file terdokumentasi</span></label>
                </div>
                @else
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <label class="flex items-center space-x-2 cursor-pointer"><input type="checkbox" name="checkpoint[]" value="desain" class="rounded border-gray-300 text-black focus:ring-black"> <span>Desain/warna/wording disetujui</span></label>
                    <label class="flex items-center space-x-2 cursor-pointer"><input type="checkbox" name="checkpoint[]" value="material" class="rounded border-gray-300 text-black focus:ring-black"> <span>Material & ukuran sesuai</span></label>
                    <label class="flex items-center space-x-2 cursor-pointer"><input type="checkbox" name="checkpoint[]" value="jumlah" class="rounded border-gray-300 text-black focus:ring-black"> <span>Jumlah/qty sesuai</span></label>
                    <label class="flex items-center space-x-2 cursor-pointer"><input type="checkbox" name="checkpoint[]" value="finishing" class="rounded border-gray-300 text-black focus:ring-black"> <span>Cetak/finishing rapi & tidak cacat</span></label>
                    <label class="flex items-center space-x-2 cursor-pointer"><input type="checkbox" name="checkpoint[]" value="pemasangan" class="rounded border-gray-300 text-black focus:ring-black"> <span>Pemasangan/penempatan aman</span></label>
                    <label class="flex items-center space-x-2 cursor-pointer"><input type="checkbox" name="checkpoint[]" value="pemeliharaan" class="rounded border-gray-300 text-black focus:ring-black"> <span>Pemeliharaan/pembongkaran OK</span></label>
                </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t border-gray-200">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Kesimpulan Mutu</label>
                        <select name="kesimpulan" class="w-full border rounded-md p-2 text-sm bg-white border-gray-300 focus:border-black focus:ring-black">
                            <option value="Diterima">Diterima</option>
                            <option value="Perbaikan">Diterima dengan Perbaikan</option>
                            <option value="Ditolak">Ditolak / Harus Diganti</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">No. Bukti Foto / File</label>
                        <input type="text" name="bukti_no" placeholder="Cth: Lampiran Foto #01" class="w-full border rounded-md p-2 text-sm bg-white border-gray-300 focus:border-black focus:ring-black">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 pt-2">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600">No. FT (Tindak Lanjut)</label>
                        <input type="text" name="ft_no" placeholder="FT-01/..." class="mt-1 w-full border rounded-md p-1.5 text-xs bg-white border-gray-300 focus:border-black focus:ring-black">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600">Uraian Tindak Lanjut</label>
                        <input type="text" name="ft_uraian" placeholder="Uraian perbaikan..." class="mt-1 w-full border rounded-md p-1.5 text-xs bg-white border-gray-300 focus:border-black focus:ring-black">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600">PIC / Target Selesai</label>
                        <input type="text" name="ft_pic" placeholder="Nama PIC & target..." class="mt-1 w-full border rounded-md p-1.5 text-xs bg-white border-gray-300 focus:border-black focus:ring-black">
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-200">
                    <label class="block text-sm font-medium text-gray-700">Unggah File Dokumentasi (Opsional)</label>
                    <input type="file" name="attachment" accept="image/*,video/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-gray-100 file:text-black hover:file:bg-gray-200 border border-gray-300 rounded-md bg-white">
                    <p class="mt-1 text-xs text-gray-500">Maksimal ukuran file 10 MB (JPG, PNG, MP4).</p>
                </div>
            </div>

        @elseif($form_code == 'RT-01')
            <!-- BLOK KONTEN RT-01 (REGISTER TEMUAN DAN TINDAK LANJUT) -->
            <div class="mb-8 bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
                <div class="flex justify-between items-center mb-4 border-b pb-2">
                    <h3 class="text-xl font-bold text-gray-800">Daftar Register Temuan</h3>
                    <button type="button" id="add-rt-btn" class="px-3 py-1 bg-black text-white text-xs font-semibold rounded hover:bg-gray-800 transition">
                        + Tambah Baris Temuan
                    </button>
                </div>
                <p class="text-xs text-gray-500 mb-4">Gunakan satu baris untuk setiap FT-01.</p>
                <div id="rt-container" class="space-y-4"></div>
            </div>

            <div class="bg-blue-50 p-6 rounded-lg border border-blue-200 mb-8">
                <h3 class="text-lg font-bold text-blue-900 mb-4">Rekap Status</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700">Total FT</label>
                        <input type="number" name="rekap[total]" class="mt-1 block w-full rounded-md border-gray-300 border p-2 text-sm bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700">Open</label>
                        <input type="number" name="rekap[open]" class="mt-1 block w-full rounded-md border-gray-300 border p-2 text-sm bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700">Closed</label>
                        <input type="number" name="rekap[closed]" class="mt-1 block w-full rounded-md border-gray-300 border p-2 text-sm bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700">Minor</label>
                        <input type="number" name="rekap[minor]" class="mt-1 block w-full rounded-md border-gray-300 border p-2 text-sm bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700">Mayor</label>
                        <input type="number" name="rekap[mayor]" class="mt-1 block w-full rounded-md border-gray-300 border p-2 text-sm bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700">Kritis</label>
                        <input type="number" name="rekap[kritis]" class="mt-1 block w-full rounded-md border-gray-300 border p-2 text-sm bg-white">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700">Catatan Koordinator</label>
                    <textarea name="rekap[catatan]" rows="2" class="mt-1 block w-full rounded-md border-gray-300 border p-2 text-sm bg-white"></textarea>
                </div>
            </div>

        @elseif($form_code == 'FT-01')
            <!-- BLOK KONTEN FT-01 (FORMULIR PERINGATAN / NOTIFIKASI TEMUAN) -->
            <div class="mb-8 bg-white p-6 rounded-lg border border-gray-200 shadow-sm space-y-6">
                <div class="border-b pb-2 mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Formulir Peringatan / Notifikasi Temuan</h3>
                    <p class="text-xs text-gray-500">Ketidaksesuaian mutu, jumlah, waktu, pemasangan, output atau spesifikasi pekerjaan</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">No. Temuan</label>
                        <input type="text" name="ft[no]" placeholder="FT-___/___/2026" class="mt-1 block w-full rounded-md border-gray-300 border p-2 text-sm bg-white">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Peringatan Ke</label>
                        <div class="mt-2 flex space-x-4 text-sm">
                            <label class="inline-flex items-center"><input type="radio" name="ft[peringkat]" value="I" class="form-radio text-black"> <span class="ml-2">I</span></label>
                            <label class="inline-flex items-center"><input type="radio" name="ft[peringkat]" value="II" class="form-radio text-black"> <span class="ml-2">II</span></label>
                            <label class="inline-flex items-center"><input type="radio" name="ft[peringkat]" value="III" class="form-radio text-black"> <span class="ml-2">III</span></label>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Item/Pekerjaan</label>
                        <input type="text" name="ft[item]" class="mt-1 block w-full rounded-md border-gray-300 border p-2 text-sm bg-white">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Kategori</label>
                        <div class="mt-2 flex space-x-4 text-sm">
                            <label class="inline-flex items-center"><input type="radio" name="ft[kategori]" value="Minor" class="form-radio text-black"> <span class="ml-2">Minor</span></label>
                            <label class="inline-flex items-center"><input type="radio" name="ft[kategori]" value="Mayor" class="form-radio text-black"> <span class="ml-2">Mayor</span></label>
                            <label class="inline-flex items-center"><input type="radio" name="ft[kategori]" value="Kritis" class="form-radio text-black"> <span class="ml-2">Kritis</span></label>
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700">Acuan</label>
                        <div class="mt-2 flex flex-wrap gap-4 text-sm items-center">
                            <label class="inline-flex items-center"><input type="checkbox" name="ft[acuan][]" value="Spesifikasi Teknis" class="form-checkbox text-black"> <span class="ml-2">Spesifikasi Teknis</span></label>
                            <label class="inline-flex items-center"><input type="checkbox" name="ft[acuan][]" value="BoQ" class="form-checkbox text-black"> <span class="ml-2">BoQ</span></label>
                            <label class="inline-flex items-center"><input type="checkbox" name="ft[acuan][]" value="Aanwijzing/Addendum" class="form-checkbox text-black"> <span class="ml-2">Aanwijzing/Addendum</span></label>
                            <label class="inline-flex items-center"><input type="checkbox" name="ft[acuan][]" value="Arahan Panitia" class="form-checkbox text-black"> <span class="ml-2">Arahan Panitia</span></label>
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="ft[acuan][]" value="Lainnya" class="form-checkbox text-black"> 
                                <span class="ml-2">Lainnya:</span>
                                <input type="text" name="ft[acuan_lainnya]" class="ml-2 border-b border-gray-400 bg-transparent focus:outline-none focus:border-black text-sm w-32">
                            </label>
                        </div>
                    </div>
                </div>

                <div>
                    <h4 class="text-sm font-bold text-white bg-red-800 p-2 rounded-t-md">URAIAN TEMUAN / KONDISI AKTUAL</h4>
                    <textarea name="ft[uraian]" rows="3" placeholder="Tuliskan kondisi aktual, jumlah/ukuran/hasil yang tidak sesuai, lokasi spesifik, serta dampaknya." class="block w-full rounded-b-md border-gray-300 border p-2 text-sm bg-white"></textarea>
                </div>

                <div>
                    <h4 class="text-sm font-bold text-white bg-red-800 p-2 rounded-t-md">KONDISI YANG DIPERSYARATKAN / SEHARUSNYA</h4>
                    <textarea name="ft[kondisi]" rows="3" placeholder="Cantumkan persyaratan spesifikasi, kuantitas, desain, standar mutu, atau waktu sebagai pembanding." class="block w-full rounded-b-md border-gray-300 border p-2 text-sm bg-white"></textarea>
                </div>

                <div>
                    <h4 class="text-sm font-bold text-white bg-red-800 p-2 rounded-t-md">INSTRUKSI / TINDAKAN PERBAIKAN</h4>
                    <textarea name="ft[instruksi]" rows="3" placeholder="Tuliskan tindakan koreksi yang wajib dilakukan penyedia/EO, termasuk penggantian/penambahan/perbaikan bila diperlukan." class="block w-full rounded-b-md border-gray-300 border p-2 text-sm bg-white"></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border border-gray-200 p-4 rounded-md">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Batas Waktu</label>
                        <div class="flex items-center space-x-4 text-sm">
                            <label class="inline-flex items-center"><input type="radio" name="ft[batas_waktu]" value="Segera" class="form-radio text-black"> <span class="ml-1">Segera</span></label>
                            <label class="inline-flex items-center"><input type="radio" name="ft[batas_waktu]" value="Hari ini" class="form-radio text-black"> <span class="ml-1">Hari ini</span></label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="ft[batas_waktu]" value="Tgl/Jam" class="form-radio text-black"> 
                                <span class="ml-1">Tgl/Jam:</span>
                                <input type="text" name="ft[batas_waktu_tgl]" class="ml-2 border-b border-gray-400 bg-transparent focus:outline-none focus:border-black text-sm w-32">
                            </label>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Status Awal</label>
                        <p class="mt-1 font-bold text-gray-800">OPEN</p>
                        <input type="hidden" name="ft[status_awal]" value="OPEN">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Tindakan segera</label>
                        <input type="text" name="ft[tindakan_segera]" class="mt-1 block w-full border-b border-gray-300 bg-transparent focus:outline-none text-sm p-1">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Bukti Temuan</label>
                        <div class="flex space-x-3 text-sm">
                            <label class="inline-flex items-center"><input type="checkbox" name="ft[bukti][]" value="Foto" class="form-checkbox text-black"> <span class="ml-1">Foto</span></label>
                            <label class="inline-flex items-center"><input type="checkbox" name="ft[bukti][]" value="Video" class="form-checkbox text-black"> <span class="ml-1">Video</span></label>
                            <label class="inline-flex items-center"><input type="checkbox" name="ft[bukti][]" value="File" class="form-checkbox text-black"> <span class="ml-1">File</span></label>
                            <label class="inline-flex items-center"><input type="checkbox" name="ft[bukti][]" value="Sampel" class="form-checkbox text-black"> <span class="ml-1">Sampel</span></label>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Distribusi</label>
                        <div class="flex space-x-3 text-sm">
                            <label class="inline-flex items-center"><input type="checkbox" name="ft[distribusi][]" value="Pengawas" class="form-checkbox text-black"> <span class="ml-1">Pengawas</span></label>
                            <label class="inline-flex items-center"><input type="checkbox" name="ft[distribusi][]" value="Penyedia/EO" class="form-checkbox text-black"> <span class="ml-1">Penyedia/EO</span></label>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">No. Lampiran</label>
                        <input type="text" name="ft[no_lampiran]" class="mt-1 block w-full border-b border-gray-300 bg-transparent focus:outline-none text-sm p-1">
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-gray-200">
                    <label class="block text-sm font-medium text-gray-700">Unggah File Lampiran (Opsional)</label>
                    <input type="file" name="attachment" accept=".jpg,.jpeg,.png,.mp4,.mov,.pdf,.zip" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-gray-100 file:text-black hover:file:bg-gray-200 border border-gray-300 rounded-md bg-white">
                    <p class="mt-1 text-xs text-gray-500">Maksimal ukuran file 10 MB (JPG, PNG, MP4, PDF, ZIP).</p>
                </div>
            </div>

                </div>
            </div>

        @elseif($form_code == 'BA-TL-01')
            <!-- BLOK KONTEN BA-TL-01 (BERITA ACARA VERIFIKASI TINDAK LANJUT) -->
            <div class="mb-8 bg-white p-6 rounded-lg border border-gray-200 shadow-sm space-y-6">
                <div class="border-b pb-2 mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Berita Acara Verifikasi Tindak Lanjut / Perbaikan Temuan</h3>
                    <p class="text-xs text-gray-500">Pemeriksaan ulang setelah tindakan koreksi/perbaikan</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">No. Berita Acara</label>
                        <input type="text" name="ba[no]" placeholder="BA-TL-___/___/2026" class="mt-1 block w-full rounded-md border-gray-300 border p-2 text-sm bg-white">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Ref. No. Temuan</label>
                        <input type="text" name="ba[ref_ft]" placeholder="FT-___/___/2026" class="mt-1 block w-full rounded-md border-gray-300 border p-2 text-sm bg-white">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Tanggal Temuan</label>
                        <input type="date" name="ba[tgl_temuan]" class="mt-1 block w-full rounded-md border-gray-300 border p-2 text-sm bg-white">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Tanggal Verifikasi</label>
                        <input type="date" name="ba[tgl_verifikasi]" class="mt-1 block w-full rounded-md border-gray-300 border p-2 text-sm bg-white">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Lokasi</label>
                        <input type="text" name="ba[lokasi]" class="mt-1 block w-full rounded-md border-gray-300 border p-2 text-sm bg-white">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Item/Pekerjaan</label>
                        <input type="text" name="ba[item]" class="mt-1 block w-full rounded-md border-gray-300 border p-2 text-sm bg-white">
                    </div>
                </div>

                <div>
                    <h4 class="text-sm font-bold text-white bg-red-800 p-2 rounded-t-md">RINGKASAN TEMUAN AWAL</h4>
                    <textarea name="ba[ringkasan_awal]" rows="3" class="block w-full rounded-b-md border-gray-300 border p-2 text-sm bg-white"></textarea>
                </div>

                <div>
                    <h4 class="text-sm font-bold text-white bg-red-800 p-2 rounded-t-md">TINDAKAN PERBAIKAN YANG TELAH DILAKUKAN PENYEDIA / EO</h4>
                    <textarea name="ba[tindakan_perbaikan]" rows="3" placeholder="Jelaskan perbaikan, penggantian, penambahan, pemasangan ulang, revisi file, atau tindakan lain yang dilakukan." class="block w-full rounded-b-md border-gray-300 border p-2 text-sm bg-white"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Metode Verifikasi</label>
                    <div class="flex flex-wrap gap-4 text-sm items-center">
                        <label class="inline-flex items-center"><input type="checkbox" name="ba[metode][]" value="Visual" class="form-checkbox text-black"> <span class="ml-2">Visual</span></label>
                        <label class="inline-flex items-center"><input type="checkbox" name="ba[metode][]" value="Ukur" class="form-checkbox text-black"> <span class="ml-2">Ukur</span></label>
                        <label class="inline-flex items-center"><input type="checkbox" name="ba[metode][]" value="Hitung" class="form-checkbox text-black"> <span class="ml-2">Hitung</span></label>
                        <label class="inline-flex items-center"><input type="checkbox" name="ba[metode][]" value="Uji fungsi" class="form-checkbox text-black"> <span class="ml-2">Uji fungsi</span></label>
                        <label class="inline-flex items-center"><input type="checkbox" name="ba[metode][]" value="Dokumen/File" class="form-checkbox text-black"> <span class="ml-2">Dokumen/File</span></label>
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="ba[metode][]" value="Lainnya" class="form-checkbox text-black"> 
                            <span class="ml-2">Lainnya:</span>
                            <input type="text" name="ba[metode_lainnya]" class="ml-2 border-b border-gray-400 bg-transparent focus:outline-none focus:border-black text-sm w-32">
                        </label>
                    </div>
                </div>

                <div>
                    <h4 class="text-sm font-bold text-white bg-red-800 p-2 rounded-t-md">HASIL PEMERIKSAAN ULANG / VERIFIKASI</h4>
                    <textarea name="ba[hasil_verifikasi]" rows="3" placeholder="Tuliskan hasil ukur/hitung/cek visual/uji fungsi dan simpulan kesesuaian." class="block w-full rounded-b-md border-gray-300 border p-2 text-sm bg-white"></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border border-gray-200 p-4 rounded-md bg-gray-50">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Hasil Verifikasi</label>
                        <div class="flex flex-col space-y-2 text-sm">
                            <label class="inline-flex items-center"><input type="radio" name="ba[hasil_akhir]" value="CLOSED" class="form-radio text-black"> <span class="ml-2">CLOSED</span></label>
                            <label class="inline-flex items-center"><input type="radio" name="ba[hasil_akhir]" value="CLOSED DENGAN CATATAN" class="form-radio text-black"> <span class="ml-2">CLOSED DENGAN CATATAN</span></label>
                            <label class="inline-flex items-center"><input type="radio" name="ba[hasil_akhir]" value="OPEN" class="form-radio text-black"> <span class="ml-2">OPEN</span></label>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Tanggal Closed</label>
                        <input type="date" name="ba[tgl_closed]" class="mt-1 block w-full rounded-md border-gray-300 border p-2 text-sm bg-white">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Bukti Perbaikan</label>
                        <div class="flex flex-wrap gap-3 text-sm">
                            <label class="inline-flex items-center"><input type="checkbox" name="ba[bukti][]" value="Foto sebelum-sesudah" class="form-checkbox text-black"> <span class="ml-1">Foto sebelum-sesudah</span></label>
                            <label class="inline-flex items-center"><input type="checkbox" name="ba[bukti][]" value="Video" class="form-checkbox text-black"> <span class="ml-1">Video</span></label>
                            <label class="inline-flex items-center"><input type="checkbox" name="ba[bukti][]" value="File revisi" class="form-checkbox text-black"> <span class="ml-1">File revisi</span></label>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">No. Lampiran</label>
                        <input type="text" name="ba[no_lampiran]" class="mt-1 block w-full border-b border-gray-300 bg-transparent focus:outline-none text-sm p-1">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Catatan akhir</label>
                        <input type="text" name="ba[catatan_akhir]" class="mt-1 block w-full border-b border-gray-300 bg-transparent focus:outline-none text-sm p-1">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Tindak lanjut sisa</label>
                        <input type="text" name="ba[tindak_lanjut_sisa]" class="mt-1 block w-full border-b border-gray-300 bg-transparent focus:outline-none text-sm p-1">
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-gray-200">
                    <label class="block text-sm font-medium text-gray-700">Unggah File Lampiran (Opsional)</label>
                    <input type="file" name="attachment" accept=".jpg,.jpeg,.png,.mp4,.mov,.pdf,.zip" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-gray-100 file:text-black hover:file:bg-gray-200 border border-gray-300 rounded-md bg-white">
                    <p class="mt-1 text-xs text-gray-500">Maksimal ukuran file 10 MB (JPG, PNG, MP4, PDF, ZIP).</p>
                </div>
            </div>

        @else
            <!-- BLOK KONTEN FORM KKH STANDAR -->
            <div class="mb-8 bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
                <div class="flex justify-between items-center mb-4 border-b pb-2">
                    <h3 class="text-xl font-bold text-gray-800">Temuan Prioritas dan Tindak Lanjut</h3>
                    <button type="button" id="add-temuan-btn" class="px-3 py-1 bg-black text-white text-xs font-semibold rounded hover:bg-gray-800 transition">
                        + Tambah Baris Temuan
                    </button>
                </div>
                <p class="text-xs text-gray-500 mb-4">Isi tabel di bawah ini jika terdapat ketidaksesuaian (TS) yang memerlukan catatan atau tindakan koreksi.</p>
                <div id="temuan-container" class="space-y-4"></div>
            </div>

            <div class="bg-blue-50 p-6 rounded-lg border border-blue-200 mb-8">
                <h3 class="text-lg font-bold text-blue-900 mb-4">Kesimpulan Harian</h3>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kondisi Keseluruhan</label>
                    <div class="flex flex-wrap gap-4">
                        <label class="inline-flex items-center cursor-pointer"><input type="radio" name="kondisi" value="Sesuai" class="w-4 h-4 text-black form-radio"><span class="ml-2 text-sm">Sesuai</span></label>
                        <label class="inline-flex items-center cursor-pointer"><input type="radio" name="kondisi" value="Catatan" class="w-4 h-4 text-black form-radio"><span class="ml-2 text-sm">Sesuai dgn catatan</span></label>
                        <label class="inline-flex items-center cursor-pointer"><input type="radio" name="kondisi" value="Perbaikan" class="w-4 h-4 text-black form-radio"><span class="ml-2 text-sm">Perlu perbaikan segera</span></label>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Unggah File Dokumentasi (Opsional)</label>
                    <input type="file" name="attachment" accept="image/*,video/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-gray-100 file:text-black hover:file:bg-gray-200 border border-gray-300 rounded-md bg-white">
                    <p class="mt-1 text-xs text-gray-500">Maksimal ukuran file 10 MB (JPG, PNG, MP4).</p>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">No. File Dokumentasi (Foto/Video)</label>
                    <input type="text" name="dokumentasi" placeholder="Misal: IMG_001.jpg s.d IMG_010.jpg" class="mt-1 block w-full rounded-md border-gray-300 border p-2 text-sm bg-white border-gray-300 focus:border-black focus:ring-black">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Catatan Akhir</label>
                    <textarea name="final_notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 border p-2 text-sm bg-white border-gray-300 focus:border-black focus:ring-black"></textarea>
                </div>
            </div>
        @endif

        <!-- BLOK 4: TANDA TANGAN -->
        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm mb-8">
            <h3 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Pengesahan Dokumen</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- TTD Pengawas / Team Leader -->
                <div class="{{ in_array($form_code, ['RT-01', 'FT-01']) ? 'md:col-start-1' : '' }}">
                    <label class="block text-sm font-bold text-gray-700 mb-2 text-center">
                        {{ $form_code == 'RHP-00' ? 'Koordinator / Team Leader' : 'Konsultan Pengawas' }} <span class="text-red-500">*</span>
                    </label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg bg-gray-50 relative">
                        <canvas id="pad-supervisor" class="w-full h-40 rounded-lg cursor-crosshair"></canvas>
                    </div>
                    <div class="text-center mt-2">
                        <button type="button" onclick="clearPad(sigPadSupervisor)" class="text-xs text-gray-800 hover:text-black font-medium">Bersihkan TTD</button>
                    </div>
                    <input type="hidden" name="signature_supervisor" id="input-supervisor">
                </div>

                @if($form_code != 'RT-01')
                <!-- TTD Penyedia/EO -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 text-center">
                        {{ $form_code == 'RHP-00' ? 'Perwakilan Penyedia / EO' : ($form_code == 'FT-01' ? 'PIC Penyedia/EO (menerima notifikasi)' : 'PIC Penyedia / EO') }}
                    </label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg bg-gray-50 relative">
                        <canvas id="pad-provider" class="w-full h-40 rounded-lg cursor-crosshair"></canvas>
                    </div>
                    <div class="text-center mt-2">
                        <button type="button" onclick="clearPad(sigPadProvider)" class="text-xs text-gray-800 hover:text-black font-medium">Bersihkan TTD</button>
                    </div>
                    <input type="hidden" name="signature_provider" id="input-provider">
                </div>
                @endif

                @if($form_code != 'FT-01')
                <!-- TTD Panitia/Koordinator -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 text-center">
                        {{ $form_code == 'RHP-00' ? 'Mengetahui Panitia*' : ($form_code == 'BA-TL-01' ? 'Mengetahui Panitia/PPK*' : 'Koordinator / Panitia') }}
                    </label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg bg-gray-50 relative">
                        <canvas id="pad-committee" class="w-full h-40 rounded-lg cursor-crosshair"></canvas>
                    </div>
                    <div class="text-center mt-2">
                        <button type="button" onclick="clearPad(sigPadCommittee)" class="text-xs text-gray-800 hover:text-black font-medium">Bersihkan TTD</button>
                    </div>
                    <input type="hidden" name="signature_committee" id="input-committee">
                </div>
                @endif
            </div>
        </div>

        @if($form_code == 'BA-TL-01')
            <p class="text-xs text-gray-500 mb-6 italic">*Kolom Panitia/PPK digunakan bila diperlukan sesuai mekanisme administrasi kegiatan.</p>
        @endif

        <button type="submit" class="w-full bg-black text-white font-bold text-lg py-4 px-4 rounded-lg shadow-lg hover:bg-gray-800 transition uppercase tracking-wide">
            SIMPAN DAN PROSES DOKUMEN
        </button>
    </form>
</div>

@push('scripts')
<script>
    let sigPadSupervisor, sigPadProvider, sigPadCommittee;

    function clearPad(padInstance) {
        padInstance.clear();
    }

    document.addEventListener("DOMContentLoaded", function() {
        function setupCanvas(canvasId) {
            const canvas = document.getElementById(canvasId);
            if (!canvas) return null;
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
            return canvas;
        }

        const cSup = setupCanvas('pad-supervisor');
        const cPro = setupCanvas('pad-provider');
        const cCom = setupCanvas('pad-committee');

        if (cSup) sigPadSupervisor = new SignaturePad(cSup, { penColor: "rgb(0, 0, 139)" });
        if (cPro) sigPadProvider = new SignaturePad(cPro, { penColor: "rgb(0, 0, 139)" });
        if (cCom) sigPadCommittee = new SignaturePad(cCom, { penColor: "rgb(0, 0, 139)" });

        document.getElementById('pengawasanForm').addEventListener('submit', function (e) {
            if (sigPadSupervisor && sigPadSupervisor.isEmpty()) {
                e.preventDefault();
                alert("Tanda Tangan Pengawas / Team Leader wajib diisi!");
                return;
            }

            if (sigPadSupervisor) document.getElementById('input-supervisor').value = sigPadSupervisor.toDataURL('image/png');
            if (sigPadProvider && !sigPadProvider.isEmpty()) document.getElementById('input-provider').value = sigPadProvider.toDataURL('image/png');
            if (sigPadCommittee && !sigPadCommittee.isEmpty()) document.getElementById('input-committee').value = sigPadCommittee.toDataURL('image/png');
            
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = 'Menyimpan...';
                submitBtn.classList.add('opacity-70', 'cursor-not-allowed');
            }
        });
    });

    // Dynamic Row Handler untuk Tabel Temuan KKH Standar
    const addTemuanBtn = document.getElementById('add-temuan-btn');
    if (addTemuanBtn) {
        let temuanIndex = 0;
        addTemuanBtn.addEventListener('click', function() {
            const container = document.getElementById('temuan-container');
            const rowHTML = `
                <div class="p-4 bg-gray-50 border border-gray-200 rounded-md relative temuan-row">
                    <button type="button" onclick="this.parentElement.remove()" class="absolute top-2 right-2 text-gray-800 hover:text-red-800 text-xs font-bold">Hapus</button>
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-700">No. FT</label>
                            <input type="text" name="temuan[${temuanIndex}][no_ft]" placeholder="Cth: FT-01/01" class="mt-1 block w-full rounded-md border-gray-300 border p-1.5 text-xs bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700">Temuan / Ketidaksesuaian</label>
                            <input type="text" name="temuan[${temuanIndex}][deskripsi]" placeholder="Uraian temuan..." class="mt-1 block w-full rounded-md border-gray-300 border p-1.5 text-xs bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700">Tindakan yang Diminta</label>
                            <input type="text" name="temuan[${temuanIndex}][tindakan]" placeholder="Instruksi perbaikan..." class="mt-1 block w-full rounded-md border-gray-300 border p-1.5 text-xs bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700">PIC / Target</label>
                            <input type="text" name="temuan[${temuanIndex}][pic]" placeholder="Nama PIC / Waktu..." class="mt-1 block w-full rounded-md border-gray-300 border p-1.5 text-xs bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700">Status</label>
                            <select name="temuan[${temuanIndex}][status]" class="mt-1 block w-full rounded-md border-gray-300 border p-1.5 text-xs bg-white">
                                <option value="Open">Open</option>
                                <option value="Closed">Closed</option>
                            </select>
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', rowHTML);
            temuanIndex++;
        });
    }

    // Dynamic Row Handler untuk Tabel Register Temuan RT-01
    const addRtBtn = document.getElementById('add-rt-btn');
    if (addRtBtn) {
        let rtIndex = 0;
        addRtBtn.addEventListener('click', function() {
            const container = document.getElementById('rt-container');
            const rowHTML = `
                <div class="p-4 bg-gray-50 border border-gray-200 rounded-md relative temuan-row">
                    <button type="button" onclick="this.parentElement.remove()" class="absolute top-2 right-2 text-gray-800 hover:text-red-800 text-xs font-bold">Hapus</button>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-700">No. FT</label>
                            <input type="text" name="rt_temuan[${rtIndex}][no_ft]" class="mt-1 block w-full rounded-md border-gray-300 border p-1.5 text-xs bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700">Tanggal</label>
                            <input type="date" name="rt_temuan[${rtIndex}][tanggal]" class="mt-1 block w-full rounded-md border-gray-300 border p-1.5 text-xs bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700">Lokasi / Item</label>
                            <input type="text" name="rt_temuan[${rtIndex}][lokasi]" class="mt-1 block w-full rounded-md border-gray-300 border p-1.5 text-xs bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700">Kategori</label>
                            <input type="text" name="rt_temuan[${rtIndex}][kategori]" class="mt-1 block w-full rounded-md border-gray-300 border p-1.5 text-xs bg-white">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-700">Ringkasan Temuan</label>
                            <input type="text" name="rt_temuan[${rtIndex}][ringkasan]" class="mt-1 block w-full rounded-md border-gray-300 border p-1.5 text-xs bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700">PIC</label>
                            <input type="text" name="rt_temuan[${rtIndex}][pic]" class="mt-1 block w-full rounded-md border-gray-300 border p-1.5 text-xs bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700">Target</label>
                            <input type="text" name="rt_temuan[${rtIndex}][target]" class="mt-1 block w-full rounded-md border-gray-300 border p-1.5 text-xs bg-white">
                        </div>
                        <div class="md:col-span-4">
                            <label class="block text-xs font-medium text-gray-700">Status / Ref. BA</label>
                            <input type="text" name="rt_temuan[${rtIndex}][status]" class="mt-1 block w-full rounded-md border-gray-300 border p-1.5 text-xs bg-white">
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', rowHTML);
            rtIndex++;
        });
    }
</script>
@endpush

@endsection

@push('scripts')
<script>
    function handleFileSelect(input, otherInputName) {
        const container = input.closest('.mt-2');
        const previewContainer = container.querySelector('.file-preview-container');
        const nameLabel = container.querySelector('.file-name-label');
        const otherInput = container.querySelector(`input[name="${otherInputName}"]`);
        
        if (input.files && input.files.length > 0) {
            nameLabel.textContent = input.files[0].name;
            previewContainer.classList.remove('hidden');
            if(otherInput) otherInput.value = ''; 
        } else {
            previewContainer.classList.add('hidden');
        }
    }

    function clearFileSelection(btn) {
        const container = btn.closest('.mt-2');
        const inputs = container.querySelectorAll('.file-upload-input');
        inputs.forEach(input => input.value = '');
        
        const previewContainer = container.querySelector('.file-preview-container');
        previewContainer.classList.add('hidden');
    }
</script>
@endpush

