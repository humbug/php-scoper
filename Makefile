BOX=vendor-bin/box/vendor/bin/box
PHPUNIT=vendor/bin/phpunit
PHPSCOPER=bin/php-scoper.phar

.DEFAULT_GOAL := help
.PHONY: build test tu tc e2e tb


help:
	@fgrep -h "##" $(MAKEFILE_LIST) | fgrep -v fgrep | sed -e 's/\\$$//' | sed -e 's/##//'


##
## Build
##---------------------------------------------------------------------------

build:            ## Build the PHAR
build: vendor
	# Cleanup existing artefacts
	rm -f bin/php-scoper.phar
	rm -rf build
	# Delete local `composer.lock` to ensure fresh dependencies are used
	rm composer.lock
	#
	# As of now, files included in `autoload-dev` are not excluded from the
	# classmap.
	#
	# See: https://github.com/composer/composer/issues/6457
	#
	# As a result, the the flag `--no-dev` for `composer install` cannot
	# be used and `box.json.dist` must include the `tests` directory
	#
	composer install --prefer-dist
	php -d zend.enable_gc=0 bin/php-scoper add-prefix --force
	composer dump-autoload -d build  --classmap-authoritative
	php -d zend.enable_gc=0 $(BOX) build
	# Install back all the dependencies
	composer install


##
## Tests
##---------------------------------------------------------------------------

test:             ## Run all the tests
test: tu e2e

tu:               ## Run PHPUnit tests
tu: vendor
	php -d zend.enable_gc=0 $(PHPUNIT)

tc:               ## Run PHPUnit tests with test coverage
tc: vendor
	phpdbg -qrr -d zend.enable_gc=0 $(PHPUNIT) --coverage-html=dist/coverage --coverage-text

e2e:			  ## Run end-to-end tests
e2e: scoper
	php -d zend.enable_gc=0 $(PHPSCOPER) add-prefix fixtures/set004 -o build/set004 -f
	composer -d=build/set004 dump-autoload
	php -d zend.enable_gc=0 $(BOX) build -c build/set004/box.json.dist
	php build/set004/bin/greet.phar > build/output
	diff fixtures/set004/expected-output build/output

	php -d zend.enable_gc=0 $(PHPSCOPER) add-prefix fixtures/set005 -o build/set005 -f
	composer -d=build/set005 dump-autoload
	php -d zend.enable_gc=0 $(BOX) build -c build/set005/box.json.dist
	php build/set005/bin/greet.phar > build/output
	diff fixtures/set005/expected-output build/output

tb:				  ## Run Blackfire profiling
tb: vendor
	rm -rf build
  	#
  	# As of now, files included in `autoload-dev` are not excluded from the
  	# classmap.
  	#
  	# See: https://github.com/composer/composer/issues/6457
  	#
  	# As a result, the the flag `--no-dev` for `composer install` cannot
  	# be used and `box.json.dist` must include the `tests` directory
  	#
	composer install --prefer-dist --classmap-authoritative
	blackfire --new-reference run bin/php-scoper add-prefix -f -q
	composer install


##
## Rules from files
##---------------------------------------------------------------------------

vendor: composer.lock
	composer install

composer.lock: composer.json
	@echo compose.lock is not up to date.

scoper: build

