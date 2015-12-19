
$ mkdir projects
$ chmod 777 projects
$ cd projects

$ git clone https://github.com/Corretx/bgse-dashboard-project-group9.git
$ cd bgse-dashboard-project-group9

$ sudo mysql
> GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' IDENTIFIED BY 'root';
> GRANT ALL PRIVILEGES ON *.* TO 'root'@'localhost' IDENTIFIED BY 'root';
> \q


$ sudo chmod 777 /var/www/html

$ sh setup.sh install


http://MY_SERVER/MyApp
