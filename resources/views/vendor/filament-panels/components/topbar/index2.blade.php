@props([
    'navigation',
])

<div
    class="fi-topbar sticky top-0 z-20 overflow-x-clip border-b border-gray-200 bg-white backdrop-blur-xl dark:border-gray-700 dark:bg-gray-900/80">
    <nav class="fi-topbar-nav flex h-16 items-center gap-x-4 px-4 md:px-6 lg:px-8">
        <div class="flex items-center gap-x-4">
            <x-filament-panels::logo/>
        </div>

        <div class="me-auto"></div>

        <!-- Page Header Section -->
        <div class="flex-1 flex items-center justify-between min-w-0 px-4">
            <!-- Left side: Breadcrumbs and Title -->
            <div class="flex items-center gap-x-4 min-w-0">
                <!-- Breadcrumbs -->
                @if (filament()->hasBreadcrumbs())
                    <nav class="fi-breadcrumbs hidden sm:block">
                        <ol class="fi-breadcrumbs-list flex items-center gap-x-2">
                            @foreach ($breadcrumbs ?? [] as $url => $label)
                                <li class="fi-breadcrumbs-item flex gap-x-2">
                                    @if (! $loop->last)
                                        <a
                                            href="{{ $url }}"
                                            class="fi-breadcrumbs-item-label text-sm font-medium text-gray-500 transition duration-75 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                                        >
                                            {{ $label }}
                                        </a>

                                        <svg
                                            class="fi-breadcrumbs-item-separator flex h-5 w-5 text-gray-400 dark:text-gray-500"
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20"
                                            fill="currentColor"
                                            aria-hidden="true"
                                        >
                                            <path
                                                fill-rule="evenodd"
                                                d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z"
                                                clip-rule="evenodd"
                                            />
                                        </svg>
                                    @else
                                        <span
                                            class="fi-breadcrumbs-item-label text-sm font-medium text-gray-700 dark:text-gray-200">
                                            {{ $label }}
                                        </span>
                                    @endif
                                </li>
                            @endforeach
                        </ol>
                    </nav>
                @endif

                <!-- Page Title -->
                @if (isset($heading))
                    <div class="flex items-center gap-x-4">
                        <h1 class="fi-header-heading text-xl font-bold tracking-tight text-gray-950 dark:text-white sm:text-2xl">
                            {{ $heading }}
                        </h1>
                    </div>
                @endif
            </div>

            <!-- Right side: Actions -->
            <div class="flex items-center gap-x-4">
                @if (isset($headerActions) && count($headerActions))
                    <div class="fi-header-actions flex items-center gap-x-4">
                        @foreach ($headerActions as $action)
                            {{ $action }}
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Original Topbar Right Items -->
        <div class="flex items-center gap-x-4">
            @if (filament()->hasDatabaseNotifications())
                @livewire(Filament\Livewire\DatabaseNotifications::class, ['lazy' => true])
            @endif

            <x-filament-panels::user-menu/>
        </div>
    </nav>
</div>
