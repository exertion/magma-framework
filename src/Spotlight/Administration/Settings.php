<?php

namespace Spotlight\Administration;

use TDP\OptionsKit;
use Spotlight\Helpers\Helper;

class Settings
{
    protected static $enabled;
    protected static $tabs = [];
    protected static $prefix;

    // used for chaining.
    public static $currentTab;
    public static $options = [];
    private static $_instance = null;

    public function __construct($enabled)
    {
        self::$prefix = str_replace('-', '_', Helper::slugify(config('name'))); // Build prefix from plugin name.
        self::$enabled = $enabled;
    }

    /**
     * Setup the menu for the options panel.
     *
     * @param array $menu
     *
     * @return array
     */
    public function setupMenu()
    {
    	// These defaults can be customized
    	$menu['parent'] = 'options-general.php';
    	$menu['menu_title'] = 'Settings Panel';
    	$menu['capability'] = 'manage_options';

    	$menu['page_title'] = __( config('name').' Settings' );
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
    public function registerSettingsTabs( )
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
    public function registerSettingsSubsections( $subsections )
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
    public function registerSettings( $settings )
    {
    	return self::$options;
    }

    /**
    *   ADD LINK TO SETTINGS PAGE FROM PLUGINS PAGE (BESIDES THIS PLUGIN)
    */
    public function appendPluginSettings($links) {
        $settings_link = "<a href='options-general.php?page=". self::$prefix ."-settings'>Settings</a>";
        array_unshift($links, $settings_link);
        return $links;
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
            $panel = new OptionsKit(self::$prefix);
            $panel->set_page_title( __( config('name').' Settings' ) );

            add_filter( self::$prefix.'_menu', [ self::class, 'setupMenu' ] );
            add_filter( self::$prefix.'_settings_tabs', [ self::class, 'registerSettingsTabs' ] );
            add_filter( self::$prefix.'_registered_settings_sections', [ self::class, 'registerSettingsSubsections' ] );
            add_filter( self::$prefix.'_registered_settings', [ self::class, 'registerSettings' ] );

            add_filter('plugin_action_links_'.config('plugin_basename'), [self::class, 'appendPluginSettings'] ); // Add settings link beside plugins list.
        }
    }
}
