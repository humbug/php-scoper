# See https://tech.davis-hansson.com/p/make/
MAKEFLAGS += --warn-undefined-variables
MAKEFLAGS += --no-builtin-rules

SRC_FILES := $(shell find bin/ src/ vendor-hotfix/ -type f)

PHP_SCOPER_PHAR_BIN = bin/php-scoper.phar
PHP_SCOPER_PHAR = $(PHP_SCOPER_PHAR_BIN)

COMPOSER_BIN_PLUGIN_VENDOR = vendor/bamarni/composer-bin-plugin

PHPSTAN_BIN = vendor-bin/phpstan/vendor/bin/phpstan
PHPSTAN = $(PHPSTAN_BIN) analyze src tests --level max --memory-limit=-1

BOX_BIN = bin/box
BOX = $(BOX_BIN)

COVERAGE_DIR = build/coverage
COVERAGE_XML = $(COVERAGE_DIR)/xml
COVERAGE_HTML = $(COVERAGE_DIR)/html

PHPUNIT_BIN = bin/phpunit
PHPUNIT = $(PHPUNIT_BIN)
PHPUNIT_COVERAGE_INFECTION = XDEBUG_MODE=coverage $(PHPUNIT) --coverage-xml=$(COVERAGE_XML) --log-junit=$(COVERAGE_DIR)/phpunit.junit.xml
PHPUNIT_COVERAGE_HTML = XDEBUG_MODE=coverage $(PHPUNIT) --coverage-html=$(COVERAGE_HTML)

COVERS_VALIDATOR_BIN = vendor-bin/covers-validator/bin/covers-validator
COVERS_VALIDATOR = $(COVERS_VALIDATOR_BIN)

PHP_CS_FIXER_BIN = vendor-bin/php-cs-fixer/vendor/friendsofphp/php-cs-fixer/php-cs-fixer
PHP_CS_FIXER = $(PHP_CS_FIXER_BIN) fix

BLACKFIRE = blackfire


.DEFAULT_GOAL := help


.PHONY: help
help:
	@echo "\033[33mUsage:\033[0m\n  make TARGET\n\n\033[32m#\n# Commands\n#---------------------------------------------------------------------------\033[0m\n"
	@fgrep -h "##" $(MAKEFILE_LIST) | fgrep -v fgrep | sed -e 's/\\$$//' | sed -e 's/##//' | awk 'BEGIN {FS = ":"}; {printf "\033[33m%s:\033[0m%s\n", $$1, $$2}'


#
# Commands
#---------------------------------------------------------------------------

.PHONY: check
check: ## Runs all checks
check: composer_root_version_lint cs autoreview test composer_root_version_check

.PHONY: build
build: ## Builds the PHAR
build:
	rm $(PHP_SCOPER_PHAR_BIN) || true
	$(MAKE) $(PHP_SCOPER_PHAR_BIN)

.PHONY: fixtures_composer_outdated
fixtures_composer_outdated: ## Reports outdated dependencies
fixtures_composer_outdated:
	@find fixtures -name 'composer.json' -type f -depth 2 -exec dirname '{}' \; | xargs -I % sh -c 'printf "Installing dependencies for %;\n" $$(composer install --working-dir=% --ansi)'
	@find fixtures -name 'composer.json' -type f -depth 2 -exec dirname '{}' \; | xargs -I % sh -c 'printf "Checking dependencies for %;\n" $$(composer outdated --direct --working-dir=% --ansi)'

.PHONY: cs
cs: ## Fixes CS
cs: gitignore_sort composer_normalize php_cs_fixer

.PHONY: cs_lint
cs_lint: ## Checks CS
cs_lint: composer_normalize_lint php_cs_fixer_lint

.PHONY: php_cs_fixer
php_cs_fixer: $(PHP_CS_FIXER_BIN)
	$(PHP_CS_FIXER)

.PHONY: php_cs_fixer_lint
php_cs_fixer_lint: $(PHP_CS_FIXER_BIN)
	$(PHP_CS_FIXER) --dry-run

.PHONY: composer_normalize
composer_normalize: composer.json vendor
	composer normalize

.PHONY: composer_normalize_lint
composer_normalize_lint: composer.json vendor
	composer normalize --dry-run

.PHONY: gitignore_sort
gitignore_sort:
	LC_ALL=C sort -u .gitignore -o .gitignore

.PHONY: phpstan
phpstan: $(PHPSTAN_BIN)
	$(PHPSTAN)

.PHONY: autoreview
autoreview: ## Runs the AutoReview checks
autoreview: cs_lint phpstan covers_validator

.PHONY: test
test: ## Runs all the tests
test: validate_package phpunit e2e

.PHONY: validate_package
validate_package:
	composer validate --strict

.PHONY: composer_root_version_check
composer_root_version_check: ## Runs all checks for the ComposerRootVersion app
composer_root_version_check:
	cd composer-root-version-checker; $(MAKE) --file Makefile check

.PHONY: composer_root_version_lint
composer_root_version_lint: ## Checks that the COMPOSER_ROOT_VERSION is up to date
composer_root_version_lint: .composer-root-version
	cd composer-root-version-checker; $(MAKE) --makefile Makefile check_root_version

.PHONY: composer_root_version_update
composer_root_version_update: ## Updates the COMPOSER_ROOT_VERSION
composer_root_version_update:
	rm .composer-root-version || true
	$(MAKE) .composer-root-version

.PHONY: covers_validator
covers_validator: $(COVERS_VALIDATOR_BIN)
	$(COVERS_VALIDATOR)

