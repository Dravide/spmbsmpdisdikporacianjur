<?php

namespace App\Livewire\Admin;

use App\Models\Berkas;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Atur Form Input Berkas')]
class DataBerkasField extends Component
{
    public $berkas;

    public $formFields = [];

    public function mount($id)
    {
        $this->berkas = Berkas::findOrFail($id);
        $this->formFields = $this->berkas->form_fields ?? [];
    }

    protected $rules = [
        'formFields.*.label' => 'required|string',
        'formFields.*.name' => 'required|string|alpha_dash',
        'formFields.*.type' => 'required|in:text,number,select,textarea',
        'formFields.*.group' => 'nullable|string', // New Grouping Property
        'formFields.*.required' => 'boolean',
    ];

    public function addField()
    {
        $this->formFields[] = [
            'label' => '',
            'name' => '',
            'type' => 'number', // Default relevant for nilai
            'group' => '',
            'required' => true,
        ];
    }

    public function removeField($index)
    {
        unset($this->formFields[$index]);
        $this->formFields = array_values($this->formFields);
    }

    public function duplicateField($index)
    {
        if (isset($this->formFields[$index])) {
            $field = $this->formFields[$index];
            $field['label'] .= ' (Copy)';
            $field['name'] .= '_copy_'.rand(100, 999); // Ensure uniqueness

            // Insert after current index
            array_splice($this->formFields, $index + 1, 0, [$field]);
        }
    }

    public function save()
    {
        $this->validate();

        $this->berkas->update([
            'form_fields' => count($this->formFields) > 0 ? $this->formFields : null,
        ]);

        session()->flash('message', 'Konfigurasi form berhasil disimpan.');
    }

    public function render()
    {
        return view('livewire.admin.data-berkas-field');
    }
}
