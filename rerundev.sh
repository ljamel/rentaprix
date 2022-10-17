# for run this script tap "bash rerundev.sh"

# stop process server symfony befor rerun
symfony server:stop

# clear cache
php bin/console cache:clear

# update css and js
yarn dev

# run server dev
# if not work in your brother use this commande directli in CLI "symfony server:start"
symfony server:start