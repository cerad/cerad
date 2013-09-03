
# This is your workspace directory which will hold one or more application repositories
~/zayso2016

# This is the directory used to clone your application repository
~/zayso2016/myapp>

# To create a fresh database called zayso with username of cerad

~/zayso2016/myapp> mysql -uroot

mysql> CREATE DATABASE zayso;
mysql> GRANT ALL ON zayso.* TO 'cerad"@"localhost';
mysql> FLUSH PRIVILEGES;

# To change zayso's password
mysql> SET PASSWORD FOR 'cerad"@"localhost' = PASSWORD('new_password');
mysql> FLUSH PRIVILEGES;

# List databases, switch to a database, list tables, show database layout
mysql> SHOW DATABASES;
mysql> USE zayso;
mysql> SHOW TABLES;
mysql> DESCRIBE table_name;

# From symfony
# Assume you have an entity manager called zayso pointing to the zayso database
# If you are using the default entity manager then skip the --em=zayso portion

# This will generate an error but will actually list the various doctrine commands
# Probably a cleaner way to do it
# Handy if you forget the commands

~/zayso2016/myapp> ./app/console list doctrine

# As with all console commands, a --help will reveal details

~/zayso2016/myapp> ./app/console doctrine:schema:update --help

# This will completely drop the database and rebuild
# All tables will be lost

~/zayso2016/myapp> ./app/console doctrine:database:drop   --em=zayso --force
~/zayso2016/myapp> ./app/console doctrine:database:create --em=zayso

# This will drop only the tables managed by the entity manager 
# And then recreate them
# It differs from the database commands since a given database can be pointed to by multiple entity managers.
# Only the tables managed by the specified entity manager will be impacted.

~/zayso2016/myapp> ./app/console doctrine:schema:drop      --em=zayso --force
~/zayso2016/myapp> ./app/console doctrine:schema:create    --em=zayso

# Use these command to modify an existing schema without droping the tables
# The first one only shows the proposed sql changes
# The second one actually applies the changes

~/zayso2016/myapp> ./app/console doctrine:schema:update    --em=zayso --dump-sql
~/zayso2016/myapp> ./app/console doctrine:schema:update    --em=zayso --force

# This will show a list of all mapped entities

./console doctrine:mapping:info


