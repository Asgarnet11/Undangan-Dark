@props(['guest' => null])

@php
    $seating = $guest?->visibleSeatingInfo();
@endphp

@if($seating !== null)
    <div
        class="shared-seating-info"
        data-seating-info
        style="margin-top: 1rem; padding: 0.9rem 1rem; border-radius: 10px; border: 1px solid var(--inv-border, rgba(128, 128, 128, 0.25)); background: var(--inv-surface, rgba(128, 128, 128, 0.08)); text-align: center;"
    >
        <p style="font-size: 0.82rem; font-weight: 600; letter-spacing: 0.08em; text-transform: uppercase; opacity: 0.8; margin-bottom: 0.35rem;">
            Informasi Tempat Duduk
        </p>
        @if($seating['assigned'])
            <p style="font-size: 1rem; font-weight: 600;">
                Meja: {{ $seating['table_name'] }}
                @if($seating['seat_label'])
                    &bull; Kursi: {{ $seating['seat_label'] }}
                @endif
            </p>
        @else
            <p style="font-size: 0.9rem; opacity: 0.85;">
                {{ $seating['unassigned_note'] }}
            </p>
        @endif
    </div>
@endif
