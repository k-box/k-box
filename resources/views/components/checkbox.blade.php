<label class="inline-flex items-center">
    <input type="checkbox" class="form-checkbox" name="{{ $name ?? 'checkbox' }}"  {{ isset($checked) && $checked ? 'checked' : '' }}>
    <span class="ml-2">{{ $slot }}</span>
</label>