<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div><p class="text-xs text-gray-400 uppercase tracking-wider font-medium">Billing › Currencies</p><h1 class="text-xl font-bold text-gray-800">{{ $currency->name }}</h1></div>
            <div class="flex items-center gap-2">
                <a href="{{ route('currencies.edit', $currency) }}" class="px-4 py-2 bg-amber-500 text-white text-sm font-medium rounded-lg hover:bg-amber-600 transition">Edit</a>
                <form action="{{ route('currencies.destroy', $currency) }}" method="POST" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-500 text-white text-sm font-medium rounded-lg hover:bg-red-600 transition">Delete</button></form>
            </div>
        </div>
    </x-slot>
    <div class="p-6 max-w-lg">
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="divide-y divide-gray-100">
                @foreach(['Name'=>$currency->name,'Code'=>$currency->code,'Created'=>$currency->created_at->format('Y-m-d H:i')] as $label=>$value)
                <div class="flex items-start px-5 py-3"><span class="w-36 text-sm font-medium text-gray-500 shrink-0">{{ $label }}</span><span class="text-sm text-gray-800">{{ $value }}</span></div>
                @endforeach
            </div>
        </div>
        <div class="mt-4"><a href="{{ route('currencies.index') }}" class="text-sm text-theme-from hover:opacity-80">← Back to Currencies</a></div>
    </div>
</x-app-layout>
