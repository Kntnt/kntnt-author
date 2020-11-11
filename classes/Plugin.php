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
                'Avatar_Loader',
            ],
        ],
        'admin' => [
            'init' => [
                'Settings',
                'User_Edit_Form_Fixer',
                'Post_Edit_Form_Fixer',
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

    public function classes_to_load() {
        return self::is_dependencies_satisfied() ? self::$classes : [];
    }

}
