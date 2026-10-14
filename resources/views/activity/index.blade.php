<x-layouts.app :title="__('Activity log')">
    <x-card class="overflow-hidden">
        <x-filters :placeholder="__('Search descriptions...')">
            <select name="action" class="input sm:w-36">
                <option value="">{{ __('All actions') }}</option>
                @foreach (['created', 'updated', 'deleted', 'login', 'logout'] as $a)
                    <option value="{{ $a }}" @selected(request('action') === $a)>{{ __('action.'.$a) }}</option>
                @endforeach
            </select>
            <select name="user" class="input sm:w-44">
                <option value="">{{ __('All users') }}</option>
                @foreach ($users as $u)
                    <option value="{{ $u->id }}" @selected(request('user') == $u->id)>{{ $u->name }}</option>
                @endforeach
            </select>
        </x-filters>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="table-head"><tr><th>{{ __('When') }}</th><th>{{ __('User') }}</th><th>{{ __('Action') }}</th><th>{{ __('Description') }}</th><th>IP</th></tr></thead>
                <tbody class="table-body">
                    @forelse ($logs as $log)
                        <tr>
                            <td class="text-slate-500"><span dir="ltr">{{ $log->created_at->format('Y-m-d H:i') }}</span></td>
                            <td>{{ $log->user?->name ?? __('System') }}</td>
                            <td><span class="rounded-md bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600">{{ __('action.'.$log->action) }}</span></td>
                            <td dir="auto">{{ $log->description }}</td>
                            <td class="font-mono text-xs text-slate-400">{{ $log->ip_address ?? '—' }}</td>
                        </tr>
                    @empty
                        <x-empty />
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-100 p-4">{{ $logs->links() }}</div>
    </x-card>
</x-layouts.app>
