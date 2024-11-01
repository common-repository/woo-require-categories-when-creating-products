<p><?php _e('Thank you for installing our plugin.', WooCommerce_Required_Taxonomies::$textname); ?></p>

<?php
$upgrade_url = 'https://wpsheeteditor.com/upgrade-required-taxonomies-wporg';
$contact_url = 'https://wpsheeteditor.com/contact-required-taxonomies-wporg';

$steps = array();


$post_types = get_post_types(array(
	'public' => true,
		), 'objects', 'OR');
$allowed_post_types = woocommerce_required_taxonomies_Obj()->get_allowed_post_types();
$allowed_taxonomies = array(
	'product_cat'
);

$enabled_post_types = get_option('woocommerce_required_taxonomies_post_types', array());
$enabled_taxonomies = get_option('woocommerce_required_taxonomies_taxonomies', array());
$error_message = get_option('woocommerce_required_taxonomies_error_message');

if (empty($enabled_post_types)) {
	$enabled_post_types = array();
}
if( count($allowed_post_types) == 1 ){
	$enabled_post_types = array_keys($allowed_post_types);
}
if (empty($enabled_taxonomies)) {
	$enabled_taxonomies = array();
}
if (empty($error_message)) {
	$error_message = __('{taxonomy_name} is required', WooCommerce_Required_Taxonomies::$textname);
}
ob_start();
?>
<p><?php _e('Available post types', WooCommerce_Required_Taxonomies::$textname); ?></p>

<?php
foreach ($post_types as $post_type) {
	$key = $post_type->name;
	$post_type_name = $post_type->label;
	$disabled = !isset($allowed_post_types[$key]) ? ' disabled ' : '';
	$maybe_go_premium = !empty($disabled) ? '<small><a href="' . esc_url($upgrade_url) . '">' . __('(Pro)', WooCommerce_Required_Taxonomies::$textname) . '</a></small>' : '';
	?>
	<div class="post-type-field post-type-<?php echo $key; ?>"><input type="checkbox" name="post_types[]" value="<?php echo esc_attr($key); ?>" id="<?php echo esc_attr($key); ?>" <?php checked(in_array($key, $enabled_post_types)); ?> <?php echo $disabled; ?>> <label for="<?php echo esc_attr($key); ?>"><?php echo sanitize_text_field($post_type_name); ?> <?php echo $maybe_go_premium; ?></label></div>
<?php } ?>
<?php
$post_type_select = ob_get_clean();
$steps['select_post_type'] = $post_type_select;


ob_start();
?>
<p><?php _e('Force users to select at least one term of these taxonomies when creating posts:', WooCommerce_Required_Taxonomies::$textname); ?></p>

<?php
foreach ($post_types as $post_type) {
	$key = $post_type->name;
	$post_type_name = $post_type->label;
	$taxonomies = get_object_taxonomies($key, 'objects');
	?>
	<div class="post-type-taxonomies post-type-taxonomies-<?php echo $key; ?>">
		<b><?php echo $post_type_name; ?></b>

		<?php if (empty($taxonomies)) { ?>
			<p><?php printf(__('%s don´t have taxonomies.', WooCommerce_Required_Taxonomies::$textname), $post_type_name); ?></p>
		<?php } ?>
		<?php
		foreach ($taxonomies as $taxonomy_key => $taxonomy) {
			if (!$taxonomy->show_ui) {
				continue;
			}
			$disabled = !in_array($taxonomy_key, $allowed_taxonomies) ? ' disabled ' : '';
			$maybe_go_premium = !empty($disabled) ? '<small><a href="' . esc_url($upgrade_url) . '">' . __('(Pro)', WooCommerce_Required_Taxonomies::$textname) . '</a></small>' : '';
			?>
			<div class="taxonomy-field taxonomy-<?php echo esc_attr($key); ?> taxonomy-<?php echo esc_attr($taxonomy_key); ?>"><input type="checkbox" name="taxonomies[<?php echo esc_attr($key); ?>][]" value="<?php echo esc_attr($taxonomy_key); ?>" id="<?php echo esc_attr($taxonomy_key); ?>" <?php echo $disabled; ?> <?php checked(isset($enabled_taxonomies[$key]) && in_array($taxonomy_key, $enabled_taxonomies[$key])); ?>> <label for="<?php echo esc_attr($taxonomy_key); ?>"><?php echo $taxonomy->label; ?> <?php echo $maybe_go_premium; ?></label></div>
		<?php } ?>

	</div>
	<?php
}

