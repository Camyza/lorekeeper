@extends('home.layout')

@section('home-title')
    Armoury
@endsection

@section('home-content')
    {!! breadcrumbs(['Armoury' => 'armoury']) !!}

    <h1>
        Armoury
    </h1>
    <p>This is your armoury. Click on any stack to view more details and actions you can perform on it.</p>

    <div class="card mb-3">
        <div class="card-header h3" data-toggle="collapse" data-target="#gear">
            Gear
        </div>
        <div class="card-body collapse" id="gear">
            @foreach ($gears as $categoryId => $categoryGears)
                <div class="card mb-3 inventory-category">
                    <h5 class="card-header inventory-header">
                        {!! isset($categories[$categoryId]) ? '<a href="' . $categories[$categoryId]->searchUrl . '">' . $categories[$categoryId]->name . '</a>' : 'Miscellaneous' !!}
                    </h5>
                    <div class="card-body inventory-body">
                        @foreach ($categoryGears->chunk(4) as $chunk)
                            <div class="row mb-3">
                                @foreach ($chunk as $gearId => $stack)
                                    <div class="col-sm-3 col-6 text-center inventory-item" data-id="{{ $stack->pivot->id }}" data-name="{{ $user->name }}'s {{ $stack->name }}">
                                        <div class="mb-1">
                                            <a href="#" class="inventory-gear">
                                                @if ($stack->pivot->has_image)
                                                    <img class="rounded" src="{{ $stack->getStackImageUrl($stack->pivot->id) }}" data-toggle="tooltip" title="{{ $stack->name }}" />
                                                @elseif($stack->imageUrl)
                                                    <img class="rounded" src="{{ $stack->imageUrl }}" data-toggle="tooltip" title="{{ $stack->name }}" />
                                                @endif
                                            </a>
                                        </div>
                                        <div>
                                            <a href="#" class="inventory-gear inventory-gear-name">
                                                {!! $stack->displayName !!}
                                            </a>
                                            @if ($stack->pivot->has_image)
                                                <i class="fas fa-image" data-toggle="tooltip" title="This gear has a unique image."></i>
                                            @endif
                                            @if ($stack->pivot->character_id)
                                                <p class="small mb-0">Attached to {!! getDisplayName(\App\Models\Character\Character::class, $stack->pivot->character_id) !!}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
            <div class="text-right mb-4">
                <a href="{{ url(Auth::user()->url . '/gear-logs') }}">View logs...</a>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header h3" data-toggle="collapse" data-target="#weapons">
            Weapons
        </div>
        <div class="card-body collapse" id="weapons">
            @foreach ($weapons as $categoryId => $categoryWeapons)
                <div class="card mb-3 inventory-category">
                    <h5 class="card-header inventory-header">
                        {!! isset($categories[$categoryId]) ? '<a href="' . $categories[$categoryId]->searchUrl . '">' . $categories[$categoryId]->name . '</a>' : 'Miscellaneous' !!}
                    </h5>
                    <div class="card-body inventory-body">
                        @foreach ($categoryWeapons->chunk(4) as $chunk)
                            <div class="row mb-3">
                                @foreach ($chunk as $weaponId => $stack)
                                    <div class="col-sm-3 col-6 text-center inventory-item" data-id="{{ $stack->pivot->id }}" data-name="{{ $user->name }}'s {{ $stack->name }}">
                                        <div class="mb-1">
                                            <a href="#" class="inventory-weapon">
                                                @if ($stack->pivot->has_image)
                                                    <img class="rounded" src="{{ $stack->getStackImageUrl($stack->pivot->id) }}" data-toggle="tooltip" title="{{ $stack->name }}" />
                                                @elseif($stack->imageUrl)
                                                    <img class="rounded" src="{{ $stack->imageUrl }}" data-toggle="tooltip" title="{{ $stack->name }}" />
                                                @endif
                                            </a>
                                        </div>
                                        <div>
                                            <a href="#" class="inventory-weapon inventory-weapon-name">
                                                {!! $stack->displayName !!}
                                            </a>
                                            @if ($stack->pivot->has_image)
                                                <i class="fas fa-image" data-toggle="tooltip" title="This weapon has a unique image."></i>
                                            @endif
                                            @if ($stack->pivot->character_id)
                                                <p class="small mb-0">Attached to {!! getDisplayName(\App\Models\Character\Character::class, $stack->pivot->character_id) !!}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
            <div class="text-right mb-4">
                <a href="{{ url(Auth::user()->url . '/weapon-logs') }}">View logs...</a>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            $('.inventory-gear').on('click', function(e) {
                e.preventDefault();
                var $parent = $(this).parent().parent();
                loadModal("{{ url('armoury/gear') }}/" + $parent.data('id'), $parent.data('name'));
            });
            $('.inventory-weapon').on('click', function(e) {
                e.preventDefault();
                var $parent = $(this).parent().parent();
                loadModal("{{ url('armoury/weapons') }}/" + $parent.data('id'), $parent.data('name'));
            });
        });
    </script>
@endsection
