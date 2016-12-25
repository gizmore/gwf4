#!/bin/bash

echo "Updating Core"

#git pull

echo "Updating Modules"

MODULES=modules/*

for module in "$MODULES"
do
	echo $module

	if [ -d $module/.git ]
	then
		echo $module
	fi
done
