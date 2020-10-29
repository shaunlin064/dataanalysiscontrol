## About data_analysis_control

### Tech Stack

* backEnd
    * Laravel
    * PHP
    * Mysql 
    * Memcached
    * python3
* frontEnd
    * vue.js
    * vuex
    * axios
* other tool
    * Log Notification
        * 透過 discord Chat Bot 即時回報系統錯誤
 
 ### 基本說明
 
 * 此系統主要是針對業務與財務,方便兩方user圖像化資料與作業
 * 讓Level 1 主管可以看到所有金流動向,整理報告與訂定戰略目標
 * 透過CronJob與主系統call api更新資料
 * 各項瀏覽資料,設定快取釋放機制減少loading時間
  
 ### 主要功能
 
 * 提供財務資料圖像化
 * 獎金發放功能
 * user 可以追蹤案件收款與獎金發放狀態
 * 設定user 獎金Kpi
 
 ### 次要功能
 
 * user權限設定 (為RBAC0規範)
 * menu 設定
 * 每日抓取台灣銀行牌價匯率 (python) 
 
 ## show case
 
![GITHUB](https://raw.githubusercontent.com/shaunlin064/dataanalysiscontrol/master/storage/app/public/showcase/head.png "head")
![GITHUB](https://raw.githubusercontent.com/shaunlin064/dataanalysiscontrol/master/storage/app/public/showcase/review-1.png "review")
![GITHUB](https://raw.githubusercontent.com/shaunlin064/dataanalysiscontrol/master/storage/app/public/showcase/review-2.png "review")
![GITHUB](https://raw.githubusercontent.com/shaunlin064/dataanalysiscontrol/master/storage/app/public/showcase/review-3.png "review")
![GITHUB](https://raw.githubusercontent.com/shaunlin064/dataanalysiscontrol/master/storage/app/public/showcase/review-4.png "review")
![GITHUB](https://raw.githubusercontent.com/shaunlin064/dataanalysiscontrol/master/storage/app/public/showcase/review-5.png "review")
![GITHUB](https://raw.githubusercontent.com/shaunlin064/dataanalysiscontrol/master/storage/app/public/showcase/provide-1.png "review")
![GITHUB](https://raw.githubusercontent.com/shaunlin064/dataanalysiscontrol/master/storage/app/public/showcase/info-1.png "review")
![GITHUB](https://raw.githubusercontent.com/shaunlin064/dataanalysiscontrol/master/storage/app/public/showcase/info-2.png "review")
![GITHUB](https://raw.githubusercontent.com/shaunlin064/dataanalysiscontrol/master/storage/app/public/showcase/setting.png "review")
![GITHUB](https://raw.githubusercontent.com/shaunlin064/dataanalysiscontrol/master/storage/app/public/showcase/exchange.png "review")
