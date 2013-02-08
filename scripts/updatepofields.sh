#!/bin/sh
#get sources files that contains PO fields
find /opt/code/application/views -name .svn -a -type d -prune -o -type f -print > /tmp/pofiles.txt
find /opt/code/application/config -name .svn -a -type d -prune -o -type f -print >> /tmp/pofiles.txt
find /opt/code/application/controllers -name .svn -a -type d -prune -o -type f -print >> /tmp/pofiles.txt
find /opt/code/application/errors -name .svn -a -type d -prune -o -type f -print >> /tmp/pofiles.txt
find /opt/code/application/helpers -name .svn -a -type d -prune -o -type f -print >> /tmp/pofiles.txt
find /opt/code/application/libraries -name .svn -a -type d -prune -o -type f -print >> /tmp/pofiles.txt
find /opt/code/application/models -name .svn -a -type d -prune -o -type f -print >> /tmp/pofiles.txt
find /opt/code/application/views -name .svn -a -type d -prune -o -type f -print >> /tmp/pofiles.txt
find /opt/code/application/language/multi -name .svn -a -type d -prune -o -type f -print >> /tmp/pofiles.txt
#create template file
xgettext --files-from=/tmp/pofiles.txt -o /opt/code/scripts/ajpotemplate.pot -L PHP --from-code=UTF-8
#update all PO files
find /opt/languages/ci/ -type f -name "*.po" -exec msgmerge -U {} /opt/code/scripts/ajpotemplate.pot -v \;

