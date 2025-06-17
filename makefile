PHONY: build-dev-containers
db_username = vguser
db_password = vgpassword
db_name = videgrenier


connect-dev-db:
	docker exec -it dev-db-container mariadb -u $(db_username) -p$(db_password) $(db_name)

connect-preprod-db:
	docker exec -it preprod-db-container mariadb -u $(db_username) -p$(db_password) $(db_name)

connect-prod-db:
	docker exec -it prod-db-container mariadb -u $(db_username) -p$(db_password) $(db_name)

restore-db:
	docker exec -it dev-db-container mariadb -u vguser -pvgpassword -e "CREATE DATABASE videgrenier;"

delete-containers:
	cd scripts && bash delete_containers.sh

build-containers:
	cd scripts && bash build_containers.sh

update-preprod-code:
	cd scripts && bash update_preprod_code.sh

attach-container:
	docker exec -it preprod-php-container bash