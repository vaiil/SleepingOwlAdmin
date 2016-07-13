@if($isOrderable && $orderURL)
    <a href="{{ $orderURL }}">
        {!! $title !!}

        @if($isOrdered)
            @if($direction == 'asc')
                <span class="fa fa-sort-asc"></span>
            @elseif($direction == 'desc')
                <span class="fa fa-sort-desc"></span>
            @endif
        @endif
    </a>
@else
    {!! $title !!}
@endif