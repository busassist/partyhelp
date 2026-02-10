@php
    $url = url('/admin/user-guide');
@endphp
<a
    href="{{ $url }}"
    class="fi-topbar-item-btn fi-user-guide-link flex shrink-0 items-center gap-x-1.5 rounded-lg px-3 py-2 text-sm font-medium text-gray-700 outline-none transition duration-75 hover:bg-gray-50 hover:text-gray-900 focus:bg-gray-50 focus:text-gray-900 dark:text-gray-200 dark:hover:bg-white/5 dark:hover:text-white dark:focus:bg-white/5 dark:focus:text-white whitespace-nowrap"
>
    <svg class="fi-topbar-item-icon shrink-0" style="width:22px;height:22px" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
    </svg>
    <span class="fi-topbar-item-label min-w-[90px]">User Guide</span>
</a>
