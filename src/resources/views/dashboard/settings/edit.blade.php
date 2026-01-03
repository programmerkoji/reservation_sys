<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            設定
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-4">
                <form method="POST" action="{{ route('dashboard.settings.update') }}">
                    @csrf
                    @method('PATCH')

                    <div class="space-y-6">
                        <div>
                            <div class="text-sm font-medium text-gray-700">営業日</div>
                            <div class="mt-2 grid grid-cols-4 gap-2 text-sm">
                                @php
                                    $labels = [
                                        0 => '日',
                                        1 => '月',
                                        2 => '火',
                                        3 => '水',
                                        4 => '木',
                                        5 => '金',
                                        6 => '土',
                                    ];
                                    $open = old('open_weekdays', $setting->open_weekdays);
                                @endphp
                                @foreach ($labels as $key => $label)
                                    <label class="inline-flex items-center gap-2">
                                        <input type="checkbox" name="open_weekdays[]" value="{{ $key }}"
                                            @checked(in_array($key, $open, true))
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                        <span>{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('open_weekdays')" />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="open_time" :value="'営業時間（開始）'" />
                                <x-text-input id="open_time" name="open_time" type="time" class="mt-1 block w-full"
                                    :value="old('open_time', $setting->open_time)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('open_time')" />
                            </div>

                            <div>
                                <x-input-label for="close_time" :value="'営業時間（終了）'" />
                                <x-text-input id="close_time" name="close_time" type="time" class="mt-1 block w-full"
                                    :value="old('close_time', $setting->close_time)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('close_time')" />
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <x-primary-button>保存</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

