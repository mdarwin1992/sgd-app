<?php

namespace App\Http\Controllers\helpers;

use App\Http\Controllers\Controller;
use App\Models\Entity;
use App\Models\EntityCounter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HelpersController extends Controller
{
    public static function getEntityCounterValue($entityId, $value)
    {
        $counter = EntityCounter::where([['entity_id', '=', $entityId], ['current_code', '=', $value]])->first();
        return $counter ? $counter->current_count : 0;
    }

    public static function listAllCounters()
    {
        return EntityCounter::with('entity')->get()->map(function ($counter) {
            return [
                'entity_id' => $counter->entity_id,
                'entity_name' => $counter->entity->name ?? 'Unknown',  // Asumiendo que Entity tiene un campo 'name'
                'counter_value' => $counter->current_count
            ];
        });
    }

    public static function getLoggedUserEntityName()
    {

        $user = Auth::user();

        $entityName = Entity::whereHas('departments.offices.user', function ($query) use ($user) {
            $query->where('users.id', $user->id);
        })->value('name');

        return $entityName;
    }

    public static function getEntityData($entityId)
    {

        $entity = Entity::where('id', $entityId)->first();

        return $entity;
    }


    public function showCounter($entityId, $value)
    {
        $counterValue = self::getEntityCounterValue($entityId, $value);
        $date = date('Ymd');
        return response()->json([
            'reference_code' => $date . $counterValue,
            'system_code' => $counterValue,
            'entity_id' => $entityId
        ]);
    }

    public function listCounters()
    {
        $counters = self::listAllCounters();
        return response()->json($counters);
    }

}
