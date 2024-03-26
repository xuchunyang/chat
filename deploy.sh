#!/usr/bin/env bash

php artisan down

composer install --no-dev --optimize-autoloader --no-interaction

php artisan migrate --force

php artisan optimize

# 重启队列，因为 artisan queue:work 常驻后台，如果不重启不会收到新代码
php artisan queue:restart

# 重启 Laravel Reverb，但是我推测：除了修改 reverb 配置，不然也不用重启
php artisan reverb:restart

npm ci && npm run build

php artisan up
