PHONY: build-dev-containers
db_username = vguser
db_password = vgpassword
db_name = videgrenier

build-dev-containers:
	docker-compose -f docker-compose.dev.yml down && docker-compose -f docker-compose.dev.yml up --build -d 

connect-dev-db:
	docker exec -it dev-db-container mariadb -u $(db_username) -p$(db_password) $(db_name)

build-preprod-containers:
	docker-compose -p preprod_net -f docker-compose.preprod.yml down && docker-compose -p preprod -f docker-compose.preprod.yml up --build -d 

connect-preprod-db:
	docker exec -it preprod-db-container mariadb -u $(db_username) -p$(db_password) $(db_name)

build-prod-containers:
	docker-compose -p prod_net -f docker-compose.prod.yml down && docker-compose -p prod -f docker-compose.prod.yml up --build -d 

connect-prod-db:
	docker exec -it prod-db-container mariadb -u $(db_username) -p$(db_password) $(db_name)

restore-db:
	docker exec -it dev-db-container mariadb -u vguser -pvgpassword -e "CREATE DATABASE videgrenier;"

delete-all-containers:
	cd scripts && bash delete_all_containers.sh

build-containers:
	cd scripts && bash build_containers.sh