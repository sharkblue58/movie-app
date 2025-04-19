<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Serie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        
        $request->validate([
            'limit' => 'integer|min:10'
        ]);
        $limit = $request->query('limit', 10); 
    
        // استخدام paginate بدلاً من take للحصول على البيانات paginated
        $latestMovies = Movie::latest('release_date')
            ->select(['id', 'title', 'release_date', 'rating', 'poster_url'])
            ->paginate($limit);
    
        $latestSeries = Serie::latest('release_date')
            ->select(['id', 'title', 'release_date', 'rating', 'poster_url'])
            ->paginate($limit);
    
        // ترندينج (مع paginated)
        $trending = collect(
            DB::table('movies')
                ->select('id', 'title', 'release_date', 'rating', 'poster_url', DB::raw("'movie' as type"))
                ->unionAll(
                    DB::table('series')
                        ->select('id', 'title', 'release_date', 'rating', 'poster_url', DB::raw("'series' as type"))
                )
                ->orderByDesc('release_date')
                ->orderByDesc('rating')
                ->paginate($limit)
        );
    
        // الأكثر مشاهدة (مع paginated)
        $mostWatched = collect(
            DB::table('movies')
                ->select('id', 'title', 'rating', 'poster_url', DB::raw("'movie' as type"))
                ->unionAll(
                    DB::table('series')
                        ->select('id', 'title', 'rating', 'poster_url', DB::raw("'series' as type"))
                )
                ->orderByDesc('rating')
                ->paginate($limit)
        );
    
        $watchLater = null;
        $watchAgain = null;
    
        return response()->json([
            'latest_movies' => $latestMovies,
            'latest_series' => $latestSeries,
            'trending' => $trending,
            'most_watched' => $mostWatched,
            'watch_later' => $watchLater,
            'watch_again' => $watchAgain
        ]);
    }
    
}
