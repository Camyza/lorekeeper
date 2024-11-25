{!! Form::label('Select Stock:') !!}
{!! Form::select('item_id', ['random' => 'Random'] + $items->toArray(), $stock->item_id ?? null, ['class' => 'form-control selectize']) !!}


<script>
    $(document).ready(function() {
        $('.selectize').selectize();
    });
</script>
