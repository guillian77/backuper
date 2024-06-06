#!/bin/bash

VERSION=$(date +"%Y.%m.%d")
SCRIPT=$(pwd)
PLUGIN="backuper"
ARCHIVE="${SCRIPT}/archive"
PACKAGE="${ARCHIVE}/${VERSION}_${PLUGIN}.txz"

tmpdir=/tmp/tmp.${PLUGIN}

mkdir -p $tmpdir
mkdir -p ${SCRIPT}/archive

cp --parents -f $(find . -type f ! -iname "make_pkg.sh" ! -name "*.txz" ) $tmpdir/

[[ -f "$PACKAGE" ]] && rm $PACKAGE

cd $tmpdir \
  && makepkg -l y -c y ${PACKAGE} \
  && md5sum ${PACKAGE}

[[ -f "$PACKAGE" ]] \
  && echo "Plugin package built successfully." \
  || echo "Error when trying to build package."
