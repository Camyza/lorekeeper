@extends('world.layout')

@section('title')
    Stats
@endsection

@section('content')
    {!! breadcrumbs(['Encyclopedia' => 'world', 'Stats' => 'world/stats', $stat->name => 'stats/' . $stat->abbreviation]) !!}

    <div class="card mb-3">
        <div class="card-body">
            @include('world._stat_entry', ['stat' => $stat])
        </div>
    </div>
@endsection
