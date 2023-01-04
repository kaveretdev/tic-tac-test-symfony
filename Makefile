DOCKER_COMPOSE = docker compose
EXEC_PHP = $(DOCKER_COMPOSE) exec php
COMPOSER = $(EXEC_PHP) composer

down:
	cd ./.docker && ${DOCKER_COMPOSE} down
up:
	cd ./.docker && ${DOCKER_COMPOSE} up --remove-orphans --no-recreate -d

vendor: composer.lock
	$(COMPOSER) install

start: up vendor

restart: down start