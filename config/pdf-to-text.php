<?php

return [
    /*
     * This package will use this binary to extract text from PDFs.
     */
    'binaries' => [
        'pdf_to_text' => [
            'bin' => env('PDF_TO_TEXT_BINARY', '/usr/bin/pdftotext'),
            'options' => [
                '-layout',
                '-enc', 'UTF-8'
            ],
        ],
    ],

    /*
     * The driver that will be used to extract text.
     */
    'driver' => 'pdf_to_text',
];
