<?php

namespace PluginLib;


class Options
{
    static private Options $_instance;
    static public string $plugin_url;
    static public string $plugin_path;

    protected string $option_name;
    protected bool $has_options = false;
    protected array $properties = [];

    function __construct() {
        $this->read();
    }

    public function values() : array {
        $values = [];
        foreach ($this->properties as $name) {
            $values[$name] = $this->{$name};
        }
        return $values;
    }

    public function properties() : array {
        return $this->properties;
    }

    public function read(?array $defaults=null) : void
    {
        $options = get_option($this->option_name, $defaults);
        if (gettype($options) == 'array') {
            foreach ($this->properties as $name) {
                if (isset($options[$name])) {
                    $this->{$name} = $options[$name];
                }
            }
            $this->has_options = true;
        } else {
            $this->has_options = false;
        }
    }

    public function save() : void
    {
        if ($this->has_options) {
            update_option($this->option_name, $this->values());
        }
    }

    static function init() : void {
        $class = static::class;
        self::$_instance = new $class();
        self::$plugin_path = dirname(__FILE__, 2);
        self::$plugin_url = plugins_url('', self::$plugin_path . '/hack');
    }

    static function instance() : self {
        if (!isset(self::$_instance)) {
            self::init();
        }
        return self::$_instance;
    }
}
