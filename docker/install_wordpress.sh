#!/usr/bin/env sh

echo "================================================================="
echo "Nexpress is running the installation..."
echo "================================================================="

# exit on error
set -e

mysql_ready='netcat -z www_db_1 3306'

if ! $mysql_ready
then
    printf 'Waiting for MySQL.'
    while ! $mysql_ready
    do
        printf '.'
        sleep 1
    done
    echo
fi

if wp core is-installed
then
    echo "WordPress is already installed, exiting."
    exit
fi

wp core download --force

[ -f wp-config.php ] || wp config create \
    --dbhost="$WORDPRESS_DB_HOST" \
    --dbname="$WORDPRESS_DB_NAME" \
    --dbuser="$WORDPRESS_DB_USER" \
    --dbpass="$WORDPRESS_DB_PASSWORD"

# Activate nexpress theme and delete other themes
#wp theme activate postlight-headless-wp
#wp theme delete twentysixteen twentyseventeen twentynineteen

# remove preinstalled plugins
wp plugin delete akismet hello

# install and activate default plugins
wp plugin install --activate --force \
    advanced-custom-fields \
    https://github.com/wp-graphql/wp-graphql/archive/master.zip \
    https://github.com/wp-graphql/wp-graphql-acf/archive/master.zip \

# rename uncategorised category
wp term update category 1 --name="Featured"

# create header menu
wp menu create "Header Menu"
wp menu item add-post header-menu 1
wp menu item add-post header-menu 2
wp menu item add-term header-menu category 1
wp menu item add-custom header-menu "Read about the Starter Kit" https://postlight.com/trackchanges/introducing-postlights-wordpress-react-starter-kit
wp menu location assign header-menu header-menu
wp post update 1 --post_title="Nexpress is great" --post_name=nexpress-is-great

echo "================================================================="
echo "Nexpress installation is now complete!"
echo "================================================================="
