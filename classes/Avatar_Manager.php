<?php


namespace Kntnt\Author;


class Avatar_Manager {

    public static function authors( $post_id = null ) {
        $authors = [];
        if ( $post_id = $post_id ?: get_the_ID() ) {
            $user_ids = get_field( 'authors', $post_id, false );
            foreach ( $user_ids as $user_id ) {
                $authors[ $user_id ] = get_user_by( 'id', $user_id );
            }
        }
        return $authors;
    }

    public static function byline( $post_id = null ) {
        $authors = [];
        foreach ( self::authors( $post_id ) as $author ) {
            $url = get_author_posts_url( $author->ID, $author->user_nicename );
            $authors[] = "<a href=\"$url\">$author->display_name</a>";
        }
        $last_author = array_pop( $authors );
        if ( count( $authors ) ) {
            $authors = join( ', ', $authors );
            $authors = sprintf( _x( "%s and %s", 'List of authors', 'kntnt-acf-post-authors' ), $authors, $last_author );
        }
        else {
            $authors = $last_author;
        }
        return "<div class=\"kntnt-acf-post-authors\">$authors</div>";
    }

    // Mimics get_avatar() in wp-includes/pluggable.php to be compatible
    // with plugins and themes depending on it.
    public static function get_avatar( $user, $size, $default, $alt, $args ) {

        $user = self::user( $user );

        $args['size'] = ( is_numeric( $size ) ? abs( (int) $size ) : 96 ) ?: 96;
        $args['width'] = ( isset( $args['width'] ) && is_numeric( $args['width'] ) ? abs( (int) $args['width'] ) : $default ) ?: $args['size'];
        $args['height'] = ( isset( $args['height'] ) && is_numeric( $args['height'] ) ? abs( (int) $args['height'] ) : $default ) ?: $args['size'];

        switch ( $default ?: get_option( 'avatar_default', 'mystery' ) ) {
            case 'mm':
            case 'mystery':
            case 'mysteryman':
                $args['default'] = 'mm';
                break;
            case 'gravatar_default':
                $args['default'] = false;
                break;
        }

        $args['alt'] = $alt;

        $args['found_avatar'] = false;

        $defaults = [
            'force_default' => false,
            'rating' => get_option( 'avatar_rating' ),
            'scheme' => null,
            'class' => null,
            'force_display' => false,
            'loading' => wp_lazy_loading_enabled( 'img', 'get_avatar' ) ? 'lazy' : null,
            'extra_attr' => '',
            'processed_args' => null,
        ];

        $args = wp_parse_args( $args, $defaults );

        $args['force_default'] = (bool) $args['force_default'];

        $args['rating'] = strtolower( $args['rating'] );

        if ( is_null( $avatar = apply_filters( 'pre_get_avatar', null, $user, $args ) ) ) {

            if ( ! $args['force_display'] && ! get_option( 'show_avatars' ) ) {
                return false;
            };

            $url2x = self::get_avatar_url( $user, array_merge( $args, [
                'size' => $args['size'] * 2,
                'width' => $args['width'] * 2,
                'height' => $args['height'] * 2,
            ] ) );

            $args = self::get_avatar_data( $user, $args );

            if ( ! isset( $args['url'] ) || ! $args['url'] || is_wp_error( $args['url'] ) ) {
                return false;
            }

            $class = [ 'avatar', 'avatar-' . (int) $args['size'], 'photo' ];

            if ( ! $args['found_avatar'] || $args['force_default'] ) {
                $class[] = 'avatar-default';
            }

            if ( $args['class'] ) {
                if ( is_array( $args['class'] ) ) {
                    $class = array_merge( $class, $args['class'] );
                }
                else {
                    $class[] = $args['class'];
                }
            }

            $extra_attr = $args['extra_attr'];
            $loading = $args['loading'];

            if ( in_array( $loading, [ 'lazy', 'eager' ], true ) && ! preg_match( '/\bloading\s*=/', $extra_attr ) ) {
                if ( ! empty( $extra_attr ) ) {
                    $extra_attr .= ' ';
                }

                $extra_attr .= "loading='{$loading}'";
            }

            $avatar = sprintf(
                "<img alt='%s' src='%s' srcset='%s' class='%s' height='%d' width='%d' %s/>",
                esc_attr( $args['alt'] ),
                esc_url( $args['url'] ),
                esc_url( $url2x ) . ' 2x',
                esc_attr( join( ' ', $class ) ),
                (int) $args['height'],
                (int) $args['width'],
                $extra_attr
            );

        }

        return apply_filters( 'get_avatar', $avatar, $user, $size, $default, $alt );

    }

