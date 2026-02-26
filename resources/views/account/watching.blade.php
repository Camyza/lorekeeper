@extends('account.layout')

@section('account-title')
    Watching
@endsection

@section('account-content')
    {!! breadcrumbs(['My Account' => Auth::user()->url, 'Watching' => 'account/watching']) !!}

    <h1>Watching</h1>
    <div class="alert alert-info mb-4">
        <p>This page is where you can see gallery submissionss from users that you watch.  It will paginate when it hits 20 to make sure there is no lag if you follow a lot of people or someone who has a ton of art.  Watching someone doesn't alert them.  To unwatch someone, you must go to their profile.</p>
    </div>

    @if($submissions->count())
        <div class="d-flex align-content-around flex-wrap mb-2">
            @foreach ($submissions as $submission)
                @include('account._thumb', ['submission' => $submission, 'gallery' => false])
            @endforeach
        </div>
        {!! $submissions->render() !!} 
    @else
        <p>No submissions found from users you're watching.</p>
    @endif
@endsection
