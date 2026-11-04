<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Halaman Tidak Ditemukan - Nautivance</title>
    {{-- Samakan dengan yang dipakai layout user-mu --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-[#10243a] antialiased">
    <main class="mx-auto flex min-h-screen max-w-2xl flex-col items-center justify-center px-4 py-16 text-center">
        <p class="font-serif text-8xl font-bold text-[#0a3768] md:text-9xl">4<span class="text-[#d7a72b]">0</span>4</p>

        <h1 class="mt-4 font-serif text-2xl font-bold md:text-3xl">Kapal ini salah arah</h1>
        <p class="mt-3 max-w-md text-slate-500">
            Halaman yang kamu cari tidak ada, sudah dipindahkan, atau alamatnya salah ketik.
            Coba cari artikelnya, atau kembali ke pelabuhan awal.
        </p>
        <a href="{{ url('/') }}" class="mt-10 inline-block rounded-lg bg-[#d7a72b] px-6 py-3 font-semibold text-[#10243a] transition hover:brightness-110">
            Kembali ke Beranda
        </a>
    </main>
</body>
</html>