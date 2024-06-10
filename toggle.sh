#!/bin/bash

EM_PLUGIN="/usr/local/emhttp/plugins/backuper"
TMP_PLUGIN="/usr/local/emhttp/plugins/backuper_tmp"

function dev() {
  if [ -d "$EM_PLUGIN" ];then
   echo "Save existing plugin dir to ${TMP_PLUGIN}"
   mv "$EM_PLUGIN" "${TMP_PLUGIN}"
  fi

  echo "Create symbolic link"
  [[ -d "$EM_PLUGIN" ]] && rm $EM_PLUGIN

  ln -s /tmp/backuper/source/backuper/usr/local/emhttp/plugins/backuper $EM_PLUGIN

  [[ -d "${EM_PLUGIN}" ]] \
    && echo "Successfully create link." \
    || echo "Unable to create symbolic link."
}

function prod() {
    if [ ! -d "${TMP_PLUGIN}" ]; then
        echo "$TMP_PLUGIN does no exist."
        exit 1
    fi

    echo "Use $TMP_PLUGIN to back to prod."

    echo "Remove symbolic link."
    rm $EM_PLUGIN 2> /dev/null || true

    echo "Rename TMP to prod."
    mv $TMP_PLUGIN $EM_PLUGIN

    [[ -d "$EM_PLUGIN" ]] \
        && echo "Successfully backed to prod." \
        || echo "Unable to back to prod."
}

case $1 in
dev)
  dev
  ;;
prod)
  prod
  ;;
*)
  echo "Usage: ./dev.sh dev | prod"
esac
