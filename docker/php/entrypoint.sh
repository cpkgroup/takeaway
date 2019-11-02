#!/bin/bash

mkdir -p --verbose --mode=0777 /www/var
exec "$@"
