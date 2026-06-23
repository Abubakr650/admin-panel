أنت تعمل على مشروع Laravel يستخدم Blade + Alpine.js + Tailwind CSS.

══════════════════════════════════════════════════════
🎯 المطلوب تنفيذه (يُملأ قبل إرسال هذا البرومت)
══════════════════════════════════════════════════════

القسم (Section):        Pharmacy
الموديول (Module):      Suppliers
اسم الـ Model:          Supplier (App\Models\Pharmacy\Supplier)
اسم الـ Controller:     SupplierController
مسار الـ Views:         pharmacy/suppliers
اسم الـ Route prefix:   pharmacy.suppliers

حقول الجدول (Fields):
  - name: string + required
  - phone: string + nullable
  - email: string + nullable
  - address: text + nullable
  - country: string + nullable
  - notes: text + nullable

حقول تحتاج Dropdown Filter في Index:
  - لا يوجد حالياً (يمكن إضافة مدينة أو تصنيف لاحقاً)

علاقات (Relations):
  - hasMany PharmacyBatch
  - hasMany WarehouseItem

هل يحتاج Archive (SoftDeletes)؟ نعم

══════════════════════════════════════════════════════
🏗️ المرجع الصارم للمشروع
══════════════════════════════════════════════════════

المرجع الأساسي للتصميم هو أقسام Clinic:
  - PatientController   → app/Http/Controllers/Clinic/PatientController.php
  - AppointmentController → app/Http/Controllers/Clinic/AppointmentController.php
  - patients/index      → resources/views/clinic/patients/index.blade.php
  - patients/create     → resources/views/clinic/patients/create.blade.php
  - patients/show       → resources/views/clinic/patients/show.blade.php

Layout الوحيد المستخدم:
  <x-app-layout>
    <x-slot name="header"> ... </x-slot>
    {{ المحتوى }}
  </x-app-layout>

══════════════════════════════════════════════════════
🧩 Components المتاحة (يجب استخدامها بالضبط)
══════════════════════════════════════════════════════

<x-button-back href="{{ route('ROUTE.index') }}" label="MODULE" />
<x-button-link href="{{ route('ROUTE.create') }}"> <x-icon name="plus" class="w-4 h-4" /> New ITEM </x-button-link>
<x-button-archive>Archive</x-button-archive>     ← داخل form POST DELETE
<x-button-submit> <x-icon name="ICON" class="w-4 h-4 mr-1.5" /> LABEL </x-button-submit>
<x-button-cancel/>
<x-button-archive-view :archived="$isArchived" :indexRoute="..." :archiveRoute="..." :archivedCount="..." />
<x-search-bar action="{{ route('ROUTE.index') }}" placeholder="..." :extraParams="[...]">
    {{-- Dropdown filters داخل الـ slot --}}
</x-search-bar>
<x-table-actions :archived="$isArchived" :editRoute="..." :deleteRoute="..." :restoreRoute="..." :itemName="..." />
<x-message-empty colspan="N"> ... </x-message-empty>
<x-toast-notification />
<x-input-error :messages="$errors->get('FIELD')" class="mt-1.5" />
<x-icon name="ICON_NAME" class="w-4 h-4 text-theme-from" />
<x-card-link href="..." icon="ICON" title="TITLE" subtitle="SUB" color="theme-from" />
<x-scan-qr-modal name="scan-qr" mode="search/fill" :action="..." />

الأيقونات المتاحة في x-icon:
  user, user-add, user-off, users, gender-male(filled), gender-female(filled),
  calendar, phone, location, mail, plus, x, chevron-down, clock, search,
  edit, document, filter, stethoscope, academic-cap, clipboard, info,
  check-circle, x-circle, exclamation, badge-check, arrow-right, arrow-up,
  arrow-down, qr-code, truck, library, cash, credit-card

══════════════════════════════════════════════════════
📐 هيكل صفحة index (النمط الكامل)
══════════════════════════════════════════════════════

