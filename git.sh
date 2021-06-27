#!/bin/sh
set HOME=%USERPROFILE%
git.exe add .
git commit -m "update" -a
git push
