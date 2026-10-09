<?php
// Exercise real formatter code with minimal doubles for PrestaShop's boundary.
class Configuration
{
    public static $values = [];
    public static function get($name, $language = null) { return self::$values[$name] ?? false; }
}
class Context
{
    public static function getContext() { return (object) ['language' => (object) ['id' => 1]]; }
}
class OrderInvoice
{
    public $date_add;
}
require_once __DIR__ . '/../src/Formatter.php';

function check($actual, $expected, $name)
{
    if ($actual !== $expected) {
        throw new RuntimeException("$name: expected $expected, got $actual");
    }
    echo "PASS: $name\n";
}

Configuration::$values = ['INF_PATTERN' => '{prefix}{year}{number}', 'INF_MIN_DIGITS' => 2, 'PS_INVOICE_PREFIX' => 'VTU'];
$formatter = new InvoiceFormatter();
$invoice = new OrderInvoice();
$invoice->date_add = '2025-02-10 12:00:00';
check($formatter->format(14, ['OrderInvoice' => $invoice]), 'VTU2514', 'historical invoice year');
$invoice->date_add = '2026-07-20 12:00:00';
check($formatter->format(14, ['OrderInvoice' => $invoice, 'pattern' => '{prefix}-{year}-{number}', 'min_digits' => 4]), 'VTU-26-0014', 'custom pattern and padding');
check($formatter->format(12345, ['OrderInvoice' => $invoice, 'min_digits' => 4]), 'VTU2612345', 'large invoice is not truncated');
Configuration::$values['INF_PATTERN'] = '';
Configuration::$values['INF_MIN_DIGITS'] = 0;
check($formatter->format(1, ['OrderInvoice' => $invoice]), 'VTU2601', 'configuration defaults');
