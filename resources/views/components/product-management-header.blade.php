<div class="mb-6 flex flex-wrap items-center justify-between gap-2">
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded text-sm font-medium transition">
            ← Admin Dashboard
        </a>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.sets') }}" class="px-3 py-1.5 rounded text-sm font-medium {{ request()->routeIs('admin.sets-page') ? 'bg-blue-600 text-white' : 'bg-blue-50 text-blue-700 hover:bg-blue-100' }}">
            Sets
        </a>
        <a href="{{ route('admin.rarities') }}" class="px-3 py-1.5 rounded text-sm font-medium {{ request()->routeIs('admin.rarities-page') ? 'bg-pink-600 text-white' : 'bg-pink-50 text-pink-700 hover:bg-pink-100' }}">
            Rarities
        </a>
        <a href="{{ route('admin.cards') }}" class="px-3 py-1.5 rounded text-sm font-medium {{ request()->routeIs('admin.cards-page') ? 'bg-indigo-600 text-white' : 'bg-indigo-50 text-indigo-700 hover:bg-indigo-100' }}">
            Cards
        </a>
        <a href="{{ route('admin.products') }}" class="px-3 py-1.5 rounded text-sm font-medium {{ request()->routeIs('admin.products-page') ? 'bg-orange-600 text-white' : 'bg-orange-50 text-orange-700 hover:bg-orange-100' }}">
            Products
        </a>
    </div>
</div>
