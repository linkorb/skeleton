# skeleton
The LinkORB development project template

This repository can be used to start new simple projects like API's or webapps.

## Getting started

### Install dependencies:
```
composer install
```

### Configuration

Copy config template files:
```
cp app/config/parameters.yml.dist app/config/parameters.yml
cp app/config/security.yml.dist app/config/security.yml
```
Now edit `app/config/parameters.yml` and `app/config/security.yml` to your situation

### Initializing database schema

The database schema is defined in `app/schema.xml`. You can load this schema in your database 
using [DBTK Schema Loader](https://github.com/dbtk/schema-loader) (view it's README.md for more information)

Note: Create the database before loading the schema.

```
vendor/bin/dbtk-schema-loader schema:load app/schema.xml mysql://username:password@localhost/skeleton
```

### Loading example data / fixtures

After initializing the database schema, you can load it with some example data through
[linkorb/haigha](https://github.com/linkorb/haigha) (View Haigha's README.md for further information):

```
vendor/bin/haigha fixtures:load test/fixture/example-data.yml mysql://username:password@localhost/skeleton
```

### Start the server

```
php -S 0.0.0.0:8080 -t web/
```
Now open [http://127.0.0.1:8080](http://127.0.0.1:8080) in your browser.

### Run console command

A simple console command is included, you can run it like this:

```
app/console skeleton:example
```

## Implementing your own application

Now that the service is running, simply delete the `.git` directory, and initialize a new clean git project:

```
rm -rf .git
git init
```

Now you can edit `app/config/routes.yml` to add new routes, and implement the controllers in `src/Controller/ExampleController.php`
New templates for your route can be added to `templates/`

If you need any further information, be sure to <a href="http://engineering.linkorb.com/contact">send us a message!</a>

Have fun!  
*The LinkORB Engineering Team*
