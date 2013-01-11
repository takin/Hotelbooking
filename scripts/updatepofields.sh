#!/bin/sh
#get sources files that contains PO fields
find /opt/application/views -name .svn -a -type d -prune -o -type f -print > /tmp/pofiles.txt
find /opt/application/config -name .svn -a -type d -prune -o -type f -print >> /tmp/pofiles.txt
find /opt/application/controllers -name .svn -a -type d -prune -o -type f -print >> /tmp/pofiles.txt
find /opt/application/errors -name .svn -a -type d -prune -o -type f -print >> /tmp/pofiles.txt
find /opt/application/helpers -name .svn -a -type d -prune -o -type f -print >> /tmp/pofiles.txt
find /opt/application/libraries -name .svn -a -type d -prune -o -type f -print >> /tmp/pofiles.txt
find /opt/application/models -name .svn -a -type d -prune -o -type f -print >> /tmp/pofiles.txt
find /opt/application/views -name .svn -a -type d -prune -o -type f -print >> /tmp/pofiles.txt
find /opt/application/language/multi -name .svn -a -type d -prune -o -type f -print >> /tmp/pofiles.txt
#create template file
xgettext --files-from=/tmp/pofiles.txt -o ajpotemplate.pot -L PHP --from-code=UTF-8
#update all PO files
find /opt/languages/ci/ -type f -name "*.po" -exec msgmerge -U {} ajpotemplate.pot -v \;

