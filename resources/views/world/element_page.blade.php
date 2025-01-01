@extends('world.layout')

@section('title')
    {{ $element->name }}
@endsection

@section('meta-img')
    {{ $element->imageUrl }}
@endsection

@section('content')
    <x-admin-edit title="Element" :object="$element" />
    {!! breadcrumbs(['World' => 'world', 'Elements' => 'world/elements', $element->name => $element->idUrl]) !!}

    <div class="row">
        <div class="col-sm">
        </div>
        <div class="col-lg-6 col-lg-10">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row world-entry">
                        @if ($element->imageUrl)
                            <div class="col-md-3 world-entry-image"><a href="{{ $element->imageUrl }}" data-lightbox="entry" data-title="{{ $element->name }}">
                                    <img src="{{ $element->imageUrl }}" class="world-entry-image" alt="{{ $element->name }}" /></a>
                            </div>
                        @endif
                        <div class="{{ $element->imageUrl ? 'col-md-9' : 'col-12' }}">
                            <h1>
                                @if (!$element->is_visible)
                                    <i class="fas fa-eye-slash mr-1"></i>
                                @endif
                                {!! $element->name !!}
                            </h1>
                            <div class="world-entry-text">
                                {!! $element->parsed_description !!}
                                <h5>Weaknesses</h5>
                                @if ($element->weaknesses->count())
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th width="70%">Name</th>
                                                <th width="30%">Multiplier</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($element->weaknesses as $weakness)
                                                <tr>
                                                    <td>{!! $weakness->weakness->displayName !!}</td>
                                                    <td>x{{ $weakness->multiplier }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <p>No Weaknesses Found</p>
                                @endif
                                <h5>Immunities</h5>
                                @if ($element->immunities->count())
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($element->immunities as $immunity)
                                                <tr>
                                                    <td>{!! $immunity->immunity->displayName !!}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <p>No Immunities Found</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm">
        </div>
    </div>
@endsection
