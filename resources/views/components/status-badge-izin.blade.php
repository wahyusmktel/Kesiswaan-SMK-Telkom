@props(['status'])

@php
$classes = [
    'menunggu' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
    'disetujui' => 'bg-green-100 text-green-800 border-green-200',
    'ditolak' => 'bg-red-100 text-red-800 border-red-200',
][$status] ?? 'bg-gray-100 text-gray-800 border-gray-200';

$label = [
    'menunggu' => 'Menunggu',
    'disetujui' => 'Setuju',
    'ditolak' => 'Tolak',
][$status] ?? ucfirst($status);
@endphp

<span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black border {{ $classes }} uppercase tracking-tighter">
    {{ $label }}
</span>
