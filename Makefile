.PHONY: install tests tests.ci phpstan.run security.check tests.var-dump-checker.ci

install:
	composer install

tests: phpstan.run security.check tests.var-dump-checker.ci
tests.ci: phpstan.run security.check tests.var-dump-checker.ci

phpstan.run:
	bin/phpstan analyse --level=1 src

security.check:
	bin/security-checker security:check

tests.var-dump-checker.ci:
	bin/var-dump-check --symfony --exclude vendor --exclude demo .
