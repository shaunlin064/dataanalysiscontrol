<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ExchageRatesAll
 * @package App
 */
class ExchangeRatesAll extends Model
{
    //
    protected $guarded = ['_token'];
    
    public function saveData ($datas)
    {
        foreach($datas as $currency => $currencyDatas){
            foreach ($currencyDatas as $currencyData){
                $set_date = substr(str_replace('/','',$currencyData[0][0]),0,6);
    
                $existsData = self::where('currency',$currency)->where('year_month',$set_date)->first();
    
                if($existsData){
                    $existsData->update(['data' => json_encode($currencyData)]);
                }else{
                    self::create(
                        [
                            'year_month' => $set_date,
                            'currency' => $currency,
                            'data' => json_encode($currencyData)
                        ]
                    );
                }
                $setDateFirst = new \DateTime($set_date.'01');
                $setDateFirst = $setDateFirst->format('Y-m-d');
                $obfExchang = ExchangeRate::where('currency',$currency)->where('set_date',$setDateFirst);
                
                if($obfExchang->exists()){
                    $obfExchang->first()->update(['rate' => $currencyData[0][4]]);
                }else{
                    ExchangeRate::create(
                        [
                            'set_date' => $setDateFirst,
                            'currency' => $currency,
                            'rate' => $currencyData[0][4],
                            'created_by_erp_user_id' => 157
                        ]
                    );
                }
            }
        }
    }
}
