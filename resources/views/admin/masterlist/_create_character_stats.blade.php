@foreach ($stats as $stat)
    <div class="form-group">
        @php
            $base = null;
            if ($subtype_id) {
                $base = $stat->hasBaseValue('subtype', $subtype_id) ? $stat->hasBaseValue('subtype', $subtype_id) : null;
            }

            if (!$base && $species_id) {
                $base = $stat->hasBaseValue('species', $species_id) ? $stat->hasBaseValue('species', $species_id) : null;
            }

            $base = $base ? $base : $stat->base;
        @endphp
        {!! Form::label($stat->name) !!} {!! $stat->displayLimits(true) ? '<b>(Limited to: ' . $stat->displayLimits(true) . ')</b>' : '' !!}
        {!! Form::number('stats[' . $stat->id . ']', $base, ['class' => 'form-control']) !!}
    </div>
@endforeach
