#!/usr/bin/env sh
/usr/local/php/bin/php /home/web/ask-9939-com/crontab/fetch120/fetch_120_ask.php 2000 >> /home/web/ask-9939-com/crontab/log/`date +%Y-%m-%d`_fetch120.log
