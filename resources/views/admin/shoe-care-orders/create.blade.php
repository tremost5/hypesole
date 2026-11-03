@extends('admin.layout')
@section('title', 'Buat Order Shoe Care')
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
        <p class="text-xs font-black uppercase tracking-[.2em] text-cyan-700">Penerimaan & inspeksi</p>
        <h1 class="mt-1 text-3xl font-black">Buat Order Shoe Care</h1>
        <p class="mt-2 text-sm text-slate-500">Satu nomor order dapat berisi hingga 10 pasang sepatu, masing-masing dengan treatment sendiri.</p>
    </div>
    <a href="{{ route('admin.shoe-care-orders.index') }}" class="inline-flex min-h-11 items-center rounded-xl border border-slate-300 bg-white px-4 font-bold text-slate-700 hover:bg-slate-50">← Kembali ke order</a>
</div>

<form method="POST" action="{{ route('admin.shoe-care-orders.store') }}" enctype="multipart/form-data" class="space-y-5">
@csrf
<section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
    <div class="mb-4"><span class="rounded-full bg-cyan-50 px-3 py-1 text-xs font-black text-cyan-800">01 · CUSTOMER</span><h2 class="mt-2 text-xl font-black">Data pelanggan</h2></div>
    <div class="grid gap-4 sm:grid-cols-2">
        <label class="sm:col-span-2"><span class="mb-2 block text-sm font-bold">Pilih customer terdaftar (opsional)</span>
            <select name="customer_id" id="customer_id" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3">
                <option value="">Customer baru / walk-in</option>
                @foreach($customers as $customer)<option value="{{ $customer->id }}" data-name="{{ $customer->name }}" data-phone="{{ $customer->phone }}" @selected(old('customer_id')==$customer->id)>{{ $customer->name ?: 'Tanpa nama' }} — {{ $customer->phone }}</option>@endforeach
            </select>
        </label>
        <label><span class="mb-2 block text-sm font-bold">Nama customer *</span><input id="customer_name" name="customer_name" value="{{ old('customer_name') }}" required maxlength="120" class="w-full rounded-xl border border-slate-300 px-4 py-3"></label>
        <label><span class="mb-2 block text-sm font-bold">Nomor WhatsApp *</span><input id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}" required maxlength="24" inputmode="tel" placeholder="08xxxxxxxxxx" class="w-full rounded-xl border border-slate-300 px-4 py-3"></label>
    </div>
</section>

<section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div><span class="rounded-full bg-violet-50 px-3 py-1 text-xs font-black text-violet-800">02 · DETAIL SEPATU</span><h2 class="mt-2 text-xl font-black">Daftar sepatu & treatment</h2><p class="mt-1 text-sm text-slate-500">Isi satu kartu untuk setiap pasang sepatu.</p></div>
        <button type="button" id="add-item" style="background:#0e7490;color:#fff" class="min-h-11 rounded-xl px-4 py-2.5 text-sm font-black shadow-sm">+ TAMBAH SEPATU</button>
    </div>

    <div id="items-container" class="mt-5 space-y-4">
        <article class="shoe-item rounded-2xl border border-slate-200 bg-slate-50 p-4 sm:p-5" data-index="0">
            <div class="mb-4 flex items-center justify-between"><h3 class="item-heading font-black">Sepatu 1</h3><button type="button" class="remove-item hidden rounded-lg border border-rose-200 bg-white px-3 py-2 text-xs font-black text-rose-700">HAPUS</button></div>
            <div class="grid gap-4 sm:grid-cols-2">
                <label class="sm:col-span-2"><span class="mb-2 block text-sm font-bold">Treatment *</span><select name="items[0][service_id]" required class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3"><option value="">Pilih treatment</option>@foreach($services as $service)<option value="{{ $service->id }}">{{ $service->category?->name }} · {{ $service->name }} · Rp{{ number_format((float)$service->price,0,',','.') }}</option>@endforeach</select></label>
                <label><span class="mb-2 block text-sm font-bold">Brand</span><input name="items[0][shoe_brand]" maxlength="100" placeholder="Nike, Adidas..." class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3"></label>
                <label><span class="mb-2 block text-sm font-bold">Model</span><input name="items[0][shoe_model]" maxlength="150" placeholder="Air Force 1..." class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3"></label>
                <label><span class="mb-2 block text-sm font-bold">Warna</span><input name="items[0][shoe_color]" maxlength="80" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3"></label>
                <label><span class="mb-2 block text-sm font-bold">Ukuran</span><input name="items[0][shoe_size]" maxlength="30" placeholder="42 / 8.5 US" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3"></label>
                <label class="sm:col-span-2"><span class="mb-2 block text-sm font-bold">Keluhan / kondisi sepatu *</span><textarea name="items[0][issue_description]" required rows="3" maxlength="3000" placeholder="Ceritakan kondisi dan bagian yang perlu ditangani..." class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3"></textarea></label>
                <label><span class="mb-2 block text-sm font-bold">Staff penanggung jawab</span><select name="items[0][assigned_staff_id]" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3"><option value="">Belum ditentukan</option>@foreach($staff as $person)<option value="{{ $person->id }}">{{ $person->name }} — {{ $person->role?->name }}</option>@endforeach</select></label>
                <label><span class="mb-2 block text-sm font-bold">Foto kondisi sepatu (maks. 5)</span><input type="file" name="items[0][before_photos][]" accept="image/jpeg,image/png,image/webp" multiple class="block w-full rounded-xl border border-slate-300 bg-white p-2.5 text-sm"></label>
            </div>
        </article>
    </div>
    <p class="mt-3 text-xs text-slate-500">Harga per item mengikuti harga treatment yang dipilih. Harga akhir dapat dikonfirmasi setelah inspeksi.</p>
