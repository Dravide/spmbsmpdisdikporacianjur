<?php

namespace App\Livewire\Admin;

use App\Models\ActivityLog;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Log Aktivitas')]
class ActivityLogViewer extends Component
{
    use WithPagination;

    public $search = '';
    public $filterAction = '';
    public $filterType = '';
    public $filterDate = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterAction()
    {
        $this->resetPage();
    }

    public function updatingFilterType()
    {
        $this->resetPage();
    }

    public function updatingFilterDate()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->filterAction = '';
        $this->filterType = '';
        $this->filterDate = '';
        $this->resetPage();
    }

    public function render()
    {
        $logs = ActivityLog::query()
            ->with('causer')
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('description', 'like', '%' . $this->search . '%')
                        ->orWhere('ip_address', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterAction, function ($q) {
                $q->where('action', $this->filterAction);
            })
            ->when($this->filterType, function ($q) {
                $q->where('log_type', $this->filterType);
            })
            ->when($this->filterDate, function ($q) {
                $q->whereDate('created_at', $this->filterDate);
            })
            ->orderByDesc('created_at')
            ->paginate(20);

        $actionOptions = ActivityLog::distinct()->pluck('action')->toArray();
        $typeOptions = ActivityLog::distinct()->pluck('log_type')->toArray();

        return view('livewire.admin.activity-log-viewer', [
            'logs' => $logs,
            'actionOptions' => $actionOptions,
            'typeOptions' => $typeOptions,
        ]);
    }
}
