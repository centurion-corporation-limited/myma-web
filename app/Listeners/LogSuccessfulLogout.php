<?php
namespace App\Listeners;

use App\User;
use Event;

class LogSuccessfulLogout
{
    /**
     * @event user.created
     *
     * Send email confirm
     *
     * @param $user_id
     */
    public function logout($data)
    {
        if($data){
            if($data->user)
                $data->user->update(['last_logged' => NULL]);
        }
    }
}
