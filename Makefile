phar:
	phar-composer build .
	chmod +x ./abraflexi-backup.phar

run:
	./abraflexi-backup.phar .env
