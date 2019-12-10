<?php

namespace App\Classes;

use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Handlers\BeforeHandlerInterface;
use Carbon\Carbon;
use App\User;

class ActivityHandler implements BeforeHandlerInterface
{
    /**
     * Log activity in an Eloquent model.
     *
     * @param string $text
     * @param $userId
     * @param array  $attributes
     *
     * @return bool
     */
    public function shouldLog($text, $userId)
    {

        if (strpos(strtolower($text), 'cron') !== false) {
          return true;
        }
        if($userId){
            $user = User::find($userId);
            if($user->hasRole('admin') || $user->hasRole('food-admin|driver|restaurant-owner-single|restaurant-owner-catering') || $user->hasRole('administrator')){
                return false;
            }
        }

        return true;
    }

    /**
     * Clean old log records.
     *
     * @param int $maxAgeInMonths
     *
     * @return bool
     */
    public function cleanLog($maxAgeInMonths)
    {
        $minimumDate = Carbon::now()->subMonths($maxAgeInMonths);
        Activity::where('created_at', '<=', $minimumDate)->delete();

        return true;
    }
}
