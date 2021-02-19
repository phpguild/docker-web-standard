#!/usr/bin/env bash

realPath="$(dirname "$(readlink -f "$0")")"
rootPath="$(dirname "${realPath}")"

cmd="${1}"

if [ -z "${cmd}" ]; then
  exit
fi

cd "${rootPath}" || exit
make "${cmd}"
