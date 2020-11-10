<?php


namespace Kntnt\Author;


class Settings extends Abstract_Settings {

    protected function menu_title() {
        return __( 'Author Roles', 'kntnt-author' );
    }

    protected function fields() {

        $fields['roles'] = [
            'type' => 'checkbox group',
            'options' => $this->roles(),
            'label' => __( "Author roles", 'kntnt-author' ),
            'description' => __( 'Select author roles.', 'kntnt-author' ),
        ];

        $fields['submit'] = [
            'type' => 'submit',
        ];

        return $fields;

    }

    // Returns an array where keys are taxonomies machine name and values are
    // corresponding name in clear text.
    private function roles() {
        $roles = [];
        foreach ( wp_roles()->roles as $key => $role ) {
            $roles[ $key ] = $role['name'];
        }
        return $roles;
    }

}