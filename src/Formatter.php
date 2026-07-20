<?php

class InvoiceFormatter
{
    public function format($number)
    {
        $pattern = Configuration::get(
            'INF_PATTERN'
        );

        if (!$pattern) {
            $pattern = '{prefix}{year}{number}';
        }


        $digits = (int) Configuration::get(
            'INF_MIN_DIGITS'
        );

        if ($digits < 1) {
            $digits = 2;
        }


        $context = Context::getContext();


        $prefix = Configuration::get(
            'PS_INVOICE_PREFIX',
            (int) $context->language->id
        );


        $year = date('y');


        $formattedNumber = str_pad(
            (string) $number,
            $digits,
            '0',
            STR_PAD_LEFT
        );


        return str_replace(
            [
                '{prefix}',
                '{year}',
                '{number}',
            ],
            [
                $prefix,
                $year,
                $formattedNumber,
            ],
            $pattern
        );
    }
}