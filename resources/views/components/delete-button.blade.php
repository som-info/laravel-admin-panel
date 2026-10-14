@props(['action'])
@can('delete-records')
<form method="POST" action="{{ $action }}" onsubmit="return confirm('{{ __('Are you sure?') }}')" class="inline">
    @csrf @method('DELETE')
    <button class="text-sm font-medium text-rose-600 hover:text-rose-800">{{ __('Delete') }}</button>
</form>
@endcan
