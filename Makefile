.PHONY: $(MAKECMDGOALS)

.DEFAULT_GOAL := help
SHELL := /bin/bash

up: ## Up containers
	docker-compose up -d

up-alone: ## Up containers from current project and remove others
	docker-compose up -d --remove-orphans

down: ## Down containers in current project
	docker-compose down

down-all: ## Down containers in current project and others
	docker-compose down --remove-orphans

fixtures:
	docker-compose exec php php bin/console doctrine:schema:update --dump-sql && docker-compose exec php php bin/console doctrine:schema:update --force && docker-compose exec php php bin/console doctrine:fixtures:load

db-update:
	docker-compose exec php php bin/console doctrine:schema:update --dump-sql && docker-compose exec php php bin/console doctrine:schema:update --force

db-validate:
	docker-compose exec php php bin/console doctrine:schema:validate

composer-install:
	docker-compose exec php composer install

npm-install:
	docker-compose exec php npm install

yarn:
	docker-compose exec php yarn encore dev

build:
	docker-compose exec php composer install && docker-compose exec php npm install && docker-compose exec php npm rebuild node-sass && docker-compose exec php yarn encore dev && docker-compose exec php php bin/console doctrine:schema:update --dump-sql && docker-compose exec php php bin/console doctrine:schema:update --force

rm:
	docker-compose down --remove-orphans && docker stop $$(docker ps -qa) && docker network prune -f

host:
	/bin/bash ./hosts.sh

db-update-tests:
	docker-compose exec php php bin/console doctrine:schema:update --env=test --dump-sql && docker-compose exec php php bin/console doctrine:schema:update --force --env=test

fixtures-tests:
	docker-compose exec php php bin/console doctrine:schema:update --env=test --dump-sql && docker-compose exec php php bin/console doctrine:schema:update --env=test --force && docker-compose exec php php bin/console doctrine:fixtures:load --env=test