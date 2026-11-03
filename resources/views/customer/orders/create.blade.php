<!DOCTYPE html>
<html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Booking Shoe Care | HYPESOLE</title>@vite(['resources/css/app.css','resources/js/app.js'])</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
<header class="bg-slate-950 px-4 py-4 text-white"><div class="mx-auto flex max-w-4xl items-center justify-between"><a href="{{ route('home') }}" class="font-black">HYPESOLE</a><a href="{{ route('customer.dashboard') }}" class="text-sm font-bold">Dashboard</a></div></header>
<main class="mx-auto max-w-4xl px-4 py-8">
<p class="text-xs font-black tracking-widest text-teal-700">CUSTOMER BOOKING</p><h1 class="mt-2 text-3xl font-black">Booking Shoe Care</h1><p class="mt-2 text-sm text-slate-600">Isi detail sepatu yang ingin dirawat. Harga di bawah merupakan harga awal; workshop akan memeriksa kondisi dan mengirim penawaran final untuk kamu setujui.</p>
@if($errors->any())<div class="mt-5 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-800"><ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form method="POST" action="{{ route('customer.booking.store') }}" class="mt-6 space-y-5">@csrf
<div id="shoe-items" class="space-y-4">
<section class="shoe-item rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
<div class="mb-4 flex items-center justify-between"><h2 class="font-black">Sepatu <span class="item-number">1</span></h2><button type="button" class="remove-item hidden text-sm font-bold text-red-600">Hapus</button></div>
<label class="block text-sm font-bold">Pilih layanan <select name="items[0][service_id]" required class="mt-1 min-h-12 w-full rounded-xl border border-slate-300 px-3">@foreach($services as $service)<option value="{{ $service->id }}">{{ $service->name }} — Rp{{ number_format((float)$service->price,0,',','.') }}</option>@endforeach</select></label>
<div class="mt-3 grid gap-3 sm:grid-cols-2"><label class="text-sm font-bold">Brand<input name="items[0][shoe_brand]" maxlength="100" class="mt-1 min-h-11 w-full rounded-xl border border-slate-300 px-3"></label><label class="text-sm font-bold">Model<input name="items[0][shoe_model]" maxlength="150" class="mt-1 min-h-11 w-full rounded-xl border border-slate-300 px-3"></label><label class="text-sm font-bold">Warna<input name="items[0][shoe_color]" maxlength="80" class="mt-1 min-h-11 w-full rounded-xl border border-slate-300 px-3"></label><label class="text-sm font-bold">Ukuran<input name="items[0][shoe_size]" maxlength="30" class="mt-1 min-h-11 w-full rounded-xl border border-slate-300 px-3"></label></div>
<label class="mt-3 block text-sm font-bold">Keluhan / kondisi sepatu<textarea name="items[0][issue_description]" required minlength="5" maxlength="3000" rows="3" class="mt-1 w-full rounded-xl border border-slate-300 p-3" placeholder="Contoh: bagian midsole menguning dan terdapat noda di upper..."></textarea></label>
</section>
</div>
<button type="button" id="add-shoe" class="min-h-11 rounded-xl border border-teal-700 px-4 text-sm font-black text-teal-800">+ TAMBAH SEPatu (MAKS. 10)</button>
<label class="block text-sm font-bold">Catatan tambahan (opsional)<textarea name="customer_notes" maxlength="2000" rows="3" class="mt-1 w-full rounded-xl border border-slate-300 p-3"></textarea></label>
<div class="rounded-2xl bg-amber-50 p-4 text-sm text-amber-900">Dengan mengirim booking, kamu belum menyetujui harga final. Pengerjaan baru dapat dilanjutkan setelah kamu menyetujui penawaran dari workshop.</div>
<div class="flex flex-col gap-3 sm:flex-row"><button class="min-h-12 rounded-xl bg-teal-800 px-6 font-black text-white">KIRIM BOOKING</button><a href="{{ route('customer.orders.index') }}" class="inline-flex min-h-12 items-center justify-center rounded-xl border border-slate-300 px-6 font-bold">Kembali</a></div>
</form></main>
<script>
(() => {
 const wrap=document.getElementById('shoe-items'), add=document.getElementById('add-shoe');
 const template=wrap.firstElementChild.cloneNode(true);
 function renumber(){[...wrap.children].forEach((card,i)=>{card.querySelector('.item-number').textContent=i+1;card.querySelector('.remove-item').classList.toggle('hidden',i===0);card.querySelectorAll('[name]').forEach(el=>el.name=el.name.replace(/items\[\d+\]/,`items[${i}]`));});add.disabled=wrap.children.length>=10;add.classList.toggle('opacity-50',add.disabled);}
 add.addEventListener('click',()=>{if(wrap.children.length>=10)return;const card=template.cloneNode(true);card.querySelectorAll('input,textarea').forEach(el=>el.value='');wrap.appendChild(card);renumber();});
 wrap.addEventListener('click',e=>{if(e.target.closest('.remove-item')){e.target.closest('.shoe-item').remove();renumber();}});
 renumber();
})();
</script></body></html>
