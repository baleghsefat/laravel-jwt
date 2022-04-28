COMPOSE_FILE=docker-compose.yml

rebuild:
	docker-compose -f $(COMPOSE_FILE) kill
	docker-compose -f $(COMPOSE_FILE) rm --force
	docker-compose -f $(COMPOSE_FILE) build
	docker-compose -f $(COMPOSE_FILE) up -d

command:
	docker-compose -f $(COMPOSE_FILE) exec php zsh
