<p>
    Potions by default can only apply one element a specific object, and by default can only apply to characters.
    <br />
    Potions can only apply elements to characters that have less than 2 elements.
</p>

<div class="form-group">
    {!! Form::label('Element') !!}
    {!! Form::select('element_id', $elements, $tag->getData()['element_id'], ['class' => 'form-control']) !!}
</div>
