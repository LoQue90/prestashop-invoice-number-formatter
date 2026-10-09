#!/usr/bin/env python3
"""Build a reproducible ZIP with PrestaShop's required module directory name."""
from pathlib import Path
from datetime import datetime, timezone
import re
import subprocess
import zipfile

ROOT = Path(__file__).resolve().parents[1]
MODULE = 'invoicenumberformatter'
FILES = [
    'invoicenumberformatter.php',
    'src/Formatter.php',
    'views/js/admin.js',
    'README.md',
    'LICENSE',
]


def build():
    source = (ROOT / 'invoicenumberformatter.php').read_text()
    match = re.search(r"\$this->version\s*=\s*'([0-9]+\.[0-9]+\.[0-9]+)'", source)
    if not match:
        raise SystemExit('Missing module version')
    version = match.group(1)
    epoch = int(subprocess.check_output(['git', 'log', '-1', '--format=%ct'], cwd=ROOT, text=True).strip())
    stamp = datetime.fromtimestamp(max(epoch, 315532800), timezone.utc).timetuple()[:6]
    out = ROOT / 'dist' / f'{MODULE}-{version}.zip'
    out.parent.mkdir(exist_ok=True)
    with zipfile.ZipFile(out, 'w', compression=zipfile.ZIP_DEFLATED) as archive:
        for name in FILES:
            info = zipfile.ZipInfo(f'{MODULE}/{name}', stamp)
            info.create_system = 3
            info.external_attr = 0o100644 << 16
            info.compress_type = zipfile.ZIP_DEFLATED
            archive.writestr(info, (ROOT / name).read_bytes())
    with zipfile.ZipFile(out) as archive:
        if archive.testzip() is not None:
            raise SystemExit('ZIP integrity check failed')
        assert archive.namelist() == [f'{MODULE}/{name}' for name in FILES]
        assert archive.read(f'{MODULE}/invoicenumberformatter.php') == (ROOT / 'invoicenumberformatter.php').read_bytes()
    print(out)


if __name__ == '__main__':
    build()
