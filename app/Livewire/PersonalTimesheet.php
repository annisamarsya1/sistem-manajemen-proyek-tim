<?php

namespace App\Livewire;

use App\Models\TimeLog;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Personal Timesheet')]
#[Layout('layouts.app', ['title' => 'Personal Timesheet'])]
class PersonalTimesheet extends Component
{
    use WithPagination;

    public string $filterStart = '';

    public string $filterEnd = '';

    public function mount(): void
    {
        $this->filterStart = now()->subDays(30)->format('Y-m-d');
        $this->filterEnd = now()->format('Y-m-d');
    }

    // ---------------------------------------------------------------------------
    // Typed auth helper
    // ---------------------------------------------------------------------------

    private function currentUser(): User
    {
        $user = Auth::user();

        assert($user instanceof User);

        return $user;
    }

    // ---------------------------------------------------------------------------
    // Summary: total approved hours this week
    // ---------------------------------------------------------------------------

    public function getWeekHoursProperty(): float
    {
        $weekStart = now()->startOfWeek()->format('Y-m-d');

        return (float) TimeLog::where('user_id', Auth::id())
            ->where('status', 'approved')
            ->whereDate('start_time', '>=', $weekStart)
            ->sum('duration_hours');
    }

    // ---------------------------------------------------------------------------
    // Summary: total approved hours this month
    // ---------------------------------------------------------------------------

    public function getMonthHoursProperty(): float
    {
        $monthStart = now()->startOfMonth()->format('Y-m-d');

        return (float) TimeLog::where('user_id', Auth::id())
            ->where('status', 'approved')
            ->whereDate('start_time', '>=', $monthStart)
            ->sum('duration_hours');
    }

    // ---------------------------------------------------------------------------
    // Paginated time logs with filters
    // ---------------------------------------------------------------------------

    public function getTimeLogsProperty(): LengthAwarePaginator
    {
        return TimeLog::with(['project', 'task'])
            ->where('user_id', Auth::id())
            ->when($this->filterStart, fn ($q) => $q->whereDate('start_time', '>=', $this->filterStart))
            ->when($this->filterEnd, fn ($q) => $q->whereDate('start_time', '<=', $this->filterEnd))
            ->orderBy('start_time', 'desc')
            ->paginate(20);
    }

    // ---------------------------------------------------------------------------
    // Reset pagination on filter change
    // ---------------------------------------------------------------------------

    public function updatedFilterStart(): void
    {
        $this->resetPage();
    }

    public function updatedFilterEnd(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        return view('livewire.personal-timesheet', [
            'weekHours' => $this->weekHours,
            'monthHours' => $this->monthHours,
            'timeLogs' => $this->timeLogs,
        ]);
    }
}
