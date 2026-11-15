#!/bin/sh
# Build wordpress/rk-developers-theme.zip (run from the repository root).
set -e
cd "$(dirname "$0")/.."
rm -f rk-developers-theme.zip
zip -qr -X rk-developers-theme.zip rk-developers-theme -x '*.DS_Store' -x '*/.git*'
ls -lh rk-developers-theme.zip
