<?php

namespace feiron\laragql;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;
use feiron\laragql\connector\connector;
class laraGQLServiceProvider extends ServiceProvider implements DeferrableProvider{

    public function register(){
        $this->app->singleton('gql', function ($app) {
            return new connector($app['config']['gql']);
        });
    }

    public function provides(){
        return [connector::class];
    }
}