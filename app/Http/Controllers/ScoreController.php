<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Score;

class ScoreController extends Controller
{
    public function index()
    {
        return Score::orderBy('score', 'desc')->take(10)->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'score' => 'required|integer',
        ]);

        $score = new Score;
        $score->score = $request->score;
        $score->save();

        return response()->json(['success' => true]);
    }
}
