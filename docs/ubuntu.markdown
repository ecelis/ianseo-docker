---
layout: default
title:  "Ubuntu"
date:   2022-03-17
categories: ianseo docker install
---

[![asciicast](https://asciinema.org/a/477827.svg)](https://asciinema.org/a/477827)

Install docker 

```
sudo apt install docker.io -y
```

Launch database

```
docker run -d --name ianseodb -e MARIADB_USER=ianseo \
  -e MARIADB_DATABASE=ianseo -e MARIADB_PASSWORD=ianseo \
  -e MARIADB_ROOT_PASSWORD=ianseo mariadb:10
```

Launch ianseo

```
docker run -d --name ianseo --link ianseodb:mysql \
  -p 8080:80 arqueria/ianseo

```

Browse to [http://localhost:8080](http://localhost:8080)
