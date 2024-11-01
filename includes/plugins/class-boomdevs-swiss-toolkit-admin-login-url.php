<?php

/**
 * Prevent direct access to this file.
 */
if (!defined('ABSPATH')) {
    exit;
}

// Include the plugin's settings class
require_once BDSTFW_SWISS_TOOLKIT_PATH . 'includes/class-boomdevs-swiss-toolkit-settings.php';

/**
 * Manages the login url for the plugin.
 */
if (!class_exists('BDSTFW_Swiss_Toolkit_Admin_Login_Url')) {
    class BDSTFW_Swiss_Toolkit_Admin_Login_Url
    {
        private $wp_login_php;
    
        /**
         * The single instance of the class.
         */
        protected static $instance;
    
        private function use_trailing_slashes()
        {
            return '/' === substr(get_option('permalink_structure'), -1, 1);
        }
    
        private function user_trailingslashit($string)
        {
            return $this->use_trailing_slashes() ? trailingslashit($string) : untrailingslashit($string);
        }
    
        /**
         * Returns single instance of the class
         */
        public static function get_instance()
        {
            if (is_null(self::$instance)) {
                self::$instance = new self();
            }
    
            return self::$instance;
        }
    
        /**
         * Constructor.
         * Initializes the custom login URL if enabled in plugin settings.
         */
        public function __construct()
        {
            $settings = BDSTFW_Swiss_Toolkit_Settings::get_settings();
    
            if (
                isset($settings['boomdevs_swiss_change_login_url_switcher']) &&
                isset($settings['boomdevs_swiss_change_login_url_text'])
            ) {
                if ($settings['boomdevs_swiss_change_login_url_switcher'] === '1') {
                    add_action('init', array($this, 'plugins_loaded'), 1);
                    add_action('wp_loaded', array($this, 'wp_loaded'));
                    add_filter('site_url', array($this, 'site_url'), 10, 4);
                    add_filter('wp_redirect', array($this, 'wp_redirect'), 10, 2);
                    remove_action('template_redirect', 'wp_redirect_admin_locations', 1000);
                }
            }
        }
    
        /**
         * Load the WordPress template and handle custom login URL logic.
         */
        private function wp_template_loader()
        {
            global $pagenow;
    
            // Set $pagenow to 'index.php' to load the WordPress template.
            $pagenow = 'index.php';
    
            // Ensure that WordPress uses themes.
            if (!defined('WP_USE_THEMES')) {
                define('WP_USE_THEMES', true);
            }
    
            // Initialize WordPress.
            wp();
    
            // Check if the request URI matches a specific pattern.
            if ($_SERVER['REQUEST_URI'] === $this->user_trailingslashit(str_repeat('-/', 10))) {
                // Modify the REQUEST_URI to point to '/wp-login-php/' if the pattern is matched.
                $_SERVER['REQUEST_URI'] = $this->user_trailingslashit('/wp-login-php/');
            }
    
            // Include the WordPress template loader.
            require_once(ABSPATH . WPINC . '/template-loader.php');
    
            // Terminate the script execution.
            die;
        }
    
        /**
         * Get the modified login URL based on plugin settings.
         *
         * @param string|null $scheme Optional. The scheme to use (http or https).
         * @return string The modified login URL.
         */
        public function new_login_url($scheme = null)
        {
            // Get plugin settings.
            $settings = BDSTFW_Swiss_Toolkit_Settings::get_settings();
    
            // Check if the custom login URL text is set in the settings.
            if (isset($settings['boomdevs_swiss_change_login_url_text'])) {
                $login_url_text = $settings['boomdevs_swiss_change_login_url_text'];
    
                if ($login_url_text) {
                    // Build and return the modified login URL with a trailing slash.
                    return $this->user_trailingslashit(home_url('/', $scheme) . $login_url_text);
                } else {
                    // Build and return the modified login URL with a query parameter.
                    return home_url('/', $scheme) . '?' . $settings['boomdevs_swiss_change_login_url_text'];
                }
            }
        }
    
        /**
         * Handle plugin initialization and URL redirection logic.
         */
        public function plugins_loaded()
        {
            global $pagenow;
            $settings = BDSTFW_Swiss_Toolkit_Settings::get_settings();
    
            // Check if the request is for 'wp-signup' or 'wp-activate' pages and disable the feature.
            if (
                (strpos(rawurldecode($_SERVER['REQUEST_URI']), 'wp-signup') !== false || strpos(rawurldecode($_SERVER['REQUEST_URI']), 'wp-activate') !== false)
            ) {
                wp_die(__('This feature is not enabled.', 'boomdevs-swiss-toolkit'));
            }
    
            $request = parse_url(rawurldecode($_SERVER['REQUEST_URI']));
    
            // Check if the request is for 'wp-login.php' and not in the admin section.
            if (
                (strpos(rawurldecode($_SERVER['REQUEST_URI']), 'wp-login.php') !== false
                    || (isset($request['path']) && untrailingslashit($request['path']) === site_url('wp-login', 'relative')))
                && !is_admin()
            ) {
                $this->wp_login_php = true;
                $_SERVER['REQUEST_URI'] = $this->user_trailingslashit('/' . str_repeat('-/', 10));
                $pagenow = 'index.php';
            } elseif ((isset($request['path']) && untrailingslashit($request['path']) === home_url($settings['boomdevs_swiss_change_login_url_text'], 'relative'))
                || (!get_option('permalink_structure')
                    && isset($_GET[$settings['boomdevs_swiss_change_login_url_text']])
                    && empty($_GET[$settings['boomdevs_swiss_change_login_url_text']]))
            ) {
                $pagenow = 'wp-login.php';
            } elseif (
                (strpos(rawurldecode($_SERVER['REQUEST_URI']), 'wp-register.php') !== false
                    || (isset($request['path']) && untrailingslashit($request['path']) === site_url('wp-register', 'relative')))
                && !is_admin()
            ) {
                $this->wp_login_php = true;
                $_SERVER['REQUEST_URI'] = $this->user_trailingslashit('/' . str_repeat('-/', 10));
                $pagenow = 'index.php';
            }
        }
    
        /**
         * Handle WordPress loaded action, perform URL redirection, and template loading.
         */
        public function wp_loaded()
        {
            global $pagenow;
    
            $request = parse_url(rawurldecode($_SERVER['REQUEST_URI']));
    
            // Check if the current page is 'wp-login.php' and the URL path is not trailing-slashed.
            if (
                $pagenow === 'wp-login.php' &&
                $request['path'] !== $this->user_trailingslashit($request['path']) &&
                get_option('permalink_structure')
            ) {
                // Redirect to the login URL with a trailing slash.
                wp_safe_redirect($this->user_trailingslashit($this->new_login_url()) . (!empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : ''));
                die;
            }
            // Check if the plugin is in the custom login URL mode.
            elseif ($this->wp_login_php) {
                // Check if the referer contains 'wp-activate.php'.
                if (
                    ($referer = wp_get_referer()) &&
                    strpos($referer, 'wp-activate.php') !== false &&
                    ($referer = parse_url($referer)) &&
                    !empty($referer['query'])
                ) {
                    parse_str($referer['query'], $referer);
    
                    // Check if activation was already completed or the blog is already taken.
                    if (
                        !empty($referer['key']) &&
                        ($result = wpmu_activate_signup($referer['key'])) &&
                        is_wp_error($result) && ($result->get_error_code() === 'already_active' ||
                            $result->get_error_code() === 'blog_taken'
                        )
                    ) {
                        // Redirect to the login URL with query parameters.
                        wp_safe_redirect($this->new_login_url() . (!empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : ''));
                        die;
                    }
                }
    
                // Load the custom login template.
                $this->wp_template_loader();
            }
            // Check if the current page is 'wp-login.php'.
            elseif ($pagenow === 'wp-login.php') {
                global $error, $interim_login, $action, $user_login;
    
                // Include the original 'wp-login.php'.
                @require_once ABSPATH . 'wp-login.php';
    
                die;
            }
        }
    
    
        /**
         * Modify the site URL to handle custom login URL.
         *
         * @param string $url     The original URL.
         * @param string $path    The path to the resource.
         * @param string $scheme  The scheme to use (http or https).
         * @param int    $blog_id The blog ID.
         * @return string         The modified URL.
         */
        public function site_url($url, $path, $scheme, $blog_id)
        {
            return $this->filter_wp_login_php($url, $scheme);
        }
    
        /**
         * Modify the redirect location to handle custom login URL.
         *
         * @param string $location The original redirect location.
         * @param int    $status   The redirect status code.
         * @return string          The modified redirect location.
         */
        public function wp_redirect($location, $status)
        {
            return $this->filter_wp_login_php($location);
        }
    
        /**
         * Handles the plugin's custom redirection logic.
         *
         * @param string $url     The URL to be redirected.
         * @param string $scheme  The scheme to use (http or https).
         * @return string         The modified URL.
         */
        public function filter_wp_login_php($url, $scheme = null)
        {
            $current_url = isset($_SERVER['PHP_SELF']) ? sanitize_text_field(wp_unslash($_SERVER['PHP_SELF'])) : '';
    
            // Check if the URL contains 'wp-login.php' or 'wp-login'.
            if (is_int(strpos($url, 'wp-login.php')) || is_int(strpos($url, 'wp-login'))) {
                // Check if SSL is enabled and set the scheme accordingly.
                if (is_ssl()) {
                    $scheme = 'https';
                }
    
                $args = explode('?', $url);
                if (isset($args[1])) {
                    wp_parse_str($args[1], $args);
                    $url = add_query_arg($args, $this->new_login_url($scheme));
                } else {
                    $url = $this->new_login_url($scheme);
                }
            }
    
            // Check if the current URL is not in 'wp-admin'.
            if (!is_int(strpos($current_url, 'wp-admin'))) {
                return $url;
            }
    
            // Check if the 'is_user_logged_in' function exists.
            if (!function_exists('is_user_logged_in')) {
                return $url;
            }
    
            // Check if the user is not logged in and redirect to the home URL.
            if (!is_user_logged_in()) {
                return home_url();
            }
    
            return $url;
        }
    
        /**
         * Get an array of forbidden slugs.
         *
         * @return array An array of forbidden slugs.
         */
        public function forbidden_slugs()
        {
            $wp = new WP;
            return array_merge($wp->public_query_vars, $wp->private_query_vars);
        }
    }
    
    // Initialize the Boomdevs_Swiss_Toolkit_Login_Url class
    BDSTFW_Swiss_Toolkit_Admin_Login_Url::get_instance();
}