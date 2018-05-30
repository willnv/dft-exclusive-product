<?php

class DftExProd_Front {

    public function dft_get_current_user() {
        $user = wp_get_current_user();
        $user = $user->data->user_login;

        return $user;
    }

    /**
     * Hide user-specified products on shop page / product sidebars.
     */
    public function dft_hide_product( $q ) {
        global $post;

        // Do not apply on admin
        if ( $q->query['post_type'] === 'product' && ! is_admin() ):

            $meta_query = $q->get( 'meta_query' );

            $meta_query = array(
                'relation' => 'OR',
                array(
                    'key'   => 'campo-exclusivo',
                    'value'   => $this->dft_get_current_user(),
                    'compare' => 'IN'
                ),
                array(
                    'key'   => 'campo-exclusivo',
                    'compare' => 'NOT EXISTS'
                )
            );

            $q->set( 'meta_query', $meta_query );

        endif;
    }

    /** 
     * Required for working with Woof's AJAX filtering.
    */
    public function dft_woof_ajax_support( $data ) {

        $args = array(
            'relation' => 'OR',
            array(
                'key'   => 'campo-exclusivo',
                'value'   => $this->dft_get_current_user(),
                'compare' => 'IN'
            ),
            array(
                'key'   => 'campo-exclusivo',
                'compare' => 'NOT EXISTS'
            ) 
        );

        $data['meta_query'] = $args;

        return $data;
    }

} #class