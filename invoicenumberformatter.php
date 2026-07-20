<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once __DIR__ . '/src/Formatter.php';


class InvoiceNumberFormatter extends Module
{
    public function __construct()
    {
        $this->name = 'invoicenumberformatter';
        $this->version = '1.1.0';
        $this->author = 'Lorenz Uhlig';

        $this->tab = 'administration';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = $this->l(
            'Invoice Number Formatter'
        );

        $this->description = $this->l(
            'Customize invoice number format.'
        );
    }


    public function install()
    {
        return parent::install()
            && Configuration::updateValue(
                'INF_PATTERN',
                '{prefix}{year}{number}'
            )
            && Configuration::updateValue(
                'INF_MIN_DIGITS',
                2
            )
            && $this->registerHook(
                'actionInvoiceNumberFormatted'
            );
    }


    public function uninstall()
    {
        Configuration::deleteByName('INF_PATTERN');
        Configuration::deleteByName('INF_MIN_DIGITS');

        return parent::uninstall();
    }


    public function getContent()
    {
        $output = '';

        if (Tools::isSubmit('submitInvoiceFormatter')) {

            Configuration::updateValue(
                'INF_PATTERN',
                Tools::getValue('INF_PATTERN')
            );

            Configuration::updateValue(
                'INF_MIN_DIGITS',
                (int) Tools::getValue('INF_MIN_DIGITS')
            );

            $output .= $this->displayConfirmation(
                $this->l('Settings saved.')
            );
        }


        return $output . $this->renderForm();
    }


    private function renderForm()
    {
        $fieldsForm = [
            'form' => [
                'legend' => [
                    'title' => $this->l(
                        'Invoice number format'
                    ),
                    'icon' => 'icon-cogs',
                ],

                'input' => [

                    [
                        'type' => 'text',
                        'label' => $this->l(
                            'Pattern'
                        ),
                        'name' => 'INF_PATTERN',
                        'desc' => $this->l(
                            'Available variables: {prefix}, {year}, {number}'
                        ),
                    ],

                    [
                        'type' => 'text',
                        'label' => $this->l(
                            'Minimum number digits'
                        ),
                        'name' => 'INF_MIN_DIGITS',
                    ],

                ],

                'submit' => [
                    'title' => $this->l(
                        'Save'
                    ),
                    'name' => 'submitInvoiceFormatter',
                ],
            ],
        ];


        $helper = new HelperForm();

        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite(
            'AdminModules'
        );

        $helper->currentIndex =
            AdminController::$currentIndex .
            '&configure=' .
            $this->name;


        $helper->submit_action =
            'submitInvoiceFormatter';


        $helper->fields_value = [
            'INF_PATTERN' =>
                Configuration::get('INF_PATTERN'),

            'INF_MIN_DIGITS' =>
                Configuration::get('INF_MIN_DIGITS'),
        ];


        return $helper->generateForm(
            [$fieldsForm]
        );
    }


    public function hookActionInvoiceNumberFormatted($params)
    {
        if (!isset($params['number'])) {
            return '';
        }

        $formatter = new InvoiceFormatter();

        return $formatter->format(
            (int) $params['number'],
            $params
        );
    }
}