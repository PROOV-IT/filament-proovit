SHELL := /bin/bash

QA := vendor/bin/pint --test src tests && vendor/bin/phpstan analyse src tests --memory-limit=1G && vendor/bin/pest

.PHONY: format analyse test qa

format:
	@vendor/bin/pint src tests

analyse:
	@vendor/bin/phpstan analyse src tests --memory-limit=1G

test:
	@vendor/bin/pest

qa:
	@$(QA)
