#!/bin/sh

if [ $# -eq 0 ];
then
    echo "Usage: fetch.sh <YYYYMMDD>"
    exit 1
fi
cd "${ROOT_DIR}"/src
ROOT_DIR=$(pwd)
IANSEO_SRC=Ianseo_${1}.zip
curl -LSO https://ianseo.net/Release/"${IANSEO_SRC}"
mkdir -pv "${ROOT_DIR}"/tmp
cd "${ROOT_DIR}"/tmp
unzip "${ROOT_DIR}"/"${IANSEO_SRC}"
rsync -av --delete "${ROOT_DIR}"/tmp/ "${ROOT_DIR}"/src
cd "${ROOT_DIR}"
rm "${IANSEO_SRC}" ; rm -rf "${ROOT_DIR}"/tmp