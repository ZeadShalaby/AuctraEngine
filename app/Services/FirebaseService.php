<?php

namespace App\Services;

use Kreait\Firebase\Factory;

class FirebaseService
{
    private $messaging;

    public function messaging()
    {
        if (!$this->messaging) {
            $factory = (new Factory)
                ->withServiceAccount(
                    storage_path('app/firebase/firebase_credentials.json')
                );

            $this->messaging = $factory->createMessaging();
        }

        return $this->messaging;
    }
    
}