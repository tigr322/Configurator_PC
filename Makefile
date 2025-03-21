.PHONY: clear-cache

clear-cache:
	php artisan cache:clear
	php artisan config:clear
	php artisan route:clear
	php artisan view:clear
	php artisan event:clear
	php artisan optimize:clear