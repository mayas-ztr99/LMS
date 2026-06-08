<?php

namespace App\Services;

use App\Models\LatestNews;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LatestNewsService
{
    public function index()
    {
        return LatestNews::orderBy('sort_order', 'desc')->latest()->get();
    }

    public function show(LatestNews $latestNews){
        return $latestNews;
    }
    public function store(array $data)
    {
        return DB::transaction(function () use ($data) {
            return LatestNews::create([
                'title'      => $data['title'],
                'content'    => $data['content'],
                'sort_order' => $data['sort_order'] ?? 0,
            ]);
        });
    }

    public function update(LatestNews $news, array $data)
    {

        return DB::transaction(function () use ($news, $data) {
            // dd($data);
            $news->fill($data);
            if(!$news->isDirty()){
                throw ValidationException::withMessages(['payload'=>['No changes detected.']]);
            }
            $news->save();
            return $news->fresh();
        });
    }

    public function destroy(LatestNews $news)
    {
        return DB::transaction(function () use ($news) {
            return $news->delete();
        });
    }
}
