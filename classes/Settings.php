<?php


namespace Kntnt\Author;


class Settings extends Abstract_Settings {

    protected function menu_title() {
        return __( 'Kntnt Author', 'kntnt-author' );
    }

    protected function fields() {

        $fields['roles'] = [
            'type' => 'checkbox group',
            'options' => $this->roles(),
            'label' => __( "Author roles", 'kntnt-author' ),
            'description' => __( 'Select author roles.', 'kntnt-author' ),
        ];

        // TODO: Replace this with an "Add media" button.
        $fields['avatar-default-attachment'] = [
            'type' => 'integer',
            'min' => 1,
            'default' => '',
            'label' => __( "Default author portrait", 'kntnt-author' ),
            'description' => __( 'Enter attachment ID for default author portrait.', 'kntnt-author' ),
        ];

        $fields['submit'] = [
            'type' => 'submit',
        ];

        return $fields;

    }

    // Returns an array where keys are role slugs and values are corresponding
    // role names.
    private function roles() {
        $roles = [];
        foreach ( wp_roles()->roles as $key => $role ) {
            $roles[ $key ] = $role['name'];
        }
        return $roles;
    }

}