.PHONY: phpunit
phpunit: $(PHPUNIT_BIN) vendor
	$(PHPUNIT)

.PHONY: phpunit_coverage_infection
phpunit_coverage_infection: $(PHPUNIT_BIN) vendor
	$(PHPUNIT_COVERAGE_INFECTION)

.PHONY: phpunit_coverage_html
phpunit_coverage_html: ## Runs PHPUnit with code coverage with HTML report
phpunit_coverage_html: $(PHPUNIT_BIN) vendor
	$(PHPUNIT_COVERAGE_HTML)

.PHONY: infection
infection: $(COVERAGE_XML) vendor
#infection: $(INFECTION_BIN) $(COVERAGE_XML) vendor
	if [ -d $(COVERAGE_XML) ]; then $(INFECTION); fi

include .makefile/e2e.file
.PHONY: e2e
e2e: ## Runs end-to-end tests
e2e: e2e_004 \
		e2e_005 \
		e2e_011 \
		e2e_013 \
		e2e_014 \
		e2e_015 \
		e2e_016 \
		e2e_017 \
		e2e_018 \
		e2e_019 \
		e2e_020 \
		e2e_024 \
		e2e_025 \
		e2e_027 \
		e2e_028 \
		e2e_029 \
		e2e_030 \
		e2e_031 \
		e2e_032 \
		e2e_033 \
		e2e_034 \
		e2e_035 \
		e2e_036 \
		e2e_037

.PHONY: blackfire
blackfire: ## Runs Blackfire profiling
blackfire: vendor
	@echo "By https://blackfire.io"
	@echo "This might take a while (~2min)"
	$(BLACKFIRE) run php bin/php-scoper add-prefix --output-dir=build/php-scoper --force --quiet

.PHONY: clean
clean: ## Cleans all created artifacts
clean:
	git clean --exclude=.idea/ -ffdx


#
# Rules from files
#---------------------------------------------------------------------------

.composer-root-version:
	cd composer-root-version-checker; $(MAKE) --makefile Makefile dump_root_version
	touch -c $@

vendor: composer.lock .composer-root-version
	$(MAKE) vendor_install

# Sometimes we need to re-install the vendor. Since it has a few dependencies
# we do not want to check over and over, as unlike re-installing dependencies
# which is fast, those might have a significant overhead (e.g. checking the
# composer root version), we do not want to repeat the step of checking the
# vendor dependencies.
.PHONY: vendor_install
vendor_install:
	/bin/bash -c 'source .composer-root-version && composer install'
	touch -c vendor
	touch -c $(COMPOSER_BIN_PLUGIN_VENDOR)
	touch -c $(PHPUNIT_BIN)
	touch -c $(BOX_BIN)

composer.lock: composer.json
	@echo "$(@) is not up to date. You may want to run the following command:"
	@echo "$$ composer update --lock && touch -c $(@)"

vendor-hotfix: vendor
	composer dump-autoload
	touch -c $@

$(COMPOSER_BIN_PLUGIN_VENDOR): composer.lock .composer-root-version
	$(MAKE) --always-make vendor_install
	touch -c $@

$(PHPUNIT_BIN): composer.lock .composer-root-version
	$(MAKE) --always-make vendor_install
	touch -c $@

$(BOX_BIN): composer.lock .composer-root-version
	$(MAKE) --always-make vendor_install
	touch -c $@

$(COVERS_VALIDATOR_BIN): vendor-bin/covers-validator/vendor
vendor-bin/covers-validator/vendor: vendor-bin/covers-validator/composer.lock $(COMPOSER_BIN_PLUGIN_VENDOR)
	composer bin covers-validator install
	touch -c $@
vendor-bin/covers-validator/composer.lock: vendor-bin/covers-validator/composer.json
	@echo "$(@) is not up to date. You may want to run the following command:"
	@echo "$$ composer bin covers-validator update --lock && touch -c $(@)"

.PHONY: php_cs_fixer_install
php_cs_fixer_install: $(PHP_CS_FIXER_BIN)

$(PHP_CS_FIXER_BIN): vendor-bin/php-cs-fixer/vendor
	touch -c $@
vendor-bin/php-cs-fixer/vendor: vendor-bin/php-cs-fixer/composer.lock $(COMPOSER_BIN_PLUGIN_VENDOR)
	composer bin php-cs-fixer install
	touch -c $@
vendor-bin/php-cs-fixer/composer.lock: vendor-bin/php-cs-fixer/composer.json
	@echo "$(@) is not up to date. You may want to run the following command:"
	@echo "$$ composer bin php-cs-fixer update --lock && touch -c $(@)"

.PHONY: phpstan_install
phpstan_install: $(PHPSTAN_BIN)

$(PHPSTAN_BIN): vendor-bin/phpstan/vendor
	touch -c $@
vendor-bin/phpstan/vendor: vendor-bin/phpstan/composer.lock $(COMPOSER_BIN_PLUGIN_VENDOR)
	composer bin phpstan install
	touch -c $@
vendor-bin/phpstan/composer.lock: vendor-bin/phpstan/composer.json
	@echo "$(@) is not up to date. You may want to run the following command:"
	@echo "$$ composer bin phpstan update --lock && touch -c $(@)"

$(PHP_SCOPER_PHAR_BIN): $(BOX) bin/php-scoper $(SRC_FILES) vendor scoper.inc.php box.json.dist
	$(BOX) compile --no-parallel
	touch -c $@

$(COVERAGE_XML): $(PHPUNIT_BIN) $(SRC_FILES)
	$(MAKE) phpunit_coverage_infection
	touch -c "$@"
