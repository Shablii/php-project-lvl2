#Makefile

install:
	composer install
gendiff:
	./bin/gendiff
validate:
	composer validate
lint:
	composer run-script phpcs -- --standard=PSR12 src bin
test:
	composer exec --verbose phpunit tests
test-coverage:
	composer exec --verbose phpunit tests -- --coverage-clover build/logs/clover.xml

