phpstan:
	vendor/bin/phpstan analyse -c phpstan.neon

fixcs:
	vendor/bin/php-cs-fixer fix src

phpcs:
	vendor/bin/php-cs-fixer fix src --dry-run
