#!/bin/bash

i=1
name=$(printf 'v%02d.csv' $i)
cid=$(printf 'v%02d' $i)

while read line
do
 if [[ $line =~ ^\"(v15[ab])\" ]]; then
    cur=${BASH_REMATCH[1]}
    if [ "x$cur" != "x$cid" ]; then
       i=15
       name=$(printf 'v%02d.csv' $i)
       cid=$cur
    fi
 elif [[ $line =~ ^\"(v[0-9][0-9])\" ]]; then
    cur=${BASH_REMATCH[1]}
    if [ "x$cur" != "x$cid" ]; then
       i=$(($i+1))
       name=$(printf 'v%02d.csv' $i)
       cid=$(printf 'v%02d' $i)
    fi
 fi
 echo $line >> $name
done