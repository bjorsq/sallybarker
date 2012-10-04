#!/bin/bash
# compiles/minifies all CSS and Javascript files
YUIC=/home/pete/opt/yuicompressor-2.4.7/build/yuicompressor-2.4.7.jar
THEMEFOLDER=/var/www/vhosts/pe/sallybarker/httpdocs/wp-content/themes/sallybarker.org
CSSFOLDER=$THEMEFOLDER/css
cd $CSSFOLDER/less
/usr/bin/lessc $CSSFOLDER/less/sb.less > $CSSFOLDER/sb.css
/usr/bin/lessc $CSSFOLDER/less/editor-style.less > $THEMEFOLDER/editor-style.css
/usr/bin/java -jar $YUIC $CSSFOLDER/sb.css --type css -v --charset UTF-8 -o $CSSFOLDER/sb.min.css
cat $THEMEFOLDER/js/sb.js $THEMEFOLDER/js/libs/bootstrap-dropdown.js $THEMEFOLDER/js/libs/bootstrap-carousel.js > $THEMEFOLDER/js/sb.all.js
/usr/bin/java -jar $YUIC $THEMEFOLDER/js/sb.all.js --type js -v --charset UTF-8 -o $THEMEFOLDER/js/sb.min.js
