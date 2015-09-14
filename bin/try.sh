#!/bin/sh

DATABASE='sandbox_linkorb_skeleton'

if [ ! -f composer.json ]; then
    echo "You need to run this command from project root."
    echo "Right from directory where composer.json placed."
    exit;
fi

echo "Test mysql connection..."
mysql -u root -e "SELECT 'SUCCESS' as Status;"
if [ $? -eq 0 ]; then
    echo "Mysql connection SUCCEED"
else
    echo "Mysql connection FAILED"
    echo "You need to have mysql server running with empty root password."
    exit;
fi

# Backup parameters
if [ -f app/config/parameters.yml ]; then
    cp app/config/parameters.yml app/config/parameters.yml.bak
fi

cp app/config/parameters.yml.dist app/config/parameters.yml

if [ ! -f /share/config/database/$DATABASE.conf ]; then
    sudo cp app/config/$DATABASE.conf.dist /share/config/database/$DATABASE.conf
fi

composer install
bower install
# npm install
# grunt

mysqladmin -u root -f drop $DATABASE
mysqladmin -u root create $DATABASE

vendor/bin/dbtk-schema-loader schema:load app/schema.xml $DATABASE --apply
vendor/bin/haigha fixtures:load test/fixture/example-data.yml $DATABASE

./bin/run.sh

# Restore parameters
if [ -f app/config/parameters.yml.bak ]; then
    cp app/config/parameters.yml.bak app/config/parameters.yml
    echo "\nparameters.yml restored"
fi
