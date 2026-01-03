window.reservationCalendar = function reservationCalendar(config) {
    const customers = config.customers ?? [];

    return {
        calendar: null,
        customers,
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
            if (!window.FullCalendar || !window.FullCalendar.Calendar) {
                setTimeout(() => this.init(), 50);
                return;
            }

            const calendarEl = document.getElementById('calendar');
            if (!calendarEl) {
                return;
            }

            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
            const formatLocalDateTime = (date) => {
                const pad = (n) => String(n).padStart(2, '0');
                return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())} ${pad(date.getHours())}:${pad(date.getMinutes())}:00`;
            };

            this.calendar = new window.FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridWeek',
                locale: 'ja',
                timeZone: 'local',
                height: 'auto',
                buttonText: {
                    today: '今日',
                },
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
                                Accept: 'application/json',
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
                            Accept: 'application/json',
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
                            Accept: 'application/json',
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
                headers: { Accept: 'application/json' },
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
};
