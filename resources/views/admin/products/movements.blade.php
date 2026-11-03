@extends('admin.layout')
@section('title', 'Riwayat Stok')
@section('content')
    <a href="{{ route('admin.products.index') }}" class="text-sm font-bold text-cyan-800">← Kembali ke produk</a>
    <h1 class="mt-3 text-3xl font-black">Riwayat Stok</h1>
    <p class="mt-2 text-sm text-slate-500">{{ $product->name }} · SKU {{ $product->sku }} · Stok sekarang {{ $product->stock }}</p>
    <div class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white">
        <div class="overflow-x-auto"><table class="w-full min-w-[650px] text-left text-sm">
            <thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-4 py-3">Waktu</th><th class="px-4 py-3">Jenis</th><th class="px-4 py-3">Jumlah</th><th class="px-4 py-3">Sebelum</th><th class="px-4 py-3">Sesudah</th><th class="px-4 py-3">Petugas / Catatan</th></tr></thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($movements as $movement)
                <tr><td class="px-4 py-3 whitespace-nowrap">{{ $movement->created_at->format('d/m/Y H:i') }}</td><td class="px-4 py-3">{{ ['in'=>'Masuk','out'=>'Keluar','adjustment'=>'Penyesuaian'][$movement->movement_type] }}</td><td class="px-4 py-3">{{ $movement->quantity }}</td><td class="px-4 py-3">{{ $movement->stock_before }}</td><td class="px-4 py-3 font-bold">{{ $movement->stock_after }}</td><td class="px-4 py-3">{{ $movement->user?->name ?? 'Sistem' }}<div class="text-xs text-slate-500">{{ $movement->note }}</div></td></tr>
                @empty <tr><td colspan="6" class="px-4 py-8 text-center text-slate-500">Belum ada pergerakan stok.</td></tr> @endforelse
            </tbody>
        </table></div>
    </div>
    <div class="mt-5">{{ $movements->links() }}</div>
@endsection
