<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {

        $plans = [


            /*
            |--------------------------------------------------------------------------
            | START
            |--------------------------------------------------------------------------
            */

            [
                'package' => 'START',
                'duration_months' => 1,
                'promo_price' => 5900,
                'regular_price' => 10000,
            ],

            [
                'package' => 'START',
                'duration_months' => 2,
                'promo_price' => 11800,
                'regular_price' => 20000,
            ],

            [
                'package' => 'START',
                'duration_months' => 3,
                'promo_price' => 17700,
                'regular_price' => 30000,
            ],

            [
                'package' => 'START',
                'duration_months' => 6,
                'promo_price' => 35400,
                'regular_price' => 60000,
            ],

            [
                'package' => 'START',
                'duration_months' => 12,
                'promo_price' => 70800,
                'regular_price' => 120000,
            ],




            /*
            |--------------------------------------------------------------------------
            | BUSINESS
            |--------------------------------------------------------------------------
            */

            [
                'package' => 'BUSINESS',
                'duration_months' => 1,
                'promo_price' => 9900,
                'regular_price' => 18000,
            ],

            [
                'package' => 'BUSINESS',
                'duration_months' => 2,
                'promo_price' => 19800,
                'regular_price' => 36000,
            ],

            [
                'package' => 'BUSINESS',
                'duration_months' => 3,
                'promo_price' => 29700,
                'regular_price' => 54000,
            ],

            [
                'package' => 'BUSINESS',
                'duration_months' => 6,
                'promo_price' => 59400,
                'regular_price' => 108000,
            ],

            [
                'package' => 'BUSINESS',
                'duration_months' => 12,
                'promo_price' => 118800,
                'regular_price' => 216000,
            ],





            /*
            |--------------------------------------------------------------------------
            | PREMIUM
            |--------------------------------------------------------------------------
            */

            [
                'package' => 'PREMIUM',
                'duration_months' => 1,
                'promo_price' => 24900,
                'regular_price' => 49800,
            ],

            [
                'package' => 'PREMIUM',
                'duration_months' => 2,
                'promo_price' => 49800,
                'regular_price' => 99600,
            ],

            [
                'package' => 'PREMIUM',
                'duration_months' => 3,
                'promo_price' => 74700,
                'regular_price' => 149400,
            ],

            [
                'package' => 'PREMIUM',
                'duration_months' => 6,
                'promo_price' => 149400,
                'regular_price' => 298800,
            ],

            [
                'package' => 'PREMIUM',
                'duration_months' => 12,
                'promo_price' => 298800,
                'regular_price' => 597600,
            ],


        ];



        foreach ($plans as $plan) {


            SubscriptionPlan::updateOrCreate(

                [
                    'package' => $plan['package'],
                    'duration_months' => $plan['duration_months'],
                ],

                [

                    'promo_price' => $plan['promo_price'],
                    'regular_price' => $plan['regular_price'],
                    'active' => true,

                ]

            );


        }


    }
}