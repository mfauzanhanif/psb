<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'PSB Dar Al Tauhid' }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/Logo Pondok web.ico">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Vite Assets (CSS & JS) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>

<body class="bg-gray-50 flex flex-col min-h-screen text-dat-text">

    <!-- Navbar - Sticky with Mobile Menu -->
    <nav x-data="{ open: false }"
        class="sticky top-0 z-50 bg-white/95 backdrop-blur border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="/" class="flex-shrink-0 flex items-center">
                        <img src="/Logo Pondok web.png" alt="Logo Dar Al Tauhid" class="h-10 w-auto mr-3">
                        <span class="font-bold text-lg md:text-xl text-dat-text hidden sm:block">PSB Dar Al
                            Tauhid</span>
                        <span class="font-bold text-lg text-dat-text sm:hidden">PSB DAR AL TAUHID</span>
                    </a>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-1">
                    <a href="/"
                        class="text-dat-text hover:text-dat-primary font-medium px-3 py-2 rounded-md text-sm transition">Beranda</a>
                    <a href="/#alur"
                        class="text-dat-text hover:text-dat-primary font-medium px-3 py-2 rounded-md text-sm transition">Alur</a>
                    <a href="/#persyaratan"
                        class="text-dat-text hover:text-dat-primary font-medium px-3 py-2 rounded-md text-sm transition">Persyaratan</a>
                    <a href="/#biaya"
                        class="text-dat-text hover:text-dat-primary font-medium px-3 py-2 rounded-md text-sm transition">Biaya</a>
                    <a href="/cek-status"
                        class="text-dat-text hover:text-dat-primary font-medium px-3 py-2 rounded-md text-sm transition">Cek
                        Status</a>
                    <a href="/daftar"
                        class="bg-dat-primary hover:bg-dat-secondary text-white font-medium px-4 py-2 rounded-md text-sm transition shadow-md ml-2">Daftar
                        Sekarang</a>
                </div>

                <!-- Mobile Hamburger Button -->
                <div class="flex items-center md:hidden">
                    <button @click="open = !open" type="button"
                        class="inline-flex items-center justify-center p-2 rounded-md text-dat-text hover:text-dat-primary hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-dat-primary transition"
                        aria-controls="mobile-menu" :aria-expanded="open">
                        <span class="sr-only">Open main menu</span>
                        <!-- Icon when menu is closed -->
                        <svg x-show="!open" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <!-- Icon when menu is open -->
                        <svg x-show="open" x-cloak class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Dropdown -->
        <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-1" class="md:hidden bg-white border-t border-gray-100"
            id="mobile-menu">
            <div class="px-4 pt-2 pb-4 space-y-1">
                <a href="/" @click="open = false"
                    class="block px-3 py-3 rounded-lg text-base font-medium text-dat-text hover:text-dat-primary hover:bg-green-50 transition">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Beranda
                </a>
                <a href="/#alur" @click="open = false"
                    class="block px-3 py-3 rounded-lg text-base font-medium text-dat-text hover:text-dat-primary hover:bg-green-50 transition">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    Alur Pendaftaran
                </a>
                <a href="/#persyaratan" @click="open = false"
                    class="block px-3 py-3 rounded-lg text-base font-medium text-dat-text hover:text-dat-primary hover:bg-green-50 transition">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Persyaratan
                </a>
                <a href="/#biaya" @click="open = false"
                    class="block px-3 py-3 rounded-lg text-base font-medium text-dat-text hover:text-dat-primary hover:bg-green-50 transition">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Biaya
                </a>
                <a href="/cek-status" @click="open = false"
                    class="block px-3 py-3 rounded-lg text-base font-medium text-dat-text hover:text-dat-primary hover:bg-green-50 transition">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Cek Status
                </a>
                <a href="/daftar" @click="open = false"
                    class="block px-3 py-3 rounded-lg text-base font-medium text-white bg-dat-primary hover:bg-dat-secondary transition text-center mt-2 shadow">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Daftar Sekarang
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-gradient-to-r from-dat-primary to-dat-secondary text-white mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div
                    class="flex flex-col sm:flex-row items-center sm:items-start space-y-3 sm:space-y-0 sm:space-x-3 text-center sm:text-left">
                    <img src="/Logo Pondok web.png" alt="Logo" class="h-12 w-auto">
                    <div>
                        <h3 class="text-lg font-bold mb-2">Pondok Pesantren</h3>
                        <h3 class="text-lg font-bold mb-2">Dar Al Tauhid</h3>
                    </div>
                </div>
                <div class="text-center md:text-left">
                    <h3 class="text-lg font-bold mb-4">Kontak Kami</h3>
                    <ul class="text-green-100 text-sm space-y-3">
                        <li class="flex items-start justify-center md:justify-start">
                            <svg class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>Jl. KH. A. Syathori, RT/RW 06/02, Desa Arjawinangun, Kec. Arjawinangun, Kab. Cirebon,
                                Jawa Barat - 45162</span>
                        </li>
                        <li class="flex items-center justify-center md:justify-start">
                            <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                            </svg>
                            <a href="https://wa.me/6285624568440" target="_blank"
                                class="hover:text-white transition">085624568440</a>
                        </li>
                        <li class="flex items-center justify-center md:justify-start">
                            <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <a href="mailto:psb@daraltauhid.com"
                                class="hover:text-white transition">psb@daraltauhid.com</a>
                        </li>
                    </ul>
                </div>
                <div class="text-center md:text-left">
                    <h3 class="text-lg font-bold mb-4">Tautan</h3>
                    <ul class="text-green-100 text-sm space-y-2">
                        <li><a href="https://daraltauhid.com" target="_blank"
                                class="hover:text-white transition">Tentang Kami</a></li>
                        <li><a href="/#persyaratan" class="hover:text-white transition">Berkas Persyaratan</a>
                        </li>
                        <li><a href="/#biaya" class="hover:text-white transition">Rincian Biaya</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-green-600/50 mt-8 pt-8 text-center text-green-200 text-sm">
                &copy; {{ date('Y') }} Yayasan Dar Al Tauhid. All rights reserved.
            </div>
        </div>
    </footer>

    {{-- x-cloak style sudah dipindahkan ke app.css --}}

    @livewireScripts
</body>

</html>