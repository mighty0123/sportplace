@props([
    'id' => 'confirm-modal',
    'title' => 'Konfirmasi Tindakan',
    'message' => 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
    'confirmText' => 'Ya, Lanjutkan',
    'cancelText' => 'Batal',
    'isDanger' => false,
])

<dialog id="{{ $id }}" class="backdrop:bg-slate-900/60 rounded-xl p-0 shadow-2xl max-w-md w-full border border-slate-200 open:animate-in open:fade-in-0">
    <div class="p-6">
        <h3 class="text-lg font-semibold text-slate-900">{{ $title }}</h3>
        <p class="mt-2 text-sm text-slate-600 leading-relaxed">{{ $message }}</p>

        {{ $slot }}

        <div class="mt-6 flex justify-end gap-3">
            <button type="button" onclick="document.getElementById('{{ $id }}').close()" class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition min-h-[44px]">
                {{ $cancelText }}
            </button>
            <button type="button" id="{{ $id }}-confirm-btn" class="px-4 py-2 text-sm font-semibold rounded-lg text-white transition min-h-[44px] {{ $isDanger ? 'bg-rose-600 hover:bg-rose-700' : 'bg-blue-600 hover:bg-blue-700' }}">
                {{ $confirmText }}
            </button>
        </div>
    </div>
</dialog>

