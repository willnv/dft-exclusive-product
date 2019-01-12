<?php

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPP_Main' ) ):

    /**
     * Woocommerce Private Product
     * Main class
     * 
     * @author Willon Nava <willonnava@gmail.com>
     */
    class WPP_Main {

        /**
         * ID selector for the dropdown menu
         * on admin's panel.
         *
         * @var string $select_id
         */
        private $select_id;

        /**
         * Constructor
         */
        public function __construct() {
            $this->select_id = "restrict-user-list";
            $this->spawn();
        }

        /**
         * Initialize and hook proper callbacks
         */
        public function spawn() {
            add_action( 'pre_get_posts', array( $this, 'wpp_hide_product' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'load_assets' ) );
            add_action( 'woocommerce_product_options_general_product_data', array( $this, 'output_user_dropdown' ) );
            add_action( 'woocommerce_process_product_meta', array( $this, 'save_private_users' ) );
            add_action( 'plugins_loaded', array( $this, 'wpp_i18n' ) );
        }

        /**
         * Returns the user's name to print
         * in the dropdown list.
         *
         * @return string $user_name
         */
        protected function wpp_get_current_user() {
            $user = wp_get_current_user();
            $user_name = $user->data->user_login;

            return $user_name;
        }

        /**
         * Hide user-restricted products on the main
         * Woocommerce product loop
         * 
         * @param WP_Query $query
         */
        public function wpp_hide_product( WP_Query $query ) {

            # Do not run on admin
            if ( !is_admin() && @$query->query['post_type'] === 'product' ):

                $meta_query = array(array(
                    'key'     => $this->select_id,
                    'value'   => json_encode( strval( get_current_user_id() ) ),
                    'compare' => 'LIKE'
                ));

                $query->set( 'meta_query', $meta_query );
            endif;
        }

        /**
         * Loads the plugin's text domain for i18n
         */
        public function wpp_i18n() {
            $path = basename( dirname( __DIR__ ) ) . '/languages';

            load_plugin_textdomain( 'woo-private-product', false, $path );
        }

        /**
         * Enqueue required scripts, including select2.
         */
        public function load_assets() {
            global $typenow;

            # Only call scripts on product page.
            if ( !( is_admin() && $typenow === 'product' ) )
                return;

            # Main script/style files
            wp_enqueue_script( 'wpp-main-js', ASSETS_PATH . 'js/min/wpp.min.js', array( 'jquery' ), false, true );
            wp_enqueue_style( 'wpp-main-css', ASSETS_PATH . 'css/wpp.css' );

            # Slim Select
            wp_enqueue_script( 'slim-select-js', 'https://cdnjs.cloudflare.com/ajax/libs/slim-select/1.18.7/slimselect.min.js' );
            wp_enqueue_style( 'slim-select-css', 'https://cdnjs.cloudflare.com/ajax/libs/slim-select/1.18.7/slimselect.min.css' );
        }

        /**
         * Shows the select field for custom user choosing
         */
        public function output_user_dropdown() {
            global $post;

            $meta_value = get_post_meta( $post->ID, $this->select_id, true );
            $values = json_decode( $meta_value, true );

            $users = get_users(); ?>
            
            <div class="options-group private-products">
                <p><span class="dashicons dashicons-lock"></span><?php _e( 'Set the visibility of this product to specified users.', 'woo-private-product' ); ?></p>
                <p class="form-field">
                    <label for="restrict-user-list"><?php _e( 'Private to: ', 'woo-private-product' ); ?></label>
                    <select data-searchplaceholder="<?php _e( 'Search', 'woo-private-product' ); ?>" data-searchtext="<?php _e( 'No results', 'woo-private-product' ); ?>" class="short" name="restrict-user-list[]" id="restrict-user-list" multiple>
                        <option data-placeholder="true"><?php _e( 'Choose users...', 'woo-private-product' ); ?></option>
                        <?php foreach ( $users as $key => $user ):
                            $user_name = $user->data->display_name; 
                            $selected = in_array( $user->ID, $values ) ? 'selected="selected"' : ''; ?>

                            <option <?= $selected; ?> value="<?= $user->ID; ?>"><?= $user_name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </p>
            </div>

            <?php
        }

        /**
         * Saves the product's meta.
         * 
         * @param int $post_id
         */
        public function save_private_users( int $post_id ) {

            # Always store as array
            $data = (array) $_POST[$this->select_id];
            
            if ( !( isset( $_POST['woocommerce_meta_nonce'], $data ) || wp_verify_nonce( sanitize_key( $_POST['woocommerce_meta_nonce'] ), 'woocommerce_save_data' ) ) ) {
                return false;
            }
            
            $private_user = json_encode( $data );
            
            update_post_meta(
                $post_id,
                $this->select_id,
                $private_user
            ); 
        }
    }
endif;