<!-- Hero Section -->
<div class="relative bg-dat-primary overflow-hidden">
    <div
        class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\" 60\" height=\"60\" viewBox=\"0 0 60
        60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg
        fill=\"%23ffffff\" fill-opacity=\"0.05\"%3E%3Cpath d=\"M36
        34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6
        4V0H4v4H0v2h4v4h2V6h4V4H6z\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-30">
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-center">
            <!-- Text Content -->
            <div class="text-center lg:text-left order-2 lg:order-1 animate-fade-in">
                <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-5xl font-extrabold text-white leading-tight">
                    Selamat Datang di<br>
                    <span class="text-green-200">Pondok Pesantren</span><br>
                    Dar Al Tauhid
                </h1>
                <p class="mt-6 text-xl md:text-2xl text-green-100 font-medium">
                    Pendaftaran Santri Baru Tahun Ajaran 2026/2027 telah dibuka!
                </p>
                <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                    <a href="/daftar"
                        class="inline-flex items-center justify-center px-8 py-4 border-2 border-white text-lg font-semibold rounded-lg text-dat-primary bg-white hover:bg-green-50 transition shadow-lg transform hover:-translate-y-1">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Daftar Sekarang
                    </a>
                    <a href="/cek-status"
                        class="inline-flex items-center justify-center px-8 py-4 border-2 border-white/50 text-lg font-semibold rounded-lg text-white hover:bg-white/10 transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Cek Status
                    </a>
                </div>
            </div>

            <!-- Hero Image -->
            <div class="order-1 lg:order-2 flex justify-center lg:justify-end animate-fade-in-delay">
                <div class="relative">
                    <div class="absolute -inset-4 bg-white/10 rounded-full blur-2xl"></div>
                    <img src="{{ asset('FOTOSANTRI.png') }}" alt="Santri Dar Al Tauhid"
                        class="relative w-64 h-auto sm:w-72 md:w-80 lg:w-96 object-contain drop-shadow-2xl">
                </div>
            </div>
        </div>
    </div>

    <!-- Wave Decoration -->
    <div class="absolute bottom-0 left-0 right-0">
        <svg class="w-full h-12 md:h-16" viewBox="0 0 1440 54" fill="none" xmlns="http://www.w3.org/2000/svg"
            preserveAspectRatio="none">
            <path
                d="M0 22L60 27C120 32 240 42 360 47C480 52 600 52 720 44C840 36 960 22 1080 17C1200 12 1320 17 1380 19L1440 22V54H1380C1320 54 1200 54 1080 54C960 54 840 54 720 54C600 54 480 54 360 54C240 54 120 54 60 54H0V22Z"
                fill="#fefce8" />
        </svg>
    </div>
</div>

<!-- Info Banner: Pendaftaran Offline & Brosur -->
<section class="bg-yellow-50 border-b border-yellow-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
        <div class="flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-start md:items-center text-center md:text-left">
                <div
                    class="hidden md:flex w-12 h-12 bg-yellow-100 rounded-full items-center justify-center mr-4 flex-shrink-0">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                        </path>
                    </svg>
                </div>
                <div>
                    <p class="text-yellow-800 font-medium">Melayani Pendaftaran Offline di Kantor Pondok Pesantren
                    </p>
                    <p class="text-yellow-700 text-sm">Jam Operasional: 08.00 s.d. 15.00 WIB (Setiap Hari)</p>
                </div>
            </div>
            <a href="https://drive.google.com/drive/folders/1ImBD_zIfcYtWkI-WCfUt5wl3bl6xo0QR?usp=drive_link"
                target="_blank"
                class="inline-flex items-center px-5 py-2.5 bg-yellow-500 hover:bg-yellow-600 text-white font-medium rounded-lg transition shadow-sm flex-shrink-0">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                Download Brosur
            </a>
        </div>
    </div>
</section>
