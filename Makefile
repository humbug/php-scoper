BOX=vendor-bin/box/vendor/bin/box
PHPUNIT=vendor/bin/phpunit

.DEFAULT_GOAL := help
.PHONY: build test tc


help:
	@fgrep -h "##" $(MAKEFILE_LIST) | fgrep -v fgrep | sed -e 's/\\$$//' | sed -e 's/##//'


##
## Build
##---------------------------------------------------------------------------

build:            ## Build the PHAR
build: bin/php-scoper
	rm -rf build
	rm composer.lock
	composer install --no-dev --prefer-dist --classmap-authoritative
	php -d zend.enable_gc=0 bin/php-scoper add-prefix --force
	cd build && composer dump-autoload --classmap-authoritative
	php -d zend.enable_gc=0 $(BOX) build
	mv build/bin/php-scoper.phar bin/
	composer install


##
## Tests
##---------------------------------------------------------------------------

test:             ## Run PHPUnit tests
test: vendor
	php -d zend.enable_gc=0 $(PHPUNIT)

tc:               ## Run PHPUnit tests with test coverage
tc: vendor
	phpdbg -qrr -d zend.enable_gc=0 $(PHPUNIT) --coverage-html=dist/coverage --coverage-text


##
## Rules from files
##---------------------------------------------------------------------------

vendor: composer.lock
	composer install

composer.lock: composer.json
	@echo compose.lock is not up to date.
