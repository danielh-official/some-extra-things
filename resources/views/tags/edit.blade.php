<x-layouts.app>
    <div class="flex flex-col gap-4 w-full max-w-lg">
        <div>
            <a href="{{ route('tags.show', $tagModel->things_id ?? $tagModel->id) }}"
                class="text-xs text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-white">&larr; {{ $tagModel->name }}</a>
        </div>

        <h1 class="text-sm font-medium">Edit Tag</h1>

        @if ($errors->any())
            <div class="flex flex-col gap-1">
                @foreach ($errors->all() as $error)
                    <p class="text-xs text-red-600 dark:text-red-400">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('tags.update', $tagModel->id) }}" class="flex flex-col gap-4">
            @csrf
            @method('PATCH')

            <div class="flex flex-col gap-1">
                <label for="name" class="text-xs font-medium">Name</label>
                <input id="name" name="name" type="text" value="{{ old('name', $tagModel->name) }}" required
                    class="border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm px-2 py-1 text-xs bg-[#FDFDFC] dark:bg-[#161615]">
            </div>

            <div class="flex flex-col gap-1">
                <label for="parent_tag_id" class="text-xs font-medium">Parent Tag</label>
                <select id="parent_tag_id" name="parent_tag_id"
                    class="border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm px-2 py-1 text-xs bg-[#FDFDFC] dark:bg-[#161615]">
                    <option value="">None</option>
                    @foreach ($otherTags as $tag)
                        <option value="{{ $tag->id }}"
                            {{ old('parent_tag_id', $tagModel->parent_tag_id) == $tag->id ? 'selected' : '' }}>
                            {{ $tag->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit"
                    class="inline-block px-4 py-1.5 bg-[#1b1b18] dark:bg-[#eeeeec] text-white dark:text-[#1C1C1A] border border-black dark:border-[#eeeeec] rounded-sm text-xs leading-normal hover:bg-black dark:hover:bg-white transition-all cursor-pointer">
                    Save
                </button>
                <a href="{{ route('tags.show', $tagModel->things_id ?? $tagModel->id) }}"
                    class="text-xs text-[#706f6c] dark:text-[#A1A09A]">Cancel</a>
            </div>
        </form>
    </div>
</x-layouts.app>
