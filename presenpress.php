<?php
/*
Plugin Name: PresenPress
Author: Takayuki Miyauchi
Plugin URI: http://wpist.me/
Description: Presentation with WordPress + Leap Motion.
Version: 0.1.0
Author URI: http://wpist.me/
Domain Path: /languages
Text Domain: presenpress
*/

define('PRESENPRESS_REWRITE_PATH', 'presenpress');
define('PRESENPRESS_URL', plugins_url('', __FILE__));
define('PRESENPRESS_PATH', dirname(__FILE__));

$presenpress = new PresenPress();
$presenpress->register();

register_activation_hook(__FILE__, 'presenpress_activation');
register_deactivation_hook(__FILE__, 'presenpress_deactivation');

function presenpress_activation(){
    flush_rewrite_rules();
}

function presenpress_deactivation(){
    flush_rewrite_rules();
}

class PresenPress {

const presenpress_version = '0.1.0';
const reveal_version = '2.4.0';
const post_type = 'presenpress';

function __construct()
{
}

public function register()
{
    add_action('plugins_loaded', array($this, 'plugins_loaded'));
}

public function plugins_loaded()
{
    add_action('init', array($this, 'init'));
    add_action('template_redirect', array($this, 'template_redirect'));
    add_action('wp_enqueue_scripts', array($this, 'wp_enqueue_scripts'));
    add_action('wp_head', array($this, 'wp_head'));
    add_action('save_post', array($this, 'save_post'));
    add_action('admin_head', array($this, 'admin_head'));
}

public function admin_head()
{
    echo <<<EOL
<style>
#presenpress-settings th,
#presenpress-settings td
{
    padding: 5px 10px;
}
</style>
EOL;
}

public function wp_head()
{
    if (!$this->is_presen()) {
        return false;
    }

    global $wp_query;

?>
        <script>
            var presenpress_url  = '<?php echo PRESENPRESS_URL; ?>';
            document.write( '<link rel="stylesheet" href="' + presenpress_url + '/reveal/css/print/' + ( window.location.search.match( /print-pdf/gi ) ? 'pdf' : 'paper' ) + '.css" type="text/css" media="print">' );
            var presentation_settings = {
                history: <?php echo get_post_meta($wp_query->post->ID, '_presenpress_history', true) ? 'true' : 'false'; ?>,
                transition: '<?php echo esc_js(get_post_meta($wp_query->post->ID, '_presenpress_transition', true)); ?>'
            };
        </script>
        <!--[if lt IE 9]>
        <script src="<?php echo PRESENPRESS_URL; ?>/reveal/lib/js/html5shiv.js"></script>
        <![endif]-->
<?php

    if ($style = get_post_meta($wp_query->post->ID, '_presenpress_style', true)) {
        echo "<style>\n";
        echo $style;
        echo "</style>\n";
    }
}

public function wp_enqueue_scripts()
{
    if (!$this->is_presen()) {
        return false;
    }

    wp_enqueue_style(
        'reveal',
        PRESENPRESS_URL.'/reveal/css/reveal.min.css',
        array(),
        self::reveal_version
    );

    global $wp_query;
    $theme_css = PRESENPRESS_URL.'/reveal/css/theme/default.css';
    if ($theme = get_post_meta($wp_query->post->ID, '_presenpress_theme', true)) {
        if (preg_match('/^[a-zA-Z0-9]+$/', $theme)) {
            $theme_css = PRESENPRESS_URL.'/reveal/css/theme/'.$theme.'.css';
        }
    }

    wp_enqueue_style(
        'reveal-theme',
        $theme_css,
        array('reveal'),
        self::reveal_version
    );

    wp_enqueue_style(
        'reveal-zenburn',
        PRESENPRESS_URL.'/reveal/lib/css/zenburn.css',
        array('reveal-theme'),
        self::reveal_version
    );

    wp_enqueue_style(
        'presenpress-style',
        apply_filters(
            'presenpress_stylesheet_url',
            PRESENPRESS_URL.'/css/presenpress.min.css'
        ),
        array('reveal-zenburn'),
        apply_filters(
            'presenpress_stylesheet_version',
            self::presenpress_version
        )
    );

    wp_enqueue_script(
        'reveal-js',
        PRESENPRESS_URL.'/js/reveal-package.min.js',
        array('jquery'),
        self::reveal_version,
        true
    );

    wp_enqueue_script(
        'presenpress-js',
        PRESENPRESS_URL.'/js/presenpress.min.js',
        array('reveal-js', 'jquery'),
        self::presenpress_version,
        true
    );
}

public function init()
{
    $args = array(
        'label' => __('Presentations', 'presenpress'),
        'labels' => array(
            'singular_name' => __('Presentation', 'presenpress'),
            'add_new_item' => __('Add New Presentation', 'presenpress'),
            'edit_item' => __('Edit Presentation', 'presenpress'),
            'add_new' => __('Add New', 'presenpress'),
            'new_item' => __('New Presentation', 'presenpress'),
            'view_item' => __('View Presentation', 'presenpress'),
            'not_found' => __('No Presentations found.', 'presenpress'),
            'not_found_in_trash' => __(
                'No Presentations found in Trash.',
                'presenpress'
            ),
            'search_items' => __('Search Presentations', 'presenpress'),
        ),
        'public' => true,
        'publicly_queryable' => true,
        'exclude_from_search' => true,
        'show_ui' => true,
        'capability_type' => 'post',
        'hierarchical' => false,
        'rewrite' => array(
            'slug' => 'presentations',
            'with_front' => false
        ),
        'show_in_nav_menus' => false,
        'can_export' => false,
        'menu_icon' => plugins_url('img/icon.png', __FILE__),
        'register_meta_box_cb' => array($this, 'register_meta_box_cb'),
        'supports' => array(
            'title',
            'editor',
            'excerpt',
            'thumbnail',
            'revisions',
        )
    );

    register_post_type(
        self::post_type,
        apply_filters('presenpress_register_post_type_args', $args)
    );

}

public function register_meta_box_cb()
{
    add_meta_box(
        'presenpress-settings',
        __('Presentation Settings', 'presenpress'),
        array($this, 'meta_box_settings'),
        self::post_type,
        'normal',
        'low'
    );

    add_meta_box(
        'presenpress-style',
        __('Presentation Styles', 'presenpress'),
        array($this, 'meta_box_styles'),
        self::post_type,
        'normal',
        'low'
    );
}

public function meta_box_settings($post, $metabox)
{
    echo '<table>';


    $theme = get_post_meta($post->ID, '_presenpress_theme', true);

    $themes = array();
    $dir = dirname(__FILE__).'/reveal/css/theme';
    if (is_dir($dir)) {
        if ($dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {
                if (preg_match('/\.css$/', $file)) {
                    $themes[] = $file;
                }
            }
        }
    }

    echo '<tr>';
    echo '<th style="text-align: left; font-weight: normal;">Theme:</th>';
    echo '<td>';
    echo "<select name=\"presenpress_theme\">";
    echo '<option value="default">Default</option>';
    foreach ($themes as $t) {
        $option = preg_replace('/\.css$/', '', $t);
        if ($option !== 'default') {
            if ($option === $theme) {
                $selected = 'selected="selected"';
            } else {
                $selected = '';
            }
            printf(
                '<option value="%s" %s>%s</option>',
                esc_attr($option),
                $selected,
                esc_html(ucwords($option))
            );
        }
    }
    echo "</select>";
    echo '</td>';
    echo '</tr>';


    $hist = get_post_meta($post->ID, '_presenpress_history', true);

    echo '<tr>';
    echo '<th style="text-align: left; font-weight: normal;">Browsing History:</th>';
    echo '<td>';
    if ($hist) {
        $checked = 'checked="checked"';
    } else {
        $checked = '';
    }
    $radio = '<input type="checkbox" id="%1$s" name="%1$s" value="%2$s" %4$s> <label for="%1$s">%3$s</label>&nbsp;';
    printf(
        $radio,
        'presenpress_history',
        1,
        'Enabled',
        $checked
    );
    echo '</td>';
    echo '</tr>';


    $transition = get_post_meta($post->ID, '_presenpress_transition', true);

    echo '<tr>';
    echo '<th style="text-align: left; font-weight: normal;">Transtion:</th>';
    echo '<td>';
    $transitions = array(
        'default',
        'cube',
        'page',
        'concave',
        'zoom',
        'linear',
        'fade',
        'none',
    );
    echo "<select name=\"presenpress_transition\">";
    foreach ($transitions as $t) {
        if ($t === $transition) {
            $selected = 'selected="selected"';
        } else {
            $selected = '';
        }
        printf(
            '<option value="%s" %s>%s</option>',
            esc_attr($t),
            $selected,
            esc_html(ucwords($t))
        );
    }
    echo '</td>';
    echo '</tr>';

    echo '</table>';
}

public function meta_box_styles($post, $metabox)
{
    $style = get_post_meta($post->ID, '_presenpress_style', true);
    if (!$style) {
        $style = "/*
.reveal h1, .reveal h2, .reveal h3, .reveal h4, .reveal h5, .reveal h6
{
    font-family: sans-serif;
    text-transform: none;
}
*/";
    }

    printf(
        '<textarea name="presenpress_style" style="width: 100%%;height: 200px;">%s</textarea>',
        esc_html($style)
    );
}

public function save_post($id)
{
    if (!$this->is_presen()) {
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return $id;

    if (isset($_POST['action']) && $_POST['action'] == 'inline-save')
        return $id;

    if (isset($_POST['presenpress_style'])) {
        update_post_meta($id, '_presenpress_style', $_POST['presenpress_style']);
    }

    if (isset($_POST['presenpress_theme'])) {
        update_post_meta($id, '_presenpress_theme', $_POST['presenpress_theme']);
    }

    if (isset($_POST['presenpress_history'])) {
        update_post_meta($id, '_presenpress_history', 1);
    } else {
        update_post_meta($id, '_presenpress_history', 0);
    }

    if (isset($_POST['presenpress_transition'])) {
        update_post_meta($id, '_presenpress_transition', $_POST['presenpress_transition']);
    }
}

public function template_redirect()
{
    if ($this->is_presen()) {
        require_once(dirname(__FILE__).'/includes/app.php');
        exit;
    }
}

private function is_presen()
{
    if (self::post_type === get_post_type()) {
        return true;
    } else {
        return false;
    }
}

} // end class

// EOF
