<?php

return [
    'mode'                  => 'utf-8',
    'format'                => 'A4',
    'default_font_size'     => 11,
    'default_font'          => 'freeserif',
    'auto_script_to_lang'   => true,
    'auto_lang_to_font'     => true,
    'margin_left'           => 10,
    'margin_right'          => 10,
    'margin_top'            => 10,
    'margin_bottom'         => 10,
    'margin_header'         => 0,
    'margin_footer'         => 0,
    'orientation'           => 'P',

    'custom_font_dir'       => public_path('fonts'),
    'custom_font_data'      => [
        'notosansgujarati' => [
            'R' => 'NotoSansGujarati-Regular.ttf',
            // Map all variants to the same TTF so Gujarati still renders when HTML uses <strong>/<em>.
            'B' => 'NotoSansGujarati-Regular.ttf',
            'I' => 'NotoSansGujarati-Regular.ttf',
            'BI' => 'NotoSansGujarati-Regular.ttf',
        ],
    ],
];
