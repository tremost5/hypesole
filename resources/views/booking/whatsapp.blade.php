<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#07152f">
    <title>Booking via WhatsApp — HYPESOLE</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
      :root{--ink:#07152f;--blue:#246bfe;--muted:#65738b}
      body{background:linear-gradient(145deg,#f4f7fd,#fff 52%,#edf3ff);color:var(--ink)}
      .field{width:100%;min-height:48px;border:1px solid #d8e0ed;border-radius:13px;padding:11px 13px;background:#fff;color:#0b1d40;font-size:14px}
      .field:focus{outline:3px solid rgba(36,107,254,.18);border-color:#246bfe}
      .field-label{display:block;margin-bottom:7px;font-size:12px;font-weight:800;color:#253653}
      .section-card{border:1px solid #e2e8f0;border-radius:22px;background:#fff;padding:20px;box-shadow:0 12px 35px rgba(15,35,75,.045)}
    </style>
</head>
<body class="min-h-screen antialiased">
<header class="border-b border-white/10 bg-[#07152f] text-white">
  <div class="mx-auto flex min-h-[68px] max-w-4xl items-center justify-between px-4">
    <a href="{{ route('home') }}" class="leading-tight"><span class="block text-[9px] font-bold tracking-[.28em] text-blue-200">CARE • CLEAN • SHOP</span><strong class="text-xl font-black tracking-widest">HYPESOLE</strong></a>
    <a href="{{ route('home') }}" class="rounded-xl border border-white/20 px-4 py-2 text-xs font-bold text-white">KEMBALI</a>
  </div>
</header>
<main class="mx-auto max-w-4xl px-4 py-8 sm:py-12">
  <div class="mb-7">
    <p class="text-xs font-black tracking-[.22em] text-blue-700">GUEST BOOKING</p>
    <h1 class="mt-2 text-3xl font-black tracking-tight sm:text-4xl">Booking Shoe Care</h1>
    <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600">Isi data di bawah tanpa perlu login. Setelah dikirim, WhatsApp akan terbuka dengan ringkasan booking yang siap kamu kirim ke tim HYPESOLE.</p>
  </div>

  @if ($errors->any())
    <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
      <p class="font-black">Periksa kembali data booking:</p>
      <ul class="mt-2 list-inside list-disc space-y-1">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
  @endif

  <form
    method="POST"
    action="{{ route('guest.booking.whatsapp.store') }}"
    class="space-y-5"
    id="guest-booking-form"
>
    @csrf
    <section class="section-card">
      <div class="mb-5"><h2 class="text-lg font-black">Data pelanggan</h2><p class="mt-1 text-xs text-slate-500">Nomor WhatsApp digunakan tim kami untuk menghubungi kamu.</p></div>
      <div class="grid gap-4 sm:grid-cols-2">
        <div><label class="field-label" for="customer_name">Nama lengkap *</label><input class="field" id="customer_name" name="customer_name" value="{{ old('customer_name') }}" required maxlength="120" placeholder="Nama kamu"></div>
        <div><label class="field-label" for="customer_phone">Nomor WhatsApp aktif *</label><input class="field" id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}" required inputmode="tel" maxlength="25" placeholder="08xxxxxxxxxx"></div>
        <div class="sm:col-span-2"><label class="field-label" for="customer_address">Alamat lengkap *</label><textarea class="field" id="customer_address" name="customer_address" rows="3" required maxlength="1000" placeholder="Nama jalan, nomor rumah, kelurahan, kecamatan, kota">{{ old('customer_address') }}</textarea></div>
        <div><label class="field-label" for="pickup_method">Metode penyerahan *</label><select class="field" id="pickup_method" name="pickup_method" required><option value="pickup" @selected(old('pickup_method','pickup')==='pickup')>Minta pickup / dijemput</option><option value="dropoff" @selected(old('pickup_method')==='dropoff')>Saya antar sendiri</option></select></div>
        <div><label class="field-label" for="preferred_date">Tanggal yang diinginkan</label><input class="field" type="date" id="preferred_date" name="preferred_date" min="{{ now()->toDateString() }}" value="{{ old('preferred_date') }}"></div>
      </div>
    </section>

    <section class="section-card">
      <div class="mb-5 flex flex-col justify-between gap-3 sm:flex-row sm:items-center"><div><h2 class="text-lg font-black">Detail sepatu</h2><p class="mt-1 text-xs text-slate-500">Tambahkan setiap pasang sepatu agar layanan dan keluhannya tercatat dengan jelas.</p></div><span id="shoe-count" class="w-fit rounded-full bg-blue-50 px-3 py-2 text-[11px] font-black text-blue-800">1 PASANG</span></div>
      <div id="shoe-items" class="space-y-4">
        @php($oldItems = old('items', [[]]))
        @foreach($oldItems as $index => $oldItem)
          <div class="shoe-item rounded-2xl border border-slate-200 bg-slate-50/80 p-4" data-index="{{ $index }}">
            <div class="mb-4 flex items-center justify-between"><h3 class="shoe-heading text-sm font-black">Sepatu {{ $index + 1 }}</h3>@if($index > 0)<button type="button" class="remove-shoe text-xs font-bold text-red-600">Hapus</button>@endif</div>
            <div class="grid gap-3 sm:grid-cols-2">
              <div><label class="field-label">Layanan *</label><select class="field" name="items[{{ $index }}][service_id]" required><option value="">Pilih layanan</option>@foreach($services as $service)<option value="{{ $service->id }}" @selected(($oldItem['service_id'] ?? '') == $service->id)>{{ $service->name }}@if(isset($service->price)) — Rp{{ number_format((float)$service->price,0,',','.') }}@endif</option>@endforeach</select></div>
              <div><label class="field-label">Jenis sepatu *</label><select class="field" name="items[{{ $index }}][shoe_type]" required><option value="">Pilih jenis</option>@foreach(['Sneakers','Running shoes','Canvas','Leather','Suede',' boots','Formal shoes','Lainnya'] as $type)<option value="{{ trim($type) }}" @selected(($oldItem['shoe_type'] ?? '')===trim($type))>{{ trim($type) }}</option>@endforeach</select></div>
              <div><label class="field-label">Brand</label><input class="field" name="items[{{ $index }}][shoe_brand]" value="{{ $oldItem['shoe_brand'] ?? '' }}" maxlength="100" placeholder="Nike, Adidas, New Balance..."></div>
              <div><label class="field-label">Model / seri</label><input class="field" name="items[{{ $index }}][shoe_model]" value="{{ $oldItem['shoe_model'] ?? '' }}" maxlength="120" placeholder="Air Force 1, Samba..."></div>
              <div><label class="field-label">Ukuran</label><input class="field" name="items[{{ $index }}][shoe_size]" value="{{ $oldItem['shoe_size'] ?? '' }}" maxlength="20" placeholder="42"></div>
              <div><label class="field-label">Warna</label><input class="field" name="items[{{ $index }}][shoe_color]" value="{{ $oldItem['shoe_color'] ?? '' }}" maxlength="60" placeholder="Putih / hitam"></div>
              <div class="sm:col-span-2"><label class="field-label">Keluhan / kondisi sepatu *</label><textarea class="field" name="items[{{ $index }}][issue_description]" rows="2" required maxlength="1000" placeholder="Contoh: bagian upper kotor, noda di midsole...">{{ $oldItem['issue_description'] ?? '' }}</textarea></div>
              </div>
          </div>
        @endforeach
      </div>
      <button type="button" id="add-shoe" class="mt-4 min-h-11 w-full rounded-xl border border-dashed border-blue-300 bg-blue-50 px-4 text-sm font-black text-blue-800 hover:bg-blue-100">+ TAMBAH PASANG SEPATU</button>
      <p class="mt-2 text-xs text-slate-500">Maksimal 10 pasang dalam satu pengajuan.</p>
    </section>

    <section class="section-card">
      <label class="field-label" for="customer_notes">Catatan tambahan (opsional)</label>
      <textarea class="field" id="customer_notes" name="customer_notes" rows="3" maxlength="1500" placeholder="Patokan alamat, jam pickup, atau informasi lainnya...">{{ old('customer_notes') }}</textarea>
    </section>

    <div class="rounded-2xl border border-blue-100 bg-blue-50 p-4 text-xs leading-5 text-blue-900">Dengan menekan tombol di bawah, kamu akan diarahkan ke WhatsApp HYPESOLE. Booking baru dikirim setelah kamu menekan tombol <strong>Kirim</strong> di WhatsApp.</div>
    <button type="submit" class="flex min-h-14 w-full items-center justify-center gap-2 rounded-2xl px-5 text-sm font-black shadow-lg transition" style="background-color:#16a34a !important;color:#ffffff !important;border:1px solid #15803d;"><span>BOOKING VIA WHATSAPP</span><span aria-hidden="true">↗</span></button>
  </form>
</main>

<template id="shoe-template">
  <div class="shoe-item rounded-2xl border border-slate-200 bg-slate-50/80 p-4" data-index="__INDEX__">
    <div class="mb-4 flex items-center justify-between"><h3 class="shoe-heading text-sm font-black">Sepatu __NUMBER__</h3><button type="button" class="remove-shoe text-xs font-bold text-red-600">Hapus</button></div>
    <div class="grid gap-3 sm:grid-cols-2">
      <div><label class="field-label">Layanan *</label><select class="field" name="items[__INDEX__][service_id]" required><option value="">Pilih layanan</option>@foreach($services as $service)<option value="{{ $service->id }}">{{ $service->name }}@if(isset($service->price)) — Rp{{ number_format((float)$service->price,0,',','.') }}@endif</option>@endforeach</select></div>
      <div><label class="field-label">Jenis sepatu *</label><select class="field" name="items[__INDEX__][shoe_type]" required><option value="">Pilih jenis</option>@foreach(['Sneakers','Running shoes','Canvas','Leather','Suede','Boots','Formal shoes','Lainnya'] as $type)<option value="{{ $type }}">{{ $type }}</option>@endforeach</select></div>
      <div><label class="field-label">Brand</label><input class="field" name="items[__INDEX__][shoe_brand]" maxlength="100" placeholder="Nike, Adidas, New Balance..."></div>
      <div><label class="field-label">Model / seri</label><input class="field" name="items[__INDEX__][shoe_model]" maxlength="120" placeholder="Air Force 1, Samba..."></div>
      <div><label class="field-label">Ukuran</label><input class="field" name="items[__INDEX__][shoe_size]" maxlength="20" placeholder="42"></div>
      <div><label class="field-label">Warna</label><input class="field" name="items[__INDEX__][shoe_color]" maxlength="60" placeholder="Putih / hitam"></div>
      <div class="sm:col-span-2"><label class="field-label">Keluhan / kondisi sepatu *</label><textarea class="field" name="items[__INDEX__][issue_description]" rows="2" required maxlength="1000" placeholder="Contoh: bagian upper kotor, noda di midsole..."></textarea></div>
      </div>
  </div>
</template>
<script>
(() => {
  const list = document.getElementById('shoe-items');
  const add = document.getElementById('add-shoe');
  const template = document.getElementById('shoe-template');
  const count = document.getElementById('shoe-count');
  const max = 10;
  function refresh() {
    [...list.querySelectorAll('.shoe-item')].forEach((card, i) => {
      card.dataset.index = i;
      card.querySelector('.shoe-heading').textContent = `Sepatu ${i + 1}`;
      card.querySelectorAll('[name]').forEach(field => {
        field.name = field.name.replace(/items\[\d+\]/, `items[${i}]`);
      });
    });
    const total = list.querySelectorAll('.shoe-item').length;
    count.textContent = `${total} PASANG`;
    add.disabled = total >= max;
    add.classList.toggle('opacity-40', total >= max);
  }
  add.addEventListener('click', () => {
    const total = list.querySelectorAll('.shoe-item').length;
    if (total >= max) return;
    list.insertAdjacentHTML('beforeend', template.innerHTML.replaceAll('__INDEX__', String(total)).replaceAll('__NUMBER__', String(total + 1)));
    refresh();
  });
  list.addEventListener('click', event => {
    if (event.target.closest('.remove-shoe')) {
      event.target.closest('.shoe-item').remove();
      refresh();
    }
  });
  refresh();
})();
</script>
</body>
</html>
