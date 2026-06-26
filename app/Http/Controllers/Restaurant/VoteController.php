<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateVoteRequest;
use App\Models\City;
use App\Models\Restaurant;
use App\Services\VoteService;
use Illuminate\Http\Request;

class VoteController extends Controller
{
    public function __construct(private VoteService $voteService) {}

    public function store(CreateVoteRequest $request, City $city, Restaurant $restaurant)
    {
        $result = $this->voteService->cast($request, $restaurant);

        inertia()->flash([
            'type' => $result['status'] === 'success' ? 'success' : 'error',
            'message' => $result['message'],
        ]);

        return back()->withCookie(cookie('visitor_id', $request->input('visitor_id'), 60 * 24));
    }
}
