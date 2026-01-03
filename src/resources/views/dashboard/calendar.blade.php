<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            カレンダー
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-4">
                <script>
                    window.__RESERVATION_CALENDAR__ = {
                        slotMinutes: {{ (int) $setting->slot_minutes }},
                        openTime: @js($setting->open_time),
                        closeTime: @js($setting->close_time),
                        eventsUrl: @js(route('dashboard.calendar.reservations.index')),
                        storeUrl: @js(route('dashboard.calendar.reservations.store')),
                        showUrlBase: @js(url('/dashboard/calendar/reservations')),
                        updateUrlBase: @js(url('/dashboard/calendar/reservations')),
                        customers: @js($customers),
                    };
                </script>

                <div x-data="reservationCalendar(window.__RESERVATION_CALENDAR__)" x-init="init()">
                    <div id="calendar" class="w-full"></div>

                    <div x-cloak x-show="create.open" class="fixed inset-0 z-50 flex items-center justify-center px-4">
                        <div class="absolute inset-0 bg-black/30" @click="closeCreate()"></div>
                        <div class="relative w-full max-w-md rounded-lg bg-white shadow-lg p-5">
                            <h3 class="text-lg font-semibold text-gray-900">仮予約</h3>

                            <div class="mt-4 space-y-3">
                                <div class="text-sm text-gray-700">
                                    <div>開始：<span class="font-medium" x-text="create.startText"></span></div>
                                    <div>終了：<span class="font-medium" x-text="create.endText"></span></div>
                                </div>

                                <div>
                                    <label class="block text-sm text-gray-700">顧客名（任意）</label>
                                    <input type="text" x-model="create.customerName"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                            </div>

                            <div class="mt-5 flex justify-end gap-2">
                                <x-secondary-button type="button" @click="closeCreate()">閉じる</x-secondary-button>
                                <x-primary-button type="button" @click="saveCreate()" x-bind:disabled="create.saving">
                                    仮予約で保存
                                </x-primary-button>
                            </div>
                        </div>
                    </div>

                    <div x-cloak x-show="edit.open" class="fixed inset-0 z-50 flex items-center justify-center px-4">
                        <div class="absolute inset-0 bg-black/30" @click="closeEdit()"></div>
                        <div class="relative w-full max-w-md rounded-lg bg-white shadow-lg p-5">
                            <h3 class="text-lg font-semibold text-gray-900">予約の編集</h3>

                            <div class="mt-4 space-y-3">
                                <div class="text-sm text-gray-700">
                                    <div>開始：<span class="font-medium" x-text="edit.startText"></span></div>
                                    <div>終了：<span class="font-medium" x-text="edit.endText"></span></div>
                                </div>

                                <div>
                                    <label class="block text-sm text-gray-700">顧客</label>
                                    <select x-model="edit.customerId" @change="syncCustomerFields()"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option :value="null">未設定</option>
                                        <template x-for="c in customers" :key="c.id">
                                            <option :value="c.id" x-text="c.name"></option>
                                        </template>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm text-gray-700">顧客名</label>
                                    <input type="text" x-model="edit.customerName"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                <div>
                                    <label class="block text-sm text-gray-700">電話番号</label>
                                    <input type="text" x-model="edit.customerPhone"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                <div>
                                    <label class="block text-sm text-gray-700">メモ</label>
                                    <textarea x-model="edit.memo" rows="3"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                </div>

                                <div>
                                    <label class="block text-sm text-gray-700">状態</label>
                                    <select x-model="edit.status"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="booked">予約</option>
                                        <option value="done">完了</option>
                                        <option value="cancel">キャンセル</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mt-5 flex justify-end gap-2">
                                <x-secondary-button type="button" @click="closeEdit()">閉じる</x-secondary-button>
                                <x-primary-button type="button" @click="saveEdit()" x-bind:disabled="edit.saving">
                                    保存
                                </x-primary-button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @once
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css">
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
    @endonce
</x-app-layout>
