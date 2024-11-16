#cd .git/objects
#ls -al
#sudo chown -R yourname:yourgroup *
#nvm use v16
npm i



composer install

npx mix watch
sudo find . -type d -exec chmod 775 {} \;
sudo find . -type d -exec chown ubuntu {} \;
sudo find . -type f -exec chmod 664 {} \;
sudo find . -type f -exec chown ubuntu {} \;
sudo chown -R $USER:www-data storage
sudo chown -R $USER:www-data bootstrap/cache node_modules
sudo chmod -R 775 storage node_modules
sudo chmod -R 775 bootstrap/cache
sudo chmod -R 777 storage
sudo chmod -R 777 public
php artisan storage:link
php artisan optimize:clear
php artisan cache:clear
sudo systemctl restart apache2.service
