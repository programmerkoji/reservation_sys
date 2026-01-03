<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                お客様
            </h2>

            <button type="button" @click="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'customer-create' }))"
                class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                追加
            </button>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid gap-6 lg:grid-cols-2">
                <div class="bg-white shadow-sm sm:rounded-lg p-4">
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left text-gray-600 border-b">
                                    <th class="py-2">名前</th>
                                    <th class="py-2">電話</th>
                                    <th class="py-2">メモ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($customers as $customer)
                                    <tr class="border-b">
                                        <td class="py-2">
                                            <a class="text-indigo-700 hover:underline"
                                                href="{{ route('dashboard.customers.index', ['customer_id' => $customer->id]) }}">
                                                {{ $customer->name }}
                                            </a>
                                        </td>
                                        <td class="py-2">{{ $customer->phone }}</td>
                                        <td class="py-2 text-gray-700">{{ $customer->memo }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $customers->links() }}
                    </div>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg p-4">
                    @if ($selectedCustomer)
                        <div class="flex items-center justify-between">
                            <h3 class="font-semibold text-gray-900">{{ $selectedCustomer->name }}</h3>
                            <button type="button" @click="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'customer-edit' }))"
                                class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                                編集
                            </button>
                        </div>

                        <div class="mt-3 grid gap-2 text-sm">
                            <div><span class="text-gray-600">電話：</span> {{ $selectedCustomer->phone }}</div>
                            <div><span class="text-gray-600">メモ：</span> {{ $selectedCustomer->memo }}</div>
                        </div>

                        <div class="mt-6">
                            <h4 class="font-semibold text-gray-900">予約履歴</h4>
                            <div class="mt-2 overflow-x-auto">
                                <table class="min-w-full text-sm">
                                    <thead>
                                        <tr class="text-left text-gray-600 border-b">
                                            <th class="py-2">日時</th>
                                            <th class="py-2">状態</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($selectedCustomer->reservations as $reservation)
                                            <tr class="border-b">
                                                <td class="py-2">
                                                    {{ $reservation->start_at->format('Y-m-d H:i') }}
                                                    -
                                                    {{ $reservation->end_at->format('H:i') }}
                                                </td>
                                                <td class="py-2">
                                                    @if ($reservation->status === 'booked')
                                                        予約
                                                    @elseif ($reservation->status === 'done')
                                                        完了
                                                    @else
                                                        キャンセル
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('dashboard.customers.destroy', $selectedCustomer) }}" class="mt-6">
                            @csrf
                            @method('DELETE')
                            <x-danger-button onclick="return confirm('削除しますか？')">削除</x-danger-button>
                        </form>
                    @else
                        <div class="text-sm text-gray-600">
                            左の一覧からお客様を選んでください。
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <x-modal name="customer-create" focusable>
        <form method="POST" action="{{ route('dashboard.customers.store') }}" class="p-6">
            @csrf
            <h2 class="text-lg font-medium text-gray-900">お客様の追加</h2>

            <div class="mt-4 space-y-4">
                <div>
                    <x-input-label for="create_name" :value="'名前'" />
                    <x-text-input id="create_name" name="name" type="text" class="mt-1 block w-full" required />
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                </div>

                <div>
                    <x-input-label for="create_phone" :value="'電話'" />
                    <x-text-input id="create_phone" name="phone" type="text" class="mt-1 block w-full" />
                </div>

                <div>
                    <x-input-label for="create_memo" :value="'メモ'" />
                    <textarea id="create_memo" name="memo" rows="4"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <x-primary-button>保存</x-primary-button>
            </div>
        </form>
    </x-modal>

    @if ($selectedCustomer)
        <x-modal name="customer-edit" focusable>
            <form method="POST" action="{{ route('dashboard.customers.update', $selectedCustomer) }}" class="p-6">
                @csrf
                @method('PATCH')
                <h2 class="text-lg font-medium text-gray-900">お客様の編集</h2>

                <div class="mt-4 space-y-4">
                    <div>
                        <x-input-label for="edit_name" :value="'名前'" />
                        <x-text-input id="edit_name" name="name" type="text" class="mt-1 block w-full"
                            :value="old('name', $selectedCustomer->name)" required />
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <div>
                        <x-input-label for="edit_phone" :value="'電話'" />
                        <x-text-input id="edit_phone" name="phone" type="text" class="mt-1 block w-full"
                            :value="old('phone', $selectedCustomer->phone)" />
                    </div>

                    <div>
                        <x-input-label for="edit_memo" :value="'メモ'" />
                        <textarea id="edit_memo" name="memo" rows="4"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('memo', $selectedCustomer->memo) }}</textarea>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <x-primary-button>保存</x-primary-button>
                </div>
            </form>
        </x-modal>
    @endif

</x-app-layout>
