<x-layouts.app>
    <div
        class="w-full text-[13px] leading-5 bg-white dark:bg-[#161615] dark:text-[#EDEDEC] shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] rounded-lg p-6 lg:p-8">
        <h1 class="mb-2 font-medium text-sm">Local API Server</h1>

        <div class="mb-4 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-[#FDFDFC] dark:bg-[#161615]">
            <div class="p-10 flex flex-col gap-2">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex flex-col gap-2">
                        <div>
                            <p class="text-[#706f6c] dark:text-[#A1A09A] ml-1">get all items</p>
                            <code
                                class="px-1.5 py-0.5 rounded-sm bg-[#fff2f2] dark:bg-[#1D0002] border border-[#e3e3e0] dark:border-[#3E3E3A]">GET {{ url('/') }}/api/items</code>
                        </div>
                        <div>
                            <p class="text-[#706f6c] dark:text-[#A1A09A] ml-1">create / upsert</p>
                            <code
                                class="px-1.5 py-0.5 rounded-sm bg-[#fff2f2] dark:bg-[#1D0002] border border-[#e3e3e0] dark:border-[#3E3E3A]">POST {{ url('/') }}/api/items</code>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 pt-6 border-t border-[#e3e3e0] dark:border-[#3E3E3A] flex items-center justify-between">
            <div>
                <p class="text-[#706f6c] dark:text-[#A1A09A]">Move all items to Trash.</p>
            </div>
            <form method="POST" action="{{ route('settings.items.destroy') }}"
                onsubmit="return confirm('Are you sure you want to move items to the trash? They can be recovered.');">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="inline-block px-3 py-1 bg-transparent text-xs text-[#706f6c] dark:text-[#A1A09A] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm hover:bg-[#f5f5f2] dark:hover:bg-[#161615] transition-all cursor-pointer">
                    Trash All Items
                </button>
            </form>
        </div>
    </div>
</x-layouts.app>