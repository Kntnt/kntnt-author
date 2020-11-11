<?php


namespace Kntnt\Author;


class User_Edit_Form_Fixer {

    private $use_description_as_default = true;

    public function run() {
        add_action( 'user_edit_form_tag', [ $this, 'start' ] );
        add_action( 'show_user_profile', [ $this, 'stop' ] );
        add_action( 'edit_user_profile', [ $this, 'stop' ] );
        add_action( 'acf/save_post', [ $this, 'save_post' ], 20, 1 );
        add_action( 'acf/load_value/name=biography', [ $this, 'load_value' ], 5, 3 );
    }

    public function start() {

        if ( $user_id = $this->user_id() ) {

            $user_meta = get_userdata( $user_id );
            Plugin::debug( 'User with id = %s has following roles: %s', $user_id, $user_meta->roles );

            if ( $user_meta && array_intersect( $user_meta->roles, Plugin::option( 'roles', [] ) ) ) {

                ob_start( function ( $content ) {

                    // Remove the section about the user
                    $start = '<h2>(?:' . __( 'About Yourself' ) . '|' . __( 'About the user' ) . ')</h2>';
                    $stop = '<h2>' . __( 'Account Management' ) . '</h2>';
                    $content = preg_replace( "`$start.*(?=$stop)`s", '', $content, 1 );

                    return $content;

                } );

            }

        }

    }

    public function stop() {
        if ( true ) {
            ob_end_flush();
        }
    }

    private function user_id() {
        if ( defined( 'IS_PROFILE_PAGE' ) && IS_PROFILE_PAGE ) {
            $user_id = get_current_user_id();
        }
        else if ( ! empty( $_GET['user_id'] ) && is_numeric( $_GET['user_id'] ) ) {
            $user_id = $_GET['user_id'];
        }
        else {
            $user_id = false; // Should never happen.
        }
        return $user_id;
    }

    public function save_post( $acf_id ) {

        $user_id = (int) substr( $acf_id, 5 );

        $this->use_description_as_default = false;
        $biography = get_field( 'biography', $acf_id, false );
        $this->use_description_as_default = true;

        if ( $biography ) {
            Plugin::debug( 'Updating biography for user with id = %s', $user_id );
            update_user_meta( $user_id, 'description', $biography );
        }
        else {
            Plugin::debug( 'Deleting biography for user with id = %s', $user_id );
            delete_user_meta( $user_id, 'description' );
        }

    }

    public function load_value( $value, $acf_id, $field ) {
        if ( ! $value && $this->use_description_as_default ) {
            $user_id = (int) substr( $acf_id, 5 );
            $value = get_the_author_meta( 'description', $user_id );
        }
        return $value;
    }

}