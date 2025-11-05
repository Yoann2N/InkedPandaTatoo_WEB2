<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">
            {{ $page['title'] ?? 'Page' }}
        </h2>
    </x-slot>

    @push('styles')
      {{-- CSS de ton template (placé dans public/template/css) --}}
      <link rel="stylesheet" href="{{ asset('template/css/style.css') }}">
    @endpush

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- ... ton rendu JSON (slideshow etc.) ... --}}
        </div>
    </div>

    @push('scripts')
      {{-- JS de ton template (placé dans public/template/js) --}}
      <script src="{{ asset('template/js/main.js') }}"></script>
    @endpush
</x-app-layout>
