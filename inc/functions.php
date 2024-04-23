<?php
/**
 * Get the value of a settings field
 *
 * @param  string $option  settings field name
 * @param  string $section the section name this field belongs to
 * @param  string $default default text if it's not found
 * @return mixed
 */
function cixww_get_option( $option = '', $default = '', $section = 'cixwishlist_settings' ) {
	$options = get_option( $section );
	return ( isset( $options[ $option ] ) ) ? $options[ $option ] : $default;
}
