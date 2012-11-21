#!/bin/sh
find . -name *.mo -type f -exec rm {} \;
find . -name *.po -type f -exec msgfmt {} -v -o {}mo \;
find . -name "*.pomo" -exec rename s/.pomo/.mo/ {} \;
find . -name "*.mo" -type f -exec chmod 644 {} \;
echo "change owner to admin!"
find . -name "*.mo" -type f -exec chown admin:admin {} \;