$taxonomy_select = ob_get_clean();
$steps['select_taxonomies'] = $taxonomy_select;

ob_start();
?>
<label><?php _e('Error message displayed when the product category is not selected.', WooCommerce_Required_Taxonomies::$textname); ?> <br/>
	<input type="text" name="error_message" value="<?php echo esc_attr($error_message); ?>" style="width: 100%; display: block;"/></label>


<?php
wp_nonce_field(WooCommerce_Required_Taxonomies::$textname);
?>
<br/>
<button class="save-settings button button-primary" data-loading-text="<?php _e('Loading...', WooCommerce_Required_Taxonomies::$textname); ?>"  data-success-text="<?php _e('Settings saved', WooCommerce_Required_Taxonomies::$textname); ?>"><?php _e('Save settings', WooCommerce_Required_Taxonomies::$textname); ?></button>

<?php
$steps['error_message'] = ob_get_clean();
?>
<?php
$steps['get_started'] = '<p>' . sprintf(__('Done. Now when users create or edit a new product, they will be forced to select a product category according to the settings', WooCommerce_Required_Taxonomies::$textname)) . '</p>';

$allowed_message = '<p>' . sprintf(__('You are using the Free plugin. You can make product categories required only.', WooCommerce_Required_Taxonomies::$textname)) . '</p>';

$allowed_message .= sprintf(__('<h3>Go Premium</h3><p>Make WooCommerce Products Tags Required when creating products<br/>Make WooCommerce Product Attributes Required (Color, Sizes, etc.)<br/>Make Event Categories and Taxonomies Required when creating events<br/>Make blog categories or tags required.<br/>Make any taxonomy required on any post type<br/>And more.</p><a href="%s" class="button button-primary">Buy Premium Plugin</a> - <a href="%s" class="button" target="_blank">Do you need help?</a></p>.', WooCommerce_Required_Taxonomies::$textname), $upgrade_url, $contact_url);

$steps['allowed'] = $allowed_message;


if (!function_exists('WC')) {
	$steps = array();
	$steps['wc_required'] = __('This plugin requires WooCommerce.', WooCommerce_Required_Taxonomies::$textname);
}

$steps = apply_filters('WooCommerce_Required_Taxonomies/welcome_steps', $steps);

if (!empty($steps)) {
	echo '<ol class="steps">';
	foreach ($steps as $key => $step_content) {
		?>
		<li><?php echo $step_content; ?></li>		
		<?php
	}

	echo '</ol>';
}
?>
<hr>
<p>
	<?php
	printf(__('<a href="%s" class="button"><span class="dashicons dashicons-email"></span> Contact Support</a>.', WooCommerce_Required_Taxonomies::$textname), $contact_url);

	printf(__('<a href="%s" class="button button-primary"><span class="dashicons dashicons-cart"></span> Upgrade</a>.', WooCommerce_Required_Taxonomies::$textname), $upgrade_url);
	?>
</p>
<style>
	.post-type-taxonomies {
		display: none;
	}
</style>
<script>
	jQuery(document).ready(function () {
		function showTaxonomiesForPostType() {
			var $postTypesSelected = jQuery('.post-type-field input:checked');
			jQuery('.post-type-taxonomies').hide();
			$postTypesSelected.each(function () {
				var postType = jQuery(this).val();
				jQuery('.post-type-taxonomies-' + postType).show();
			});
		}

		showTaxonomiesForPostType();
		jQuery('.post-type-field input').change(function () {
			showTaxonomiesForPostType();
		});

		var $saveButton = jQuery('.save-settings');
		$saveButton.click(function (e) {
			e.preventDefault();

			$saveButton.data('original-text', $saveButton.text());
			$saveButton.text($saveButton.data('loading-text'));

			var data = jQuery('.vg-plugin-sdk-page .steps input').serializeArray();
			data.push({
				name: 'action',
				value: 'woocommerce_required_taxonomies_save_settings'
			});

			jQuery.post(ajaxurl, data, function (response) {
				if (response.success) {
					alert($saveButton.data('success-text'));
					$saveButton.text($saveButton.data('original-text'));
				}
			});
		});
	});
</script>