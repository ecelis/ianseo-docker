---
layout: default
title:  "Let's try I@anseo on Docker"
date:   2022-03-17
categories: ianseo docker install
---

<script id="asciicast-474827" src="https://asciinema.org/a/477827.js" async></script>

[![asciicast](https://asciinema.org/a/477827.svg)](https://asciinema.org/a/477827)

Install docker `sudo apt install docker.io -y`

Launch database

```
docker run -d --name ianseodb -e MARIADB_USER=ianseo \
  -e MARIADB_DATABASE=ianseo -e MARIADB_PASSWORD=ianseo \
  -e MARIADB_ROOT_PASSWORD=ianseo mariadb:10
```

Launch ianseo

```
docker run -d --name ianseo --link ianseodb:mysql -p 8080:80 arqueria/ianseo

```