    public function run() {
        add_action( 'do_meta_boxes', [ $this, 'remove_meta_box' ], 5, 2 );
        add_action( 'acf/save_post', [ $this, 'save_post' ], 20, 1 );
        add_action( 'acf/load_value/name=authors', [ $this, 'load_value' ], 5, 3 );
    }

    public function remove_meta_box( $screen, $context ) {
        if ( 'post' == $screen ) {
            remove_meta_box( 'authordiv', $screen, $context );
        }
    }

    public function save_post( $post_id ) {
        $authors = get_field( 'authors', $post_id, false );
        wp_update_post( [ 'ID' => $post_id, 'post_author' => $authors[0] ] );
    }

    public function load_value( $value, $post_id, $field ) {
        if ( ! $value ) {
            $value[] = get_post_field( 'post_author', $post_id );
        }
        return $value;
    }

    private static function user( $user_identification ) {

        if ( is_object( $user_identification ) && isset( $user_identification->comment_ID ) ) {
            $user_identification = get_comment( $user_identification );
        }

        if ( $user_identification instanceof \WP_User ) {
            $user = $user_identification;
        }
        else if ( is_numeric( $user_identification ) ) {
            $user = get_user_by( 'id', absint( $user_identification ) );
        }
        else if ( is_string( $user_identification ) ) {
            if ( ! strpos( $user_identification, '@md5.gravatar.com' ) ) {
                $user = false;
            }
            else {
                $user = get_user_by( 'email', $user_identification );
            }
        }
        else if ( $user_identification instanceof \WP_Post ) {
            $user = get_user_by( 'id', (int) $user_identification->post_author );
        }
        else if ( $user_identification instanceof \WP_Comment ) {
            if ( ! is_avatar_comment_type( get_comment_type( $user_identification ) ) ) {
                $user = false;
            }
            if ( ! empty( $user_identification->user_id ) ) {
                $user = get_user_by( 'id', (int) $user_identification->user_id );
            }
            if ( ( ! $user || is_wp_error( $user ) ) && ! empty( $user_identification->comment_author_email ) ) {
                $user = get_user_by( 'email', $user_identification->comment_author_email );
            }
        }
        else {
            $user = false;
        }

        return $user;

    }

    private static function get_avatar_url( $user, $args ) {
        $args = self::get_avatar_data( $user, $args );
        return isset( $args['url'] ) ? $args['url'] : false;
    }

    private static function get_avatar_data( $user, $args ) {

        $args = apply_filters( 'pre_get_avatar_data', $args, $user );

        if ( empty( $args['url'] ) ) {

            if ( $user && ! $args['force_default'] ) {
                $id = get_user_meta( $user->ID, 'kntnt-author-avatar', true );
                $args['found_avatar'] = true;
            }
            else {
                $id = false;
            }

            if ( ! $id ) {
                $id = apply_filters( 'kntnt-avatar-default-attachment', get_option( 'kntnt-avatar-default-attachment' ), $args, $user );
            }

            if ( $id ) {
                if ( $src = wp_get_attachment_image_src( $id, [ $args['width'], $args['height'] ] ) ) {
                    $args['url'] = apply_filters( 'get_avatar_url', $src[0], $user, $args );
                }
            }

        }

        return apply_filters( 'get_avatar_data', $args, $user );

    }

}
