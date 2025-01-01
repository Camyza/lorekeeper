@php
    // This file represents a common source and definition for assets used in loot_select
    // While it is not per se as tidy as defining these in the controller(s),
    // doing so this way enables better compatibility across disparate extensions
    $items = \App\Models\Item\Item::orderBy('name')->pluck('name', 'id');
    $currencies = \App\Models\Currency\Currency::where('is_user_owned', 1)->orderBy('name')->pluck('name', 'id');
@endphp

<div id="limitRowData" class="hide">
    <table class="table table-sm">
        <tbody id="limitRow">
            <tr class="limit-row">
                <td>{!! Form::select('limit_type[]', ['Item' => 'Item', 'Currency' => 'Currency'], null, ['class' => 'form-control limit-type', 'placeholder' => 'Select limit Type']) !!}</td>
                <td class="limit-row-select"></td>
                <td>{!! Form::text('limit_quantity[]', 1, ['class' => 'form-control']) !!}</td>
                <td class="text-right"><a href="#" class="btn btn-danger remove-limit-button">Remove</a></td>
            </tr>
        </tbody>
    </table>
    {!! Form::select('limit_id[]', $items, null, ['class' => 'form-control limit-item-select', 'placeholder' => 'Select Item']) !!}
    {!! Form::select('limit_id[]', $currencies, null, ['class' => 'form-control limit-currency-select', 'placeholder' => 'Select Currency']) !!}
</div>
