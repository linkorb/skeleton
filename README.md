# skeleton

The LinkORB development project template

This repository can be used to start new simple projects like API's or webapps.


## Getting started

We assume that you have empty mysql root password on your developer machine.

### Try

Run next command to try:

```
sudo ./bin/try.sh
```

Now open [http://127.0.0.1:8080](http://127.0.0.1:8080) in your browser and enjoy.

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

## Test

Run:

```
cp phpunit.xml.dist phpunit.xml
vendor/bin/phpunit
```

## License

Please refer to the included LICENSE.md file

# Contact us

If you need any further information, be sure to <a href="http://engineering.linkorb.com/contact">send us a message!</a>

Have fun!
*The LinkORB Engineering Team*

## Brought to you by the LinkORB Engineering team

<img src="http://www.linkorb.com/d/meta/tier1/images/linkorbengineering-logo.png" width="200px" /><br />
Check out our other projects at [engineering.linkorb.com](http://engineering.linkorb.com).

Btw, we're hiring!
