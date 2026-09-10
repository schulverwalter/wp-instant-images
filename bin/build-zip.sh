#!/usr/bin/env bash
#
# Build an installable plugin ZIP in dist/.
#
# The archive holds only what the plugin needs at runtime and unpacks into an
# "instant-images/" directory, so WordPress replaces an existing install rather
# than adding a second copy of the plugin.
set -euo pipefail

SLUG="instant-images"
ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
DEST="${ROOT}/dist/${SLUG}"

# Everything the plugin loads at runtime, plus the files WordPress reads to
# describe it. Anything not listed here stays out of the archive.
CONTENTS=(
	admin
	api
	build
	lang
	instant-images.php
	uninstall.php
	README.txt
	LICENSE.txt
)

if [ ! -f "${ROOT}/build/instant-images.js" ]; then
	echo "build/ is missing - run 'npm run build' first." >&2
	exit 1
fi

rm -rf "${ROOT}/dist"
mkdir -p "${DEST}"

for item in "${CONTENTS[@]}"; do
	if [ ! -e "${ROOT}/${item}" ]; then
		echo "Missing expected plugin file: ${item}" >&2
		exit 1
	fi
	cp -R "${ROOT}/${item}" "${DEST}/"
done

# Source maps are development artifacts.
find "${DEST}" -name '*.map' -delete

cd "${ROOT}/dist"
zip -rq "${SLUG}.zip" "${SLUG}"
echo "Created dist/${SLUG}.zip"
