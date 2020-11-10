<?php


namespace Kntnt\Author;


final class Plugin extends Abstract_Plugin {

    use Logger;
    use Options;
    use Dependency_Check;

    private static $classes = [
        'any' => [
            'init' => [
                'ACF_Loader',
                'Avatar_Manager',
            ],
        ],
        'admin' => [
            'init' => [
                'Settings',
                'User_Edit_Form_Fixer',
            ],
        ],
    ];

    private static $dependencies = [
        [
            'advanced-custom-fields/acf.php' => "Advanced Custom Fields",
            'advanced-custom-fields-pro/acf.php' => "Advanced Custom Fields Pro",
        ],
    ];

    public static function dependencies() {
        return self::$dependencies;
    }

    public function __construct() {
        parent::__construct();
        if ( self::is_dependencies_satisfied() ) {
            require Plugin::plugin_dir( 'includes/get_avatar.php' );
        }
    }

    public function classes_to_load() {
        return self::is_dependencies_satisfied() ? self::$classes : [];
    }

}
