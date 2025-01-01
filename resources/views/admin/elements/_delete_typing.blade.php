@if ($typing)
    {!! Form::open(['url' => 'admin/typing/delete/' . $typing->id]) !!}

    <p>You are about to delete the typing for <strong>{!! $typing->object->displayName !!}</strong>. This is not reversible.</p>
    <p>Are you sure you want to delete <strong>{!! $typing->object->displayName !!}</strong>?</p>

    <div class="text-right">
        {!! Form::submit('Delete Typing', ['class' => 'btn btn-danger']) !!}
    </div>

    {!! Form::close() !!}
@else
    Invalid typing selected.
@endif
