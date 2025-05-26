<?php

namespace App\Http\Controllers\helpers;

use App\Http\Controllers\Controller;
use App\Models\Counter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CounterController extends Controller
{
    public function incrementOrCreate($seriesId, $parentId)
    {
        //$seriesId = $request->input('series_id');
        // $parentId = $request->input('id');

        if (!$seriesId) {
            return response()->json(['error' => 'Se requiere series_id'], 400);
        }

        if ($parentId) {
            // Incrementar hijo existente dentro de la serie
            $counter = Counter::where('series_id', $seriesId)
                ->where('parent_count', $parentId)
                ->latest()
                ->first();

            if (!$counter) {
                return response()->json(['error' => 'Padre no encontrado en esta serie'], 404);
            }

            $newCounter = Counter::create([
                'series_id' => $seriesId,
                'parent_count' => $parentId,
                'child_count' => $counter->child_count + 1
            ]);
        } else {
            // Crear nuevo padre dentro de la serie
            $latestParent = Counter::where('series_id', $seriesId)->max('parent_count');
            $newParentId = $latestParent ? $latestParent + 1 : 1;

            $newCounter = Counter::create([
                'series_id' => $seriesId,
                'parent_count' => $newParentId,
                'child_count' => 1
            ]);
        }

        return response()->json([
            'counter' => $newCounter->parent_count . '.' . $newCounter->child_count,
            'series_id' => $newCounter->series_id
        ]);
    }

    public function getAllCounters($id)
    {


        $lastCounters = DB::table('counters')->select('parent_count')
            ->whereNotNull('parent_count')
            ->whereNotNull('child_count')
            ->orderBy('id', 'desc')
            ->first();

        $counters = Counter::selectRaw('parent_count, MAX(child_count) as max_child')
            ->groupBy('parent_count')
            ->orderBy('parent_count')
            ->where('series_entity_id', '=', $id)
            ->get();


        $formattedCounters = $counters->map(function ($counter) {
            return [
                'parent_count' => $counter->parent_count,
                'max_child' => $counter->max_child
            ];
        });

        return response()->json([
            'data' => $formattedCounters,
            'lastCounters' => $lastCounters,
        ]);
    }
}
