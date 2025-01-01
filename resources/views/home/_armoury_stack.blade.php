@if (!$stack)
    <div class="text-center">Invalid equipment selected.</div>
@else
    <div class="text-center">
        <div class="mb-1"><a href="{{ $stack->equipment->url }}">
                <img src="{{ $stack->imageUrl }}" /></a>
        </div>
        <div class="mb-1"><a href="{{ $stack->equipment->url }}">{{ $stack->equipment->name }}</a></div>
    </div>

    @if (isset($stack->data['notes']) || isset($stack->data['data']))
        <div class="card mt-3">
            <ul class="list-group list-group-flush">
                @if (isset($stack->data['notes']))
                    <li class="list-group-item">
                        <h5 class="card-title">Notes</h5>
                        <div>{!! $stack->data['notes'] !!}</div>
                    </li>
                @endif
                @if (isset($stack->data['data']))
                    <li class="list-group-item">
                        <h5 class="card-title">Source</h5>
                        <div>{!! $stack->data['data'] !!}</div>
                    </li>
                @endif
            </ul>
        </div>
    @endif

    @if ($user && !$readOnly && ($stack->user_id == $user->id || $user->hasPower('edit_inventories')))
        <div class="card mt-3">
            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    @php
                        $now = Carbon\Carbon::parse($stack->attached_at);
                        $diff = $now->addDays(Settings::get('claymore_cooldown'));
                    @endphp
                    @if ($stack->character_id && $diff < Carbon\Carbon::now())
                        <a class="card-title h5 collapse-title" data-toggle="collapse" href="#attachForm">
                            @if ($stack->user_id != $user->id)
                                [ADMIN]
                            @endif Detach Equipment from Character
                        </a>
                        {!! Form::open(['url' => 'armoury/' . $type . '/detach/' . $stack->id, 'id' => 'attachForm', 'class' => 'collapse']) !!}
                        <p>This equipment is currently attached to {!! getDisplayName(\App\Models\Character\Character::class, $stack->character_id) !!}, do you want to detach them?</p>
                        <div class="text-right">
                            {!! Form::submit('Detach', ['class' => 'btn btn-primary']) !!}
                        </div>
                        {!! Form::close() !!}
                    @elseif(!$stack->character_id || $diff < Carbon\Carbon::now())
                        <a class="card-title h5 collapse-title" data-toggle="collapse" href="#attachForm">
                            @if ($stack->user_id != $user->id)
                                [ADMIN]
                            @endif Attach Equipment to Character
                        </a>
                        {!! Form::open(['url' => 'armoury/' . $type . '/attach/' . $stack->id, 'id' => 'attachForm', 'class' => 'collapse']) !!}
                        <p>Attach this equipment to a character you own! They'll appear on the character's page and any stat bonuses will automatically be applied.</p>
                        <p>Gears can be detached.</p>
                        <div class="form-group">
                            {!! Form::label('id', 'Slug') !!} {!! add_help('Insert your character\'s slug.') !!}
                            {!! Form::select('id', $chara, null, ['class' => 'form-control']) !!}
                        </div>
                        <div class="text-right">
                            {!! Form::submit('Attach', ['class' => 'btn btn-primary']) !!}
                        </div>
                        {!! Form::close() !!}
                    @else
                        <a class="card-title h5">You cannot currently attach / detach this equipment! It is under cooldown.</a>
                    @endif
                </li>
                @if ($stack->equipment->parent_id && $stack->equipment->cost && $stack->equipment->currency_id <= 0)
                    <li class="list-group-item">
                        <a class="card-title h5 collapse-title" data-toggle="collapse" href="#upgradeForm">
                            @if ($stack->user_id != $user->id)
                                [ADMIN]
                            @endif Upgrade Equipment
                        </a>
                        {!! Form::open(['url' => 'armoury/' . $type . '/upgrade/' . $stack->id, 'id' => 'upgradeForm', 'class' => 'collapse']) !!}
                        <p class="alert alert-info my-2">This equipment can be upgraded to {!! $stack->equipment->parent->displayName !!}!</p>
                        <p>Upgrade costs {{ $stack->equipment->cost }}
                            @if ($stack->equipment->currency_id != 0)
                                <img src="{!! $stack->equipment->currency->iconurl !!}"> {!! $stack->equipment->currency->displayName !!}.
                            @else
                                stat points.
                            @endif
                            The upgrade cannot be reversed.
                        </p>
                        <div class="text-right">
                            {!! Form::submit('Upgrade', ['class' => 'btn btn-primary']) !!}
                        </div>
                        {!! Form::close() !!}
                    </li>
                @endif
                @if ($stack->isTransferrable || $user->hasPower('edit_inventories'))
                    @if (!$stack->character_id)
                        <li class="list-group-item">
                            <a class="card-title h5 collapse-title" data-toggle="collapse" href="#transferForm">
                                @if ($stack->user_id != $user->id)
                                    [ADMIN]
                                @endif Transfer Equipment
                            </a>
                            {!! Form::open(['url' => 'armoury/' . $type . '/transfer/' . $stack->id, 'id' => 'transferForm', 'class' => 'collapse']) !!}
                            @if (!$stack->isTransferrable)
                                <p class="alert alert-warning my-2">This equipment is account-bound, but your rank allows you to transfer it to another user.</p>
                            @endif
                            <div class="form-group">
                                {!! Form::label('user_id', 'Recipient') !!} {!! add_help('You can only transfer gears to verified users.') !!}
                                {!! Form::select('user_id', $userOptions, null, ['class' => 'form-control']) !!}
                            </div>
                            <div class="text-right">
                                {!! Form::submit('Transfer', ['class' => 'btn btn-primary']) !!}
                            </div>
                            {!! Form::close() !!}
                        </li>
                    @else
                        <li class="list-group-item bg-light">
                            <h5 class="card-title mb-0 text-muted"><i class="fas fa-lock mr-2"></i> Currently Attached to a Character (Cannot Transfer)</h5>
                        </li>
                    @endif
                @else
                    <li class="list-group-item bg-light">
                        <h5 class="card-title mb-0 text-muted"><i class="fas fa-lock mr-2"></i> Account-bound</h5>
                    </li>
                @endif
                @if (Auth::user()->isStaff)
                    <li class="list-group-item">
                        <a class="card-title h5 collapse-title" data-toggle="collapse" href="#imageForm"> [ADMIN] Unique Image to {{ $displayType }}</a>
                        {!! Form::open(['url' => 'armoury/' . $type . '/image/' . $stack->id, 'files' => true, 'id' => 'imageForm', 'class' => 'collapse']) !!}
                        <p class="alert alert-info my-2">Give this equipment a unique image.</p>
                        <div>{!! Form::file('image') !!}</div>
                        <div class="text-muted">Recommended size: 100px x 100px</div>
                        @if ($stack->has_image)
                            <div class="form-check">
                                {!! Form::checkbox('remove_image', 1, false, ['class' => 'form-check-input']) !!}
                                {!! Form::label('remove_image', 'Remove current image', ['class' => 'form-check-label']) !!}
                            </div>
                        @endif
                        <div class="text-right">
                            {!! Form::submit('Upload', ['class' => 'btn btn-primary']) !!}
                        </div>
                        {!! Form::close() !!}
                    </li>
                @endif
                <li class="list-group-item">
                    <a class="card-title h5 collapse-title" data-toggle="collapse" href="#deleteForm">
                        @if ($stack->user_id != $user->id)
                            [ADMIN]
                        @endif Delete Equipment
                    </a>
                    {!! Form::open(['url' => 'armoury/' . $type . '/delete/' . $stack->id, 'id' => 'deleteForm', 'class' => 'collapse']) !!}
                    <p>This action is not reversible. Are you sure you want to delete this equipment?</p>
                    <div class="text-right">
                        {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                    </div>
                    {!! Form::close() !!}
                </li>
            </ul>
        </div>
    @endif
@endif
