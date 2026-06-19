<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        

    $interfacesPath = app_path('Repositories/Interfaces');
    
    if (File::exists($interfacesPath)) {
        $files = File::files($interfacesPath);
        
        foreach ($files as $file) {
            $interfaceName = $file->getFilenameWithoutExtension(); 
            
            $repositoryName = str_replace('Interface', '', $interfaceName); 
            
            $interfaceNamespace = "App\\Repositories\\Interfaces\\{$interfaceName}";
            $repositoryNamespace = "App\\Repositories\\Eloquent\\{$repositoryName}";
            
            $this->app->bind($interfaceNamespace, $repositoryNamespace);
        }
    }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Model::unguard();
        //
       Relation::morphMap([
            'user'          => \App\Models\User::class,
            'post'          => \App\Models\Post::class,
            'reel'          => \App\Models\Reels::class,
            'ads'           => \App\Models\Ads::class,  
            'auction'       => \App\Models\Auction::class,
            'auction_term'  => \App\Models\AuctionTerm::class,
            'category'      => \App\Models\Category::class,
            'comment'       => \App\Models\Comment::class,
            'complaint'     => \App\Models\Complaint::class,
            'favourite'     => \App\Models\Favourite::class,
            'interest'      => \App\Models\Interest::class,
            'reel_interest' => \App\Models\ReelInterest::class,
            'review'        => \App\Models\Review::class,
            'setting'       => \App\Models\Setting::class,
            'report'        => \App\Models\reports\Report::class, 
            'payment'       => \App\Models\Wallet\Payment::class,
            'transaction'   => \App\Models\Wallet\Transaction::class,
        ]);
    }
}
