<x-layouts.app>
    <div
        class="w-full max-w-2xl text-[13px] leading-[20px] bg-white dark:bg-[#161615] dark:text-[#EDEDEC] shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] rounded-lg p-6 lg:p-8">
        <h1 class="mb-2 font-medium text-sm">Things Local API Server</h1>
        <p class="mb-4 text-[#706f6c] dark:text-[#A1A09A]">
            Enable or disable the local Things REST API server that other apps can use on this machine.
        </p>

        <div class="mb-4 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-[#FDFDFC] dark:bg-[#161615]">
            <div class="p-10 flex flex-col gap-2">
                <div class="flex items-center justify-between mb-2">
                    <div>
                        <p class="font-medium">
                            Server status:
                            @if ($serverEnabled)
                                <p class="text-[#1b1b18] dark:text-[#EDEDEC]">Enabled</p>
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
                            @else
                            <p class="text-[#706f6c] dark:text-[#A1A09A]">Disabled</p>
                        @endif
                        </p>
                    </div>

                    <form method="POST" action="{{ route('server.toggle') }}">
                        @csrf
                        <button type="submit"
                            class="inline-block px-5 py-1.5 bg-[#1b1b18] dark:bg-[#eeeeec] text-white dark:text-[#1C1C1A] border border-black dark:border-[#eeeeec] rounded-sm text-sm leading-normal hover:bg-black dark:hover:bg-white hover:border-black dark:hover:border-white transition-all cursor-pointer">
                            @if ($serverEnabled)
                                Disable server
                            @else
                                Enable server
                            @endif
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>