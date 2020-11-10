<?php

defined( 'ABSPATH' ) || die;

if ( ! function_exists( 'get_avatar' ) ) {

    function get_avatar( $id_or_email, $size = 96, $default = '', $alt = '', $args = [] ) {
        return Kntnt\Author\Avatar_Manager::get_avatar( $id_or_email, $size, $default, $alt, $args );
    }

}
