<?php
/*
Plugin Name: Custom Reviews Woocommerce
Plugin URI: http://wordpress.org/plugins/custom-reviews-and-ratings-for-woocommerce/
Description: You can add custom reviews and ratings to your woocommerce products to attract more customers.
Author: Modal Web
Version: 1.0.0
Author URI: https://profiles.wordpress.org/modalweb
*/
if(!class_exists('mw_custom_reviews_woocommerce')) {
    class mw_custom_reviews_woocommerce {
        /*
        * Auto load hooks
        * Constructor
        */
        public function __construct()
        {
            register_activation_hook(__FILE__, array(&$this, 'mw_custom_reviews_woocommerce_install'));
            add_action('admin_menu', array($this,'register_custom_reviews_woocommerce_submenu_page'));
            add_action("add_meta_boxes", array(&$this, "mw_custom_reviews_woocommerce_meta_box"));
            add_action( 'wp_ajax_mw_save_ratings_ratings', array(&$this,'mw_save_ratings_ratings'));
        }
        /*
        * Activate Install
        */
        public function mw_custom_reviews_woocommerce_install() {
            $settings = get_option('mw_custom_ratings_reviews_woocommerce_options');
            $options = array(
                              'mw_enable_custom_review_wc' => '1',												 
                              );
             if(!$settings['mw_enable_custom_review_wc']) {
                 update_option('mw_custom_ratings_reviews_woocommerce_options', $options);
             }         
        }
        /*
        * Register Meta Box for Reviews
        */
        public function mw_custom_reviews_woocommerce_meta_box() {
            $settings = get_option('mw_custom_ratings_reviews_woocommerce_options');
            if(isset($settings['mw_enable_custom_review_wc']) && $settings['mw_enable_custom_review_wc'] == '1') {
              add_meta_box("mw-crw_meta_box", __("Custom Reviews", 'custom-ratings-reviews-woocommerce'), array(&$this, "mw_custom_reviews_woocommerce_meta_box_return"),'product', "advanced", "high", null);
            }
        }
        /*
        * Callback Meta Box for Reviews
        */
        public function mw_custom_reviews_woocommerce_meta_box_return($object) {
         if(is_admin() && current_user_can('manage_options')) {
             include('admin/reviewform.php');
         }
        }
        /*
        * Register Sub Menu
        */
        public function register_custom_reviews_woocommerce_submenu_page() {
            add_submenu_page( 'woocommerce', __( 'Custom Reviews', 'custom-ratings-reviews-woocommerce' ),  __( 'Custom Reviews', 'custom-ratings-reviews-woocommerce' ) , 'manage_woocommerce', 'custom-reviews-woocommerce', array( $this, 'register_custom_reviews_woocommerce_submenu_page_callback' ) );
        }
        /*
        * Register Sub Menu Callback
        */
        public function register_custom_reviews_woocommerce_submenu_page_callback() {
            if(is_admin() && current_user_can('manage_options')) {
                include('admin/custom_reviews.php');
            }
        }
        /*
        * Ajax Callback to save reviews
        */
        public function mw_save_ratings_ratings(){
             $mw_nonce = $_POST['mw_nonce'];
            if ( !wp_verify_nonce( $mw_nonce, 'mw_nonce' ) ) {
                die( 'Security check' ); 
            } else {
                $comment_ratings = array();
                $mw_uid = intval($_POST['mw_uid']);
                $mw_wc_rating = intval($_POST['mw_wc_rating']);
                $mw_wc_review = sanitize_text_field($_POST['mw_wc_review']);
                $mw_wc_review_date = $_POST['mw_wc_review_date'];
                $mw_product_id = intval($_POST['mw_product_id']);
                $user_info = get_userdata($mw_uid);
                $data = array(
                    'comment_post_ID' => $mw_product_id,
                    'comment_author' =>  $user_info->display_name,
                    'comment_author_email' => $user_info->user_email,
                    'comment_author_url' => site_url(),
                    'comment_content' => $mw_wc_review,
                    'comment_type' => '',
                    'comment_parent' => 0,
                    'user_id' => $user_info->ID,
                    'comment_author_IP' => $this->getIP(),
                    'comment_agent' => $_SERVER['HTTP_USER_AGENT'],
                    'comment_date' => $mw_wc_review_date,
                    'comment_date_gmt' => $mw_wc_review_date,
                    'comment_approved' => 1,
                );            
               $comment_ID = wp_insert_comment($data);
               if($comment_ID) {
                update_comment_meta( $comment_ID, 'rating', $mw_wc_rating);
                $comments = get_comments(array('post_id' => $mw_product_id));
                if($comments) {
                    foreach($comments as $comment) :
                        $comment_ratings[] =  get_comment_meta( $comment->comment_ID, 'rating', true );
                    endforeach;    
                }
                $vals = array_count_values($comment_ratings);
                update_post_meta($mw_product_id, '_wc_rating_count', $vals );
                $_wc_rating_count = get_post_meta($mw_product_id,'_wc_rating_count',true);
                $_wc_rating_count_unserialized = maybe_unserialize($_wc_rating_count);
                $average_ratings = array_sum($comment_ratings)/array_sum($_wc_rating_count_unserialized);
                $average_ratings_nf = number_format($average_ratings, 2, '.', '');
                update_post_meta($mw_product_id, '_wc_average_rating', $average_ratings_nf);
                echo '<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible"> 
                <p><strong>You have rated product successfully.</strong></p><button type="button" class="notice-dismiss">
                <span class="screen-reader-text">Dismiss this notice.</span></button></div>';
               } else {
                echo '<div id="setting-error-settings_updated" class="error settings-error notice is-dismissible"> 
                <p><strong>Failed to rate product.</strong></p><button type="button" class="notice-dismiss">
                <span class="screen-reader-text">Dismiss this notice.</span></button></div>'; 
               }
                die;
            }
        }
        /*
        * gettings ips
        */
        public function getIP() {
            $ipaddress = '';
            if (getenv('HTTP_CLIENT_IP'))
                $ipaddress = getenv('HTTP_CLIENT_IP');
            else if(getenv('HTTP_X_FORWARDED_FOR'))
                $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
            else if(getenv('HTTP_X_FORWARDED'))
                $ipaddress = getenv('HTTP_X_FORWARDED');
            else if(getenv('HTTP_FORWARDED_FOR'))
                $ipaddress = getenv('HTTP_FORWARDED_FOR');
            else if(getenv('HTTP_FORWARDED'))
               $ipaddress = getenv('HTTP_FORWARDED');
            else if(getenv('REMOTE_ADDR'))
                $ipaddress = getenv('REMOTE_ADDR');
            else
                $ipaddress = 'UNKNOWN';
            return $ipaddress;
            }
         /*
         * Saving Options
         */
        public function save() {
		  if(isset($_POST['submit']) && wp_verify_nonce( $_POST['mw_custom_ratings_reviews_woocommerce_nonce'], 'mw_custom_ratings_reviews_woocommerce_action' )):
		  $save = update_option( 'mw_custom_ratings_reviews_woocommerce_options', $_POST );
		  if($save){ $this->redirect('?page=custom-reviews-woocommerce&type=1'); } else { $this->redirect('?page=custom-reviews-woocommerce&type=2');}
		  endif;
        }
        /*
        * Redirection
         */
		public function redirect($url) {
			echo '<script>';
			echo 'window.location.href="'.$url.'"';
			echo '</script>';
		}
    }
    new mw_custom_reviews_woocommerce;
}
