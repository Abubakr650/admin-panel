<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full gap-4">
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-widest font-semibold">System</p>
                <h1 class="text-lg font-bold text-gray-800 leading-tight">Reports Dashboard</h1>
            </div>
        </div>
    </x-slot>

    <div class="flex gap-6 max-w-7xl mx-auto sm:px-6 lg:px-8 mt-6">
        <div class="flex-1 min-w-0">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    
                    <!-- Pharmacy Reports -->
                    <div class="border border-gray-100 rounded-xl p-5 hover:shadow-md hover:border-theme-from/30 transition-all group bg-gray-50/50">
                        <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                            <x-icon name="document-text" class="w-6 h-6" />
                        </div>
                        <h4 class="font-bold text-gray-800">Pharmacy Sales</h4>
                        <p class="text-sm text-gray-500 mt-1 mb-5">View dispensed medicines, quantities, and total revenue over a period.</p>
                        <a href="#" class="text-emerald-600 font-semibold text-sm hover:underline flex items-center gap-1">
                            Generate Report <x-icon name="arrow-right" class="w-4 h-4" />
                        </a>
                    </div>

                    <!-- Clinic Reports -->
                    <div class="border border-gray-100 rounded-xl p-5 hover:shadow-md hover:border-theme-from/30 transition-all group bg-gray-50/50">
                        <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                            <x-icon name="users" class="w-6 h-6" />
                        </div>
                        <h4 class="font-bold text-gray-800">Appointments Report</h4>
                        <p class="text-sm text-gray-500 mt-1 mb-5">Analyze patient visits, statuses, and doctor performance.</p>
                        <a href="#" class="text-blue-600 font-semibold text-sm hover:underline flex items-center gap-1">
                            Generate Report <x-icon name="arrow-right" class="w-4 h-4" />
                        </a>
                    </div>
                    
                    <!-- Financial Reports -->
                    <div class="border border-gray-100 rounded-xl p-5 hover:shadow-md hover:border-theme-from/30 transition-all group bg-gray-50/50">
                        <div class="w-12 h-12 bg-amber-100 text-amber-600 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                            <x-icon name="currency-dollar" class="w-6 h-6" />
                        </div>
                        <h4 class="font-bold text-gray-800">Revenue & Billing</h4>
                        <p class="text-sm text-gray-500 mt-1 mb-5">Comprehensive financial overview including invoices, payments, and discounts.</p>
                        <a href="#" class="text-amber-600 font-semibold text-sm hover:underline flex items-center gap-1">
                            Generate Report <x-icon name="arrow-right" class="w-4 h-4" />
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
