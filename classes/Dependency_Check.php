<?php


namespace Kntnt\Author;


trait Dependency_Check {

    static private $unsatisfied_dependencies = null;

    // Returns an array of dependency groups. Each dependency group is an array
    // with alternative plugins. Each plugin is a key/value-pair, where the key
    // is the path to the plugin file relative to the plugins directory,
    // and the value is the name of the plugin. If one of the plugins in a
    // dependency group is active, then the dependency on that dependency group
    // is satisfied. If all dependency groups are satisfied, the dependencies
    // of this plugin is satisfied.
    abstract protected static function dependencies();

    final public static function is_dependencies_satisfied() {
        return ! self::unsatisfied_dependencies();
    }

    // Returns dependencies() except satisfied dependencies.
    final public static function unsatisfied_dependencies() {
        if ( null === self::$unsatisfied_dependencies ) {
            self::$unsatisfied_dependencies = [];
            $plugins = (array) get_option( 'active_plugins', [] );
            foreach ( static::dependencies() as $dependency_group ) {
                if ( ! array_intersect( array_keys( $dependency_group ), $plugins ) ) {
                    self::$unsatisfied_dependencies[] = $dependency_group;
                }
            }
            if ( self::$unsatisfied_dependencies && ( $traits = class_uses( self::class ) ) && isset( $traits[ __NAMESPACE__ . '\\Logger' ] ) ) {
                Plugin::error( static::unsatisfied_dependencies_message() );
            }
        }
        return self::$unsatisfied_dependencies;
    }

    // Returns a message listing missing dependencies.
    // Override to provide a customized message.
    public static function unsatisfied_dependencies_message() {
        $msg = '';
        if ( $plugins = self::$unsatisfied_dependencies ) {
            $n = 0;
            foreach ( $plugins as &$plugin ) {
                $n += count( $plugin );
                $plugin = join( ' ' . __( 'or', 'kntnt' ) . ' ', $plugin );
            }
            $plugins = join( ' ' . __( ', and', 'kntnt' ) . ' ', $plugins );
            $msg = sprintf( _n( '%s is required.', '%s are required', $n, 'kntnt' ), $plugins );
        }
        return $msg;
    }

}