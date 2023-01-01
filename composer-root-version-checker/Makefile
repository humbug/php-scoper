# See https://tech.davis-hansson.com/p/make/
MAKEFLAGS += --warn-undefined-variables
MAKEFLAGS += --no-builtin-rules


.DEFAULT_GOAL := help


PHPUNIT_BIN = vendor/bin/phpunit
PHPUNIT = $(PHPUNIT_BIN)


.PHONY: help
help:
	@printf "\033[33mUsage:\033[0m\n  make TARGET\n\n\033[32m#\n# Commands\n#---------------------------------------------------------------------------\033[0m\n\n"
	@fgrep -h "##" $(MAKEFILE_LIST) | fgrep -v fgrep | sed -e 's/\\$$//' | sed -e 's/##//' | awk 'BEGIN {FS = ":"}; {printf "\033[33m%s:\033[0m%s\n", $$1, $$2}'


.PHONY: check
check: cs cs_lint check_root_version phpunit


.PHONY: check_root_version
check_root_version: ## Checks that the Composer root version is up to date
check_root_version: vendor
	@./bin/check


.PHONY: dump_root_version
dump_root_version:  ## Dumps the latest Composer root version
dump_root_version: vendor
	@./bin/dump


.PHONY: cs
cs:		    ## Runs the fixers
cs: gitignore_sort composer_normalize php_cs_fixer


.PHONY: cs_lint
cs_lint:	    ## Runs the linters
cs_lint: composer_validate composer_normalize_lint php_cs_fixer_lint


.PHONY: test
test:	    	    ## Runs the tests
test: phpunit


.PHONY: gitignore_sort
gitignore_sort:
	LC_ALL=C sort -u .gitignore -o .gitignore

.PHONY: composer_validate
composer_validate: vendor
	composer validate --strict

.PHONY: composer_normalize
composer_normalize: vendor
	composer normalize

.PHONY: composer_normalize_lint
composer_normalize_lint: vendor
	composer normalize --dry-run

.PHONY: php_cs_fixer
php_cs_fixer:
	cd ..; $(MAKE) --makefile Makefile $@

.PHONY: php_cs_fixer_lint
php_cs_fixer_lint:
	cd ..; $(MAKE) --makefile Makefile $@

.PHONY: phpunit
phpunit: $(PHPUNIT_BIN) vendor
	$(PHPUNIT)

vendor: composer.lock
	composer install
	touch -c $@
	touch -c $(PHPUNIT_BIN)

composer.lock: composer.json
	@echo "$(@) is not up to date. You may want to run the following command:"
	@echo "$$ composer update --lock"

$(PHPUNIT_BIN): vendor
	touch -c $@
