#!/usr/bin/env bash
#
# To generate IDE help files of Swoole.
#
# How to use this script:
#     ./bin/generator.sh       # To generate stubs with latest code from the master branch of Swoole.
#     ./bin/generator.sh 4.4.7 # To generate stubs for a specific version of Swoole.
#

set -e

pushd "`dirname "$0"`" > /dev/null
ROOT_PATH="`pwd -P`/.."
popd > /dev/null # Switch back to current directory.

cd "${ROOT_PATH}" # Switch to root directory of project "ide-helper".

if [ -z "${1}" ] ; then
    echo INFO: Generating stubs with latest code from the master branch of Swoole.
    image_tag=latest
else
    if [[ "${1}" =~ ^[1-9][0-9]*\.(0|[1-9][0-9]*)\.(0|[1-9][0-9]*)(-[A-Za-z0-9_]+)?$ ]] ; then
        echo INFO: Generating stubs for Swoole ${1}.
        image_tag=${1}-php7.3
    else
        echo "Error: '${1}' is not a valid Swoole version."
        exit 1
    fi
fi
image_tag=${image_tag}-dev

rm -rf ./output
docker run --rm                      \
    -v "`pwd`":/var/www              \
    -e SWOOLE_EXT_ASYNC=enabled      \
    -e SWOOLE_EXT_ORM=enabled        \
    -e SWOOLE_EXT_POSTGRESQL=enabled \
    -e SWOOLE_EXT_SERIALIZE=enabled  \
    -e SWOOLE_EXT_ZOOKEEPER=enabled  \
    -t phpswoole/swoole:${image_tag} \
    bash -c "composer install && SWOOLE_SRC_DIR=/usr/src/swoole ./bin/generator.php"
git add ./output
