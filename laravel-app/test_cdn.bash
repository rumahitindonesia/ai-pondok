#!/bin/bash
urls=(
  "https://cdn.jsdelivr.net/npm/@oracle/oraclejet@20.0.0/dist/js/libs/oj/ojL10n.js"
  "https://cdn.jsdelivr.net/npm/jquery-ui@1.13.2/ui/widget.js"
  "https://cdn.jsdelivr.net/npm/requirejs-text@2.0.15/text.js"
  "https://cdn.jsdelivr.net/npm/preact@10.19.3/dist/preact.umd.js"
)
for url in "${urls[@]}"; do
  echo "$(curl -o /dev/null -s -w '%{http_code}' "$url") - $url"
done
