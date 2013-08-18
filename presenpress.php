<?php
/*
Plugin Name: PresenPress
*/

define('PRESENPRESS_REWRITE_PATH', 'presenpress');
define('PRESENPRESS_URL', plugins_url('', __FILE__));
define('PRESENPRESS_PATH', dirname(__FILE__));

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

const reveal_version = '2.4.0';

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
    add_action('presenpress_enqueue_scripts', array($this, 'presenpress_enqueue_scripts'));
    add_action('presenpress_head', array($this, 'presenpress_head'));
    add_action('presenpress_footer', array($this, 'presenpress_footer'));
    add_action('wp', array($this, 'parse_request'), 9999);
}

public function parse_request()
{
    if ($this->is_presen()) {
        add_filter('show_admin_bar', '__return_false');
    }
}

public function presenpress_footer()
{
    wp_print_footer_scripts();
}

public function presenpress_head()
{
    do_action('presenpress_enqueue_scripts');

    wp_print_styles();
    wp_print_head_scripts();

?>
        <script>
            var presenpress_url  = '<?php echo PRESENPRESS_URL; ?>';
            document.write( '<link rel="stylesheet" href="' + presenpress_url + '/reveal/css/print/' + ( window.location.search.match( /print-pdf/gi ) ? 'pdf' : 'paper' ) + '.css" type="text/css" media="print">' );
        </script>
        <!--[if lt IE 9]>
        <script src="<?php echo PRESENPRESS_URL; ?>/reveal/lib/js/html5shiv.js"></script>
        <![endif]-->
<?php
}

public function presenpress_enqueue_scripts()
{
    wp_enqueue_style(
        'reveal',
        PRESENPRESS_URL.'/reveal/css/reveal.min.css',
        array(),
        self::reveal_version
    );

    wp_enqueue_style(
        'reveal-theme',
        PRESENPRESS_URL.'/reveal/css/theme/default.css',
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
        PRESENPRESS_URL.'/css/presenpress.css',
        array(),
        filemtime(dirname(__FILE__).'/css/presenpress.css')
    );

    wp_enqueue_script(
        'leapjs',
        '//js.leapmotion.com/0.2.0/leap.min.js',
        array('jquery'),
        '0.2.0',
        true
    );

    wp_enqueue_script(
        'jquery-leapmotion',
        '//jqueryleapmotion.s3.amazonaws.com/jquery.leapmotion.min.js',
        array('leapjs'),
        false,
        true
    );

    wp_enqueue_script(
        'reveal-head-js',
        PRESENPRESS_URL.'/reveal/lib/js/head.min.js',
        array('jquery'),
        self::reveal_version,
        true
    );

    wp_enqueue_script(
        'reveal-js',
        PRESENPRESS_URL.'/reveal/js/reveal.min.js',
        array('reveal-head-js'),
        self::reveal_version,
        true
    );

    wp_enqueue_script(
        'presenpress-js',
        PRESENPRESS_URL.'/js/presenpress.js',
        array('reveal-js', 'jquery-leapmotion'),
        self::reveal_version,
        filemtime(dirname(__FILE__).'/js/presenpress.js')
    );
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

    if ($this->is_presen()) {
    }
}

public function query_vars($vars)
{
    $vars[] = PRESENPRESS_REWRITE_PATH;
    return $vars;
}

public function template_redirect()
{
    if ($this->is_presen()) {
        require_once(dirname(__FILE__).'/includes/app.php');
        exit;
    }
}

private function send_404()
{
    $wp_query->set_404();
    status_header(404);
    return;
}

private function is_presen()
{
    global $wp_query;
    if (isset($wp_query->query[PRESENPRESS_REWRITE_PATH])) {
        return true;
    } else {
        return false;
    }
}

} // end class

// EOF
