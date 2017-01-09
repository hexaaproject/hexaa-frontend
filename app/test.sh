#!/bin/bash
if [[ "$#" -ne 1 ]]; then
	"argumentum: mount könyvtár, ahol a behat.yml van."
fi
ROOT_DIR=$1
echo $*
shift
$ROOT_DIR/vendor/bin/behat -c $ROOT_DIR/behat.yml $*