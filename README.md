| Service              | Environnement    | Port Interne | Port Exposé (Hôte)  | 
|----------------------|------------------|--------------|---------------------| 
| Service Web (Nginx)  | Développement    | 80           | 8080                | 
| Service Web (Nginx)  | Préproduction    | 80           | 8081                | 
| Service Web (Nginx)  | Production       | 80           | 8082                | 
| Code PHP             | Développement    | 80           | 9090                | 
| Code PHP             | Préproduction    | 80           | 9091                | 
| Code PHP             | Production       | 80           | 9092                | 
| MariaDB              | Développement    | 3306         | 33060               | 
| MariaDB              | Préproduction    | 3306         | 33061               | 
| MariaDB              | Production       | 3306         | 33062               | 
| FTP                  | Machine VM       | 21           | 21                  | 
| SSH                  | Machine VM       | 22           | 22                  | 

=======================================================

To test if dump and restore are working :

- db-dump.sh
- docker exec -it dev-db-container mariadb -u vguser -pvgpassword videgrenier -e "SELECT COUNT(*) FROM articles;"
- docker exec -it dev-db-container mariadb -u vguser -pvgpassword videgrenier -e "DELETE FROM articles LIMIT 10;"
- docker exec -it dev-db-container mariadb -u vguser -pvgpassword videgrenier -e "SELECT COUNT(*) FROM articles;"
- db-restore.sh
- docker exec -it dev-db-container mariadb -u vguser -pvgpassword videgrenier -e "SELECT COUNT(*) FROM articles;"

======================================================= 


