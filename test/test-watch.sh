#!/bin/sh

EVENTS="close_write,moved_to,create"
WATCH="../"

inotifywait -mr -e $EVENTS $WATCH |
while read -r directory events filename; do
	clear && phpunit
done
