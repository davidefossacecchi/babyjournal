export USER_ID := $(shell id -u)
export GROUP_ID := $(shell id -g)
THIS_FILE := $(lastword $(MAKEFILE_LIST))
.PHONY: up build down restart require console run-script
# considero come argomenti tutti gli argomenti passati a make ad esclusione del primo
# i target che li usano dovrenno poi essere lanciati con -- per indicare la fine degli argomenti e opzioni di make
args := $(wordlist 2,$(words $(MAKECMDGOALS)),$(MAKECMDGOALS))
up:
	docker-compose up -d
build:
	docker-compose up -d --build
down:
	docker-compose down --remove-orphans
restart: down up
console: bin/console
	docker-compose exec php bin/console $(args)
require:
	docker-compose exec php composer require $(args)
run-script: composer.json
	docker-compose exec php composer run $(args)
help:
help:
	make -pRrq  -f $(THIS_FILE) : 2>/dev/null | awk -v RS= -F: '/^# File/,/^# Finished Make data base/ {if ($$1 !~ "^[#.]") {print $$1}}' | sort | egrep -v -e '^[^[:alnum:]]' -e '^$@$$'