</section>

<section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
    <div class="mb-4"><span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-black text-amber-800">03 · CATATAN</span><h2 class="mt-2 text-xl font-black">Informasi penerimaan</h2></div>
    <div class="grid gap-4 sm:grid-cols-2">
        <label><span class="mb-2 block text-sm font-bold">Estimasi selesai</span><input type="datetime-local" name="estimated_completion_at" value="{{ old('estimated_completion_at') }}" class="w-full rounded-xl border border-slate-300 px-4 py-3"></label>
        <label class="sm:col-span-2"><span class="mb-2 block text-sm font-bold">Catatan customer</span><textarea name="customer_notes" rows="2" class="w-full rounded-xl border border-slate-300 px-4 py-3"></textarea></label>
    </div>
</section>

<div class="sticky bottom-3 z-20 flex flex-wrap gap-3 rounded-2xl border border-slate-200 bg-white/95 p-3 shadow-xl backdrop-blur">
    <button type="submit" style="background:#0f172a;color:#fff" class="min-h-12 flex-1 rounded-xl px-5 py-3 text-sm font-black">SIMPAN & BUAT NOMOR ORDER</button>
    <a href="{{ route('admin.shoe-care-orders.index') }}" class="inline-flex min-h-12 items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-bold text-slate-700">BATAL</a>
</div>
</form>

<template id="shoe-template">
<article class="shoe-item rounded-2xl border border-slate-200 bg-slate-50 p-4 sm:p-5">
    <div class="mb-4 flex items-center justify-between"><h3 class="item-heading font-black"></h3><button type="button" class="remove-item rounded-lg border border-rose-200 bg-white px-3 py-2 text-xs font-black text-rose-700">HAPUS</button></div>
    <div class="grid gap-4 sm:grid-cols-2">
        <label class="sm:col-span-2"><span class="mb-2 block text-sm font-bold">Treatment *</span><select data-field="service_id" required class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3"><option value="">Pilih treatment</option>@foreach($services as $service)<option value="{{ $service->id }}">{{ $service->category?->name }} · {{ $service->name }} · Rp{{ number_format((float)$service->price,0,',','.') }}</option>@endforeach</select></label>
        <label><span class="mb-2 block text-sm font-bold">Brand</span><input data-field="shoe_brand" maxlength="100" placeholder="Nike, Adidas..." class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3"></label>
        <label><span class="mb-2 block text-sm font-bold">Model</span><input data-field="shoe_model" maxlength="150" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3"></label>
        <label><span class="mb-2 block text-sm font-bold">Warna</span><input data-field="shoe_color" maxlength="80" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3"></label>
        <label><span class="mb-2 block text-sm font-bold">Ukuran</span><input data-field="shoe_size" maxlength="30" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3"></label>
        <label class="sm:col-span-2"><span class="mb-2 block text-sm font-bold">Keluhan / kondisi sepatu *</span><textarea data-field="issue_description" required rows="3" maxlength="3000" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3"></textarea></label>
        <label><span class="mb-2 block text-sm font-bold">Staff penanggung jawab</span><select data-field="assigned_staff_id" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3"><option value="">Belum ditentukan</option>@foreach($staff as $person)<option value="{{ $person->id }}">{{ $person->name }} — {{ $person->role?->name }}</option>@endforeach</select></label>
        <label><span class="mb-2 block text-sm font-bold">Foto kondisi sepatu (maks. 5)</span><input data-field="before_photos" type="file" accept="image/jpeg,image/png,image/webp" multiple class="block w-full rounded-xl border border-slate-300 bg-white p-2.5 text-sm"></label>
    </div>
</article>
</template>
<script>
(() => {
    const container = document.getElementById('items-container');
    const template = document.getElementById('shoe-template');
    const addButton = document.getElementById('add-item');
    let nextIndex = 1;

    function refreshCards() {
        const cards = [...container.querySelectorAll('.shoe-item')];
        cards.forEach((card, position) => {
            card.querySelector('.item-heading').textContent = `Sepatu ${position + 1}`;
            card.querySelector('.remove-item').classList.toggle('hidden', cards.length === 1);
        });
        addButton.disabled = cards.length >= 10;
        addButton.textContent = cards.length >= 10 ? 'MAKSIMAL 10 SEPATU' : '+ TAMBAH SEPATU';
    }

    addButton.addEventListener('click', () => {
        if (container.querySelectorAll('.shoe-item').length >= 10) return;
        const fragment = template.content.cloneNode(true);
        const card = fragment.querySelector('.shoe-item');
        card.querySelectorAll('[data-field]').forEach(field => {
            const key = field.dataset.field;
            field.name = `items[${nextIndex}][${key}]${key === 'before_photos' ? '[]' : ''}`;
        });
        container.appendChild(fragment);
        nextIndex++;
        refreshCards();
    });

    container.addEventListener('click', event => {
        const button = event.target.closest('.remove-item');
        if (!button) return;
        button.closest('.shoe-item').remove();
        refreshCards();
    });

    document.getElementById('customer_id')?.addEventListener('change', function () {
        const option = this.options[this.selectedIndex];
        if (this.value) {
            document.getElementById('customer_name').value = option.dataset.name || '';
            document.getElementById('customer_phone').value = option.dataset.phone || '';
        }
    });

    refreshCards();
})();
</script>
@endsection
