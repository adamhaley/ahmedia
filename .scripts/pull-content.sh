#!/bin/bash
# Pulls the production CMS content into the repo as a fresh snapshot. Run from the repo root on
# Adam's Mac, then review and commit database/seeders/content/.
#
# The server must have the branch that adds `php artisan content:export` deployed first.
set -euo pipefail

host="${CONTENT_HOST:-root@165.232.135.82}"
app_dir=/home/ahmedia/public_html
php=/usr/local/php84/bin/php
dest=database/seeders/content

tmp=$(ssh "$host" 'mktemp -d')
trap 'ssh "$host" "rm -rf $tmp"' EXIT

ssh "$host" "chown ahmedia $tmp && su -s /bin/bash ahmedia -c 'cd $app_dir && $php artisan content:export --path=$tmp'"

mkdir -p "$dest"
rsync -az "$host:$tmp/snapshot.json" "$dest/snapshot.json"

# Mirror each exported subfolder, deleting local files the snapshot no longer references.
for dir in $(ssh "$host" "cd $tmp && find . -mindepth 1 -maxdepth 1 -type d -printf '%f\n'"); do
    mkdir -p "$dest/$dir"
    rsync -az --delete "$host:$tmp/$dir/" "$dest/$dir/"
done

echo "Snapshot refreshed in $dest. Review with git status, then commit."
