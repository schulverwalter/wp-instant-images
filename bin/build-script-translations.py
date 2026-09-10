#!/usr/bin/env python3
"""Write Jed-format JSON translation files for the plugin's JavaScript strings.

WordPress loads these via wp_set_script_translations(). It looks for
"<domain>-<locale>-<handle>.json" in the registered path first, so this emits
one file per script handle listed on the command line.

Only entries that a JavaScript source actually references are included, so the
files stay small; the PHP strings are served from the .mo/.l10n.php instead.
"""
import json
import os
import re
import sys

DOMAIN = "instant-images"
LANG_DIR = os.path.join(os.path.dirname(os.path.abspath(__file__)), "..", "lang")


def unescape(value):
    return (
        value.replace('\\n', '\n')
        .replace('\\t', '\t')
        .replace('\\"', '"')
        .replace('\\\\', '\\')
    )


def parse_po(path):
    """Return (headers, [(msgid, msgstr, from_js)]) for a .po file."""
    entries = []
    headers = {}
    for block in open(path, encoding="utf-8").read().split("\n\n"):
        if block.lstrip().startswith("#~"):
            continue  # obsolete
        refs = " ".join(re.findall(r"^#:.*$", block, re.M))
        parts = {}
        key = None
        for line in block.split("\n"):
            m = re.match(r'^(msgid|msgstr|msgctxt)(?:\[\d+\])? "((?:[^"\\]|\\.)*)"$', line)
            if m:
                key = m.group(1)
                parts.setdefault(key, []).append(m.group(2))
                continue
            m = re.match(r'^"((?:[^"\\]|\\.)*)"$', line)
            if m and key:
                parts[key].append(m.group(1))
                continue
            key = None
        if "msgid" not in parts:
            continue
        msgid = unescape("".join(parts["msgid"]))
        msgstr = unescape("".join(parts.get("msgstr", [])))
        if msgid == "":
            for line in msgstr.split("\n"):
                if ": " in line:
                    name, _, val = line.partition(": ")
                    headers[name.strip()] = val.strip()
            continue
        if msgstr:
            entries.append((msgid, msgstr, ".js" in refs))
    return headers, entries


def main(handles):
    written = []
    for name in sorted(os.listdir(LANG_DIR)):
        if not name.endswith(".po"):
            continue
        locale = name[len(DOMAIN) + 1 : -len(".po")]
        headers, entries = parse_po(os.path.join(LANG_DIR, name))
        messages = {
            "": {
                "domain": "messages",
                "lang": locale,
                "plural-forms": headers.get("Plural-Forms", "nplurals=2; plural=n != 1;"),
            }
        }
        for msgid, msgstr, from_js in entries:
            if from_js:
                messages[msgid] = [msgstr]

        payload = {
            "translation-revision-date": headers.get("PO-Revision-Date", ""),
            "generator": "instant-images/bin/build-script-translations.py",
            "domain": "messages",
            "locale_data": {"messages": messages},
        }
        for handle in handles:
            out = os.path.join(LANG_DIR, f"{DOMAIN}-{locale}-{handle}.json")
            with open(out, "w", encoding="utf-8") as fh:
                json.dump(payload, fh, ensure_ascii=False, separators=(",", ":"))
            written.append((os.path.basename(out), len(messages) - 1))

    for path, count in written:
        print(f"  {path} ({count} strings)")


if __name__ == "__main__":
    main(sys.argv[1:] or ["instant-images-react"])
