Database Migration Scripts Loader
========================================

## How to Install

Use precompiled:
```bash
curl -O https://github.com/webreactor/db-migration/releases/download/0.1.0/db-migration
chmod a+x db-migration
mv db-migration /usr/local/bin/
```
Or build:
```bash
git clone https://github.com/webreactor/db-migration.git
cd db-migration
make install
```

For windows users:
* Download precompiled
* Create `db-migration.bat` file with `@php "%~dp0db-migration" %*`
* Make both files available through PATH (copy to `C:\WINDOWS` for example)

## Start using
* Create a folder at you repo for sql migration files
* Name you sql files with pattern `yyyy-mm-dd-nnn[-note].sql.`
* Create settings file `db-migration.yml` using `db-migration init`
* Check migrations state using `db-migration list`
* Load migrations using `db-migration load`
* If migration fails. After fix, run `db-migration reset-locked`

## Migration files
Example:
```
2016-01-25-001.sql
2016-01-25-002.sql
2016-01-26-001-votes.sql
2016-01-27-001.sql
```
Pattern: `yyyy-mm-dd-nnn[-note].sql.`

`nnn` - unique number within a day.

## Before and after scripts

* Each migration file can have two associated executable files that will be executed before and after the migration.
* Use patterns `yyyy-mm-dd-nnn-before.sh` and `yyyy-mm-dd-nnn-after.sh` to name files
* Make a sure that files are executable
* Before and after scripts run only once when the associated file is migrating

Example:
```
2016-01-25-001.sql
2016-01-25-002-before.sh
2016-01-25-002.sql
2016-01-25-002-after.sh
2016-01-26-001-votes.sql
2016-01-27-001.sql
```

## Configuration
There are two ways to pass parameters:
* [Command line parameters](#parameters)
* `db-migration.yml` file at current folder

Name for parameters at `.yml` and cli are the same.

`.yml` file supports env variables using `$`. Put `$$` if you need to escape the symbol

Example:

`db-migration list`
with `db-migration.yml` at current folder:
```yml
user: $MYSQL_USERNAME
password: $MYSQL_PASSWORD
database: aplication
migrations: db-migrations
create-database: yes
```

Same result using cli arguments:
```
db-migration list \
    --user "$MYSQL_USERNAME" \
    --password "$MYSQL_PASSWORD" \
    --database= application" \
    --migrations "db-migrations" \
    --create-database "yes"
```

**If some scripts failed, migration will be locked until you reset state**\
In case if some migration file failed
* Check output of last `load`
* Check what migration is failed `db-migration list`
* Fix you app and database
* Run `db-migration reset-locked`
* Continue migration `db-migration load`

## Parameters
```
Usage:
  db-migration <command> [--option value]

Commands:
  init            Create config YML file
  load            Load new migrations
  list            List all loaded and new migration files
  list {limit}    List last {limit} loaded and new migration files
  new             List of new migrations
  create {name}   Create new empty migration file
  reset {id}      Reset migration state
  reset-locks     Reset all migration with state no new or migrated
  config          Show current config
  help            Show help

Options:
  Full name        | Short | Default          | Note
-----------------------------------------------------
  --clean                    no                 (yes|no) Clean output, no headers
  --config           -f      db-migration.yml   Path to config YML file. Default  at current folder
  --migrations       -m      migrations         Path to migration scripts
  --driver           -r      mysql              Database driver

'mysql' driver:
  Full name        | Short | Default          | Note
-----------------------------------------------------
  --host             -h      localhost
  --port             -P      3306
  --user             -u
  --password         -p
  --unix_socket      -s
  --database         -d                         Database name
  --create-database  -c      yes                (yes|no) Creates db if not exists
  --extra            -x                         Extra parameters will bepassed to load a migration command
  --table            -t      db_migrations      Table name for migration state. Default is db-migrations
  --migration-file-extention -j      sql

All options can be specified in YML file. Pleae use full name of option.


```
