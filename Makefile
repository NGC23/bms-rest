build:
	# Temp to pull mysql from docker hub
	@echo [1]building mysql database for local development
	@docker pull mysql:8.0
	@echo [2]Done pulling mysql from dockerhub, this will be version 8.0 
	@echo [3]establish network for the database and container to communicate for local development..
	@echo [4]remove old networks running related to jeeves...
	@-docker network rm jeeves
	@echo [5]cleaned
	@docker network create --driver=bridge --attachable jeeves
	@echo [6]network bridge "jeeves" created
	@echo [7]build rest api...
	@docker build -t bms-rest .
	@echo [8]completed.


#  -e MYSQL_HOST=localhost -e MYSQL_PORT=3306 -e MYSQL_USER=root -e MYSQL_DATABASE=jeeves -e XDEBUG_MODE=coverage


run:
	@echo "Rest api starting.."
	@docker run --network jeeves -v .:/var/www/html -p 8080:80 --name bms-rest -d bms-rest
	@echo Starting mysql
	@docker run --network jeeves --name jeeves-mysql -p 3306:3306 -e MYSQL_ROOT_PASSWORD=gymmer4Life2024# -e XDEBUG_MODE=coverage -d mysql:8.0
