<x-layouts.app>
    <div
        class="w-full max-w-2xl text-[13px] leading-[20px] bg-white dark:bg-[#161615] dark:text-[#EDEDEC] shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] rounded-lg p-6 lg:p-8">
        <h1 class="mb-2 font-medium text-sm">Local API Server</h1>

        <div class="mb-4 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-[#FDFDFC] dark:bg-[#161615]">
            <div class="p-10 flex flex-col gap-2">
                <div class="flex items-center justify-between mb-2">
                    <div>
                        <p class="mt-1">
                        <p class="text-[#706f6c] dark:text-[#A1A09A] ml-1">create / upsert</p>
                        <code
                            class="px-1.5 py-0.5 rounded-sm bg-[#fff2f2] dark:bg-[#1D0002] border border-[#e3e3e0] dark:border-[#3E3E3A]">POST {{ url('/') }}/api/items</code>
                        </p>
                        <p class="mt-1">
                        <p class="text-[#706f6c] dark:text-[#A1A09A] ml-1">update</p>
                        <code
                            class="px-1.5 py-0.5 rounded-sm bg-[#fff2f2] dark:bg-[#1D0002] border border-[#e3e3e0] dark:border-[#3E3E3A]">PUT {{ url('/') }}/api/items/{id}</code>
                        </p>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>