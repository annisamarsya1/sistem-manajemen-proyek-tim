{{-- 
  Komponen: Status Badge
  Menampilkan label status berbentuk badge dengan warna sesuai string status.
  Mendukung status approval, project status, dan priority.
--}}
@props(['status'])

@php
$map = [
    'approved' => ['class' => 'bg-success-soft text-success', 'dot' => 'bg-success', 'label' => 'Approved'],
    'rejected' => ['class' => 'bg-danger-soft text-danger', 'dot' => 'bg-danger', 'label' => 'Rejected'],
    'pending'  => ['class' => 'bg-primary-soft text-primary', 'dot' => 'bg-primary', 'label' => 'Pending'],
    
    // Project Status
    'planning'  => ['class' => 'bg-subtle text-ink-secondary border border-border', 'dot' => 'bg-ink-secondary', 'label' => 'Planning'],
    'active'  => ['class' => 'bg-success-soft text-success', 'dot' => 'bg-success', 'label' => 'Active'],
    'on_hold'  => ['class' => 'bg-danger-soft text-danger', 'dot' => 'bg-danger', 'label' => 'On Hold'], 
    'completed'  => ['class' => 'bg-primary-soft text-primary', 'dot' => 'bg-primary', 'label' => 'Completed'],
    'cancelled'  => ['class' => 'bg-danger-soft text-danger', 'dot' => 'bg-danger', 'label' => 'Cancelled'],

    // Priorities
    'low' => ['class' => 'bg-subtle text-ink-secondary border border-border', 'label' => 'Low'],
    'medium' => ['class' => 'bg-primary-soft text-primary', 'label' => 'Medium'],
    'high' => ['class' => 'bg-danger-soft text-danger', 'label' => 'High'],
    'urgent' => ['class' => 'bg-danger text-white', 'label' => 'Urgent'],
];

$config = $map[strtolower($status)] ?? ['class' => 'bg-subtle text-ink-secondary', 'label' => ucfirst($status)];
@endphp

<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium {{ $config['class'] }}">
    @if(isset($config['dot']))
        <span class="w-1.5 h-1.5 rounded-full {{ $config['dot'] }}"></span>
    @endif
    {{ $config['label'] }}
</span>
