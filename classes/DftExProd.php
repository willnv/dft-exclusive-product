<?php

if ( class_exists( 'DftExProd' ) || class_exists( 'DftExProd_Front' ) )
    return;


include_once( 'DftExProd_Front.php' );

class DftExProd extends DftExProd_Front {
    
    private $select_id;
    private $path;

    public function __construct() {
        $this->path = trailingslashit( plugin_dir_url(__FILE__) );
        $this->select_id = "campo-exclusivo";
        $this->spawn();
    }

    /**
     * Several hooks and enqueues going on here.
     */
    public function spawn() {

        add_action( 'woocommerce_product_options_general_product_data', array( $this, 'exclusive_user_product' ) );
        add_action( 'woocommerce_process_product_meta', array( $this, 'save_custom_user_product' ) );
        add_action( 'init', array( $this, 'dft_add_term_personalizado' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'dft_load_scripts' ) );

        # DftExProd_Front methods
        add_action( 'pre_get_posts', array( $this, 'dft_hide_product' ) );
        add_filter( 'woof_products_query', array( $this, 'dft_woof_ajax_support' ) );
    }

    /**
     * Enqueue required scripts, including select2.
     */
    public function dft_load_scripts() {
        wp_enqueue_script( 'dft_select2_js', $this->path . '../assets/js/select2.min.js', array( 'jquery' ) );
        wp_enqueue_script( 'dft_main_js', $this->path . '../assets/js/dft_exprod.js', array( 'jquery' ) );
    }

    /**
     * Shows the select field for custom user choosing
     */
    public function exclusive_user_product() {
        global $post;
        
        $value = get_post_meta( $post->ID, $this->select_id, true );

        $users = get_users();
     
        $arr_select_options[''] = 'Selecione um usuário';
        
        foreach ( $users as $key => $user ):
            $arr_select_options[$user->data->user_login] =  $user->data->user_login;
        endforeach;
        
        $args = array(
            'id'      => $this->select_id,
            'label'   => 'Personalizado para:',
            'options' => $arr_select_options,
            'value'   => $value
        );
        
        woocommerce_wp_select( $args );
    }


    /**
     * Saves the product's custom user meta.
     */
    public function save_custom_user_product( $post_id ) {
        
        if ( !(isset( $_POST['woocommerce_meta_nonce'], $_POST[$this->select_id] ) || wp_verify_nonce( sanitize_key( $_POST['woocommerce_meta_nonce'] ), 'woocommerce_save_data' ) ) ) {
            return false;
        }
        
        $exclusive_user = $_POST[$this->select_id];
        
        update_post_meta(
            $post_id,
            $this->select_id,
            esc_attr($exclusive_user)
        ); 
    }

    /**
     * Inserts term in product_cat, required for filtering purposes.
     */
    public function dft_add_term_personalizado() {

        if ( post_type_exists( 'product' ) && taxonomy_exists( 'product_cat' ) ) {
            wp_insert_term( 'Personalizado', 'product_cat', array('slug' => 'dft_personalizado'));
        }
    }

} // class