@extends('world.layout')

@section('title')
    Stats
@endsection

@section('content')
    {!! breadcrumbs(['Encyclopedia' => 'world', 'Stats' => 'world/stats']) !!}

    <h1>Stats</h1>

    <div>
        {!! Form::open(['method' => 'GET', 'class' => '']) !!}
        <div class="form-inline justify-content-end">
            <div class="form-group ml-3 mb-3">
                {!! Form::text('name', Request::get('name'), ['class' => 'form-control', 'placeholder' => 'Name']) !!}
            </div>
            <div class="form-group ml-3 mb-3">
                {!! Form::submit('Search', ['class' => 'btn btn-primary']) !!}
            </div>
        </div>
        {!! Form::close() !!}
    </div>

    {!! $stats->render() !!}
    @foreach ($stats as $stat)
        <div class="card mb-3">
            <div class="card-body">
                @include('world._stat_entry', ['stat' => $stat])
            </div>
        </div>
    @endforeach
    {!! $stats->render() !!}
@endsection
