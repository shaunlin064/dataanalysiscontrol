#系統基本資訊
`業績查詢系統-dataAnalysisControl tech stack 以下簡稱dac`

##ABOUT DAC

### tech stack

* PHP 7.4
* python
* Mysql  5.7
* nginx
* memcached
* docker
* laravel telescope
* ui 使用 adminlte 2

### dockerFile Local Path

gcp dac 主機上  <br>
所有docker相關的檔案都在 <br>
`gcp:/home/shaun/dockerFile` 以下簡稱 dockerPath

* 主要使用docker-compose 啟動所有服務 <br>
`${dockerPath}/js_compose_nginx/docker-compose.yml` <br>
* nginx config <br>
`${dockerPath}/nginx/conf.d/conf.d/default.conf` <br>
* php.ini <br>
`${dockerPath}/js_compose_nginx/config/php/74/php.ini` <br>
* php-fpm crontab <br>
`${dockerPath}/js_compose_nginx/config/cron/schedule-cron` <br>

### project local path
`gcp:/var/project/dataanalysiscontrol` <br>

### github

[dac-github](https://github.com/JsAdways/dataanalysiscontrol)

### crontab

* crontab schedule-cron
```
   30 12 * * 1-5  sh /opt/script/backup_morning.sh 本地早上備份
   35 12 * * 1-5  sh /opt/script/rsync_backup.sh 同步回nas
   30 19 * * 1-5  sh /opt/script/backup_noon.sh 本地下午備份
   35 19 * * 1-5  sh /opt/script/rsync_backup.sh 同步回nas
   59 23 * * 1-5  echo 3 > /proc/sys/vm/drop_caches 清除記憶體
```
* script <br>

     * backup_morning.sh (backup_noon.sh 類似不贅述)
    ```
    docker exec -i mysql mysqldump -uroot -p"${PW}" data_analysis_control --ignore-table=data_analysis_control.cache > /var/backups/dataAnalysisControl/`date +%Y-%m-%d`-morning.dac.sql
    ```
    * rsync_backup.sh
    ```
  rsync -avz -e'ssh -p 6112' --no-perms --no-owner --no-group --delete --progress /var/backups/dataAnalysisControl/`date +%Y-%m-%d`-*.sql admin@${NAS_IP}:/share/CACHEDEV2_DATA/【備份】system_backup/dataAnalysisControl/database
  ```

### 啟動相關

* 啟動服務 js_compose_nginx <br>
command line : ```dacup``` <br>
* 關閉服務 js_compose_nginx <br>
command line : ```dacdown``` <br>
* 進入 docker <br>
command line : ```indocker ${CONTAINER_NAME}``` <br>
CONTAINER_NAME 詳見 指令 `docker ps -a`

