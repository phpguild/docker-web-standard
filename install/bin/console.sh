#!/usr/bin/env bash

realPath=$(dirname $(readlink -f "$0"))
rootPath=$(dirname "${realPath}")

cmd="${1}"

cd "${rootPath}"
make ${cmd}
