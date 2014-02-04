echo "drop database kambja;" | mysql
echo "create database kambja;" | mysql
mysql kambja < database-schema.sql
mysql kambja < data.sql

