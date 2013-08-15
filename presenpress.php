<?php
/*
Plugin Name: PresenPress
*/

define('PRESENPRESS_REWRITE_PATH', 'presenpress');

$presenpress = new PresenPress();
$presenpress->register();

register_activation_hook(__FILE__, 'presenpress_activation');
register_deactivation_hook(__FILE__, 'presenpress_deactivation');

function presenpress_activation(){
    add_rewrite_endpoint(PRESENPRESS_REWRITE_PATH, EP_ROOT);
    flush_rewrite_rules();
}

function presenpress_deactivation(){
    flush_rewrite_rules();
}

class PresenPress {

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
    add_action('query_vars', array($this, 'query_vars'));
    add_action('template_redirect', array($this, 'template_redirect'));
}

public function init()
{
    if (is_admin()) {
        global $pagenow;
        if ($pagenow === 'plugins.php') {
            if (isset($_GET['plugin'])) {
                if (basename(__FILE__) === basename($_GET['plugin'])) {
                    return;
                }
            }
        }
        add_rewrite_endpoint(PRESENPRESS_REWRITE_PATH, EP_ROOT);
    }
}

public function query_vars($vars)
{
    $vars[] = PRESENPRESS_REWRITE_PATH;
    return $vars;
}

public function template_redirect()
{
    global $wp_query;
    if (isset($wp_query->query[PRESENPRESS_REWRITE_PATH])) {
/*
        if (!isset($app_menu[get_query_var(MY_REWRITE_PATH)]) || !$app_menu[get_query_var(MY_REWRITE_PATH)]) {
            $wp_query->set_404();
            status_header(404);
            return;
        }
*/
        require_once(dirname(__FILE__).'/includes/app.php');
        exit;
    }
}

} // end class

// EOF
