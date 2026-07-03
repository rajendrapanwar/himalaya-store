<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - Himalaya Store</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">

<div class="flex min-h-screen">

    <aside class="w-64 bg-gray-900 text-white">
        <div class="p-6 text-2xl font-bold border-b border-gray-700">
            Admin Panel
        </div>

        <nav class="p-4 space-y-2">
            <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 rounded hover:bg-gray-800">
                Dashboard
            </a>

            <a href="{{ route('admin.categories.index') }}" class="block px-4 py-2 rounded hover:bg-gray-800">
                Categories
            </a>

            <a href="{{ route('admin.products.index') }}" class="block px-4 py-2 rounded hover:bg-gray-800">
                Products
            </a>
            
            <a href="{{ route('admin.orders.index') }}" class="block px-4 py-2 rounded hover:bg-gray-800">
                Orders
            </a>
        </nav>
    </aside>

    <main class="flex-1">
        <header class="bg-white shadow px-6 py-4">
            <h1 class="text-xl font-semibold">@yield('title')</h1>
        </header>

        <div class="p-6">
            @if(session('success'))
                <div class="mb-4 bg-green-100 text-green-800 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @yield('content')
        </div>
    </main>

</div>

</body>
</html>