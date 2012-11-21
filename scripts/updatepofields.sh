#!/bin/sh
#get sources files that contains PO fields
find ../views -name .svn -a -type d -prune -o -type f -print > pofiles.txt
find ../config -name .svn -a -type d -prune -o -type f -print >> pofiles.txt
find ../controllers -name .svn -a -type d -prune -o -type f -print >> pofiles.txt
find ../errors -name .svn -a -type d -prune -o -type f -print >> pofiles.txt
find ../helpers -name .svn -a -type d -prune -o -type f -print >> pofiles.txt
find ../libraries -name .svn -a -type d -prune -o -type f -print >> pofiles.txt
find ../models -name .svn -a -type d -prune -o -type f -print >> pofiles.txt
find ../views -name .svn -a -type d -prune -o -type f -print >> pofiles.txt
find ../language/multi -name .svn -a -type d -prune -o -type f -print >> pofiles.txt
#create template file
xgettext --files-from=pofiles.txt -o ajpotemplate.pot -L PHP --from-code=UTF-8
#update all PO files
find . -type f -name "*.po" -exec msgmerge -U {} ajpotemplate.pot -v \;

