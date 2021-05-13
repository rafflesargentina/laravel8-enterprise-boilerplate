#!/bin/bash

umask 0002

chmod 775 ./storage/logs 

find ./bootstrap/cache -type d -exec chmod 775 {} \;
find ./storage/framework -type d -exec chmod 775 {} \;
find ./storage/framework -type f -exec chmod 664 {} \;
