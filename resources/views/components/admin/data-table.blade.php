@props([
    'paginator' => null,
    'infoKey' => null,
    'showInfo' => true,
    'cardClass' => '',
    'tableClass' => '',
])

<div class="card border-0 shadow-sm rounded-4 overflow-hidden {{ $cardClass }}">
    <div class="card-body p-0">
        <table class="table table-accent mb-0 {{ $tableClass }}">
            @isset($thead)
                <thead>
                    {{ $thead }}

                    @isset($search)
                        <tr class="table-search-row">
                            {{ $search }}
                        </tr>
                    @endisset
                </thead>
            @endisset

            <tbody>
                {{ $slot }}
            </tbody>
        </table>

        @if ($paginator)
            <x-table-pagination :paginator="$paginator" :info-key="$infoKey" :show-info="$showInfo" />
        @endif
    </div>
</div>
