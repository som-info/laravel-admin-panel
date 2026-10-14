@props(['name', 'label', 'type' => 'text', 'value' => null, 'required' => false, 'options' => null, 'hint' => null])
@php $id = str_replace(['[', ']', '.'], '_', $name); $err = $errors->first(str_replace(['[', ']'], ['.', ''], $name)); @endphp
<div {{ $attributes->only('class') }}>
    <label for="{{ $id }}" class="mb-1.5 block text-sm font-medium text-slate-700">{{ $label }} @if($required)<span class="text-rose-500">*</span>@endif</label>
    @if ($options !== null)
        <select id="{{ $id }}" name="{{ $name }}" class="input {{ $err ? 'input-error' : '' }}" @required($required)>
            @foreach ($options as $key => $text)
                <option value="{{ $key }}" @selected((string) old($name, $value) === (string) $key)>{{ $text }}</option>
            @endforeach
        </select>
    @elseif ($type === 'textarea')
        <textarea id="{{ $id }}" name="{{ $name }}" rows="3" class="input {{ $err ? 'input-error' : '' }}">{{ old($name, $value) }}</textarea>
    @else
        <input id="{{ $id }}" type="{{ $type }}" name="{{ $name }}" value="{{ old($name, $value) }}" class="input {{ $err ? 'input-error' : '' }}" @required($required) {{ $attributes->except('class') }}>
    @endif
    @if ($err)
        <p class="mt-1 text-xs text-rose-600">{{ $err }}</p>
    @elseif ($hint)
        <p class="mt-1 text-xs text-slate-400">{{ $hint }}</p>
    @endif
</div>
