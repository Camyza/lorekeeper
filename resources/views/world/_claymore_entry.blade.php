{{-- for use with gear or weapons --}}
<div class="row world-entry">
    @if ($imageUrl)
        <div class="col-md-3 world-entry-image"><a href="{{ $imageUrl }}" data-lightbox="entry" data-title="{{ $name }}"><img src="{{ $imageUrl }}" class="world-entry-image" /></a></div>
    @endif
    <div class="{{ $imageUrl ? 'col-md-9' : 'col-12' }}">
        @if (isset($edit))
            <x-admin-edit title="{{ $edit['title'] }}" :object="$edit['object']" />
        @endif
        <h3>
            @if (!$visible)
                <i class="fa fa-eye-slash mr-1"></i>
            @endif
            {!! $name !!}
            @if ($item?->category)
                ({!! $item->category->displayName !!})
            @endif
            @if (isset($idUrl) && $idUrl)
                <a href="{{ $idUrl }}" class="world-entry-search text-muted"><i class="fas fa-search"></i></a>
            @endif
        </h3>
        <div class="world-entry-text">
            @if ($item)
                <h5>@include('world._typing', ['object' => $item])</h5>
                <div class="row">
                    <div class="col-6">
                        <h5>Parent</h5>
                        <div class="row">
                            @if ($item->parent_id && $item->parent)
                                <div class="col-md">
                                    {!! $item->parent->displayName !!}
                                    @if ($item->cost && $item->currency_id <= 0)
                                        <small>
                                            (Upgrade costs {{ $item->cost }}
                                            @if ($item->currency_id != 0)
                                                <img src="{!! $item->currency->iconurl !!}">
                                                {!! $item->currency->displayName !!})
                                            @elseif($item->currency_id == 0)
                                                stat points.)
                                            @endif
                                        </small>
                                    @else
                                        <small>(No upgrade cost set.)</small>
                                    @endif
                                </div>
                            @else
                                <div class="col-md">No Parent.</div>
                            @endif
                        </div>
                    </div>
                    <div class="col-6">
                        <h5>Children</h5>
                        <div class="row">
                            @if ($item->children->count())
                                @foreach ($item->children as $child)
                                    <div class="col-md">
                                        {!! $child->displayName !!}
                                    </div>
                                @endforeach
                            @else
                                <div class="col-md">No Children.</div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
            <p>
                {!! $description !!}
            </p>
            @if (count($item->stats ?? []))
                <h4>Stats</h4>
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th width="70%">Stat</th>
                            <th width="30%">Bonus</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($item->stats as $stat)
                            <tr>
                                <td>{!! $stat->stat->name !!}</td>
                                <td>{{ $stat->count }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
