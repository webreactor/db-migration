DBML - DataBase Migration scripts Loader
========================================

## Start using:

* Create a folder at you repo for sql migration files
* Name you sql files with pattern yyyy-mm-dd-nnn[-comment].sql. 
* Put db-migration.yml with connection parameters to your project, see parameters below
* Run db-migration at same folder with .yml file:
* db-migration --list _to see current state and check file naming_
* db-migration --load _to load migrations_

nnn - unique number within a day.
For example:
```
2016-01-25-001.sql
2016-01-25-002.sql
2016-01-26-001.sql
2016-01-27-001.sql
```

If some scripts failed migration will be locked:
* Fix you app
* Run db-migration --reset-locked
* Continue migration


## Parameters:
```
Usage:
  dbml <command> [options] [migration options]

Commands:
  --load          Load new migrations
  --list          List on loaded and new migration files
  --new           List of new migrations
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
  --unix_socket   Path to socket. has more priority that host
  --user
  --password
  --database          Database name
  --create-database   Creates db if not exists
  --table             Table name for migration state. Default is db-migrations
  --extra             Extra parameters will bepassed to load a migration command

All options can be specified in YML file
```