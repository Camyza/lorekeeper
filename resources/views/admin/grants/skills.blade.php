@extends('admin.layout')

@section('admin-title')
    Grant Skills
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Grant Skills' => 'admin/grants/skills']) !!}

    <h1>Grant Skills</h1>

    {!! Form::open(['url' => 'admin/grants/skills']) !!}

    <h3>Basic Information</h3>

    <div class="form-group">
        {!! Form::label('character_ids', 'Character(s)') !!} {!! add_help('You can select up to 10 characters at once.') !!}
        {!! Form::select('character_ids[]', $characters, null, ['id' => 'usernameList', 'class' => 'form-control', 'multiple']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('Skill(s)') !!} {!! add_help('Must have at least 1 skill and Quantity must be at least 1.') !!}
        <div id="skillList">
            <div class="d-flex mb-2">
                {!! Form::select('skill_ids[]', $skills, null, ['class' => 'form-control mr-2 default skill-select', 'placeholder' => 'Select Skill']) !!}
                {!! Form::text('quantities[]', 1, ['class' => 'form-control mr-2', 'placeholder' => 'Quantity']) !!}
                <a href="#" class="remove-skill btn btn-danger mb-2 disabled">×</a>
            </div>
        </div>
        <div><a href="#" class="btn btn-primary" id="add-skill">Add Skill</a></div>
        <div class="skill-row hide mb-2">
            {!! Form::select('skill_ids[]', $skills, null, ['class' => 'form-control mr-2 skill-select', 'placeholder' => 'Select Skill']) !!}
            {!! Form::text('quantities[]', 1, ['class' => 'form-control mr-2', 'placeholder' => 'Quantity']) !!}
            <a href="#" class="remove-skill btn btn-danger mb-2">×</a>
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('data', 'Reason (Optional)') !!} {!! add_help('A reason for the grant. This will be noted in the logs.') !!}
        {!! Form::text('data', null, ['class' => 'form-control', 'maxlength' => 400]) !!}
    </div>

    <div class="text-right">
        {!! Form::submit('Submit', ['class' => 'btn btn-primary']) !!}
    </div>

    {!! Form::close() !!}

    <script>
        $(document).ready(function() {
            $('#usernameList').selectize({
                maxSkills: 10
            });
            $('.default.skill-select').selectize();
            $('#add-skill').on('click', function(e) {
                e.preventDefault();
                addSkillRow();
            });
            $('.remove-skill').on('click', function(e) {
                e.preventDefault();
                removeSkillRow($(this));
            })

            function addSkillRow() {
                var $rows = $("#skillList > div")
                if ($rows.length === 1) {
                    $rows.find('.remove-skill').removeClass('disabled')
                }
                var $clone = $('.skill-row').clone();
                $('#skillList').append($clone);
                $clone.removeClass('hide skill-row');
                $clone.addClass('d-flex');
                $clone.find('.remove-skill').on('click', function(e) {
                    e.preventDefault();
                    removeSkillRow($(this));
                })
                $clone.find('.skill-select').selectize();
            }

            function removeSkillRow($trigger) {
                $trigger.parent().remove();
                var $rows = $("#skillList > div")
                if ($rows.length === 1) {
                    $rows.find('.remove-skill').addClass('disabled')
                }
            }
        });
    </script>
@endsection
