<x-layouts.app>
    <form method="POST" action="{{ route('smart-lists.update', $smartList) }}" class="w-full flex justify-center">
        @csrf
        @method('PUT')
        @include('smart-lists.form', [
            'smartList' => $smartList,
            'heading' => 'Edit smart list',
        ])
    </form>
</x-layouts.app>

