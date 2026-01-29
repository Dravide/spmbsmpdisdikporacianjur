@props(['field', 'berkasId', 'index', 'compact' => false, 'disabled' => false])

<div class="{{ $compact ? '' : 'mb-3' }}">
    <label class="form-label small mb-1 d-block text-truncate" title="{{ $field['label'] ?? $field['name'] }}">
        {{ $field['label'] ?? $field['name'] }}
        @if(!empty($field['required'])) <span class="text-danger">*</span> @endif
    </label>

    @php
        $fieldType = $field['type'] ?? 'text';
        $fieldName = $field['name'] ?? 'field_' . $index;
        $modelPath = "uploadedBerkasData.{$berkasId}.{$fieldName}";
    @endphp

    @if($fieldType == 'textarea')
        <textarea class="form-control form-control-sm" wire:model="{{ $modelPath }}" rows="{{ $compact ? 1 : 2 }}"
            placeholder="{{ $field['label'] ?? '' }}" {{ $disabled ? 'disabled' : '' }}></textarea>
    @elseif($fieldType == 'number')
        <input type="number" class="form-control form-control-sm" wire:model="{{ $modelPath }}"
            placeholder="{{ $field['label'] ?? '' }}" {{ $disabled ? 'disabled' : '' }}>
    @else
        <input type="text" class="form-control form-control-sm" wire:model="{{ $modelPath }}"
            placeholder="{{ $field['label'] ?? '' }}" {{ $disabled ? 'disabled' : '' }}>
    @endif
</div>