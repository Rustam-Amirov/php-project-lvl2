install:
	composer install 
console:
	composer run-script psysh
lint:
	composer run-script phpcs  -- --standard=PSR12 src bin
lint-fix:
	composer run-script phpcbf -- --standard=PSR12 src tests 
test:
	composer run-script phpunit tests
test-coverage:
	composer phpunit tests -- --coverage-clover build/logs/clover.xml
