<?php

namespace App\Http\Controllers\Users;
use App\Http\Controllers\Controller;
use App\Models\Character\Character;
use App\Models\Submission\Submission;
use App\Models\User\User;
use App\Services\SubmissionManager;
use App\Models\Gallery\Gallery;
use App\Models\Gallery\GalleryCharacter;
use App\Models\Gallery\GallerySubmission;
use App\Models\Prompt\Prompt;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WatchController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Watch Controller
    |--------------------------------------------------------------------------
    |
    | Handles prompt submissions and claims for the user's watching list.
    |
    */

    /**
     * Shows the watch/following index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getWatching(Request $request) {
        $user = Auth::user();
        $ids = $user->follows()->pluck('following_user_id');

        $submissions = GallerySubmission::whereIn('user_id', $ids)->with(['gallery', 'user'])->visible($user)->latest()->paginate(20)->appends($request->query());

        return view('account.watching', [
            'user'        => $user,
            'submissions' => $submissions,
        ]);
    }

	/**
     * Toggle of the watch and unwatch
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    
    public function postWatch(Request $request, $id) {
        $userToWatch = User::find($id);

        if (!$userToWatch) {
            abort(404);
        }

        auth()->user()->follows()->toggle($userToWatch->id);
        
        return back();
    }
}