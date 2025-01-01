@if ($element)
    {!! Form::open(['url' => 'admin/data/elements/delete/' . $element->id]) !!}

    <p>You are about to delete the element <strong>{!! $element->displayName !!}</strong>. This is not reversible.</p>
    <p>Are you sure you want to delete <strong>{!! $element->displayName !!}</strong>?</p>

    <div class="text-right">
        {!! Form::submit('Delete Element', ['class' => 'btn btn-danger']) !!}
    </div>

    {!! Form::close() !!}
@else
    Invalid element selected.
@endif