<x-app-layout>
@php $isArchived = request()->input('archived') === 'true'; @endphp
    <x-slot name="header">
        <div class="flex items-center justify-between w-full gap-4">
            <div class="flex items-center gap-3 flex-1 min-w-0">
                <div class="shrink-0">
                    <p class="text-xs text-gray-400 uppercase tracking-widest font-semibold">SECTION</p>
                    <h1 class="text-lg font-bold text-gray-800 leading-tight">
                        {{ $isArchived ? 'Archived ITEMS' : 'ITEMS' }}
                    </h1>
                </div>
                <div class="h-8 w-px bg-gray-200 shrink-0"></div>

                {{-- Search + Dropdown Filters داخل x-search-bar --}}
                <div class="w-64">
                    <x-search-bar
                        action="{{ route('ROUTE.index') }}"
                        placeholder="Search by ..."
                        :extraParams="array_filter(['archived' => request('archived'), 'FILTER_FIELD' => request('FILTER_FIELD')])"
                    >
                        {{-- Dropdown Filter (للحقول التي تحتاج فلتر) --}}
                        @if($filterOptions->isNotEmpty())
                        <div class="flex items-center gap-2 flex-nowrap">
                            <select name="FILTER_FIELD" @change="$refs.form.submit()"
                                class="block py-2 pl-3 pr-8 border border-gray-200 rounded-xl text-sm focus:ring-theme-from focus:border-theme-from font-medium bg-white flex-shrink-0 min-w-[120px]"
                                style="background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\' stroke=\'%236b7280\'%3E%3Cpath stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M19 9l-7 7-7-7\'/%3E%3C/svg%3E'); background-position: right 8px center; background-size: 14px; -webkit-appearance: none; appearance: none;">
                                <option value="">All FILTER_LABEL</option>
                                @foreach($filterOptions as $opt)
                                    <option value="{{ $opt }}" {{ request('FILTER_FIELD') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                    </x-search-bar>
                </div>
            </div>

            <div class="flex items-center gap-3 shrink-0">
                @unless($isArchived)
                    <x-button-link href="{{ route('ROUTE.create') }}">
                        <x-icon name="plus" class="w-4 h-4" /> New ITEM
                    </x-button-link>
                @endunless
                <x-button-archive-view
                    :archived="$isArchived"
                    :indexRoute="route('ROUTE.index')"
                    :archiveRoute="route('ROUTE.index', ['archived' => 'true'])"
                    :archivedCount="$archivedCount ?? 0"
                />
            </div>
        </div>
    </x-slot>

    <div class="space-y-4">
        <x-toast-notification />

        {{-- Stats Row --}}
        @if(!$isArchived)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-card-link 
                icon="truck" 
                title="{{ $totalCount }}" 
                label="Total Suppliers" 
                color="theme-from" 
            />
            <x-card-link 
                icon="document" 
                title="{{ $archivedCount }}" 
                label="Archived" 
                color="amber" 
                href="{{ route('pharmacy.suppliers.index', ['archived' => 'true']) }}"
            />
        </div>
        @endif

        {{-- Table --}}
        <div class="rounded-2xl bg-white border border-gray-100 shadow-sm overflow-hidden">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50/70">
                        <th class="rounded-l-lg px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">COL1</th>
                        @if($isArchived)
                            <th class="px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Archive Date</th>
                        @endif
                        <th class="rounded-l-lg px-6 py-3 bg-gray-50 text-right text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($items as $item)
                        <tr class="group hover:bg-theme-from/10 transition-colors duration-150">
                            <td class="px-5 py-3.5 font-semibold text-gray-300">{{ $loop->iteration }}</td>
                            <td class="px-5 py-3.5">
                                @if($isArchived)
                                    <p class="font-semibold text-gray-700">{{ $item->FIELD }}</p>
                                @else
                                    <a href="{{ route('ROUTE.show', $item->id) }}"
                                       class="font-semibold text-gray-800 hover:text-theme-from transition-colors duration-150">
                                        {{ $item->FIELD }}
                                    </a>
                                @endif
                            </td>
                            {{-- Badge للحقول ذات القيم المحدودة --}}
                            <td class="px-5 py-3.5">
                                @if($item->STATUS_FIELD)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-theme-from/10 text-theme-from border border-theme-from/20">
                                        {{ $item->STATUS_FIELD }}
                                    </span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            @if($isArchived)
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-1.5 text-gray-500">
                                        <x-icon name="calendar" class="w-3.5 h-3.5 text-amber-500" />
                                        {{ $item->deleted_at->format('M d, Y') }}
                                    </div>
                                </td>
                            @endif
                            <x-table-actions
                                :archived="$isArchived"
                                :editRoute="route('ROUTE.edit', $item->id)"
                                :deleteRoute="route('ROUTE.destroy', $item->id)"
                                :restoreRoute="route('ROUTE.restore', $item->id)"
                                :itemName="$item->MAIN_FIELD"
                            />
                        </tr>
                    @empty
                        <x-message-empty colspan="{{ $isArchived ? N+1 : N }}">
                            {{ $isArchived ? 'No archived ITEMS found' : 'No ITEMS found' }}
                        </x-message-empty>
                    @endforelse
                </tbody>
            </table>
            @if($items->hasPages())
                <div class="px-5 py-3 border-t border-gray-100 bg-gray-50/50">
                    {{ $items->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

══════════════════════════════════════════════════════
📐 هيكل صفحة create / edit (النمط الكامل)
══════════════════════════════════════════════════════

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wider font-medium">SECTION › ITEMS</p>
                <h1 class="text-xl font-bold text-gray-800">New ITEM / Edit ITEM</h1>
            </div>
        </div>
    </x-slot>

    <div class="p-6">
        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">

                {{-- Card Header --}}
                <div class="bg-gradient-to-r from-theme-from to-theme-to px-8 py-6">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center shadow-inner">
                            <x-icon name="ICON" class="w-7 h-7 text-white" />
                        </div>
                        <div>
                            <h2 class="text-white text-xl font-bold">TITLE</h2>
                            <p class="text-sky-100 text-sm mt-0.5">SUBTITLE</p>
                        </div>
                    </div>
                </div>

                {{-- Card Body --}}
                <div class="px-8 py-7">
                    @php
                        $inputClass = fn(string $field) =>
                            'w-full px-4 py-2.5 rounded-xl border text-gray-800 text-sm placeholder-gray-400 ' .
                            'transition-all duration-200 ' .
                            'focus:outline-none focus:ring-2 focus:ring-theme-from/40 focus:border-theme-from ' .
                            ($errors->has($field)
                                ? 'border-red-400 bg-red-50 ring-1 ring-red-300'
                                : 'border-gray-200 bg-gray-50 hover:border-theme-from');
                    @endphp

                    {{-- CREATE: action="store", بدون @method --}}
                    {{-- EDIT:   action="update", أضف @method('PUT') --}}
                    <form action="{{ route('ROUTE.store') }}" method="POST" class="space-y-5">
                        @csrf
                        {{-- في Edit أضف: @method('PUT') --}}
                        <input type="hidden" name="idempotency_key" value="{{ Str::uuid() }}">

                        {{-- Text Input --}}
                        <div class="group">
                            <label for="FIELD" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                <span class="flex items-center gap-1.5">
                                    <x-icon name="ICON" class="w-4 h-4 text-theme-from" />
                                    LABEL <span class="text-red-400">*</span>
                                </span>
                            </label>
                            {{-- CREATE:  value="{{ old('FIELD') }}" --}}
                            {{-- EDIT:    value="{{ old('FIELD', $item->FIELD) }}" --}}
                            <input type="text" id="FIELD" name="FIELD"
                                value="{{ old('FIELD') }}" required
                                placeholder="..."
                                class="{{ $inputClass('FIELD') }}">
                            <x-input-error :messages="$errors->get('FIELD')" class="mt-1.5" />
                        </div>

                        {{-- Dropdown Select (قيم ثابتة) --}}
                        <div>
                            <label for="FIELD" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                <span class="flex items-center gap-1.5">
                                    <x-icon name="ICON" class="w-4 h-4 text-theme-from" />
                                    LABEL
                                </span>
                            </label>
                            <select id="FIELD" name="FIELD"
                                class="{{ $inputClass('FIELD') }} appearance-none bg-no-repeat cursor-pointer"
                                style="background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\' stroke=\'%236b7280\'%3E%3Cpath stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M19 9l-7 7-7-7\'/%3E%3C/svg%3E'); background-position: right 12px center; background-size: 16px;">
                                <option value="">— Select LABEL —</option>
                                @foreach(['OPT1', 'OPT2', 'OPT3'] as $opt)
                                    {{-- CREATE:  old('FIELD') === $opt --}}
                                    {{-- EDIT:    old('FIELD', $item->FIELD) === $opt --}}
                                    <option value="{{ $opt }}" {{ old('FIELD') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('FIELD')" class="mt-1.5" />
                        </div>

                        {{-- Dropdown Select (من قاعدة البيانات - FK) --}}
                        <div>
                            <label for="FIELD_id" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                <span class="flex items-center gap-1.5">
                                    <x-icon name="ICON" class="w-4 h-4 text-theme-from" />
                                    LABEL <span class="text-red-400">*</span>
                                </span>
                            </label>
                            <select id="FIELD_id" name="FIELD_id" required
                                class="{{ $inputClass('FIELD_id') }} appearance-none bg-no-repeat cursor-pointer"
                                style="background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\' stroke=\'%236b7280\'%3E%3Cpath stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M19 9l-7 7-7-7\'/%3E%3C/svg%3E'); background-position: right 12px center; background-size: 16px;">
                                <option value="">— Select LABEL —</option>
                                @foreach($OPTIONS as $option)
                                    <option value="{{ $option->id }}" {{ old('FIELD_id') == $option->id ? 'selected' : '' }}>
                                        {{ $option->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('FIELD_id')" class="mt-1.5" />
                        </div>

                        {{-- Textarea --}}
                        <div>
                            <label for="notes" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                <span class="flex items-center gap-1.5">
                                    <x-icon name="document" class="w-4 h-4 text-theme-from" />
                                    Notes
                                </span>
                            </label>
                            <textarea id="notes" name="notes" rows="3"
                                placeholder="..."
                                class="{{ $inputClass('notes') }} resize-none">{{ old('notes') }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-1.5" />
                        </div>

                        <div class="border-t border-dashed border-gray-200 pt-2"></div>

                        <div class="flex items-center justify-end gap-3">
                            <x-button-cancel/>
                            <x-button-submit>
                                <x-icon name="ICON" class="w-4 h-4 mr-1.5" />
                                LABEL
                            </x-button-submit>
                        </div>
                    </form>
                </div>
            </div>

            <p class="text-center text-xs text-gray-400 mt-4 flex items-center justify-center gap-1">
                <x-icon name="info" class="w-3.5 h-3.5 text-theme-from" />
                Fields marked with <span class="text-red-400 font-medium mx-0.5">*</span> are required
            </p>
        </div>
    </div>
</x-app-layout>

══════════════════════════════════════════════════════
📐 هيكل صفحة show (النمط الكامل — مرجعه: patients/show.blade.php)
══════════════════════════════════════════════════════

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <div class="flex items-center gap-3">
                <x-button-back href="{{ route('ROUTE.index') }}" label="ITEMS" />
                <div class="h-8 w-px bg-gray-200"></div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wider font-medium">SECTION › ITEMS</p>
                    <h1 class="text-xl font-bold text-gray-800">{{ $item->MAIN_FIELD }}</h1>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <x-button-link href="{{ route('ROUTE.edit', $item->id) }}">
                    <x-icon name="edit" class="w-4 h-4 mr-1.5" /> Edit
                </x-button-link>
                <form action="{{ route('ROUTE.destroy', $item->id) }}" method="POST"
                      onsubmit="return confirm('Archive this ITEM?')">
                    @csrf @method('DELETE')
                    <x-button-archive>Archive</x-button-archive>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="space-y-5">
        <x-toast-notification />

        <div class="max-w-full mx-auto bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">

            {{-- Gradient Header --}}
            <div class="bg-gradient-to-r from-theme-from to-theme-to px-8 py-6">
                <div class="flex items-center gap-5">
                    <div class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center shadow-inner">
                        <x-icon name="ICON" class="w-8 h-8 text-white" />
                    </div>
                    <div class="flex-1">
                        <h2 class="text-white text-2xl font-bold">{{ $item->MAIN_FIELD }}</h2>
                        <div class="flex items-center gap-3 mt-1.5 flex-wrap">
                            {{-- Badges للحقول الثانوية --}}
                            @if($item->BADGE_FIELD)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-white/20 text-white backdrop-blur-sm border border-white/10">
                                    <x-icon name="ICON" class="w-3.5 h-3.5" />
                                    {{ $item->BADGE_FIELD }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Info Cards Row (4 بطاقات مهمة) --}}
            <div class="px-8 py-6 border-b border-gray-100">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="flex items-center gap-3 p-3.5 rounded-xl bg-gray-50/80 border border-gray-100">
                        <div class="flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-xl bg-theme-from/10">
                            <x-icon name="ICON" class="w-5 h-5 text-theme-from" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">LABEL</p>
                            <p class="text-sm font-medium text-gray-800 truncate">{{ $item->FIELD ?? '—' }}</p>
                        </div>
                    </div>
                    {{-- كرر البطاقة لكل حقل مهم --}}
                </div>
            </div>

            {{-- Main Content Grid --}}
            <div class="px-8 py-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                    {{-- Left: Details Card --}}
                    <div class="space-y-6">
                        <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">ITEM Details</h3>
                        <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100">
                            <div class="space-y-2.5">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-semibold text-gray-500">LABEL</span>
                                    <span class="text-sm font-bold text-gray-800">{{ $item->FIELD }}</span>
                                </div>
                                {{-- كرر لكل حقل --}}
                            </div>
                        </div>
                    </div>

                    {{-- Right: Notes + System Info --}}
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Notes</h3>
                            <div class="p-5 rounded-2xl bg-amber-50/50 border border-amber-100 min-h-[140px]">
                                @if($item->notes)
                                    <p class="text-gray-700 text-sm leading-relaxed whitespace-pre-line">{{ $item->notes }}</p>
                                @else
                                    <div class="flex flex-col items-center justify-center h-full text-gray-400 py-4">
                                        <x-icon name="document" class="w-8 h-8 opacity-20 mb-2" />
                                        <p class="text-xs italic">No notes added</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-3.5 rounded-xl bg-gray-50 border border-gray-100">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Created</p>
                                <p class="text-xs font-medium text-gray-700 mt-0.5">{{ $item->created_at->format('M d, Y') }}</p>
                            </div>
                            <div class="p-3.5 rounded-xl bg-gray-50 border border-gray-100">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Last Update</p>
                                <p class="text-xs font-medium text-gray-700 mt-0.5">{{ $item->updated_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Related Section (إذا كان هناك علاقة hasMany — مثل Batches) --}}
                <div class="mt-10 pt-8 border-t border-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest">RELATED SECTION</h3>
                        <a href="{{ route('SECTION.MODULE.index', ['PARENT_id' => $item->id]) }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-theme-from/10 text-theme-from text-xs font-semibold border border-theme-from/20 hover:bg-theme-from hover:text-white transition-all duration-200">
                            <x-icon name="plus" class="w-3.5 h-3.5" /> Manage
                        </a>
                    </div>
                    {{-- عرض العناصر أو رسالة فارغة --}}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

══════════════════════════════════════════════════════
🎛️ Controller Pattern (النمط الكامل)
══════════════════════════════════════════════════════

namespace App\Http\Controllers\SECTION;

use App\Http\Controllers\Controller;
use App\Http\Requests\MODULE\StoreXRequest;
use App\Http\Requests\MODULE\UpdateXRequest;
use App\Models\SECTION\MODEL;
use App\Helpers\Idempotency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class XController extends Controller
{
    public function index(Request $request)
    {
        $query = MODEL::query();

        // Search (Include QR Code and multiple fields)
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $safeSearch = '%' . addcslashes($searchTerm, '%_\\') . '%';
                $q->where('FIELD1', 'LIKE', $safeSearch)
                  ->orWhere('FIELD2', 'LIKE', $safeSearch)
                  ->orWhere('qr_code', 'LIKE', $safeSearch);
            });
        }

        // Dropdown Filter (للحقول ذات القيم المحدودة)
        if ($request->filled('FILTER_FIELD')) {
            $query->where('FILTER_FIELD', $request->input('FILTER_FIELD'));
        }

        // Archived
        if ($request->input('archived') == 'true') {
            $query->onlyTrashed()->orderBy('deleted_at', 'desc');
        } else {
            $query->latest();
        }

        $items         = $query->paginate(10)->onEachSide(1)->withQueryString();
        $archivedCount = MODEL::onlyTrashed()->count();
        $totalCount    = MODEL::count();

        // للـ Dropdowns: إذا كانت القيم من DB
        $filterOptions = MODEL::whereNotNull('FILTER_FIELD')->distinct()->orderBy('FILTER_FIELD')->pluck('FILTER_FIELD');
        // أو إذا كانت قيم ثابتة: $filterOptions = collect(['OPT1', 'OPT2', 'OPT3']);

        return view('SECTION.MODULE.index', compact('items', 'archivedCount', 'totalCount', 'filterOptions'));
    }

    public function create() { return view('SECTION.MODULE.create'); }

    public function store(StoreXRequest $request)
    {
        $key = $request->input('idempotency_key');
        if (Idempotency::check($key)) {
            return redirect()->route('ROUTE.index');
        }
        MODEL::create($request->validated());
        return redirect()->route('ROUTE.index')->with('success', 'ITEM created successfully!');
    }

    public function show(string $id)
    {
        $item = MODEL::findOrFail($id);
        // إذا كان هناك علاقات: MODEL::with('relation')->findOrFail($id)
        return view('SECTION.MODULE.show', compact('item'));
    }

    public function edit(string $id)
    {
        $item = MODEL::findOrFail($id);
        // إذا كان يحتاج options: $options = RelatedModel::all();
        return view('SECTION.MODULE.edit', compact('item'));
    }

    public function update(UpdateXRequest $request, string $id)
    {
        $key = $request->input('idempotency_key');
        if (Idempotency::check($key)) {
            return redirect()->route('ROUTE.index');
        }
        MODEL::findOrFail($id)->update($request->validated());
        return redirect()->route('ROUTE.index')->with('success', 'ITEM updated successfully!');
    }

    public function destroy(string $id)
    {
        $item = MODEL::withTrashed()->findOrFail($id);
        $item->delete();
        $previousUrl = URL::previous();
        $showRoute   = route('ROUTE.show', $item->id);
        if (str_contains($previousUrl, $showRoute)) {
            return redirect()->route('ROUTE.index')->with('success', 'ITEM archived successfully!');
        }
        return redirect()->back()->with('success', 'ITEM archived successfully!');
    }

    public function restore(string $id)
    {
        MODEL::withTrashed()->findOrFail($id)->restore();
        return redirect()->route('ROUTE.index', ['archived' => 'true'])->with('success', 'ITEM restored successfully!');
    }
}

══════════════════════════════════════════════════════
✅ Form Request Pattern (النمط الكامل)
══════════════════════════════════════════════════════

namespace App\Http\Requests\MODULE;

use Illuminate\Foundation\Http\FormRequest;

class StoreXRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'FIELD'  => 'required|string|max:255',
            'FIELD2' => 'nullable|string|max:255',
            'FIELD3' => 'required|numeric|min:0',
            'FIELD4' => 'nullable|in:OPT1,OPT2,OPT3',    // للـ enum/dropdown
            'FIELD5' => 'nullable|exists:table,id',        // للـ FK
        ];
    }

    public function messages(): array
    {
        return [
            'FIELD.required' => 'The FIELD is required.',
        ];
    }
}

// UpdateXRequest: نفس الملف مع تغيير unique rule إذا كانت موجودة:
// 'email' => 'required|email|unique:users,email,' . $this->route('id')

══════════════════════════════════════════════════════
🗺️ Routes Pattern (النمط الكامل)
══════════════════════════════════════════════════════

// داخل Route::middleware('auth')->group(...) في web.php
Route::resource('SECTION/MODULE', \App\Http\Controllers\SECTION\XController::class)->names('SECTION.MODULE');
Route::put('/SECTION/MODULE/{id}/restore', [\App\Http\Controllers\SECTION\XController::class, 'restore'])->name('SECTION.MODULE.restore');

══════════════════════════════════════════════════════
⚠️ قواعد صارمة (يُمنع مخالفتها)
══════════════════════════════════════════════════════

✗ لا تستخدم inline SVG — استخدم <x-icon> دائماً
✗ لا تكتب CSS classes مخصصة — استخدم نفس classes الموجودة
✗ لا تستبدل <x-button-submit> أو <x-button-cancel> بكود يدوي
✗ لا تستبدل <x-table-actions> بكود يدوي
✗ لا تستبدل <x-toast-notification> بكود session مخصص
✗ لا تستبدل <x-message-empty> بـ <tr><td> مخصص
✗ لا تستبدل <x-search-bar> بـ input مخصص
✗ لا تستبدل <x-button-link> بـ <a> مخصص
✗ لا تستبدل <x-button-back> بـ <a> مخصص في صفحات show
✗ لا تستبدل <x-button-archive> بكود يدوي في صفحات show
✓ الـ Model يجب أن يستخدم SoftDeletes إن كان archive مطلوباً
✓ استخدام x-card-link في صفحة index لإحصائيات الموديول
✓ استخدام x-scan-qr-modal إذا كان الموديول يدعم البحث بـ QR
✓ idempotency_key مطلوب في كل form (create و edit)
✓ bg-gradient-to-r from-theme-from to-theme-to مطلوب في Card Header
✓ $inputClass مطلوب في كل form لإدارة validation states
✓ show.blade.php يجب أن يتبع نمط patients/show.blade.php بالكامل:
    x-button-back + Gradient Header + Info Cards Row + 2-Column layout
✓ الـ Dropdown Filters في index تُضاف داخل <x-search-bar> slot
✓ الـ filterOptions تُرسل من Controller عبر pluck() أو collect()