<?php if ( ! defined( 'ABSPATH' ) ) exit;
$this->save(); 
$settings = get_option('mw_custom_ratings_reviews_woocommerce_options');
 ?>
<div class="wrap mw_custom_reviews">
    <h2><?php _e('Custom Reviews', 'custom-ratings-reviews-woocommerce');?></h2>
    <div id="setting-error-settings_updated" class="updated settings-error notice"> 
         <form action="" method="post" id="auto_image_alt_form">
            <?php wp_nonce_field( 'mw_custom_ratings_reviews_woocommerce_action', 'mw_custom_ratings_reviews_woocommerce_nonce' );?>
            <table class="form-table">
            <tbody><tr>
            <th scope="row"><label for="enable"><?php _e('Enable?', 'custom-ratings-reviews-woocommerce');?></label></th>
            <td>
            <input name="mw_enable_custom_review_wc" id="mw_enable_custom_review_wc" value="1" type="checkbox" <?php echo (isset($settings['mw_enable_custom_review_wc']) && $settings['mw_enable_custom_review_wc'] == '1') ? 'checked="checked"' : '';?>><?php _e('Check To Enable Custom Review Form on Product Edit Screen', 'equation-editor');?>
            </td>
            </tr>
            </tbody>
            </table>
            <p class="submit"><input name="submit" id="submit" class="button button-primary" value="Save Changes" type="submit"></p>
        </form>
    </div>
</div>