#!/bin/bash
if [[ "$#" -lt 1 ]]; then
	echo "argumentum: mount könyvtár, ahol a behat.yml van."
fi
ROOT_DIR=$1
shift
rm -rf /tmp/cache/prod
rm -rf /tmp/cache/dev
rm -rf /tmp/cache/test
$ROOT_DIR/vendor/bin/behat -c $ROOT_DIR/behat.yml $*