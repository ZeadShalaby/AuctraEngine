<?php

namespace App\Services;

use App\Models\User;

class UserToggleService
{

    public function __construct(protected User $user)
    {
    }

    /**
     * Generic toggle method
     *
     * @param string $key
     * @return array|null
     */
    public function toggle(string $key): ?array
    {
        if (!in_array($key, ['status', 'notifications_enabled', 'email_enabled', 'auction_enabled', 'ads_enabled'])) {
            return null;
        }

        if ($key === 'status') {
            $this->user->status = $this->user->status === 'active' ? 'inactive' : 'active';
            $this->user->save();

            $color = match ($this->user->status) {
                'active' => 'success',
                'inactive' => 'danger',
                'banned' => 'dark',
                default => 'secondary'
            };

            return [
                'status' => $this->user->status,
                'color' => $color
            ];
        } else {
            $this->user->{$key} = !$this->user->{$key};
            $this->user->save();

            return [
                'success' => true,
                'value' => $this->user->{$key},
                'color' => $this->user->{$key} ? 'success' : 'danger',
            ];
        }

    }
}