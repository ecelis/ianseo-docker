# i@nseo

i@nseo is a software for managing archery tournaments results

This is an un-official repository, the official site for this software
is http://www.ianseo.net/

##### Ernesto Celis notes

Mine is a fork from Brian Nelson's `brian-nelson/ianseo` repository but
mixed with the official release, since Brian's repository seems
unmaintained.

This fork goal is running i@anseo in Docker containers. Docker setup is
out of scope.

## How to use this image

Copy `sample.env` to `.env` and edit to suit your requirements.

```
cp sample.env .env`
```

### Run withDocker compose

```
docker compose up -d
```

### Run from command line, step by step

First launch a MariaDB container.

```
docker run -d --env-file .env --name ianseodb mariadb:lts
```

Now launch the ianseo container linked to the database container.

```
docker run -d --name ianseo --link ianseodb:mysql -p 8080:80 arqueria/ianseo
```

Browse to http://127.0.0.1:8080 and follow the instructions to
finish the installation.

⚠️  In the **Step 2: Database connection data** of i@anseo has a default of
`localhost` for Database host, change it for the name of the MariaDB
container, `ianseodb` in the example above.

⚠️  Fill the field for the **ADMIN Password to create users and databases**
with the value of the variable `MARIADB_ROOT_PASSWORD` of the MAriaDB
container, `ianseo` in the example above.

## Environment Variables

One of `MARIADB_ROOT_PASSWORD`, `MARIADB_ALLOW_EMPTY_ROOT_PASSWORD`, or
`MARIADB_RANDOM_ROOT_PASSWORD`, is required. The other environment
variables are optional.

**MARIADB_ROOT_PASSWORD / MYSQL_ROOT_PASSWORD**

This specifies the password that will be set for the MariaDB root
superuser account. In the above example, it was set to my-secret-pw.

**MARIADB_ALLOW_EMPTY_ROOT_PASSWORD / MYSQL_ALLOW_EMPTY_PASSWORD**

Set to a non-empty value, like yes, to allow the container to be started
with a blank password for the root user. NOTE: Setting this variable to
yes is not recommended unless you really know what you are doing, since
this will leave your MariaDB instance completely unprotected, allowing
anyone to gain complete superuser access.

**MARIADB_RANDOM_ROOT_PASSWORD / MYSQL_RANDOM_ROOT_PASSWORD**

Set to a non-empty value, like yes, to generate a random initial
password for the root user. The generated root password will be printed
to stdout (`GENERATED ROOT PASSWORD: .....`).

**MARIADB_DATABASE / MYSQL_DATABASE**

This variable allows you to specify the name of a database to be created
on image startup.

**MARIADB_USER / MYSQL_USER, MARIADB_PASSWORD / MYSQL_PASSWORD**

These are used in conjunction to create a new user and to set that
user's password. Both user and password variables are required for a
user to be created. This user will be granted all access (corresponding
to `GRANT ALL`) to the `MARIADB_DATABASE` database.

Do note that there is no need to use this mechanism to create the root
superuser, that user gets created by default with the password specified
by the `MARIADB_ROOT_PASSWORD` / `MYSQL_ROOT_PASSWORD` variable.

Refer to the MariaDB official repository for deeper information about
variable environments https://hub.docker.com/_/mariadb

## Fetch newer i@nseo releases

```
npm run ianseo:fetch <YYYYMMDD>
```

## Build the docker image

To only build the i@anseo image for the current CPU architechture run:

```
npm run ianseo:build
```

To build images for both x86_64, aarch64 and arm7, run:

```
npm run ianseo:build:multi
```

## Relase and Publish images to Docker registries

There are github actions wired to this repository, after mergin a branch into
`main` you must tag the commit to publish with semantic versioning.
Ex: v2022.01.01.1 vYEAR.MONTH.DAY.REVISION. Official I@anseo versioning has
integers as REVISION, however docker releases may add an alphabetic charater to
the REVISION integer to support my own releases linked to official ones.

```
git checkout main
git pull
git tag v<YYYY>.<mm>.<dd>.<REVISION>
git push origin v<YYYY>.<mm>.<dd>.<REVISION>
```
