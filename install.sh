#!/bin/bash

echo "Install KeepLocal-Merger for DatabaseDump:"

git config merge.keepLocal.name "KeepLocal"
git config merge.keepLocal.driver "echo 'Merged by keeping local file'"

echo "data/sql/eCamp3dev.sql merge=keepLocal" > .gitattributes;




echo "Installation done."
