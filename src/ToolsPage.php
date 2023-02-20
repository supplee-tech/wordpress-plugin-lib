<?php
namespace PluginLib;


require_once dirname(__FILE__) . '/Options.php';

const CSS_MARKDOWN = 'plugin-lib-markdown-css';


abstract class ToolsPage {
    protected string $name;
    protected string $name_abbrev;
    protected string $menu_slug;
    protected string $plugin_path;

    protected array $tabs;

    function __construct(string $name, string $name_abbrev, string $menu_slug, string $plugin_path) {
        $this->name = $name;
        $this->name_abbrev = $name_abbrev;
        $this->menu_slug = $menu_slug;
        $this->plugin_path = $plugin_path;
    }

    static function register_styles() : void {
        wp_register_style(CSS_MARKDOWN, plugins_url('/css/markdown.css', __FILE__));
    }

    static function enqueue_styles() : void {
        wp_enqueue_style(CSS_MARKDOWN);
    }

    function setup() : void {
        add_management_page( $this->name, $this->name_abbrev, 'manage_options', $this->menu_slug, [$this, 'init']);
    }

    public function set_tabs(array $tabs) {
        $this->tabs = $tabs;
    }

    function active_tab() : string {
        $action = $_GET['action'] ?? array_key_first($this->tabs);
        if (!isset($this->tabs[$action])) {
            $action = array_key_first($this->tabs);
        }
        return $action;
    }

    function echo_tab() : void {
        $method = $this->tabs[$this->active_tab()];
        $this->$method();
    }

    function echo_tabs_header() : void {
        echo <<<HTML
        <h2 class="nav-tab-wrapper">
HTML;
        $active = $this->active_tab();
        foreach ($this->tabs as $label => $method) {
            $url = add_query_arg(
                [
                    'page' => $this->menu_slug,
                    'action' => $label,
                ],
                'admin.php'
            );
            $classes = "nav-tab" . ($active == $label ? ' nav-tab-active' : '');
            $label = esc_html($label);
            echo <<<HTML
            <a href="$url" class="$classes">$label</a>
HTML;
        }
        echo "</h2>";
    }

    function init() : void {
        $results = $this->handle_post();
        ?>
        <h1><?= $this->name ?></h1>

<?php
        $this->echo_tabs_header();

        $this->echo_tab();

        if ($results) {
            echo "<h2>Results</h2>";
            echo $results;
        }
    }

    function echo_markdown_file(string $file) : void {
        self::enqueue_styles();

        echo "<div class='doc-markdown'>";
        echo \Michelf\Markdown::defaultTransform(file_get_contents($file));
        echo "</div>";
    }

    function echo_select(string $name, string $label, array $options, string $selected, array $group_labels=null) : void {
?>
        <tr>
            <td>
                <label for="<?= $name ?>_id"><?= $label ?></label>
            </td>
            <td>
                <select id="<?= $name ?>_id" name="<?= $name ?>">
<?php
                    foreach ($options as $value => $name) {
                        if (gettype($name) == 'array') {
                            $group_label = $group_labels[$value] ?? $value;
                            echo "<optgroup label='{$group_label}'>\n";
                            foreach ($name as $value2 => $name2) {
                                $is_selected = $value2 == $selected ? ' selected' : '';
                                echo "<option value='$value2'$is_selected>$name2</option>";
                            }
                            echo "</optgroup>\n";
                        } else {
                            $is_selected = $value == $selected ? ' selected' : '';
                            echo "<option value='$value'$is_selected>$name</option>";
                        }
                    }
?>
                </select>
            </td>
        </tr>

<?php
    }

    function documentation_tab() : void {
        $this->echo_markdown_file($this->plugin_path . '/DOCUMENTATION.md');
    }

    function readme_tab() : void {
        $this->echo_markdown_file($this->plugin_path . '/README.md');
    }

    function notes_tab() : void {
        $this->echo_markdown_file($this->plugin_path . '/NOTES.md');
    }

    abstract function handle_post() : string;
}
