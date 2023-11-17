phpstan:
	vendor/bin/phpstan analyse -c phpstan.neon

fixcs:
	vendor/bin/php-cs-fixer fix src
