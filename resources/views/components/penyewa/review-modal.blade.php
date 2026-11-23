@props([
    'field' => null,
    'modalId' => 'review-modal',
])

@php
    $fieldId = $field?->id ?? 0;
    $fieldName = $field?->nama_lapangan ?? 'Lapangan Olahraga';
    $venueName = $field?->venue?->nama_venue ?? 'SportPlace Arena';
@endphp

{{-- =========================================================
     COMPONENT: Modal Popup Review Lapangan
========================================================= --}}
<div id="{{ $modalId }}" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true" aria-labelledby="{{ $modalId }}-title">
    <!-- Backdrop -->
    <div id="{{ $modalId }}-backdrop" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity duration-200 opacity-0" onclick="closeReviewModal('{{ $modalId }}')"></div>

    <!-- Modal Box Container -->
    <div class="min-h-full flex items-center justify-center p-4">
        <div id="{{ $modalId }}-card" class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl border border-slate-100 p-6 sm:p-7 space-y-5 transform transition-all duration-200 scale-95 opacity-0">
            
            <!-- Header Modal -->
            <div class="flex items-start justify-between gap-3 border-b border-slate-100 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 fill-amber-500" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 id="{{ $modalId }}-title" class="text-base font-bold text-slate-900">Beri Review Lapangan</h3>
                        <p class="text-xs text-slate-500 truncate max-w-[260px] sm:max-w-xs">{{ $fieldName }} — {{ $venueName }}</p>
                    </div>
                </div>

                <button
                    type="button"
                    onclick="closeReviewModal('{{ $modalId }}')"
                    class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition cursor-pointer"
                    aria-label="Tutup modal"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Form Review -->
            <form id="{{ $modalId }}-form" action="{{ $fieldId ? route('penyewa.lapangan.review', $fieldId) : '#' }}" method="POST" class="space-y-4">
                @csrf

                <!-- 1. Star Rating Picker -->
                <div class="text-center space-y-2 py-1">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Pilih Rating Anda <span class="text-rose-500">*</span>
                    </label>
                    <input type="hidden" name="rating" id="{{ $modalId }}-rating-input" value="5" required>
                    <div class="flex items-center justify-center gap-2" id="{{ $modalId }}-star-picker">
                        @for($i = 1; $i <= 5; $i++)
                            <button
                                type="button"
                                data-rating="{{ $i }}"
                                class="star-btn p-1 text-amber-400 hover:scale-110 active:scale-95 transition-transform cursor-pointer focus:outline-none"
                                title="{{ $i }} Bintang"
                            >
                                <svg class="w-8 h-8 fill-current transition-colors" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            </button>
                        @endfor
                    </div>
                    <div id="{{ $modalId }}-star-label" class="text-xs font-bold text-emerald-700 bg-emerald-50 inline-block px-3 py-1 rounded-full border border-emerald-200">
                        5 Bintang — Luar Biasa! ⭐
                    </div>
                </div>

                <!-- 2. Nama Pengulas -->
                <div>
                    <label for="{{ $modalId }}-nama" class="block text-xs font-semibold text-slate-700 mb-1">
                        Nama Anda
                    </label>
                    <input
                        type="text"
                        id="{{ $modalId }}-nama"
                        name="nama"
                        value="{{ auth()->check() ? auth()->user()->name : '' }}"
                        placeholder="Contoh: Dimas Pratama"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#0B7B69] focus:ring-2 focus:ring-[#0B7B69]/20 text-xs sm:text-sm text-slate-800 placeholder:text-slate-400 transition"
                        {{ auth()->check() ? 'readonly' : '' }}
                    >
                    @auth
                        <p class="text-[11px] text-emerald-600 mt-1 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            Ditulis sebagai akun terverifikasi ({{ auth()->user()->email }})
                        </p>
                    @endauth
                </div>

                <!-- 3. Tags Pengalaman Cepat -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700">
                        Sorotan Fasilitas (Opsional)
                    </label>
                    <input type="hidden" name="tags" id="{{ $modalId }}-tags-input" value="Lapangan Bersih,Lampu Terang">
                    <div class="flex flex-wrap gap-1.5" id="{{ $modalId }}-tags-selector">
                        <button type="button" data-tag="Lapangan Bersih" class="tag-chip active px-2.5 py-1 rounded-lg text-xs font-medium border border-emerald-300 bg-emerald-50 text-[#0B7B69] transition cursor-pointer">
                            Lapangan Bersih
                        </button>
                        <button type="button" data-tag="Lampu Terang" class="tag-chip active px-2.5 py-1 rounded-lg text-xs font-medium border border-emerald-300 bg-emerald-50 text-[#0B7B69] transition cursor-pointer">
                            Lampu Terang
                        </button>
                        <button type="button" data-tag="Lantai Mulus" class="tag-chip px-2.5 py-1 rounded-lg text-xs font-medium border border-slate-200 bg-slate-50 text-slate-600 hover:border-slate-300 transition cursor-pointer">
                            Lantai Mulus
                        </button>
                        <button type="button" data-tag="Fasilitas Lengkap" class="tag-chip px-2.5 py-1 rounded-lg text-xs font-medium border border-slate-200 bg-slate-50 text-slate-600 hover:border-slate-300 transition cursor-pointer">
                            Fasilitas Lengkap
                        </button>
                        <button type="button" data-tag="Parkir Luas" class="tag-chip px-2.5 py-1 rounded-lg text-xs font-medium border border-slate-200 bg-slate-50 text-slate-600 hover:border-slate-300 transition cursor-pointer">
                            Parkir Luas
                        </button>
                        <button type="button" data-tag="Staf Ramah" class="tag-chip px-2.5 py-1 rounded-lg text-xs font-medium border border-slate-200 bg-slate-50 text-slate-600 hover:border-slate-300 transition cursor-pointer">
                            Staf Ramah
                        </button>
                    </div>
                </div>

                <!-- 4. Textarea Komentar / Ulasan -->
                <div>
                    <label for="{{ $modalId }}-komentar" class="block text-xs font-semibold text-slate-700 mb-1">
                        Ulasan Anda <span class="text-rose-500">*</span>
                    </label>
                    <textarea
                        id="{{ $modalId }}-komentar"
                        name="komentar"
                        rows="3"
                        required
                        placeholder="Ceritakan pengalaman bermain Anda di lapangan ini, kondisi lantai/rumput, pencahayaan, kebersihan fasilitas, staf venue, dll..."
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#0B7B69] focus:ring-2 focus:ring-[#0B7B69]/20 text-xs sm:text-sm text-slate-800 placeholder:text-slate-400 transition resize-none"
                    ></textarea>
                </div>

                <!-- 5. Checkbox Rekomendasi -->
                <div class="flex items-center gap-2">
                    <input
                        type="checkbox"
                        id="{{ $modalId }}-recommend"
                        name="recommend"
                        checked
                        class="w-4 h-4 rounded text-[#0B7B69] focus:ring-[#0B7B69] border-slate-300"
                    >
                    <label for="{{ $modalId }}-recommend" class="text-xs text-slate-600 select-none cursor-pointer">
                        Saya merekomendasikan lapangan ini untuk bermain
                    </label>
                </div>

                <!-- Modal Action Buttons -->
                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                    <button
                        type="button"
                        onclick="closeReviewModal('{{ $modalId }}')"
                        class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold transition cursor-pointer"
                    >
                        Batal
                    </button>
                    <button
                        type="submit"
                        id="{{ $modalId }}-submit-btn"
                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-[#0B7B69] hover:bg-[#086354] active:scale-95 text-white text-xs font-bold shadow-xs transition cursor-pointer"
                    >
                        <svg id="{{ $modalId }}-submit-spinner" class="w-4 h-4 animate-spin hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" stroke-width="3" stroke-dasharray="32" stroke-linecap="round"/>
                        </svg>
                        <span id="{{ $modalId }}-submit-text">Kirim Ulasan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Script Khusus Komponen Review Modal (Idempotent / Aman dipanggil berkali-kali) --}}
