#!/bin/sh
#get sources files that contains PO fields
find ../code/application/views -name .svn -a -type d -prune -o -type f -print > /tmp/pofiles.txt
find ../code/application/config -name .svn -a -type d -prune -o -type f -print >> /tmp/pofiles.txt
find ../code/application/controllers -name .svn -a -type d -prune -o -type f -print >> /tmp/pofiles.txt
find ../code/application/errors -name .svn -a -type d -prune -o -type f -print >> /tmp/pofiles.txt
find ../code/application/helpers -name .svn -a -type d -prune -o -type f -print >> /tmp/pofiles.txt
find ../code/application/libraries -name .svn -a -type d -prune -o -type f -print >> /tmp/pofiles.txt
find ../code/application/models -name .svn -a -type d -prune -o -type f -print >> /tmp/pofiles.txt
find ../code/application/views -name .svn -a -type d -prune -o -type f -print >> /tmp/pofiles.txt
find ../code/application/language/multi -name .svn -a -type d -prune -o -type f -print >> /tmp/pofiles.txt
#create template file
xgettext --files-from=/tmp/pofiles.txt -o ajpotemplate.pot -L PHP --from-code=UTF-8
#update all PO files
find ../languages/ci/ -type f -name "*.po" -exec msgmerge -U {} ajpotemplate.pot -v \;
