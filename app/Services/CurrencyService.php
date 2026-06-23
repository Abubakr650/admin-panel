<?php

namespace App\Services;

use App\Models\Billing\ExchangeRate;

class CurrencyService
{
    public static function convert($amount,$fromCurrencyId,$toCurrencyId)
    {
        if($fromCurrencyId == $toCurrencyId){
            return $amount;
        }

        $rate = ExchangeRate::where([
            'from_currency_id'=>$fromCurrencyId,
            'to_currency_id'=>$toCurrencyId
        ])->latest()->first();

        if(!$rate){
            throw new \Exception('Exchange rate not found');
        }

        return $amount * $rate->rate;
    }
}

// الاستعمال حقه في اي فورم ثانيه
/* 
use App\Services\CurrencyService;

$priceYER = CurrencyService::convert(
    $service->price,
    $service->currency_code,
    'YER'
); 

*/