<span {{ $attributes }}>
    @if ($count > 0)
        <span
            class="absolute right-0.5 top-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-charcoal px-1 text-[10px] leading-none text-ivory lg:static lg:ml-1 lg:h-auto lg:min-w-0 lg:rounded-none lg:bg-transparent lg:px-0 lg:text-xs lg:text-charcoal"
        >
            <span class="sr-only">Items in cart:</span>
            {{ $count }}
        </span>
    @endif
</span>
