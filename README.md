# PrestaShop Invoice Number Formatter

Version **1.2.1** — a lightweight module that customizes displayed invoice numbers without changing the original numbers stored in the database.

## Compatibility

- PrestaShop **9.1.0–9.1.5**.
- PHP supported by your PrestaShop installation.
- Uses the `actionInvoiceNumberFormatted` hook; no core overrides or database schema changes are required.

## Installation

1. Download **`invoicenumberformatter-1.2.1.zip`** from [GitHub Releases](https://github.com/LoQue90/prestashop-invoice-number-formatter/releases).
2. In PrestaShop, open **Modules → Module Manager → Upload a module** and select the ZIP.
3. Open **Invoice Number Formatter → Configure**.

Use the attached module ZIP. GitHub's automatically generated source archives have a different directory name and are intended for source distribution.

For manual installation, extract the ZIP into `/modules/`. The entry file must be `/modules/invoicenumberformatter/invoicenumberformatter.php`.

When upgrading an existing installation, back up your shop, replace the module files, and use the Module Manager's upgrade action if offered. Avoid uninstalling or resetting the module to preserve your settings.

## Configuration

The configuration page includes a live preview and two settings:

| Setting | Description |
| --- | --- |
| Pattern | Invoice format; must include `{number}`. Unknown placeholders are rejected. |
| Minimum number digits | Pads the invoice number with leading zeroes; defaults to 2. Larger numbers are never truncated. |

Available placeholders:

| Placeholder | Meaning |
| --- | --- |
| `{prefix}` | Invoice prefix configured in PrestaShop for the current language. |
| `{year}` | Two-digit year of the invoice's creation date. |
| `{number}` | Invoice number, padded to the configured minimum length. |

For prefix `RE`, invoice number `14`, and an invoice created in 2026:

| Pattern | Minimum digits | Result |
| --- | --- | --- |
| `{prefix}{year}{number}` | 2 | `RE2614` |
| `{prefix}-{year}-{number}` | 4 | `RE-26-0014` |

An invoice created in 2025 retains `25` as its year. The configuration preview uses the current year because it has no invoice date.

Only the displayed invoice number changes. The original invoice number remains unchanged.

## Development and releases

No Composer or npm dependencies are required for this module.

```sh
php -l invoicenumberformatter.php
php -l src/Formatter.php
php tests/formatter-smoke.php
node --check views/js/admin.js
python3 scripts/build-release.py
```

The standalone smoke tests use small PrestaShop boundary doubles. Test installation, configuration and invoice-hook behavior in a real PrestaShop shop before a release.

GitHub Actions checks PHP 8.1 and 8.5, checks JavaScript syntax and builds the module ZIP. Pushing a tag matching the module version (for example `v1.2.1`) publishes a GitHub release with the installable ZIP. Its top-level directory is always `invoicenumberformatter/`; CI files, tests and Git metadata are excluded.

## License

GNU General Public License v3.0. See [LICENSE](LICENSE).
