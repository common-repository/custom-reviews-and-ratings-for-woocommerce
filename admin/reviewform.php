<?php if ( ! defined( 'ABSPATH' ) ) exit;
$users = get_users(); 
global $post;
?>
<div id="mw_rating_save_response"></div>
<table class="form-table">
<tbody>
<tr>
<th scope="row"><label for="mw_wc_users"><?php _e('Select User*','custom-ratings-reviews-woocommerce')?></label></th>
<td>
<select name="mw_wc_users" id="mw_wc_users">
<?php foreach($users as $user) { ?>
	<option value="<?php echo $user->ID; ?>"><?php echo  esc_html( $user->display_name ) . ' ( #'.$user->ID.' )'; ?></option>
<?php } ?>    
</td>
</tr>
<tr>
<th scope="row"><label for="mw_wc_rating"><?php _e('Rating*','custom-ratings-reviews-woocommerce')?></label></th>
<td>
<select name="mw_wc_rating" id="mw_wc_rating">
<?php for($i=5; $i>=1; $i--) {?>
	<option value="<?php echo $i; ?>">
    <?php echo $i;?>
    </option>
<?php } ?>    
</td>
</tr>
<tr>
<th scope="row"><label for="mw_wc_review"><?php _e('Your Review *','custom-ratings-reviews-woocommerce')?></label></th>
<td>
<p>
<textarea name="mw_wc_review" rows="5" cols="50" id="mw_wc_review" class="large-text"></textarea>
</p>
</td>
</tr>
<tr>
<th scope="row"><?php _e('Review Date *','custom-ratings-reviews-woocommerce')?></th>
<td><input name="mw_wc_review_date" type="text" id="mw_wc_review_date" value="<?php echo date('Y-m-d H:i:s');?>" class="regular-text"></td>
</tr>
</tbody></table>
<p class="submit"><input type="submit" name="mw_wc_submit" id="mw_wc_submit" class="button button-primary" value="Save Changes">
</p>
<p><?php _e('If you like <strong>Woocommerce Custom Reviews and Ratings</strong> please leave us a <a href="https://wordpress.org/support/plugin/custom-reviews-and-ratings-for-woocommerce/reviews/?filter=5#new-post" target="_blank" class="wccr-rating-link" data-rated="Thanks :)">★★★★★</a> rating. A huge thanks in advance!	','custom-ratings-reviews-woocommerce')?></p>
<script>
jQuery(document).ready(function(){
    var mw_ajax_url = "<?php echo admin_url('admin-ajax.php'); ?>";
  jQuery('#mw_wc_submit').click(function(e){
   e.preventDefault();
   var mw_nonce = '<?php echo wp_create_nonce( 'mw_nonce' )?>';
   var mw_uid = jQuery('#mw_wc_users').val();
   var mw_wc_rating = jQuery('#mw_wc_rating').val();
   var mw_wc_review = jQuery('#mw_wc_review').val();
   var mw_wc_review_date = jQuery('#mw_wc_review_date').val();
   jQuery(this).val('Saving...');
   var data = {
		'action': 'mw_save_ratings_ratings',
		'mw_nonce': mw_nonce,
        'mw_uid':mw_uid,
        'mw_wc_rating':mw_wc_rating,
        'mw_wc_review': mw_wc_review,
        'mw_wc_review_date':mw_wc_review_date,
        'mw_product_id': "<?php  echo $post->ID ?>"
	};
    if(mw_wc_review == '') {
        jQuery('#mw_wc_submit').val('Save Changes');
        jQuery('#mw_rating_save_response').html('<div id="setting-error-settings_updated" class="error settings-error notice is-dismissible"><p><strong>All Fields are Required.</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>'); 
    } else {
        jQuery.post(mw_ajax_url, data, function(response) {
            jQuery('#mw_wc_submit').val('Save Changes');
            jQuery('#mw_rating_save_response').html(response);
            jQuery('#mw_wc_review').val('');
        });
    }
  });
});
</script>