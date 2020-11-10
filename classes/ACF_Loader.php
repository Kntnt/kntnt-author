<?php


namespace Kntnt\Author;


class ACF_Loader {

    public function run() {
        acf_add_local_field_group( $this->about_fields() );
        acf_add_local_field_group( $this->authors_field() );
    }

    private function about_fields() {
        return [
            'key' => 'group_5f96b1f9ba1a6',
            'title' => __( 'About', 'kntnt-author' ),
            'fields' => [
                [
                    'key' => 'field_5f96b2373413f',
                    'label' => __( 'Picture', 'kntnt-author' ),
                    'name' => 'portrait',
                    'type' => 'image',
                    'instructions' => __( 'Portrait image with the author centered in the middle.', 'kntnt-author' ),
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => [
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ],
                    'return_format' => 'id',
                    'preview_size' => 'medium',
                    'library' => 'all',
                    'min_width' => 96,
                    'min_height' => 96,
                    'min_size' => '',
                    'max_width' => '',
                    'max_height' => '',
                    'max_size' => '',
                    'mime_types' => 'jpg,jpeg,png,gif',
                ],
                [
                    'key' => 'field_5fa9797bb51bc',
                    'label' => __( 'Biography', 'kntnt-author' ),
                    'name' => 'biography',
                    'type' => 'textarea',
                    'instructions' => __( 'Biographical description of the author that can be shown at articles and the author archive.', 'kntnt-author' ),
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => [
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ],
                    'default_value' => '',
                    'placeholder' => '',
                    'maxlength' => '',
                    'rows' => '',
                    'new_lines' => '',
                ],
            ],
            'location' => array_map( function ( $role ) {
                return [
                    [
                        'param' => 'user_role',
                        'operator' => '==',
                        'value' => $role,
                    ],
                ];
            }, Plugin::option( 'roles', [] ) ),
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'field',
            'hide_on_screen' => '',
            'active' => true,
            'description' => '',
        ];
    }

    private function authors_field() {
        return [
            'key' => 'group_5fa70454e0090',
            'title' => __( 'Authors', 'kntnt-acf-post-authors' ),
            'fields' => [
                [
                    'key' => 'field_5fa704729505e',
                    'label' => __( 'Authors', 'kntnt-acf-post-authors' ),
                    'name' => 'authors',
                    'type' => 'user',
                    'instructions' => __( 'Main author first followed by any co-authors.', 'kntnt-acf-post-authors' ),
                    'required' => 1,
                    'conditional_logic' => 0,
                    'wrapper' => [
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ],
                    'role' => Plugin::option( 'roles', [] ),
                    'allow_null' => 0,
                    'multiple' => 1,
                    'return_format' => 'array',
                ],
            ],
            'location' => [
                [
                    [
                        'param' => 'post_type',
                        'operator' => ' == ',
                        'value' => 'post',
                    ],
                ],
            ],
            'menu_order' => 20,
            'position' => 'side',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'field',
            'hide_on_screen' => '',
            'active' => true,
            'description' => '',
        ];
    }

}