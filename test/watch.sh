#!/bin/sh

EVENTS="close_write,moved_to,create"
WATCH="../"

inotifywait -mr --exclude '(\.git)' -e $EVENTS $WATCH |
while read path events file; do
	clear && phpunit
done
