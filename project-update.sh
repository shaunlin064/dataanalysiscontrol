#! /bin/bash
  docker exec -i php74 php ./dataanalysiscontrol/artisan down
  docker exec -i php74 bash -c "git -C dataanalysiscontrol pull"
  docker exec -i php74 bash -c "cd dataanalysiscontrol && composer install && npm install -y && npm run dev"
  docker exec -i php74 php ./dataanalysiscontrol/artisan up
