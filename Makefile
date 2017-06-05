PHPUNIT=vendor/bin/phpunit

.DEFAULT_GOAL := help
.PHONY: test tc


help:
	@fgrep -h "##" $(MAKEFILE_LIST) | fgrep -v fgrep | sed -e 's/\\$$//' | sed -e 's/##//'


##
## Tests
##---------------------------------------------------------------------------

test:             ## Run PHPUnit tests
test: vendor
	php -d zend.enable_gc=0 $(PHPUNIT)

tc: vendor
	phpdbg -qrr -d zend.enable_gc=0 $(PHPUNIT) --coverage-html=dist/coverage --coverage-text


##
## Rules from files
##---------------------------------------------------------------------------

vendor: composer.lock
	composer install

composer.lock: composer.json
	@echo compose.lock is not up to date.
