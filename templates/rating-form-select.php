<?php
/**
 * Rating form template for select dropdowns
 */
?>
<select name="<?php echo $element_id; ?>" id="<?php echo $element_id; ?>">
	
	<?php
	// option values
	$index = 0;
	for ( $index; $index <= $max_option_value; $index++ ) {
			
		$is_selected = false;
		if ( $default_option_value == $index ) {
			$is_selected = true;
		}
			
		?>
		<option value="<?php echo esc_attr( $index ); ?>"<?php
			
			if ( $is_selected ) {
				?> selected="selected"<?php
			}
			
		?>><?php echo esc_html( $index ); ?></option>
		<?php
	} ?>
</select>