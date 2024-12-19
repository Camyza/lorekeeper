<li class="list-group-item">
    <a class="card-title h5 collapse-title" data-toggle="collapse" href="#openPotionForm"> Use Potion</a>
    <div id="openPotionForm" class="collapse">
        {!! Form::hidden('tag', $tag->tag) !!}
        @php
            $stat = \App\Models\Stat\Stat::find($tag->getData()['stat_id']);
        @endphp
        <div class="alert alert-info mt-2">
            This potion will
            {{ $tag->getData()['type'] == 'multiply' ? 'multiply the character\'s current ' . $stat->name . ' by ' . $tag->getData()['value'] : $tag->getData()['type'] . ' ' . $tag->getData()['value'] . ' to character\'s ' . $stat->name . ' value' }}.
        </div>
        <div class="alert alert-danger mt-2">
            All stats are capped at the character's max stat value. Be sure to double check what potion you are using on each character.
        </div>
        <p>This action is not reversible. Are you sure you want to use this item?</p>
        {{-- select character --}}
        <div class="form-group">
            {!! Form::label('Character') !!}
            {!! Form::select('character_potion_id', $stack->first()->user->characters()->get()->pluck('fullName', 'id')->toArray(), null, ['class' => 'form-control', 'placeholder' => 'Select a Character']) !!}
        </div>
        <div class="text-right">
            {!! Form::button('Use', ['class' => 'btn btn-primary', 'name' => 'action', 'value' => 'act', 'type' => 'submit']) !!}
        </div>
    </div>
</li>
