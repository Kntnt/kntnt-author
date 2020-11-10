<?php

defined( 'WPINC' ) || die;

add_option( 'kntnt-author', [
    'roles' => [
        'administrator',
        'editor',
        'author',
        'contributor',
    ],
] );