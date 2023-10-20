export USER_ID := $(shell id -u)
export GROUP_ID := $(shell id -g)

.PHONY: up build down restart
up:
	env USER_ID=$(USER_ID) GROUP_ID=$(GROUP_ID) docker-compose up -d
build:
	env USER_ID=$(USER_ID) GROUP_ID=$(GROUP_ID) docker-compose up -d --build
down:
	env USER_ID=$(USER_ID) GROUP_ID=$(GROUP_ID) docker-compose down --remove-orphans
restart: down up
