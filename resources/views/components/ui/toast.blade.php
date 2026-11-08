<div x-data="{
        toasts: [],
        add(event) {
            const id = Date.now() + Math.random();
            const toast = {
                id: id,
                message: event.detail?.message || 'Aksi berhasil',
                type: event.detail?.type || 'success',
                title: event.detail?.title || null
            };
            this.toasts.push(toast);
            setTimeout(() => {
                this.remove(id);
            }, 3800);
        },
        remove(id) {
            this.toasts = this.toasts.filter(t => t.id !== id);
        }
     }"
     @notify.window="add($event)"
     class="fixed bottom-20 md:bottom-6 right-4 sm:right-6 z-50 flex flex-col gap-2.5 max-w-sm w-full pointer-events-none"
     aria-live="polite">

    <template x-for="toast in toasts" :key="toast.id">
        <div x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 translate-y-3 sm:translate-y-0 sm:translate-x-4 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0 scale-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="pointer-events-auto p-4 rounded-2xl shadow-xl border flex items-start gap-3 backdrop-blur-md transition"
             :class="{
                'bg-emerald-950/90 text-white border-emerald-500/40': toast.type === 'success',
                'bg-rose-950/90 text-white border-rose-500/40': toast.type === 'error',
                'bg-amber-950/90 text-white border-amber-500/40': toast.type === 'warning',
                'bg-slate-900/90 text-white border-slate-700': toast.type === 'info'
             }">

            <div class="shrink-0 mt-0.5 text-base">
                <i class="fas"
                   :class="{
                       'fa-circle-check text-emerald-400': toast.type === 'success',
                       'fa-circle-xmark text-rose-400': toast.type === 'error',
                       'fa-triangle-exclamation text-amber-400': toast.type === 'warning',
                       'fa-circle-info text-sky-400': toast.type === 'info'
                   }"></i>
            </div>

            <div class="flex-1 min-w-0">
                <template x-if="toast.title">
                    <h5 class="font-heading font-bold text-xs uppercase tracking-wide text-white/90" x-text="toast.title"></h5>
                </template>
                <p class="text-xs font-semibold leading-relaxed" x-text="toast.message"></p>
            </div>

            <button type="button" @click="remove(toast.id)" class="text-white/60 hover:text-white p-1 rounded-lg transition shrink-0">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>
    </template>
</div>
