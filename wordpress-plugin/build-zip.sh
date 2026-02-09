#!/bin/bash
# Builds partyhelp-form.zip for WordPress plugin installation
set -e
cd "$(dirname "$0")"
PLUGIN_DIR="partyhelp-form"
ZIP_NAME="partyhelp-form.zip"
rm -f "../$ZIP_NAME"
cd "$PLUGIN_DIR"
zip -r "../$ZIP_NAME" . -x "*.git*" -x "*.DS_Store"
cd ..
echo "Built $ZIP_NAME"