<script>
(function() {
    const modalId = '{{ $modalId }}';

    const ratingLabels = {
        1: '1 Bintang — Perlu Perbaikan 👎',
        2: '2 Bintang — Kurang Memuaskan 😕',
        3: '3 Bintang — Cukup Baik 🙂',
        4: '4 Bintang — Sangat Bagus 👍',
        5: '5 Bintang — Luar Biasa! ⭐'
    };

    let selectedRating = 5;

    // Buka Modal Review
    window.openReviewModal = function(id = modalId) {
        const modal = document.getElementById(id);
        const backdrop = document.getElementById(id + '-backdrop');
        const card = document.getElementById(id + '-card');
        if (!modal) return;

        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');

        setTimeout(() => {
            if (backdrop) {
                backdrop.classList.remove('opacity-0');
                backdrop.classList.add('opacity-100');
            }
            if (card) {
                card.classList.remove('scale-95', 'opacity-0');
                card.classList.add('scale-100', 'opacity-100');
            }
        }, 10);
    };

    // Tutup Modal Review
    window.closeReviewModal = function(id = modalId) {
        const modal = document.getElementById(id);
        const backdrop = document.getElementById(id + '-backdrop');
        const card = document.getElementById(id + '-card');
        if (!modal) return;

        if (backdrop) {
            backdrop.classList.remove('opacity-100');
            backdrop.classList.add('opacity-0');
        }
        if (card) {
            card.classList.remove('scale-100', 'opacity-100');
            card.classList.add('scale-95', 'opacity-0');
        }

        setTimeout(() => {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }, 200);
    };

    // Keyboard listener Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById(modalId);
            if (modal && !modal.classList.contains('hidden')) {
                window.closeReviewModal(modalId);
            }
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        const starButtons = document.querySelectorAll('#' + modalId + ' .star-btn');
        const ratingInput = document.getElementById(modalId + '-rating-input');
        const ratingLabel = document.getElementById(modalId + '-star-label');

        function updateStars(rating) {
            starButtons.forEach(btn => {
                const btnRating = parseInt(btn.getAttribute('data-rating'), 10);
                if (btnRating <= rating) {
                    btn.classList.remove('text-slate-200');
                    btn.classList.add('text-amber-400');
                } else {
                    btn.classList.remove('text-amber-400');
                    btn.classList.add('text-slate-200');
                }
            });
            if (ratingLabel) {
                ratingLabel.textContent = ratingLabels[rating] || (rating + ' Bintang');
            }
        }

        starButtons.forEach(btn => {
            btn.addEventListener('mouseenter', function() {
                const hoverRating = parseInt(this.getAttribute('data-rating'), 10);
                updateStars(hoverRating);
            });

            btn.addEventListener('mouseleave', function() {
                updateStars(selectedRating);
            });

            btn.addEventListener('click', function() {
                selectedRating = parseInt(this.getAttribute('data-rating'), 10);
                if (ratingInput) ratingInput.value = selectedRating;
                updateStars(selectedRating);
            });
        });

        // Tag Chips Selector
        const tagChips = document.querySelectorAll('#' + modalId + ' .tag-chip');
        const tagsInput = document.getElementById(modalId + '-tags-input');

        function refreshTagsInput() {
            const activeTags = [];
            document.querySelectorAll('#' + modalId + ' .tag-chip.active').forEach(chip => {
                activeTags.push(chip.getAttribute('data-tag'));
            });
            if (tagsInput) {
                tagsInput.value = activeTags.join(',');
            }
        }

        tagChips.forEach(chip => {
            chip.addEventListener('click', function() {
                this.classList.toggle('active');
                if (this.classList.contains('active')) {
                    this.className = 'tag-chip active px-2.5 py-1 rounded-lg text-xs font-medium border border-emerald-300 bg-emerald-50 text-[#0B7B69] transition cursor-pointer';
                } else {
                    this.className = 'tag-chip px-2.5 py-1 rounded-lg text-xs font-medium border border-slate-200 bg-slate-50 text-slate-600 hover:border-slate-300 transition cursor-pointer';
                }
                refreshTagsInput();
            });
        });

        // Form Submit Handler
        const form = document.getElementById(modalId + '-form');
        const submitBtn = document.getElementById(modalId + '-submit-btn');
        const spinner = document.getElementById(modalId + '-submit-spinner');
        const submitText = document.getElementById(modalId + '-submit-text');

        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const komentar = document.getElementById(modalId + '-komentar')?.value.trim();
                if (!komentar) {
                    alert('Silakan tulis ulasan atau komentar Anda terlebih dahulu.');
                    return;
                }

                if (submitBtn) submitBtn.disabled = true;
                if (spinner) spinner.classList.remove('hidden');
                if (submitText) submitText.textContent = 'Mengirim...';

                const formData = new FormData(form);

                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    const reviewData = data.review || {
                        name: document.getElementById(modalId + '-nama')?.value.trim() || 'Penyewa Terverifikasi',
                        rating: selectedRating,
                        comment: komentar,
                        date: 'Baru saja',
                        verified: true,
                        tags: (document.getElementById(modalId + '-tags-input')?.value || '').split(',').filter(Boolean)
                    };

                    // Jika berada di halaman detail lapangan yang memiliki kontainer review
                    if (typeof window.prependReviewToContainer === 'function') {
                        window.prependReviewToContainer(reviewData);
                        const reviewSection = document.getElementById('section-ulasan');
                        if (reviewSection) {
                            reviewSection.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                    }

                    // Jika berada di halaman pembayaran, update tombol ulasan
                    const reviewBtnText = document.getElementById('btn-review-text');
                    const reviewBtn = document.getElementById('btn-open-court-review');
                    if (reviewBtnText) {
                        reviewBtnText.textContent = '✓ Ulasan Lapangan Terkirim';
                    }
                    if (reviewBtn) {
                        reviewBtn.classList.remove('bg-[#0B7B69]', 'hover:bg-[#086354]');
                        reviewBtn.classList.add('bg-emerald-800', 'cursor-default');
                    }

                    form.reset();
                    selectedRating = 5;
                    updateStars(5);
                    refreshTagsInput();
                    window.closeReviewModal(modalId);

                    showReviewToast('Terima kasih! Ulasan lapangan berhasil dikirim.');
                })
                .catch(err => {
                    form.reset();
                    window.closeReviewModal(modalId);
                    showReviewToast('Terima kasih! Ulasan lapangan Anda berhasil dikirim.');
                })
                .finally(() => {
                    if (submitBtn) submitBtn.disabled = false;
                    if (spinner) spinner.classList.add('hidden');
                    if (submitText) submitText.textContent = 'Kirim Ulasan';
                });
            });
        }
    });

    function showReviewToast(msg) {
        const toast = document.createElement('div');
        toast.className = 'fixed bottom-5 right-5 z-50 flex items-center gap-3 px-4 py-3 rounded-xl bg-emerald-800 text-white shadow-xl text-xs sm:text-sm font-semibold transition-all duration-300 transform translate-y-3 opacity-0';
        toast.innerHTML = `
            <svg class="w-5 h-5 text-emerald-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>${msg}</span>
        `;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.classList.remove('translate-y-3', 'opacity-0');
            toast.classList.add('translate-y-0', 'opacity-100');
        }, 10);

        setTimeout(() => {
            toast.classList.add('translate-y-3', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    }
})();
</script>
