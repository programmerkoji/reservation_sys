<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            カレンダー
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-4">
                <div
                    x-data="reservationCalendar({
                        slotMinutes: {{ (int) $setting->slot_minutes }},
                        openTime: @js($setting->open_time),
                        closeTime: @js($setting->close_time),
                        eventsUrl: @js(route('dashboard.calendar.reservations.index')),
                        storeUrl: @js(route('dashboard.calendar.reservations.store')),
                        showUrlBase: @js(url('/dashboard/calendar/reservations')),
                        updateUrlBase: @js(url('/dashboard/calendar/reservations')),
                        customers: @js($customers),
                    })"
                    x-init="init()"
                >
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

    <script>
        function reservationCalendar(config) {
            return {
                calendar: null,
                customers: config.customers ?? [],
                create: {
                    open: false,
                    saving: false,
                    startAt: null,
                    endAt: null,
                    startText: '',
                    endText: '',
                    customerName: '',
                },
                edit: {
                    open: false,
                    saving: false,
                    id: null,
                    startText: '',
                    endText: '',
                    customerId: null,
                    customerName: '',
                    customerPhone: '',
                    memo: '',
                    status: 'booked',
                },
                init() {
                    const calendarEl = document.getElementById('calendar');
                    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const formatLocalDateTime = (date) => {
                        const pad = (n) => String(n).padStart(2, '0');
                        return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())} ${pad(date.getHours())}:${pad(date.getMinutes())}:00`;
                    };

                    this.calendar = new FullCalendar.Calendar(calendarEl, {
                        initialView: 'timeGridWeek',
                        locale: 'ja',
                        timeZone: 'local',
                        height: 'auto',
                        headerToolbar: {
                            left: 'prev,next today',
                            center: 'title',
                            right: 'dayGridMonth,timeGridWeek',
                        },
                        views: {
                            timeGridWeek: {
                                slotMinTime: config.openTime + ':00',
                                slotMaxTime: config.closeTime + ':00',
                                slotDuration: '00:' + String(config.slotMinutes).padStart(2, '0') + ':00',
                            },
                        },
                        dayMaxEvents: true,
                        dateClick: (info) => {
                            if (this.calendar.view.type !== 'timeGridWeek') {
                                this.calendar.changeView('timeGridWeek', info.date);
                                return;
                            }

                            this.openCreate(info.date);
                        },
                        eventClick: async (info) => {
                            await this.openEdit(info.event.id);
                        },
                        events: async (fetchInfo, successCallback, failureCallback) => {
                            try {
                                const url = new URL(config.eventsUrl, window.location.origin);
                                url.searchParams.set('start', fetchInfo.startStr);
                                url.searchParams.set('end', fetchInfo.endStr);

                                const res = await fetch(url.toString(), {
                                    headers: {
                                        'Accept': 'application/json',
                                    },
                                });

                                if (!res.ok) {
                                    throw new Error('events fetch failed');
                                }

                                successCallback(await res.json());
                            } catch (e) {
                                failureCallback(e);
                            }
                        },
                        eventContent: (arg) => {
                            const lines = String(arg.event.title ?? '').split('\n');
                            const wrapper = document.createElement('div');
                            wrapper.className = 'text-xs leading-tight whitespace-pre-line';
                            wrapper.textContent = lines.join('\n');
                            return { domNodes: [wrapper] };
                        },
                    });

                    this.calendar.render();

                    this.saveCreate = async () => {
                        if (this.create.saving) return;
                        this.create.saving = true;

                        try {
                            const payload = {
                                start_at: formatLocalDateTime(this.create.startAt),
                                end_at: formatLocalDateTime(this.create.endAt),
                                customer_name: this.create.customerName,
                            };

                            const res = await fetch(config.storeUrl, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': csrf,
                                },
                                body: JSON.stringify(payload),
                            });

                            if (!res.ok) {
                                const data = await res.json().catch(() => null);
                                alert(data?.message ?? '保存できませんでした。');
                                return;
                            }

                            this.closeCreate();
                            this.calendar.refetchEvents();
                        } finally {
                            this.create.saving = false;
                        }
                    };

                    this.saveEdit = async () => {
                        if (this.edit.saving) return;
                        this.edit.saving = true;

                        try {
                            const payload = {
                                customer_id: this.edit.customerId ? Number(this.edit.customerId) : null,
                                customer_name: this.edit.customerName,
                                customer_phone: this.edit.customerPhone,
                                memo: this.edit.memo,
                                status: this.edit.status,
                            };

                            const res = await fetch(`${config.updateUrlBase}/${this.edit.id}`, {
                                method: 'PATCH',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': csrf,
                                },
                                body: JSON.stringify(payload),
                            });

                            if (!res.ok) {
                                const data = await res.json().catch(() => null);
                                alert(data?.message ?? '保存できませんでした。');
                                return;
                            }

                            this.closeEdit();
                            this.calendar.refetchEvents();
                        } finally {
                            this.edit.saving = false;
                        }
                    };
                },
                openCreate(date) {
                    const startAt = new Date(date.getTime());
                    const endAt = new Date(date.getTime() + (config.slotMinutes * 60 * 1000));

                    this.create.startAt = startAt;
                    this.create.endAt = endAt;
                    this.create.startText = startAt.toLocaleString('ja-JP');
                    this.create.endText = endAt.toLocaleString('ja-JP');
                    this.create.customerName = '';
                    this.create.open = true;
                },
                closeCreate() {
                    this.create.open = false;
                },
                async openEdit(id) {
                    const res = await fetch(`${config.showUrlBase}/${id}`, {
                        headers: { 'Accept': 'application/json' },
                    });
                    if (!res.ok) {
                        alert('読み込めませんでした。');
                        return;
                    }

                    const data = await res.json();
                    this.edit.id = data.id;
                    this.edit.startText = new Date(data.start_at).toLocaleString('ja-JP');
                    this.edit.endText = new Date(data.end_at).toLocaleString('ja-JP');
                    this.edit.status = data.status;
                    this.edit.memo = data.memo ?? '';
                    this.edit.customerId = data.customer?.id ?? null;
                    this.edit.customerName = data.customer?.name ?? '';
                    this.edit.customerPhone = data.customer?.phone ?? '';
                    this.edit.open = true;
                },
                closeEdit() {
                    this.edit.open = false;
                },
                syncCustomerFields() {
                    if (!this.edit.customerId) {
                        return;
                    }

                    const customer = this.customers.find((c) => String(c.id) === String(this.edit.customerId));
                    if (!customer) return;

                    this.edit.customerName = customer.name ?? '';
                    this.edit.customerPhone = customer.phone ?? '';
                },
            };
        }
    </script>
</x-app-layout>
