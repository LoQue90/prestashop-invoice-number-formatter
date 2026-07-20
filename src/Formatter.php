<?php

class InvoiceFormatter
{
    public function format($number, $params = [])
    {
        $pattern = Configuration::get(
            'INF_PATTERN'
        );


        if (
            isset($params['pattern'])
            && !empty($params['pattern'])
        ) {
            $pattern = $params['pattern'];
        }


        if (!$pattern) {
            $pattern = '{prefix}{year}{number}';
        }


       $digits = (int) Configuration::get(
            'INF_MIN_DIGITS'
        );

        if (
            isset($params['min_digits'])
            && (int) $params['min_digits'] > 0
        ) {
            $digits = (int) $params['min_digits'];
        }

        if ($digits < 1) {
            $digits = 2;
        }

        $context = Context::getContext();


        $prefix = Configuration::get(
            'PS_INVOICE_PREFIX',
            (int) $context->language->id
        );


        $year = date(
            'y'
        );


        if (
            isset($params['OrderInvoice'])
            && $params['OrderInvoice'] instanceof OrderInvoice
            && !empty($params['OrderInvoice']->date_add)
        ) {
            $year = date(
                'y',
                strtotime(
                    $params['OrderInvoice']->date_add
                )
            );
        }


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