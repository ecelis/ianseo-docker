#!/bin/sh

ROOT_DIR=$(pwd)
IANSEO_SRC=Ianseo_${1}.zip
curl -LSO https://ianseo.net/Release/"${IANSEO_SRC}"
cd "${ROOT_DIR}"/src
unzip "${ROOT_DIR}"/"${IANSEO_SRC}"
cd "${ROOT_DIR}"
rm "${IANSEO_SRC}"