#!/bin/bash

SHELL_PATH=`dirname "$0"`

LANGUAGE="en"

rst2html $SHELL_PATH/$LANGUAGE/index.rst --stylesheet=$SHELL_PATH/docs.css > $SHELL_PATH/$LANGUAGE/index.html
rst2pdf $SHELL_PATH/$LANGUAGE/index.rst 

echo "done"