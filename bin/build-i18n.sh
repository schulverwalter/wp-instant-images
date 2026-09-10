#!/usr/bin/env bash
#
# Compile the shipped translations.
#
#   lang/<domain>-<locale>.mo         PHP strings (gettext)
#   lang/<domain>-<locale>.l10n.php   PHP strings (WordPress 6.5+ fast path)
#   lang/<domain>-<locale>-<handle>.json  JavaScript strings, one per script handle
#
# Run after changing source strings, once `composer run pot` and the .po files
# are up to date.
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
WP="${ROOT}/vendor/wp-cli/wp-cli/bin/wp"

cd "${ROOT}"

for po in lang/*.po; do
	[ -e "$po" ] || { echo "No .po files in lang/ - nothing to compile."; exit 0; }
	echo "Compiling ${po}"
	"${WP}" i18n make-mo "$po" lang/ --allow-root
	"${WP}" i18n make-php "$po" lang/ --allow-root
done

# Script handles that carry translatable JS strings. WordPress looks for
# "<domain>-<locale>-<handle>.json" before falling back to a path hash, so one
# file per handle keeps this independent of the built file names.
python3 bin/build-script-translations.py \
	instant-images-react \
	instant-images-media-modal \
	instant-images-block

echo "Translations compiled."
