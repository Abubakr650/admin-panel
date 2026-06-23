<tr>
    <td colspan="{{ $colspan ?? 1 }}" class="px-6 py-16 text-center">
        <div class="flex flex-col items-center justify-center max-w-md mx-auto">
            {{-- Icon Container with Gray Background --}}
            <div class="relative mb-4">
                <div class="absolute inset-0 bg-gray-200 blur-2xl rounded-full translate-y-2"></div>
                <div class="relative flex items-center justify-center w-20 h-20 rounded-[2rem] bg-gray-50 border border-gray-100 shadow-sm group transition-all duration-500 hover:scale-105">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-gray-300 transition-colors duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            
            {{-- Text Content --}}
            <h3 class="text-lg font-bold text-gray-400 tracking-tight">
                {{ $slot }}
            </h3>
            <p class="mt-1 text-sm text-gray-300 font-medium">
                No results to display at this time.
            </p>
        </div>
    </td>
</tr>