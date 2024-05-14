build:
	@docker build -t bms-rest .

run:
	@echo "Running docker image on port 80 in background..."
	@docker run -p 8080:80 -d -v .:/var/www/html bms-rest
