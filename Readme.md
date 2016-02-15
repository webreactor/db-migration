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
* Create settings file `db-migration.yml` using `db-migration --init`
* Check migrations state using `db-migration --list`
* Load migrations using `db-migration --load`
* If migration fails. After fix, run `db-migration --reset-locked`

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

Name fo parameters at `.yml` and cli are the same.

`.yml` file supports env varialbes using `$`. Put `$$` if you need to escape the symbol

Example:

`db-migrations --list`
with `db-migration.yml` at current folder:
```yml
user: $MYSQL_USERNAME
password: $MYSQL_PASSWORD
database: allication
migrations: db-migrations
create-database: true
```

Same result using cli arguments:
```
db-migrations --list \
    --user="$MYSQL_USERNAME" \
    --password="$MYSQL_PASSWORD" \
    --database="allication" \
    --migrations="db-migrations" \
    --create-database="true"
```

**If some scripts failed migration will be locked intil you reset state**\
In case if some migration file failed
* Check output of last `load`
* Check what migration is failed `db-migration --list`
* Fix you app and database
* Run `db-migration --reset-locked`
* Continue migration `db-migration --load`

## Parameters
```
Usage:
  dbml <command> [options] [migration options]

Commands:
  --init          Create config YML file
  --load          Load new migrations
  --list          List all loaded and new migration files
  --list={limit}  List last {limit} loaded and new migration files
  --new           List of new migrations
  --create={name} Create new empty migration file
  --reset={id}    Reset migration state
  --reset-locked  Reset all migration with state no new or migrated
  --help

Options:
  --clean         Clean output, no headers
  --config        Path to config YML file. Default db-migration.yml at current folder

Migration Options:
  --migrations    Path to migration scripts
  --host          Default is localhost
  --port          Default is 3306
  --unix_socket   Path to socket, has more priority than host
  --user
  --password
  --database          Database name
  --create-database   Creates db if not exists
  --table             Table name for migration state. Default is db-migrations
  --extra             Extra parameters will bepassed to load a migration command

All options can be specified in YML file
```
