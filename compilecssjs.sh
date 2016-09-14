#!/bin/bash
# compiles/minifies all CSS and Javascript files
YUIC=/home/pete/opt/yuicompressor-2.4.7/build/yuicompressor-2.4.7.jar
CSSFOLDER=/var/www/vhosts/sallybarker.org/httpdocs/wordpress/wp-content/themes/sallybarker.org/css
JSFOLDER=/var/www/vhosts/sallybarker.org/httpdocs/wordpress/wp-content/themes/sallybarker.org/js
/usr/bin/lessc $CSSFOLDER/sb.less $CSSFOLDER/sb.css
/usr/bin/java -jar $YUIC $CSSFOLDER/sb.css --type css -v --charset UTF-8 -o $CSSFOLDER/sb.min.css
/usr/bin/java -jar $YUIC $JSFOLDER/sb.js --type js --charset UTF-8 --nomunge --preserve-semi -o $JSFOLDER/sb.min.js
