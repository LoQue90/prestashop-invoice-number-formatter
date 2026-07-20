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
        $this->version = '1.2.0';
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

            $pattern = Tools::getValue(
                'INF_PATTERN'
            );


            $validationError = $this->validatePattern(
                $pattern
            );


            if ($validationError) {

                $output .= $this->displayError(
                    $validationError
                );

            } else {

                Configuration::updateValue(
                    'INF_PATTERN',
                    $pattern
                );


                Configuration::updateValue(
                    'INF_MIN_DIGITS',
                    (int) Tools::getValue(
                        'INF_MIN_DIGITS'
                    )
                );


                $output .= $this->displayConfirmation(
                    $this->l('Settings saved.')
                );
            }
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

                    [
                        'type' => 'html',
                        'name' => 'INF_PREVIEW',
                        'html_content' => $this->getPreview(),
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
                Configuration::get(
                    'INF_PATTERN'
                ),

            'INF_MIN_DIGITS' =>
                Configuration::get(
                    'INF_MIN_DIGITS'
                ),
        ];


        $this->context->controller->addJS(
            $this->_path . 'views/js/admin.js'
        );


        Media::addJsDef([
            'invoicePrefix' =>
                Configuration::get(
                    'PS_INVOICE_PREFIX',
                    (int) $this->context->language->id
                ),
        ]);


        return $helper->generateForm(
            [
                $fieldsForm
            ]
        );
    }


    private function getPreview()
    {
        $formatter = new InvoiceFormatter();

        $preview = $formatter->format(
            14,
            [
                'pattern' => Configuration::get(
                    'INF_PATTERN'
                ),

                'min_digits' => Configuration::get(
                    'INF_MIN_DIGITS'
                ),
            ]
        );


        return '
            <div class="alert alert-info">
                <strong>'
                . $this->l('Preview')
                . ':</strong>
                <span id="invoice-preview">'
                . htmlspecialchars(
                    $preview
                )
                . '</span>
            </div>
        ';
    }


    private function validatePattern($pattern)
    {
        if (strpos($pattern, '{number}') === false) {

            return $this->l(
                'The pattern must contain {number}.'
            );
        }


        preg_match_all(
            '/\{([^}]+)\}/',
            $pattern,
            $matches
        );


        $allowed = [
            'prefix',
            'year',
            'number',
        ];


        foreach ($matches[1] as $placeholder) {

            if (!in_array(
                $placeholder,
                $allowed
            )) {

                return sprintf(
                    $this->l(
                        'Unknown placeholder: {%s}'
                    ),
                    $placeholder
                );
            }
        }


        return false;
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