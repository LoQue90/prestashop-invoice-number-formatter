document.addEventListener(
    'DOMContentLoaded',
    function () {

        const patternInput =
            document.querySelector(
                '#INF_PATTERN'
            );

        const digitsInput =
            document.querySelector(
                '#INF_MIN_DIGITS'
            );

        const preview =
            document.querySelector(
                '#invoice-preview'
            );


        if (
            !patternInput ||
            !digitsInput ||
            !preview
        ) {
            return;
        }


        function updatePreview()
        {
            let pattern =
                patternInput.value;


            let digits =
                parseInt(
                    digitsInput.value
                );


            if (
                isNaN(digits) ||
                digits < 1
            ) {
                digits = 2;
            }


            let number =
                String(14).padStart(
                    digits,
                    '0'
                );


            let year =
                new Date()
                    .getFullYear()
                    .toString()
                    .slice(-2);


            /*
             * PrestaShop invoice prefix
             * wird serverseitig gesetzt.
             * Für die Live-Vorschau holen wir
             * den gespeicherten Prefix aus dem
             * globalen JS falls vorhanden.
             */
            let prefix =
                typeof invoicePrefix !== 'undefined'
                    ? invoicePrefix
                    : '';


            let result =
                pattern
                    .replace(
                        /\{prefix\}/g,
                        prefix
                    )
                    .replace(
                        /\{year\}/g,
                        year
                    )
                    .replace(
                        /\{number\}/g,
                        number
                    );


            preview.innerHTML =
                result;
        }


        patternInput.addEventListener(
            'input',
            updatePreview
        );


        digitsInput.addEventListener(
            'input',
            updatePreview
        );


        updatePreview();
    }
);