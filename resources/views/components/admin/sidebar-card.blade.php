@props([
    'route' => '#',
    'icon' => 'fa-circle',
    'label' => '',
    'color' => 'adobe',
    'dropdown' => false,
    'dropdownId' => null,
    'small' => false,
    'showArrow' => false,
])

@php
    $resolvedDropdownId = $dropdownId ?: 'sidebar-card-dropdown-' . md5($label . $route);

    $wrapperClasses = $small ? 'property-subcard text-decoration-none' : 'text-decoration-none';

    $cardClasses = 'card border-0 shadow-sm sidebar-card transition';
    $cardClasses .= $small ? ' property-subcard--small' : '';
    $cardClasses .= $dropdown ? ' sidebar-card--split' : '';

    $bodyClasses = $small || $showArrow
        ? 'card-body d-flex align-items-center justify-content-between gap-2 p-2'
        : 'card-body d-flex align-items-center gap-3 p-2';

    $iconClasses = 'icon-box';
    $iconClasses .= $small ? ' icon-box--sm' : '';
    $iconClasses .= ' bg-' . $color . '-soft text-' . $color;

    $labelClasses = $small ? 'small fw-semibold text-dark mb-0' : 'fw-semibold text-dark';
    $leftGapClass = $small ? 'gap-2' : 'gap-3';
@endphp

@if ($dropdown)
    <div class="{{ $cardClasses }}">
        <div class="card-body d-flex align-items-center justify-content-between p-2">
            <a href="{{ $route }}" class="sidebar-card__main-link text-decoration-none d-flex align-items-center {{ $leftGapClass }}">
                <div class="{{ $iconClasses }}">
                    <i class="fa-solid {{ $icon }}"></i>
                </div>
                <span class="{{ $labelClasses }}">
                    {{ $label }}
                </span>
            </a>

            <button class="sidebar-card__toggle collapsed" type="button" data-bs-toggle="collapse"
                data-bs-target="#{{ $resolvedDropdownId }}" aria-expanded="false"
                aria-controls="{{ $resolvedDropdownId }}">
                <i class="fa-solid fa-chevron-down"></i>
            </button>
        </div>
    </div>
@else
    <a href="{{ $route }}" class="{{ $wrapperClasses }}">
        <div class="{{ $cardClasses }}">
            <div class="{{ $bodyClasses }}">
                <div class="d-flex align-items-center {{ $leftGapClass }}">
                    <div class="{{ $iconClasses }}">
                        <i class="fa-solid {{ $icon }}"></i>
                    </div>
                    <span class="{{ $labelClasses }}">
                        {{ $label }}
                    </span>
                </div>

                @if ($showArrow)
                    <i class="fa-solid fa-chevron-right property-subcard__arrow"></i>
                @endif
            </div>
        </div>
    </a>
@endif
