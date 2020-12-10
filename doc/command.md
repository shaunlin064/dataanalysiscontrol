#Command
`主要描述各種dac內的排程schedule 與 常用command`

## app/Console/Kernel.php

```
$schedule->command('update_financial_data')->twiceDaily(12, 18);
$schedule->command('update_financial_data')->daily();
$schedule->command('update_exchange_rate')->daily();
$schedule->command('check_add_new_financial_user')->daily();
$schedule->command('update_user_bonus')->monthlyOn('1', '00:00');
$schedule->command('update_sale_groups')->monthlyOn('1', '00:10');
$schedule->command('update_bonus_reach')->monthlyOn('16', '00:10');
$schedule->command('update_convener_reach')->monthlyOn('16', '00:20');
```

### UpdateFinancialData 更新財報資料
* 觸發時間 每日 00,12,18
* 主要從js erp 抓資料回來更新 financial_list
* 資料區間為 上月(16號以前) 本月 次月
ps: 15號 為 上月財報最後關帳日 故 16號 抓取最後資料

### UpdateExchangeRate 抓取台灣銀行匯率
* 用來更新dac內設定幣別匯率, 每日更新
* 本來dac內會有非台幣的財報需要計算,後來都排除了 僅已台幣計算

### CheckAddNewFinancialUser
* 每日檢查是否有新增會填寫財報的人員, 將資料加入dac並設定責任額 團隊為後勤人員
* 這部分主要是一開始只設定 業績檢視 只看sale建立的案件, 需要檢視全體資料,所以需要把後勤建立案件的案件納入

### UpdateUserBonus && UpdateSaleGroups
* 觸發時間為 每月 1號
* 每月新增新月份責任額,團隊資料
* 資料沿用上月資料

### UpdateBonusReach && UpdateConvenerReach
* 觸發時間為 每月 16號
* 計算可發放獎金清單與金額 提供財務部發放