<div>
    @if ($visible)
        <div
            class="fixed bottom-5 left-4 right-4 z-[70] mx-auto max-w-md border border-sand bg-cream px-5 py-4 shadow-sm md:left-auto md:right-6"
            role="status"
            x-data
            x-init="setTimeout(() => $wire.dismiss(), 4000)"
        >
            <div class="flex items-center justify-between gap-4">
                <p class="text-sm">Added to cart</p>
                <div class="flex items-center gap-3">
                    <a href="{{ route('cart.index') }}" class="text-[11px] uppercase tracking-[0.16em] underline underline-offset-4">View cart</a>
                    <button type="button" wire:click="dismiss" class="text-xs uppercase tracking-widest text-muted" aria-label="Dismiss">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
