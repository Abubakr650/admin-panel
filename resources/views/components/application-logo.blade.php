{{-- تيم معدل بتيلويند - Tailwind Dynamic Theme Logo --}}
<style>
    @keyframes logoPulse {
        0%, 100% { transform: scale(1); box-shadow: 0 4px 6px -1px var(--theme-shadow); }
        50%      { transform: scale(1.12); box-shadow: 0 8px 15px -3px var(--theme-shadow); }
    }
    .logo-pulse { animation: logoPulse 2.5s ease-in-out infinite; }
</style>
<div {{ $attributes->merge(['class' => 'logo-pulse flex items-center justify-center rounded-xl bg-gradient-to-br from-theme-from to-theme-to shadow-md shadow-theme-shadow/30']) }}>
    <svg class="w-3/5 h-3/5 text-white" viewBox="0 0 24 24" fill="currentColor">
        <path d="M12 2C9.5 2 7.5 3.5 6 5c-1 1-2 1.5-3 1.5S1 6 1 6s.5 2 1 3.5c.7 2 1.5 3.5 2.5 4.5.5.5 1 3 1.5 5.5.3 1.5.7 2.5 1.5 2.5s1-.5 1.5-2c.3-1 .5-2 1-3 .3-.7.7-1 1-1s.7.3 1 1c.5 1 .7 2 1 3 .5 1.5.7 2 1.5 2s1.2-1 1.5-2.5c.5-2.5 1-5 1.5-5.5 1-1 1.8-2.5 2.5-4.5.5-1.5 1-3.5 1-3.5s-1 .5-2 .5-2-.5-3-1.5C16.5 3.5 14.5 2 12 2z"/>
    </svg>
</div>