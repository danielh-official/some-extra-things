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

        <div class="mt-6 pt-6 border-t border-[#e3e3e0] dark:border-[#3E3E3A] flex flex-col gap-1">
            <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]">Version {{ config('nativephp.version') }}</p>
            <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]">Database: {{ database_path('nativephp.sqlite') }}</p>
        </div>

        <div class="mt-6 pt-6 border-t border-[#e3e3e0] dark:border-[#3E3E3A] flex items-center justify-between">
            <p class="text-[#706f6c] dark:text-[#A1A09A]">Appearance</p>
            <div class="flex items-center gap-1">
                @foreach (['system' => 'System', 'light' => 'Light', 'dark' => 'Dark'] as $value => $label)
                    <form method="POST" action="{{ route('settings.theme.update') }}">
                        @csrf
                        <input type="hidden" name="theme" value="{{ $value }}">
                        <button type="submit" aria-pressed="{{ $theme === $value ? 'true' : 'false' }}"
                            class="inline-block px-3 py-1 text-xs rounded-sm leading-normal transition-all cursor-pointer {{ $theme === $value ? 'bg-[#1b1b18] dark:bg-[#eeeeec] text-white dark:text-[#1C1C1A] border border-black dark:border-[#eeeeec]' : 'bg-transparent text-[#706f6c] dark:text-[#A1A09A] border border-[#e3e3e0] dark:border-[#3E3E3A] hover:bg-[#f5f5f2] dark:hover:bg-[#161615]' }}">
                            {{ $label }}
                        </button>
                    </form>
                @endforeach
            </div>
        </div>

        <div class="mt-6 pt-6 border-t border-[#e3e3e0] dark:border-[#3E3E3A] flex items-center justify-between"
            x-data="{
                confirmToggle(e) {
                    if (!{{ $allowTagEdits ? 'true' : 'false' }}) {
                        if (!window.confirm('Enabling tag edits allows renaming and reparenting tags directly in Things 3 via AppleScript. This will make changes to your Things 3 data. By confirming and continuing to use this feature, you accept any and all potential risks that may arise. Continue?')) {
                            e.preventDefault();
                        }
                    }
                }
            }">
            <p class="text-[#706f6c] dark:text-[#A1A09A]">Allow Tag Edits</p>
            <form method="POST" action="{{ route('settings.tag-edits.toggle') }}" @submit="confirmToggle($event)">
                @csrf
                <button type="submit"
                    class="inline-block px-3 py-1 text-xs rounded-sm leading-normal transition-all cursor-pointer {{ $allowTagEdits ? 'bg-[#1b1b18] dark:bg-[#eeeeec] text-white dark:text-[#1C1C1A] border border-black dark:border-[#eeeeec]' : 'bg-transparent text-[#706f6c] dark:text-[#A1A09A] border border-[#e3e3e0] dark:border-[#3E3E3A] hover:bg-[#f5f5f2] dark:hover:bg-[#161615]' }}">
                    {{ $allowTagEdits ? 'Disable' : 'Enable' }}
                </button>
            </form>
        </div>

        <div class="mt-6 pt-6 border-t border-[#e3e3e0] dark:border-[#3E3E3A] flex items-center justify-between"
            x-data="{ copied: false }">
            <div class="flex flex-col gap-1">
                <p class="text-[#706f6c] dark:text-[#A1A09A]">API Token</p>
                @if ($apiToken)
                    <div class="flex items-center gap-2">
                        <code
                            class="px-1.5 py-0.5 rounded-sm bg-[#fff2f2] dark:bg-[#1D0002] border border-[#e3e3e0] dark:border-[#3E3E3A] text-xs font-mono truncate max-w-full blur-sm text-transparent select-none">{{ $apiToken }}</code>
                        <button type="button"
                            @click="navigator.clipboard.writeText('{{ $apiToken }}'); copied = true; setTimeout(() => copied = false, 2000)"
                            class="inline-block px-3 py-1 text-xs rounded-sm leading-normal transition-all cursor-pointer bg-transparent text-[#706f6c] dark:text-[#A1A09A] border border-[#e3e3e0] dark:border-[#3E3E3A] hover:bg-[#f5f5f2] dark:hover:bg-[#161615]"
                            x-text="copied ? 'Copied!' : 'Copy'">
                        </button>
                    </div>
                @endif
            </div>
            <form method="POST" action="{{ route('settings.api-token.generate') }}">
                @csrf
                <button type="submit"
                    class="inline-block px-3 py-1 text-xs rounded-sm leading-normal transition-all cursor-pointer bg-transparent text-[#706f6c] dark:text-[#A1A09A] border border-[#e3e3e0] dark:border-[#3E3E3A] hover:bg-[#f5f5f2] dark:hover:bg-[#161615]">
                    {{ $apiToken ? 'Regenerate' : 'Generate' }}
                </button>
            </form>
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