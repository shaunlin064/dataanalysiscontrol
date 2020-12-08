<?php
	/**
	 * Created by PhpStorm.
	 * User: shaun
	 * Date: 2019-09-12
	 * Time: 17:12
	 */
	
	
	/*
	 *--------------------------------------------------------------------------
	 * Sale Groups config
	 *--------------------------------------------------------------------------
	 * ##20201205 改為手動user自行調整比例 convenerRateStart&&convenerRateLadder##
	 * 設定招集人 團隊 獎金比例起算 與 獎金遞減階梯
	 * 初始討論方式為 一個招集人 帶 一個人 獎金比例為 5% 每多一個人 減 0.25
	 * 系統上設定 採用 5.5 起算 一個團隊最少一定會有兩個人 一個招集人與成員 故從 5.5遞減
	 * 未來如果有變動再從這邊調整 過往的bonuslevel 皆為每個月一存
	 *--------------------------------------------------------------------------
	 *### bonusMembersBeyondSetSaleGroupIds
	 *  hardCode 設定需要計算團隊帶人獎金的團隊 因規則尚未詳細內聚,時間倉促 故直接先寫在 config 待明確運行後 再改成動態資料
	 *### bonusMembersBeyondLevel
	 * 獎金計算層級 不包括招集人 其團隊內每一個人 達標按照比例計算加給給招集人 如全員100 則額外bonus
	 *
	*/
	
	
	return [
		'convenerRateStart'                 => 5.5,
		'convenerRateLadder'                => 0.25,
		'bonusMembersBeyondSetSaleGroupIds' => [ 6, 7, 8 ],
		'bonusMembersBeyondLevel'           => [
			'level'       => [
				[
					'rate'         => 50,
					'bonus_direct' => 10000,
				],
				[
					'rate'         => 70,
					'bonus_direct' => 15000,
				],
				[
					'rate'         => 100,
					'bonus_direct' => 25000,
				]
			],
			'extra_bonus' => 20000
		]
	];
?>
