@props(['href', 'active' => false])
<a href="{{ $href }}"
   class="flex items-center px-2 py-1 rounded-md text-sm transition-colors
          {{ $active
              ? 'bg-white dark:bg-[#1e1e1c] text-[#1b1b18] dark:text-white shadow-sm font-medium'
              : 'text-[#706f6c] dark:text-[#8a8a7f] hover:text-[#1b1b18] dark:hover:text-white hover:bg-white/60 dark:hover:bg-[#1e1e1c]/60' }}">
    {{ $slot }}
</a>
