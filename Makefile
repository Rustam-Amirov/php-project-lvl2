install:
	composer install 
console:
	composer run-script psysh
lint:
	composer run-script phpcs src 
lint-fix:
	composer run-script phpcbf -- --standard=PSR12 src tests 
test:
	composer run-script phpunit tests
