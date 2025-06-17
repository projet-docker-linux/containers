PHONY: build-dev-containers
db_username = vguser
db_password = vgpassword
db_name = videgrenier

build-containers:
	cd scripts && bash build_containers.sh

connect-db:
	cd scripts && bash connect_database.sh

dump-db:
	cd scripts && bash dump_database.sh

restore-db:
	cd scripts && bash restore_database.sh

attach:
	cd scripts && bash attach_container.sh

delete-containers:
	cd scripts && bash delete_containers.sh
