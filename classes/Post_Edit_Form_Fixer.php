<?php


namespace Kntnt\Author;


class Post_Edit_Form_Fixer {

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

}
