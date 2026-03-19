<x-layouts.app>
    <form method="POST" action="{{ route('smart-lists.store') }}" class="w-full flex justify-center">
        @csrf
        @include('smart-lists.form', [
            'smartList' => $smartList,
            'heading' => 'New smart list',
            'cancelLink' => $cancelLink ?? null
        ])
    </form>
</x-layouts.app>

