# VM
mysqldump -uroot -prootpass imc2018> imc2018.sql
mysql -uroot -prootpass imc2019 < imc2018.sql