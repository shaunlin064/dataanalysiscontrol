<?php


    namespace App;

    use Illuminate\Database\Eloquent\Model;

    class Bonus extends Model {
        //
        protected $table = 'bonus';
        protected $fillable = [ 'erp_user_id', 'set_date', 'boundary' ];

        public function levels ()
        {
            return $this->hasMany(BonusLevels::CLASS);
        }

        public function bonusReach ()
        {
            return $this->hasOne(BonusReach::CLASS);
        }

        public function user ()
        {
            return $this->belongsTo(User::CLASS, 'erp_user_id', 'erp_user_id');
        }

        public function getUserBonus ( $erpUserId, $totalProfit, String $dateYearMonth )
        {
            //待解 如 搜尋舊資料 但當時未設定 bonus 預設要抓最新 or 當時前後？
            //目前預設抓最新設定

            //check exists or use Old Data

            if ( $this->where([ 'erp_user_id' => $erpUserId, 'set_date' => $dateYearMonth ])->exists() )
            {

                $userbonus = $this->where([
                                  'erp_user_id' => $erpUserId,
                                  'set_date'    => $dateYearMonth
                              ])->with('levels')->get()->first()->toArray();
            } else
            {
                $userbonus = [
                    'boundary' => 0,
                    'levels'   => [
                        [
                            'achieving_rate' => 0,
                            'bonus_rate'     => 0
                        ]
                    ]
                ];
            }
            $reachLevle = null;
            $nextLevel = null;
            /*部分人員 如 招集人 只有設定責任額沒設定 levels*/
            if(empty($userbonus['levels'])){
                $userbonus['levels'] = [
                    [
                        'achieving_rate' => 0,
                        'bonus_rate'     => 0
                    ]
                ];
            }
            
            foreach ( collect($userbonus['levels'])->sortBy('achieving_rate') as $key => $items )
            {
                $thisLevelAchieving = $userbonus['boundary'] * $items['achieving_rate'] * 0.01;
                if ( $totalProfit > $thisLevelAchieving )
                {
                    $reachLevle = $items;
                } else
                {
                    $nextLevel = $items;
                    break;
                }
            }
            //if not reach minleavel
            if ( $reachLevle == null )
            {
                $estimateBonus = 0;
                if ( $totalProfit == 0 && $userbonus['boundary'] == 0 && $nextLevel['achieving_rate'] == 0 )
                {

                } else if ( $userbonus['boundary'] == 0 && $nextLevel['achieving_rate'] == 0 )
                {

                } else
                {
                    $nextLevel['bonus_next_amount'] = $userbonus['boundary'] * $nextLevel['achieving_rate'] * 0.01 - $totalProfit;
                    //				$nextLevel['bonus_next_percentage'] = round($totalProfit/ ($userbonus['boundary'] * $nextLevel['achieving_rate'] * 0.01)*100);
                    $nextLevel['bonus_next_percentage'] = $userbonus['boundary'] != 0 ? round($totalProfit / $userbonus['boundary'] * 100) : 0;
                }
            } else
            {

                $reachLevle['bonus_direct'] = $reachLevle['bonus_direct'] ?? 0;
                $estimateBonus = round($totalProfit * $reachLevle['bonus_rate'] * 0.01);
                if ( $nextLevel )
                {
                    $nextLevel['bonus_next_amount'] = $userbonus['boundary'] * $nextLevel['achieving_rate'] * 0.01 - $totalProfit;
                    //				$nextLevel['bonus_next_percentage'] = round($totalProfit/ ($userbonus['boundary'] * $nextLevel['achieving_rate'] * 0.01)*100);
                }

                $nextLevel['bonus_next_percentage'] = $userbonus['boundary'] != 0 ? round($totalProfit / $userbonus['boundary'] * 100) : 0;
            }

            $bonusDirect = isset($reachLevle['bonus_direct']) ? $reachLevle['bonus_direct'] : 0;


            $returnData = [ 'estimateBonus' => $estimateBonus, 'reachLevle' => $reachLevle, 'nextLevel' => $nextLevel, 'bonusDirect' => $bonusDirect,'boundary' => $userbonus['boundary'] ];

            return $returnData;
        }

        public function saleGroups ()
        {
            return $this->hasMany(SaleGroupsUsers::CLASS, 'erp_user_id', 'erp_user_id')
                ->where('set_date', $this->set_date);
        }
    }
