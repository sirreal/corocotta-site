#!/bin/bash
set -u
# set -x

git ci -am 'push/pull' && ssh jon 'su -lc '"'"'cd corocotta_dev_site; git pull -q'"'"' corocotta'
exit 0
