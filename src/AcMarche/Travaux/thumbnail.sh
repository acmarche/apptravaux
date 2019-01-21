#!/bin/bash

for FILE in `find web/files/ -type f -exec file {} \; | awk -F: '{ if ($2 ~/[Ii]mage|EPS/) print $1}'`
do
    string_replace=""
    result_string="${FILE/web\//$string_replace}"
    php app/console liip:imagine:cache:resolve $result_string --filters=my_thumb --filters=zoom

done

chown www-data:www-data web -R
