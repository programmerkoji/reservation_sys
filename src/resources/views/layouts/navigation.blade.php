<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center gap-8">
                <a href="{{ route('dashboard.calendar') }}" class="text-lg font-semibold text-gray-900">
                    予約管理
                </a>

                <div class="hidden sm:flex items-center gap-6">
                    <x-nav-link :href="route('dashboard.calendar')" :active="request()->routeIs('dashboard.calendar')">
                        カレンダー
                    </x-nav-link>
                    <x-nav-link :href="route('dashboard.customers.index')" :active="request()->routeIs('dashboard.customers.*')">
                        お客様
                    </x-nav-link>
                    <x-nav-link :href="route('dashboard.settings.edit')" :active="request()->routeIs('dashboard.settings.*')">
                        設定
                    </x-nav-link>
                </div>
            </div>

            <div class="hidden sm:flex items-center">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-secondary-button type="submit">
                        ログアウト
                    </x-secondary-button>
                </form>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-gray-100">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard.calendar')" :active="request()->routeIs('dashboard.calendar')">
                カレンダー
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('dashboard.customers.index')" :active="request()->routeIs('dashboard.customers.*')">
                お客様
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('dashboard.settings.edit')" :active="request()->routeIs('dashboard.settings.*')">
                設定
            </x-responsive-nav-link>
        </div>

        <div class="pt-2 pb-3 px-4">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-secondary-button type="submit" class="w-full justify-center">
                    ログアウト
                </x-secondary-button>
            </form>
        </div>
    </div>
</nav>
