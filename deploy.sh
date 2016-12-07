#!/bin/bash

docker login -e="." -u="$QUAY_USERNAME" -p="$QUAY_PASSWORD" quay.io
docker tag keboola/gooddata-extractor quay.io/keboola/gooddata-extractor:$TRAVIS_TAG
docker images
docker push quay.io/keboola/gooddata-extractor:$TRAVIS_TAG