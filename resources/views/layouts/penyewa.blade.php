<!DOCTYPE html>
<html lang="id" class="h-full bg-[#F4F7F5]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Dashboard — SportPlace' }}</title>
    <meta name="description" content="{{ $description ?? 'Pantau jadwal sewa lapangan dan kelola aktivitas olahragamu di SportPlace.' }}">

    <!-- Google Fonts Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css'])
</head>
<body class="h-full font-sans text-slate-800 antialiased bg-[#F4F7F5] flex flex-col min-h-screen">

    <!-- ========== TOP NAVBAR COMPONENT ========== -->
    <x-penyewa.navbar :active="$active ?? 'dashboard'" />

    <!-- ========== MAIN PAGE CONTENT ========== -->
    <main class="flex-1 max-w-screen-2xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="mb-5 rounded-xl bg-emerald-50 border border-emerald-200 p-4 text-sm text-emerald-800 flex items-center gap-3 shadow-xs">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('warning'))
            <div class="mb-5 rounded-xl bg-amber-50 border border-amber-200 p-4 text-sm text-amber-800 flex items-center gap-3 shadow-xs">
                <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <span>{{ session('warning') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-5 rounded-xl bg-rose-50 border border-rose-200 p-4 text-sm text-rose-800 flex items-center gap-3 shadow-xs">
                <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{ $slot }}
    </main>

    <!-- ========== JAVASCRIPT LOGIC ========== -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Mobile navigation menu toggle
            const mobileToggle = document.getElementById('mobile-nav-toggle');
            const mobileMenu   = document.getElementById('mobile-nav-menu');
            if (mobileToggle && mobileMenu) {
                mobileToggle.addEventListener('click', function () {
                    mobileMenu.classList.toggle('hidden');
                });
            }

            // User Profile Dropdown logic
            const profileBtn       = document.getElementById('user-profile-dropdown-btn');
            const profileMenu      = document.getElementById('user-profile-menu');
            const profileContainer = document.getElementById('user-profile-dropdown-container');
            const profileChevron   = document.getElementById('profile-chevron');

            function toggleProfileDropdown() {
                if (!profileMenu) return;
                const isClosed = profileMenu.classList.contains('hidden');
                if (isClosed) {
                    profileMenu.classList.remove('hidden');
                    profileBtn.setAttribute('aria-expanded', 'true');
                    if (profileChevron) profileChevron.classList.add('rotate-180');
                } else {
                    profileMenu.classList.add('hidden');
                    profileBtn.setAttribute('aria-expanded', 'false');
                    if (profileChevron) profileChevron.classList.remove('rotate-180');
                }
            }

            function closeProfileDropdown() {
                if (profileMenu && !profileMenu.classList.contains('hidden')) {
                    profileMenu.classList.add('hidden');
                    profileBtn.setAttribute('aria-expanded', 'false');
                    if (profileChevron) profileChevron.classList.remove('rotate-180');
                }
            }

            if (profileBtn && profileMenu) {
                profileBtn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    toggleProfileDropdown();
                });

                document.addEventListener('click', function (e) {
                    if (profileContainer && !profileContainer.contains(e.target)) {
                        closeProfileDropdown();
                    }
                });

                document.addEventListener('keydown', function (e) {
                    if (e.key === 'Escape') {
                        closeProfileDropdown();
                    }
                });
            }
        });
    </script>
</body>
</html>
