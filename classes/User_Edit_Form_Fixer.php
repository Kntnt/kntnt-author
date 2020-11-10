<?php


namespace Kntnt\Author;


class User_Edit_Form_Fixer {

    public function run() {
        add_action( 'user_edit_form_tag', [ $this, 'start' ] );
        add_action( 'show_user_profile', [ $this, 'stop' ] );
        add_action( 'edit_user_profile', [ $this, 'stop' ] );
    }

    public function start() {

        if ( $user_id = $this->user_id() ) {

            $user_meta = get_userdata( $user_id );
            Plugin::debug( 'User with id = %s has following roles: %s', $user_id, $user_meta->roles );

            if ( $user_meta && array_intersect( $user_meta->roles, Plugin::option( 'roles', [] ) ) ) {

                ob_start( function ( $content ) {

                    // Remove the section with personal options.
                    $start = '<h2>' . __( 'Personal Options' ) . '</h2>';
                    $stop = '<h2>' . __( 'Name' ) . '</h2>';
                    $content = preg_replace( "`$start.*(?=$stop)`s", '', $content, 1 );

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

}