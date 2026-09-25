@extends('layouts.app')

@section('content')
<div class="mb-10 text-center">
    <h1 class="text-3xl md:text-4xl font-extrabold text-black mb-3 tracking-tight">Portal Pengawasan</h1>
    <p class="text-gray-500 max-w-lg mx-auto">Pilih kategori dan jenis formulir yang akan Anda isi atau lihat riwayat pengawasan.</p>
</div>

<!-- Alert Sukses Notifikasi -->
@if(session('success'))
    <div class="mb-6 p-4 bg-gray-900 border border-black text-white rounded-md font-medium text-sm flex items-center shadow-lg">
        <svg class="w-5 h-5 mr-2 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        {{ session('success') }}
    </div>
@endif

<!-- Bagian Navigasi Menu Form -->
<div class="space-y-8 mb-12">
    @foreach($kategoriForm as $kategori => $forms)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden transition-all duration-200 hover:shadow-md">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-bold text-black uppercase tracking-wider text-sm">{{ $kategori }}</h2>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($forms as $form)
                    <a href="{{ route('form.show', $form['code']) }}" class="block p-5 border border-gray-200 rounded-lg hover:border-black hover:bg-gray-50 transition duration-150 ease-in-out group relative overflow-hidden">
                        <div class="text-xs font-bold text-gray-400 mb-2 group-hover:text-black transition-colors">{{ $form['code'] }}</div>
                        <div class="font-semibold text-gray-900">{{ $form['name'] }}</div>
                        <div class="absolute right-4 bottom-4 opacity-0 group-hover:opacity-100 transition-opacity">
                            <svg class="w-5 h-5 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endforeach
</div>

<!-- Tabel Riwayat Pengisian & Export PDF -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-bold text-black uppercase tracking-wider text-sm">Riwayat Pengawasan Terbaru</h2>
    </div>
    
    <!-- Filter Pencarian -->
    <div class="bg-white p-6 border-b border-gray-200">
        <form action="{{ route('home') }}" method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Pencarian</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Kode Form / Lokasi / Pengawas..." class="w-full border-gray-300 rounded-md shadow-sm focus:border-black focus:ring-black sm:text-sm p-2.5 border transition-colors">
            </div>
            <div class="md:w-48">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Tanggal</label>
                <input type="date" name="date" value="{{ request('date') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-black focus:ring-black sm:text-sm p-2.5 border transition-colors">
            </div>
            <div class="flex items-end space-x-2">
                <button type="submit" class="px-5 py-2.5 bg-black text-white text-sm font-bold rounded-md hover:bg-gray-800 transition shadow-sm">Cari</button>
                <a href="{{ route('home') }}" class="px-5 py-2.5 bg-white border border-gray-300 text-black text-sm font-bold rounded-md hover:bg-gray-50 transition shadow-sm">Reset</a>
            </div>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-600 whitespace-nowrap">
            <thead class="text-xs text-gray-400 uppercase bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-4 font-bold tracking-wider">Waktu</th>
                    <th class="px-6 py-4 font-bold tracking-wider">Kode Form</th>
                    <th class="px-6 py-4 font-bold tracking-wider">Pengawas</th>
                    <th class="px-6 py-4 font-bold tracking-wider">Lokasi</th>
                    <th class="px-6 py-4 font-bold tracking-wider text-center">Tanda Tangan</th>
                    <th class="px-6 py-4 font-bold tracking-wider text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($submissions as $sub)
                <tr class="bg-white hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4">{{ \Carbon\Carbon::parse($sub->created_at)->format('d M Y H:i') }}</td>
                    <td class="px-6 py-4 font-bold text-black">{{ $sub->form_code }}</td>
                    <td class="px-6 py-4">{{ $sub->supervisor_name }}</td>
                    <td class="px-6 py-4">{{ $sub->location }}</td>
                    <td class="px-6 py-4 text-center">
                        @if($sub->signature_supervisor)
                            <img src="{{ $sub->signature_supervisor }}" alt="TTD" class="h-8 mx-auto grayscale mix-blend-multiply opacity-80">
                        @else
                            <span class="text-gray-300">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end space-x-2">
                            @if(!empty($sub->form_data['attachment_path']))
                                <a href="{{ asset($sub->form_data['attachment_path']) }}" target="_blank" class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-md text-xs font-bold text-black bg-white hover:bg-gray-100 transition-colors" title="Lihat Lampiran">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </a>
                            @endif
                            <a href="{{ route('form.pdf', $sub->id) }}" target="_blank" class="inline-flex items-center px-3 py-1.5 border border-black rounded-md text-xs font-bold text-white bg-black hover:bg-gray-800 transition-colors" title="Cetak PDF">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                PDF
                            </a>
                            <form action="{{ route('form.destroy', $sub->id) }}" method="POST" onsubmit="if(confirm('Hapus laporan pengawasan ini?')){ const btn = this.querySelector('button[type=submit]'); btn.disabled = true; btn.innerHTML = '<svg class=\'animate-spin w-4 h-4 text-red-600\' xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\'><circle class=\'opacity-25\' cx=\'12\' cy=\'12\' r=\'10\' stroke=\'currentColor\' stroke-width=\'4\'></circle><path class=\'opacity-75\' fill=\'currentColor\' d=\'M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z\'></path></svg>'; btn.classList.add('opacity-50', 'cursor-not-allowed'); this.closest('tr').style.opacity = '0.5'; return true; } return false;" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-red-200 rounded-md text-xs font-bold text-red-600 bg-white hover:bg-red-50 hover:border-red-300 transition-colors" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-400 font-medium">Belum ada data pengawasan yang diisi.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection