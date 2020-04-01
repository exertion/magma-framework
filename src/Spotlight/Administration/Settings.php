<?php

namespace Spotlight\Administration;

use TDP\OptionsKit;
use Spotlight\Helpers\Helper;

class Settings
{
    protected static $enabled;
    protected static $tabs = [];

    // used for chaining.
    public static $currentTab;
    public static $options = [];
    private static $_instance = null;

    public function __construct($enabled)
    {
        self::$enabled = $enabled;
    }

    /**
     * Setup the menu for the options panel.
     *
     * @param array $menu
     *
     * @return array
     */
    public function igp_setup_menu()
    {
    	// These defaults can be customized
    	$menu['parent'] = 'options-general.php';
    	$menu['menu_title'] = 'Settings Panel';
    	$menu['capability'] = 'manage_options';

    	$menu['page_title'] = __( 'My Plugin Settings' );
    	$menu['menu_title'] = $menu['page_title'];

    	return $menu;
    }

    /**
     * Register settings tabs.
     *
     * @param array $tabs
     *
     * @return array
     */
    public function igp_register_settings_tabs( )
    {
        return self::$tabs;
    }

    /**
     * Register settings subsections (optional)
     *
     * @param array $subsections
     *
     * @return array
     */
    public function igp_register_settings_subsections( $subsections )
    {
    	return $subsections;
    }

    /**
     * Register settings fields for the options panel.
     *
     * @param array $settings
     *
     * @return array
     */
    public function igp_register_settings( $settings )
    {
    	return self::$options;
    }


    public static function tab($name, $slug = null)
    {
        if (is_null($slug)) { $slug = $name; }
        $slug = Helper::slugify($slug);
        self::$tabs[$slug] = $name;

        // Chain the commands.
        self::$currentTab = $slug; // Set the current tab defined, used for chaining options to it.
        if (self::$_instance === null) {
            self::$_instance = new self(self::$enabled);
        }
        return self::$_instance;
    }

    // Build the option under the category.
    public function option($field)
    {
        self::$options[self::$currentTab][] = [
            'id' => Helper::slugify($field['name']),
            'name' => $field['name'],
            'type' => !is_null($field['type']) ? $field['type'] : 'text',
            'desc' => !is_null($field['description']) ? $field['description'] : NULL,
            'options' => !is_null($field['options']) ? $field['options'] : NULL,
            'std' => !is_null($field['default']) ? $field['default'] : NULL
        ];
        return $this;
    }

    public static function init()
    {
        if (self::$enabled) {
            $prefix = 'igp';
            $panel = new OptionsKit($prefix);
            $panel->set_page_title( __( 'My Plugin Settings' ) );

            add_filter( 'igp_menu', [ self::class, 'igp_setup_menu' ] );
            add_filter( 'igp_settings_tabs', [ self::class, 'igp_register_settings_tabs' ] );
            add_filter( 'igp_registered_settings_sections', [ self::class, 'igp_register_settings_subsections' ] );
            add_filter( 'igp_registered_settings', [ self::class, 'igp_register_settings' ] );
        }
    }
}
