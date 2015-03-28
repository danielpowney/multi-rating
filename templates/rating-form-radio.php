<?php
/**
 * Rating form template for radio buttons
 */
$index = 0;
for ( $index; $index <= $max_option_value; $index++ ) {

	$is_selected = false;
	if ( $default_option_value == $index ) {
		$is_selected = true;
	}
		
	?>
	<span class="radio-option">
		<input type="radio" name="<?php echo $element_id; ?>" id="<?php echo $element_id; ?>-<?php echo $index; ?>" value="<?php echo esc_attr( $index ); ?>"<?php
		
		if ( $is_selected ) {
			?> checked="checked"<?php
		}
					
		?>><?php echo esc_html( $index ); ?></input>
	</span>
	<?php
}
?>