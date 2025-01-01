@extends('admin.layout')

@section('admin-title')
    Elements
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Elements' => 'admin/data/elements']) !!}

    <h1>Elements</h1>

    <p>This is a list of elements in the game.</p>
    <p>Elements that are applied to an object are called "typings". You can add typings on existing objects by clicking the "Add Typing" button on the object's page.</p>

    <div class="text-right mb-3">
        <a class="btn btn-primary" href="{{ url('admin/data/elements/create') }}"><i class="fas fa-plus"></i> Create New Element</a>
    </div>

    <div>
        {!! Form::open(['method' => 'GET', 'class' => 'form-inline justify-content-end']) !!}
        <div class="form-group mr-3 mb-3">
            {!! Form::text('name', Request::get('name'), ['class' => 'form-control', 'placeholder' => 'Name']) !!}
        </div>
        <div class="form-group mb-3">
            {!! Form::submit('Search', ['class' => 'btn btn-primary']) !!}
        </div>
        {!! Form::close() !!}
    </div>

    @if (!count($elements))
        <p>No elements found.</p>
    @else
        {!! $elements->render() !!}
        <div class="mb-4 logs-table">
            <div class="logs-table-header">
                <div class="row">
                    <div class="col-2 col-md-2">
                        <div class="logs-table-cell">Name</div>
                    </div>
                    <div class="col-8 col-md-8">
                        <div class="logs-table-cell">Colour</div>
                    </div>
                </div>
            </div>
            <div class="logs-table-body">
                @foreach ($elements as $element)
                    <div class="logs-table-row">
                        <div class="row flex-wrap">
                            <div class="col-2 col-md-2">
                                <div class="logs-table-cell">
                                    @if (!$element->is_visible)
                                        <i class="fas fa-eye-slash mr-1"></i>
                                    @endif
                                    {{ $element->name }}
                                </div>
                            </div>
                            <div class="col-8 col-md-8">
                                <div class="logs-table-cell">
                                    {{-- make square of colour --}}
                                    <div style="background-color: {{ $element->colour }}; width: 20px; height: 20px; display: inline-block; vertical-align: middle; border-radius:3px;"></div>
                                    {{ $element->colour }}
                                </div>
                            </div>
                            <div class="col-2 col-md-2 text-right">
                                <div class="logs-table-cell">
                                    <a href="{{ url('admin/data/elements/edit/' . $element->id) }}" class="btn btn-primary py-0 px-2">Edit</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        {!! $elements->render() !!}
    @endif
@endsection
