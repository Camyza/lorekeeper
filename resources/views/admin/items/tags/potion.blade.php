<h1>Potion Settings</h1>

<h3>Basic Information</h3>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('Stat Modified') !!}
            {!! Form::select('stat_id', $stats, $tag->getData()['stat_id'], ['class' => 'form-control', 'placeholder' => 'Select a Stat']) !!}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('Modifier Type') !!}
            {!! Form::select('type', ['add' => 'Add', 'subtract' => 'Subtract', 'multiply' => 'Multiply'], $tag->getData()['type'], ['class' => 'form-control', 'placeholder' => 'Select a Modifier']) !!}
        </div>
    </div>
</div>

<div class="form-group">
    {!! Form::label('Modifier Value') !!}
    {!! Form::number('value', $tag->getData()['value'], ['class' => 'form-control']) !!}
</div>
