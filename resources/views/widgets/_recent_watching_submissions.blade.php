@if (count($watchedSubmissions))
    <div class="card my-2 text-center">
        <div class="card-header">
            <h5>From People You Watch</h5>
        </div>
        <div class="card-body">
            <div class="row">
                @foreach ($watchedSubmissions as $submission)
                    <div class="col-md-3 col-6 mb-2">
                        @include('galleries._thumb', ['submission' => $submission])
                    </div>
                @endforeach
                <div class="col-12">
                    <a class="float-right" href="account/watching">View all People You Watch...</a>
                </div>
            </div>
        </div>
    </div>
@endif
