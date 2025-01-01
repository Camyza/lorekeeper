@extends('user.layout')

@section('profile-title')
    {{ $user->name }}'s EXP Logs
@endsection

@section('profile-content')
    {!! breadcrumbs(['Users' => 'users', $user->name => $user->url, 'Stat Information' => $user->url . '/stats', 'Stat Logs' => $user->url . '/stats/logs']) !!}

    <h1>
        {!! $user->displayName !!}'s Stat Logs
    </h1>

    {!! $logs->render() !!}
    <table class="table table-sm">
        <thead>
            <th>Sender</th>
            <th>Recipient</th>
            <th>Quantity</th>
            <th>Log</th>
            <th>Date</th>
        </thead>
        <tbody>
            @foreach ($logs as $log)
                @include('user._stat_log_row', ['stat' => $log, 'owner' => $user])
            @endforeach
        </tbody>
    </table>
    {!! $logs->render() !!}
@endsection
