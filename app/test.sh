#!/bin/bash
if [[ "$#" -lt 1 ]]; then
	echo "argumentum: mount könyvtár, ahol a behat.yml van."
fi
ROOT_DIR=$1
shift
$ROOT_DIR/vendor/bin/behat -c $ROOT_DIR/behat.yml $*