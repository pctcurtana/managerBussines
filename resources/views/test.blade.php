@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <h1 class="text-3xl font-bold text-center mb-8">Tailwind CSS Test Page</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Card Component</h2>
            <p class="text-gray-600">This is a simple card component styled with Tailwind CSS.</p>
            <button class="mt-4 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors">Click Me</button>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Flex Layout</h2>
            <div class="flex justify-between items-center">
                <span class="text-gray-600">Item 1</span>
                <span class="text-gray-600">Item 2</span>
                <span class="text-gray-600">Item 3</span>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Colors</h2>
            <div class="flex flex-wrap gap-2">
                <div class="w-12 h-12 bg-red-500 rounded"></div>
                <div class="w-12 h-12 bg-blue-500 rounded"></div>
                <div class="w-12 h-12 bg-green-500 rounded"></div>
                <div class="w-12 h-12 bg-yellow-500 rounded"></div>
                <div class="w-12 h-12 bg-purple-500 rounded"></div>
            </div>
        </div>
    </div>
</div>
@endsection 