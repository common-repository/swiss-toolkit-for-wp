<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Include the settings class to retrieve settings from the settings panel.
require_once BDSTFW_SWISS_TOOLKIT_PATH . 'includes/class-boomdevs-swiss-toolkit-settings.php';

/**
 * Define the BDSTFW_Swiss_Toolkit_Optimizations for handling Optimization.
 * 
 * @package    BDSTFW_Swiss_Toolkit_Optimizations
 * @author     BoomDevs <contact@boomdevs.com>
 */
if (!class_exists('BDSTFW_Swiss_Toolkit_Optimizations')) {
    class BDSTFW_Swiss_Toolkit_Optimizations {
        /**
         * The single instance of the class.
         */
        protected static $instance;

        /**
         * Returns single instance of the class.
         */
        public static function get_instance(){
            if (is_null(self::$instance)) {
                self::$instance = new self();
            }

            return self::$instance;
        }

        /**
         * Constructor.
         * Initializes the class and registers actions and hooks.
         */
        public function __construct(){
            $settings = BDSTFW_Swiss_Toolkit_Settings::get_settings();

            if (isset($settings['boomdevs_swiss_optimizations_switch']) && $settings['boomdevs_swiss_optimizations_switch'] === '1') {
                add_action('admin_menu', [$this, 'swiss_optimizations_callback']);
            }

            add_action( 'wp_ajax_swiss_toolkit_delete_post_revisions', [$this, 'swiss_toolkit_delete_post_revisions'] );
            add_action( 'wp_ajax_nopriv_swiss_toolkit_delete_post_revisions', [$this, 'swiss_toolkit_delete_post_revisions'] );

            add_action( 'wp_ajax_swiss_toolkit_delete_post_draft', [$this, 'swiss_toolkit_delete_post_draft'] );
            add_action( 'wp_ajax_nopriv_swiss_toolkit_delete_post_draft', [$this, 'swiss_toolkit_delete_post_draft'] );

            add_action( 'wp_ajax_swiss_toolkit_delete_post_trash', [$this, 'swiss_toolkit_delete_post_trash'] );
            add_action( 'wp_ajax_nopriv_swiss_toolkit_delete_post_trash', [$this, 'swiss_toolkit_delete_post_trash'] );

            add_action('wp_ajax_swiss_toolkit_delete_spam_comments', [$this, 'swiss_toolkit_delete_spam_comments']);
            add_action('wp_ajax_nopriv_swiss_toolkit_delete_spam_comments', [$this, 'swiss_toolkit_delete_spam_comments']);

            add_action('wp_ajax_swiss_toolkit_delete_trashed_comments', [$this, 'swiss_toolkit_delete_trashed_comments']);
            add_action('wp_ajax_nopriv_swiss_toolkit_delete_trashed_comments', [$this, 'swiss_toolkit_delete_trashed_comments']);

            add_action('wp_ajax_swiss_toolkit_delete_unapproved_comments', [$this, 'swiss_toolkit_delete_unapproved_comments']);
            add_action('wp_ajax_nopriv_swiss_toolkit_delete_unapproved_comments', [$this, 'swiss_toolkit_delete_unapproved_comments']);

            add_action('wp_ajax_swiss_toolkit_delete_orphaned_postmeta', [$this, 'swiss_toolkit_delete_orphaned_postmeta']);
            add_action('wp_ajax_nopriv_swiss_toolkit_delete_orphaned_postmeta', [$this, 'swiss_toolkit_delete_orphaned_postmeta']);

            add_action('wp_ajax_swiss_toolkit_delete_orphaned_user_meta', [$this, 'swiss_toolkit_delete_orphaned_user_meta']);
            add_action('wp_ajax_nopriv_swiss_toolkit_delete_orphaned_user_meta', [$this, 'swiss_toolkit_delete_orphaned_user_meta']);

            add_action('wp_ajax_swiss_toolkit_delete_orphaned_comment_meta', [$this, 'swiss_toolkit_delete_orphaned_comment_meta']);
            add_action('wp_ajax_nopriv_swiss_toolkit_delete_orphaned_comment_meta', [$this, 'swiss_toolkit_delete_orphaned_comment_meta']);

            add_action('wp_ajax_swiss_toolkit_delete_orphaned_relationship_data', [$this, 'swiss_toolkit_delete_orphaned_relationship_data']);
            add_action('wp_ajax_nopriv_swiss_toolkit_delete_orphaned_relationship_data', [$this, 'swiss_toolkit_delete_orphaned_relationship_data']);

            add_action('wp_ajax_swiss_toolkit_delete_pingbacks', [$this, 'swiss_toolkit_delete_pingbacks']);
            add_action('wp_ajax_nopriv_swiss_toolkit_delete_pingbacks', [$this, 'swiss_toolkit_delete_pingbacks']);

            add_action('wp_ajax_swiss_toolkit_delete_trackbacks', [$this, 'swiss_toolkit_delete_trackbacks']);
            add_action('wp_ajax_nopriv_swiss_toolkit_delete_trackbacks', [$this, 'swiss_toolkit_delete_trackbacks']);

        }

        /**
         * Add "Optimization" menu under the admin menu.
         */
        public function swiss_optimizations_callback(){
            add_menu_page(
                'Database Optimizations',  // Page title
                'DB Optimizations',  // Menu title
                'manage_options',     // Capability required to access the page
                'swiss-toolkit-optimizations', // Menu slug
                array( $this, 'swiss_toolkit_optimizations_callback' ),
                'dashicons-database-remove' // Icon URL or Dashicons class name
            );
        }

        /**
         * Retrieve the total number of post revisions.
         *
         * @return int The total number of revisions.
         */
        private function get_total_revisions() {
            global $wpdb;

            // Prepare the SQL query to get the number of revisions
            $sql = "SELECT COUNT(*) 
                    FROM `" . $wpdb->posts . "` 
                    WHERE post_type = 'revision';";

            // Execute the query and get the count
            $total_revisions = $wpdb->get_var($sql);

            return $total_revisions ? $total_revisions : 0;
        }

        /**
         * Retrieve the total number of draft posts.
         *
         * @return int The total number of draft posts.
         */
        private function get_total_drafts() {
            global $wpdb;

            // Prepare the SQL query to get the number of draft posts
            $sql = "SELECT COUNT(*) 
                    FROM `" . $wpdb->posts . "` 
                    WHERE post_status = 'draft';";

            // Execute the query and get the count
            $total_drafts = $wpdb->get_var($sql);

            return $total_drafts ? $total_drafts : 0;
        }

        /**
         * Retrieve the total number of trashed posts.
         *
         * @return int The total number of trashed posts.
         */
        private function get_total_trashed_posts() {
            global $wpdb;

            // Prepare the SQL query to get the number of trashed posts
            $sql = "SELECT COUNT(*) 
                    FROM `" . $wpdb->posts . "` 
                    WHERE post_status = 'trash';";

            // Execute the query and get the count
            $total_trashed_posts = $wpdb->get_var($sql);

            return $total_trashed_posts ? $total_trashed_posts : 0;
        }



        /**
         * Retrieve the total number of spam comments.
         *
         * @return int The total number of spam comments.
         */
        private function get_total_spam_comments() {
            global $wpdb;

            // Prepare the SQL query to get the number of spam comments
            $sql = "SELECT COUNT(*) 
                    FROM " . $wpdb->comments . " 
                    WHERE comment_approved = 'spam';";

            // Execute the query and get the count
            $total_spam_comments = $wpdb->get_var($sql);

            return $total_spam_comments ? $total_spam_comments : 0;
        }

        /**
         * Retrieve the total number of trashed comments.
         *
         * @return int The total number of trashed comments.
         */
        private function get_total_trashed_comments() {
            global $wpdb;

            // Prepare the SQL query to get the number of trashed comments
            $sql = "SELECT COUNT(*) 
                    FROM " . $wpdb->comments . " 
                    WHERE comment_approved = 'trash';";

            // Execute the query and get the count
            $total_trashed_comments = $wpdb->get_var($sql);

            return $total_trashed_comments ? $total_trashed_comments : 0;
        }

        /**
         * Retrieve the total number of unapproved comments.
         *
         * @return int The total number of unapproved comments.
         */
        private function get_total_unapproved_comments() {
            global $wpdb;

            // Prepare the SQL query to get the number of unapproved comments
            $sql = "SELECT COUNT(*) 
                    FROM " . $wpdb->comments . " 
                    WHERE comment_approved = '0';";

            // Execute the query and get the count
            $total_unapproved_comments = $wpdb->get_var($sql);

            return $total_unapproved_comments ? $total_unapproved_comments : 0;
        }


        private function get_total_orphaned_postmeta() {
            global $wpdb;
        
            // Prepare the SQL query to count orphaned post meta entries
            $sql = "SELECT COUNT(*) 
                    FROM " . $wpdb->postmeta . " pm 
                    LEFT JOIN " . $wpdb->posts . " wp ON wp.ID = pm.post_id 
                    WHERE wp.ID IS NULL;";
        
            // Execute the query and get the count
            $total_orphaned_postmeta = $wpdb->get_var($sql);
        
            return $total_orphaned_postmeta ? $total_orphaned_postmeta : 0;
        }

        public function get_total_orphaned_comment_meta() {
            global $wpdb;
        
            // Prepare the SQL query to count orphaned comment meta entries
            $sql = "
                SELECT COUNT(*) 
                FROM " . $wpdb->commentmeta . " cm 
                LEFT JOIN " . $wpdb->comments . " c ON c.comment_ID = cm.comment_id 
                WHERE c.comment_ID IS NULL;
            ";
        
            // Execute the query and get the count
            $total_orphaned_comment_meta = $wpdb->get_var($sql);
        
            return $total_orphaned_comment_meta ? $total_orphaned_comment_meta : 0;
        }

        public function get_total_orphaned_relationship_data() {
            global $wpdb;
        
            // Prepare the SQL query to count orphaned relationship data entries
            $sql = "
                SELECT COUNT(*) 
                FROM " . $wpdb->prefix . "term_relationships 
                WHERE term_taxonomy_id = 1 
                  AND object_id NOT IN (SELECT ID FROM " . $wpdb->posts . ");
            ";
        
            // Execute the query and get the count
            $total_orphaned_relationship_data = $wpdb->get_var($sql);
        
            return $total_orphaned_relationship_data ? $total_orphaned_relationship_data : 0;
        }

        public function get_total_pingbacks() {
            global $wpdb;
        
            // Prepare the SQL query to count pingbacks
            $sql = "
                SELECT COUNT(*) 
                FROM " . $wpdb->comments . " 
                WHERE comment_type = 'pingback';
            ";
        
            // Execute the query and get the count
            $total_pingbacks = $wpdb->get_var($sql);
        
            return $total_pingbacks ? $total_pingbacks : 0;
        }
        
        public function get_total_trackbacks() {
            global $wpdb;
        
            // Prepare the SQL query to count trackbacks
            $sql = "
                SELECT COUNT(*) 
                FROM " . $wpdb->comments . " 
                WHERE comment_type = 'trackback';
            ";
        
            // Execute the query and get the count
            $total_trackbacks = $wpdb->get_var($sql);
        
            return $total_trackbacks ? $total_trackbacks : 0;
        }
        


        /**
         * Deletes all post revisions from the database via an AJAX request.
         *
         * This function is triggered by an AJAX request to delete all post revisions 
         * from the `wp_posts` table. It verifies the nonce for security, executes 
         * the SQL query to remove the revisions, and returns a JSON response with 
         * the number of deleted revisions or an error message.
         *
         * @return void
         */
        public function swiss_toolkit_delete_post_revisions(){
            // Verify nonce for security
            check_ajax_referer('swiss_toolkit_delete_post_revision_nonce', 'nonce');
        
            global $wpdb;
        
            // Prepare the SQL query to delete revisions
            $sql = "DELETE FROM `" . $wpdb->posts . "` WHERE post_type = 'revision';";
        
            // Execute the query
            $deleted_rows = $wpdb->query($sql);
        
            if ($deleted_rows !== false) {
                wp_send_json_success(array('message' => $deleted_rows . ' post revisions deleted.'));
            } else {
                wp_send_json_error(array('message' => 'Error deleting post revisions.'));
            }
        }

        /**
         * Deletes all draft posts from the database via an AJAX request.
         *
         * This function is triggered by an AJAX request to delete all draft posts 
         * from the `wp_posts` table. It verifies the nonce for security, executes 
         * the SQL query to remove the drafts, and returns a JSON response with 
         * the number of deleted drafts or an error message.
         *
         * @return void
         */
        public function swiss_toolkit_delete_post_draft(){
            // Verify nonce for security
            check_ajax_referer('swiss_toolkit_delete_post_draft_nonce', 'nonce');
        
            global $wpdb;
        
            // Prepare the SQL query to delete draft
            $sql = "DELETE FROM `" . $wpdb->posts . "` WHERE post_status = 'draft';";
        
            // Execute the query
            $deleted_rows = $wpdb->query($sql);
        
            if ($deleted_rows !== false) {
                wp_send_json_success(array('message' => $deleted_rows . ' post drafts deleted.'));
            } else {
                wp_send_json_error(array('message' => 'Error deleting post drafts.'));
            }
        }

        /**
         * Deletes all trashed posts from the database via an AJAX request.
         *
         * This function is triggered by an AJAX request to delete all trashed posts 
         * from the `wp_posts` table. It verifies the nonce for security, executes 
         * the SQL query to remove the trashed posts, and returns a JSON response with 
         * the number of deleted trashed posts or an error message.
         *
         * @return void
         */
        public function swiss_toolkit_delete_post_trash(){
            // Verify nonce for security
            check_ajax_referer('swiss_toolkit_delete_post_trash_nonce', 'nonce');
        
            global $wpdb;
        
            // Prepare the SQL query to delete trash
            $sql = "DELETE FROM `" . $wpdb->posts . "` WHERE post_status = 'trash';";
        
            // Execute the query
            $deleted_rows = $wpdb->query($sql);
        
            if ($deleted_rows !== false) {
                wp_send_json_success(array('message' => $deleted_rows . ' post trashs deleted.'));
            } else {
                wp_send_json_error(array('message' => 'Error deleting post trashs.'));
            }
        }

        public function swiss_toolkit_delete_spam_comments() {
            // Verify nonce for security
            check_ajax_referer('swiss_toolkit_delete_spam_comments_nonce', 'nonce');
        
            global $wpdb;
        
            // Prepare the SQL query to delete spam comments
            $sql = "DELETE FROM `" . $wpdb->comments . "` WHERE comment_approved = 'spam';";
        
            // Execute the query
            $deleted_rows = $wpdb->query($sql);
        
            if ($deleted_rows !== false) {
                wp_send_json_success(array('message' => $deleted_rows . ' spam comments deleted.'));
            } else {
                wp_send_json_error(array('message' => 'Error deleting spam comments.'));
            }
        }

        public function swiss_toolkit_delete_trashed_comments() {
            // Verify nonce for security
            check_ajax_referer('swiss_toolkit_delete_trashed_comments_nonce', 'nonce');
        
            global $wpdb;
        
            // Prepare the SQL query to delete trashed comments
            $sql = "DELETE FROM `" . $wpdb->comments . "` WHERE comment_approved = 'trash';";
        
            // Execute the query
            $deleted_rows = $wpdb->query($sql);
        
            if ($deleted_rows !== false) {
                wp_send_json_success(array('message' => $deleted_rows . ' trashed comments deleted.'));
            } else {
                wp_send_json_error(array('message' => 'Error deleting trashed comments.'));
            }
        }

        public function swiss_toolkit_delete_unapproved_comments() {
            // Verify nonce for security
            check_ajax_referer('swiss_toolkit_delete_unapproved_comments_nonce', 'nonce');
        
            global $wpdb;
        
            // Prepare the SQL query to delete unapproved comments
            $sql = "DELETE FROM `" . $wpdb->comments . "` WHERE comment_approved = '0';";
        
            // Execute the query
            $deleted_rows = $wpdb->query($sql);
        
            if ($deleted_rows !== false) {
                wp_send_json_success(array('message' => $deleted_rows . ' unapproved comments deleted.'));
            } else {
                wp_send_json_error(array('message' => 'Error deleting unapproved comments.'));
            }
        }

        public function get_total_orphaned_user_meta() {
            global $wpdb;
        
            // Prepare the SQL query to count orphaned user meta entries
            $sql = "
                SELECT COUNT(*) 
                FROM " . $wpdb->usermeta . " um 
                LEFT JOIN " . $wpdb->users . " u ON u.ID = um.user_id 
                WHERE u.ID IS NULL;
            ";
        
            // Execute the query and get the count
            $total_orphaned_user_meta = $wpdb->get_var($sql);
        
            return $total_orphaned_user_meta ? $total_orphaned_user_meta : 0;
        }        


        // Function to delete orphaned post meta data
        public function swiss_toolkit_delete_orphaned_postmeta() {
            // Verify nonce for security
            check_ajax_referer('swiss_toolkit_delete_orphaned_postmeta_nonce', 'nonce');
            
            global $wpdb;
            
            // Prepare the SQL query to delete orphaned post meta entries
            $sql = "
                DELETE pm 
                FROM " . $wpdb->postmeta . " pm 
                LEFT JOIN " . $wpdb->posts . " wp ON wp.ID = pm.post_id 
                WHERE wp.ID IS NULL;
            ";
            
            // Execute the query
            $deleted_rows = $wpdb->query($sql);
            
            if ($deleted_rows !== false) {
                wp_send_json_success(array('message' => $deleted_rows . ' orphaned post meta entries deleted.'));
            } else {
                wp_send_json_error(array('message' => 'Error deleting orphaned post meta entries.'));
            }
        }
        
        public function swiss_toolkit_delete_orphaned_user_meta() {
            // Verify nonce for security
            check_ajax_referer('swiss_toolkit_delete_orphaned_user_meta_nonce', 'nonce');
            
            global $wpdb;
            
            // Prepare the SQL query to delete orphaned user meta entries
            $sql = "
                DELETE um 
                FROM " . $wpdb->usermeta . " um 
                LEFT JOIN " . $wpdb->users . " u ON u.ID = um.user_id 
                WHERE u.ID IS NULL;
            ";
            
            // Execute the query
            $deleted_rows = $wpdb->query($sql);
            
            if ($deleted_rows !== false) {
                wp_send_json_success(array('message' => $deleted_rows . ' orphaned user meta entries deleted.'));
            } else {
                wp_send_json_error(array('message' => 'Error deleting orphaned user meta entries.'));
            }
        }
        
        public function swiss_toolkit_delete_orphaned_comment_meta() {
            // Verify nonce for security
            check_ajax_referer('swiss_toolkit_delete_orphaned_comment_meta_nonce', 'nonce');
            
            global $wpdb;
            
            // Prepare the SQL query to delete orphaned comment meta entries
            $sql = "
                DELETE cm 
                FROM " . $wpdb->commentmeta . " cm 
                LEFT JOIN " . $wpdb->comments . " c ON c.comment_ID = cm.comment_id 
                WHERE c.comment_ID IS NULL;
            ";
            
            // Execute the query
            $deleted_rows = $wpdb->query($sql);
            
            if ($deleted_rows !== false) {
                wp_send_json_success(array('message' => $deleted_rows . ' orphaned comment meta entries deleted.'));
            } else {
                wp_send_json_error(array('message' => 'Error deleting orphaned comment meta entries.'));
            }
        }

        public function swiss_toolkit_delete_orphaned_relationship_data() {
            // Verify nonce for security
            check_ajax_referer('swiss_toolkit_delete_orphaned_relationship_data_nonce', 'nonce');
            
            global $wpdb;
            
            // Prepare the SQL query to delete orphaned relationship data
            $sql = "
                DELETE FROM " . $wpdb->prefix . "term_relationships 
                WHERE term_taxonomy_id = 1 
                  AND object_id NOT IN (SELECT ID FROM " . $wpdb->posts . ");
            ";
            
            // Execute the query
            $deleted_rows = $wpdb->query($sql);
            
            if ($deleted_rows !== false) {
                wp_send_json_success(array('message' => $deleted_rows . ' orphaned relationship data entries deleted.'));
            } else {
                wp_send_json_error(array('message' => 'Error deleting orphaned relationship data entries.'));
            }
        }
        
        public function swiss_toolkit_delete_pingbacks() {
            // Verify nonce for security
            check_ajax_referer('swiss_toolkit_delete_pingbacks_nonce', 'nonce');
            
            global $wpdb;
            
            // Prepare the SQL query to delete pingbacks
            $sql = "
                DELETE FROM " . $wpdb->comments . " 
                WHERE comment_type = 'pingback';
            ";
            
            // Execute the query
            $deleted_rows = $wpdb->query($sql);
            
            if ($deleted_rows !== false) {
                wp_send_json_success(array('message' => $deleted_rows . ' pingbacks deleted.'));
            } else {
                wp_send_json_error(array('message' => 'Error deleting pingbacks.'));
            }
        }
        
        public function swiss_toolkit_delete_trackbacks() {
            // Verify nonce for security
            check_ajax_referer('swiss_toolkit_delete_trackbacks_nonce', 'nonce');
            
            global $wpdb;
            
            // Prepare the SQL query to delete trackbacks
            $sql = "
                DELETE FROM " . $wpdb->comments . " 
                WHERE comment_type = 'trackback';
            ";
            
            // Execute the query
            $deleted_rows = $wpdb->query($sql);
            
            if ($deleted_rows !== false) {
                wp_send_json_success(array('message' => $deleted_rows . ' trackbacks deleted.'));
            } else {
                wp_send_json_error(array('message' => 'Error deleting trackbacks.'));
            }
        }        
        
        


        /**
         * Display the "Optimization" message in the admin page.
         */
        public function swiss_toolkit_optimizations_callback(){
            ?>
            <div class="wrap">
                <h2><?php echo esc_html( __( 'Database Optimizations', 'swiss-toolkit-for-wp' ) ); ?></h2>
                <table id="optimizations_list" class="wp-list-table widefat striped">
                    <thead>
                        <tr>
                            <td class="check-column">
                                <input id="select_all_optimizations" type="checkbox">
                            </td>
                            <th><?php esc_html_e( 'Optimization', 'swiss-toolkit-for-wp' ); ?></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Optimization for cleaning post revisions -->
                        <tr class="swiss-toolkit-optimize-settings" 
                            id="swiss-toolkit-optimize-settings-clean-revisions" 
                            data-optimization_id="revisions" 
                            data-optimization_run_sort_order="1000">
                            
                            <th class="swiss-toolkit-optimize-settings-optimization-checkbox check-column">
                                <input name="clean-revisions" 
                                    id="optimization_checkbox_revisions" 
                                    class="optimization_checkbox" 
                                    type="checkbox" 
                                    value="true" 
                                    checked="checked">
                            </th>

                            <td>
                                <label for="optimization_checkbox_revisions"><?php esc_html_e( 'Clean all post revisions', 'swiss-toolkit-for-wp' ); ?></label>
                                <div class="swiss-toolkit-optimize-settings-optimization-info" id="optimization_info_revisions">
                                    <?php echo esc_html( $this->get_total_revisions() ) . esc_html__( ' post revisions in your database', 'swiss-toolkit-for-wp' ); ?>
                                </div>
                            </td>

                            <td class="swiss-toolkit-optimize-settings-optimization-run">
                                <div class="loading-spinner"></div>
                                <button id="swiss_toolkit_post_revisions_btn" 
                                        class="button button-secondary swiss-toolkit-optimize-settings-optimization-run-button show_on_default_sizes optimization_button_revisions" 
                                        type="button"><?php esc_html_e( 'Run optimization', 'swiss-toolkit-for-wp' ); ?>
                                </button>
                            </td>
                        </tr>

                        <!-- Optimization for cleaning draft posts -->
                        <tr class="swiss-toolkit-optimize-settings" 
                            id="swiss-toolkit-optimize-settings-clean-auto-drafts" 
                            data-optimization_id="auto-drafts" 
                            data-optimization_run_sort_order="1001">
                            
                            <th class="swiss-toolkit-optimize-settings-optimization-checkbox check-column">
                                <input name="clean-auto-drafts" 
                                    id="optimization_checkbox_auto_drafts" 
                                    class="optimization_checkbox" 
                                    type="checkbox" 
                                    value="true" 
                                    checked="checked">
                            </th>

                            <td>
                                <label for="optimization_checkbox_auto_drafts"><?php esc_html_e( 'Clean all draft posts', 'swiss-toolkit-for-wp' ); ?></label>
                                <div class="swiss-toolkit-optimize-settings-optimization-info" id="optimization_info_auto_drafts">
                                    <?php echo esc_html( $this->get_total_drafts() ) . esc_html__( ' draft posts in your database', 'swiss-toolkit-for-wp' ); ?>
                                </div>
                            </td>

                            <td class="swiss-toolkit-optimize-settings-optimization-run">
                                <div class="loading-spinner"></div>
                                <button id="swiss_toolkit_auto_drafts_btn" 
                                        class="button button-secondary swiss-toolkit-optimize-settings-optimization-run-button show_on_default_sizes optimization_button_auto_drafts" 
                                        type="button"><?php esc_html_e( 'Run optimization', 'swiss-toolkit-for-wp' ); ?>
                                </button>
                            </td>
                        </tr>

                        <!-- Optimization for cleaning trashed posts -->
                        <tr class="swiss-toolkit-optimize-settings" 
                            id="swiss-toolkit-optimize-settings-clean-trashed-posts" 
                            data-optimization_id="trashed-posts" 
                            data-optimization_run_sort_order="1002">
                            
                            <th class="swiss-toolkit-optimize-settings-optimization-checkbox check-column">
                                <input name="clean-trashed-posts" 
                                    id="optimization_checkbox_trashed_posts" 
                                    class="optimization_checkbox" 
                                    type="checkbox" 
                                    value="true" 
                                    checked="checked">
                            </th>

                            <td>
                                <label for="optimization_checkbox_trashed_posts"><?php esc_html_e( 'Clean all trashed posts', 'swiss-toolkit-for-wp' ); ?></label>
                                <div class="swiss-toolkit-optimize-settings-optimization-info" id="optimization_info_trashed_posts">
                                    <?php echo esc_html( $this->get_total_trashed_posts() ) . esc_html__( ' trashed posts in your database', 'swiss-toolkit-for-wp' ); ?>
                                </div>
                            </td>

                            <td class="swiss-toolkit-optimize-settings-optimization-run">
                                <div class="loading-spinner"></div>
                                <button id="swiss_toolkit_trashed_posts_btn" 
                                        class="button button-secondary swiss-toolkit-optimize-settings-optimization-run-button show_on_default_sizes optimization_button_trashed_posts" 
                                        type="button"><?php esc_html_e( 'Run optimization', 'swiss-toolkit-for-wp' ); ?>
                                </button>
                            </td>
                        </tr>

                        <!-- Optimization for cleaning spam comments -->
                        <tr class="swiss-toolkit-optimize-settings" 
                            id="swiss-toolkit-optimize-settings-clean-spam-comments" 
                            data-optimization_id="spam-comments" 
                            data-optimization_run_sort_order="1003">

                            <th class="swiss-toolkit-optimize-settings-optimization-checkbox check-column">
                                <input name="clean-spam-comments" 
                                    id="optimization_checkbox_spam_comments" 
                                    class="optimization_checkbox" 
                                    type="checkbox" 
                                    value="true" 
                                    checked="checked">
                            </th>

                            <td>
                                <label for="optimization_checkbox_spam_comments"><?php esc_html_e( 'Clean all spam comments', 'swiss-toolkit-for-wp' ); ?></label>
                                <div class="swiss-toolkit-optimize-settings-optimization-info" id="optimization_info_spam_comments">
                                    <?php echo esc_html( $this->get_total_spam_comments() ) . esc_html__( ' spam comments in your database', 'swiss-toolkit-for-wp' ); ?>
                                </div>
                            </td>

                            <td class="swiss-toolkit-optimize-settings-optimization-run">
                                <div class="loading-spinner"></div>
                                <button id="swiss_toolkit_spam_comments_btn" 
                                        class="button button-secondary swiss-toolkit-optimize-settings-optimization-run-button show_on_default_sizes optimization_button_spam_comments" 
                                        type="button"><?php esc_html_e( 'Run optimization', 'swiss-toolkit-for-wp' ); ?>
                                </button>
                            </td>
                        </tr>

                        <!-- Optimization for cleaning trashed comments -->
                        <tr class="swiss-toolkit-optimize-settings" 
                            id="swiss-toolkit-optimize-settings-clean-trashed-comments" 
                            data-optimization_id="trashed-comments" 
                            data-optimization_run_sort_order="1004">

                            <th class="swiss-toolkit-optimize-settings-optimization-checkbox check-column">
                                <input name="clean-trashed-comments" 
                                    id="optimization_checkbox_trashed_comments" 
                                    class="optimization_checkbox" 
                                    type="checkbox" 
                                    value="true" 
                                    checked="checked">
                            </th>

                            <td>
                                <label for="optimization_checkbox_trashed_comments"><?php esc_html_e( 'Clean all trashed comments', 'swiss-toolkit-for-wp' ); ?></label>
                                <div class="swiss-toolkit-optimize-settings-optimization-info" id="optimization_info_trashed_comments">
                                    <?php echo esc_html( $this->get_total_trashed_comments() ) . esc_html__( ' trashed comments in your database', 'swiss-toolkit-for-wp' ); ?>
                                </div>
                            </td>

                            <td class="swiss-toolkit-optimize-settings-optimization-run">
                                <div class="loading-spinner"></div>
                                <button id="swiss_toolkit_trashed_comments_btn" 
                                        class="button button-secondary swiss-toolkit-optimize-settings-optimization-run-button show_on_default_sizes optimization_button_trashed_comments" 
                                        type="button"><?php esc_html_e( 'Run optimization', 'swiss-toolkit-for-wp' ); ?>
                                </button>
                            </td>
                        </tr>

                        <!-- Optimization for cleaning unapproved comments -->
                        <tr class="swiss-toolkit-optimize-settings" 
                            id="swiss-toolkit-optimize-settings-clean-unapproved-comments" 
                            data-optimization_id="unapproved-comments" 
                            data-optimization_run_sort_order="1005">

                            <th class="swiss-toolkit-optimize-settings-optimization-checkbox check-column">
                                <input name="clean-unapproved-comments" 
                                    id="optimization_checkbox_unapproved_comments" 
                                    class="optimization_checkbox" 
                                    type="checkbox" 
                                    value="true" 
                                    checked="checked">
                            </th>

                            <td>
                                <label for="optimization_checkbox_unapproved_comments"><?php esc_html_e( 'Clean all unapproved comments', 'swiss-toolkit-for-wp' ); ?></label>
                                <div class="swiss-toolkit-optimize-settings-optimization-info" id="optimization_info_unapproved_comments">
                                    <?php echo esc_html( $this->get_total_unapproved_comments() ) . esc_html__( ' unapproved comments in your database', 'swiss-toolkit-for-wp' ); ?>
                                </div>
                            </td>

                            <td class="swiss-toolkit-optimize-settings-optimization-run">
                                <div class="loading-spinner"></div>
                                <button id="swiss_toolkit_unapproved_comments_btn" 
                                        class="button button-secondary swiss-toolkit-optimize-settings-optimization-run-button show_on_default_sizes optimization_button_unapproved_comments" 
                                        type="button"><?php esc_html_e( 'Run optimization', 'swiss-toolkit-for-wp' ); ?>
                                </button>
                            </td>
                        </tr>

                        <!-- Optimization for cleaning pingbacks -->
                        <tr class="swiss-toolkit-optimize-settings" 
                            id="swiss-toolkit-optimize-settings-clean-pingbacks" 
                            data-optimization_id="pingbacks" 
                            data-optimization_run_sort_order="1020">

                            <th class="swiss-toolkit-optimize-settings-optimization-checkbox check-column">
                                <input name="clean-pingbacks" 
                                    id="optimization_checkbox_pingbacks" 
                                    class="optimization_checkbox" 
                                    type="checkbox" 
                                    value="true" 
                                    checked="checked">
                            </th>

                            <td>
                                <label for="optimization_checkbox_pingbacks"><?php esc_html_e( 'Clean all pingbacks', 'swiss-toolkit-for-wp' ); ?></label>
                                <div class="swiss-toolkit-optimize-settings-optimization-info" id="optimization_info_pingbacks">
                                    <?php echo esc_html( $this->get_total_pingbacks() ) . esc_html__( ' pingbacks in your database', 'swiss-toolkit-for-wp' ); ?>
                                </div>
                            </td>

                            <td class="swiss-toolkit-optimize-settings-optimization-run">
                                <div class="loading-spinner"></div>
                                <button id="swiss_toolkit_delete_pingbacks_btn" 
                                        class="button button-secondary swiss-toolkit-optimize-settings-optimization-run-button show_on_default_sizes optimization_button_pingbacks" 
                                        type="button"><?php esc_html_e( 'Run optimization', 'swiss-toolkit-for-wp' ); ?>
                                </button>
                            </td>
                        </tr>

                        <!-- Optimization for cleaning trackbacks -->
                        <tr class="swiss-toolkit-optimize-settings" 
                            id="swiss-toolkit-optimize-settings-clean-trackbacks" 
                            data-optimization_id="trackbacks" 
                            data-optimization_run_sort_order="1030">

                            <th class="swiss-toolkit-optimize-settings-optimization-checkbox check-column">
                                <input name="clean-trackbacks" 
                                    id="optimization_checkbox_trackbacks" 
                                    class="optimization_checkbox" 
                                    type="checkbox" 
                                    value="true" 
                                    checked="checked">
                            </th>

                            <td>
                                <label for="optimization_checkbox_trackbacks"><?php esc_html_e( 'Clean all trackbacks', 'swiss-toolkit-for-wp' ); ?></label>
                                <div class="swiss-toolkit-optimize-settings-optimization-info" id="optimization_info_trackbacks">
                                    <?php echo esc_html( $this->get_total_trackbacks() ) . esc_html__( ' trackbacks in your database', 'swiss-toolkit-for-wp' ); ?>
                                </div>
                            </td>

                            <td class="swiss-toolkit-optimize-settings-optimization-run">
                                <div class="loading-spinner"></div>
                                <button id="swiss_toolkit_delete_trackbacks_btn" 
                                        class="button button-secondary swiss-toolkit-optimize-settings-optimization-run-button show_on_default_sizes optimization_button_trackbacks" 
                                        type="button"><?php esc_html_e( 'Run optimization', 'swiss-toolkit-for-wp' ); ?>
                                </button>
                            </td>
                        </tr>


                        <!-- Optimization for cleaning orphaned post meta -->
                        <tr class="swiss-toolkit-optimize-settings" 
                            id="swiss-toolkit-optimize-settings-clean-orphaned-postmeta" 
                            data-optimization_id="orphaned-postmeta" 
                            data-optimization_run_sort_order="1006">

                            <th class="swiss-toolkit-optimize-settings-optimization-checkbox check-column">
                                <input name="clean-orphaned-postmeta" 
                                    id="optimization_checkbox_orphaned_postmeta" 
                                    class="optimization_checkbox" 
                                    type="checkbox" 
                                    value="true" 
                                    checked="checked">
                            </th>

                            <td>
                                <label for="optimization_checkbox_orphaned_postmeta"><?php esc_html_e( 'Clean all orphaned post meta data', 'swiss-toolkit-for-wp' ); ?></label>
                                <div class="swiss-toolkit-optimize-settings-optimization-info" id="optimization_info_orphaned_postmeta">
                                    <?php echo esc_html( $this->get_total_orphaned_postmeta() ) . esc_html__( ' orphaned post meta entries in your database', 'swiss-toolkit-for-wp' ); ?>
                                </div>
                            </td>

                            <td class="swiss-toolkit-optimize-settings-optimization-run">
                                <div class="loading-spinner"></div>
                                <button id="swiss_toolkit_orphaned_postmeta_btn" 
                                        class="button button-secondary swiss-toolkit-optimize-settings-optimization-run-button show_on_default_sizes optimization_button_orphaned_postmeta" 
                                        type="button"><?php esc_html_e( 'Run optimization', 'swiss-toolkit-for-wp' ); ?>
                                </button>
                            </td>
                        </tr>

                        <!-- Optimization for cleaning orphaned user meta data -->
                        <tr class="swiss-toolkit-optimize-settings" 
                            id="swiss-toolkit-optimize-settings-clean-orphaned-user-meta" 
                            data-optimization_id="orphaned-user-meta" 
                            data-optimization_run_sort_order="1007">

                            <th class="swiss-toolkit-optimize-settings-optimization-checkbox check-column">
                                <input name="clean-orphaned-user-meta" 
                                    id="optimization_checkbox_orphaned_user_meta" 
                                    class="optimization_checkbox" 
                                    type="checkbox" 
                                    value="true" 
                                    checked="checked">
                            </th>

                            <td>
                                <label for="optimization_checkbox_orphaned_user_meta"><?php esc_html_e( 'Clean all orphaned user meta data', 'swiss-toolkit-for-wp' ); ?></label>
                                <div class="swiss-toolkit-optimize-settings-optimization-info" id="optimization_info_orphaned_user_meta">
                                    <?php echo esc_html( $this->get_total_orphaned_user_meta() ) . esc_html__( ' orphaned user meta entries in your database', 'swiss-toolkit-for-wp' ); ?>
                                </div>
                            </td>

                            <td class="swiss-toolkit-optimize-settings-optimization-run">
                                <div class="loading-spinner"></div>
                                <button id="swiss_toolkit_orphaned_user_meta_btn" 
                                        class="button button-secondary swiss-toolkit-optimize-settings-optimization-run-button show_on_default_sizes optimization_button_orphaned_user_meta" 
                                        type="button"><?php esc_html_e( 'Run optimization', 'swiss-toolkit-for-wp' ); ?>
                                </button>
                            </td>
                        </tr>

                        <!-- Optimization for cleaning orphaned comment meta data -->
                        <tr class="swiss-toolkit-optimize-settings" 
                            id="swiss-toolkit-optimize-settings-clean-orphaned-comment-meta" 
                            data-optimization_id="orphaned-comment-meta" 
                            data-optimization_run_sort_order="1008">

                            <th class="swiss-toolkit-optimize-settings-optimization-checkbox check-column">
                                <input name="clean-orphaned-comment-meta" 
                                    id="optimization_checkbox_orphaned_comment_meta" 
                                    class="optimization_checkbox" 
                                    type="checkbox" 
                                    value="true" 
                                    checked="checked">
                            </th>

                            <td>
                                <label for="optimization_checkbox_orphaned_comment_meta"><?php esc_html_e( 'Clean all orphaned comment meta data', 'swiss-toolkit-for-wp' ); ?></label>
                                <div class="swiss-toolkit-optimize-settings-optimization-info" id="optimization_info_orphaned_comment_meta">
                                    <?php echo esc_html( $this->get_total_orphaned_comment_meta() ) . esc_html__( ' orphaned comment meta entries in your database', 'swiss-toolkit-for-wp' ); ?>
                                </div>
                            </td>

                            <td class="swiss-toolkit-optimize-settings-optimization-run">
                                <div class="loading-spinner"></div>
                                <button id="swiss_toolkit_orphaned_comment_meta_btn" 
                                        class="button button-secondary swiss-toolkit-optimize-settings-optimization-run-button show_on_default_sizes optimization_button_orphaned_comment_meta" 
                                        type="button"><?php esc_html_e( 'Run optimization', 'swiss-toolkit-for-wp' ); ?>
                                </button>
                            </td>
                        </tr>

                        <!-- Optimization for cleaning orphaned relationship data -->
                        <tr class="swiss-toolkit-optimize-settings" 
                            id="swiss-toolkit-optimize-settings-clean-orphaned-relationship-data" 
                            data-optimization_id="orphaned-relationship-data" 
                            data-optimization_run_sort_order="1010">

                            <th class="swiss-toolkit-optimize-settings-optimization-checkbox check-column">
                                <input name="clean-orphaned-relationship-data" 
                                    id="optimization_checkbox_orphaned_relationship_data" 
                                    class="optimization_checkbox" 
                                    type="checkbox" 
                                    value="true" 
                                    checked="checked">
                            </th>

                            <td>
                                <label for="optimization_checkbox_orphaned_relationship_data"><?php esc_html_e( 'Clean all orphaned relationship data', 'swiss-toolkit-for-wp' ); ?></label>
                                <div class="swiss-toolkit-optimize-settings-optimization-info" id="optimization_info_orphaned_relationship_data">
                                    <?php echo esc_html( $this->get_total_orphaned_relationship_data() ) . esc_html__( ' orphaned relationship data entries in your database', 'swiss-toolkit-for-wp' ); ?>
                                </div>
                            </td>

                            <td class="swiss-toolkit-optimize-settings-optimization-run">
                                <div class="loading-spinner"></div>
                                <button id="swiss_toolkit_orphaned_relationship_data_btn" 
                                        class="button button-secondary swiss-toolkit-optimize-settings-optimization-run-button show_on_default_sizes optimization_button_orphaned_relationship_data" 
                                        type="button"><?php esc_html_e( 'Run optimization', 'swiss-toolkit-for-wp' ); ?>
                                </button>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>
            <?php
        }
    }

    // Initialize the BDSTFW_Swiss_Toolkit_Optimizations class
    BDSTFW_Swiss_Toolkit_Optimizations::get_instance();
}
