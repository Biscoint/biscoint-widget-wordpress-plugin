<?php
/*
Plugin Name: Biscoint Widget Plugin
Plugin URI: https://staging-app.biscoint.io/wordpress-plugin
Description: This plugin adds a custom widget to show Biscoint best price.
Version: 1.0
Author: Jonathas Carrijo
Author URI: https://biscoint.io/
License: GPL2
*/

// The widget class
class Biscoint_Widget extends WP_Widget {

	// Main constructor
	public function __construct() {
		parent::__construct(
			'biscoint_widget',
			__( 'Biscoint Widget', 'text_domain' ),
			array(
				'customize_selective_refresh' => true,
			)
		);
	}

	// The widget form (for the backend )
	public function form( $instance ) {

		// Set widget defaults
		$defaults = array(
			'locale'    => 'pt',
			'operation'     => 'buy',
			'base_currency' => 'BTC',
			'quote_currency' => 'BRL',
			'refreshIntervalMs'   => '5000',
		);
		
		// Parse current settings with defaults
		extract( wp_parse_args( ( array ) $instance, $defaults ) ); ?>
		
		<?php // locale ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'locale' ); ?>"><?php _e( 'Language:', 'text_domain' ); ?></label>
			<select name="<?php echo $this->get_field_name( 'locale' ); ?>" id="<?php echo $this->get_field_id( 'locale' ); ?>" class="widefat">
			<?php
			// Your options array
			$options = array(
				'pt' => __( 'Português', 'text_domain' ),
				'en' => __( 'English', 'text_domain' ),
			);

			// Loop through options and add each one to the select dropdown
			foreach ( $options as $key => $name ) {
				echo '<option value="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" '. selected( $locale, $key, false ) . '>'. $name . '</option>';

			} ?>
			</select>
		</p>
		
		<?php // operation ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'operation' ); ?>"><?php _e( 'Operation:', 'text_domain' ); ?></label>
			<select name="<?php echo $this->get_field_name( 'operation' ); ?>" id="<?php echo $this->get_field_id( 'operation' ); ?>" class="widefat">
			<?php
			// Your options array
			$options = array(
				'buy' => __( 'Buy', 'text_domain' ),
				'sell' => __( 'Sell', 'text_domain' ),
			);

			// Loop through options and add each one to the select dropdown
			foreach ( $options as $key => $name ) {
				echo '<option value="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" '. selected( $operation, $key, false ) . '>'. $name . '</option>';

			} ?>
			</select>
		</p>
		
		<?php // base_currency ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'base_currency' ); ?>"><?php _e( 'Base Currency:', 'text_domain' ); ?></label>
			<select name="<?php echo $this->get_field_name( 'base_currency' ); ?>" id="<?php echo $this->get_field_id( 'base_currency' ); ?>" class="widefat">
			<?php
			// Your options array
			$options = array(
				'BTC' => __( 'BTC', 'text_domain' ),
			);

			// Loop through options and add each one to the select dropdown
			foreach ( $options as $key => $name ) {
				echo '<option value="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" '. selected( $base_currency, $key, false ) . '>'. $name . '</option>';

			} ?>
			</select>
		</p>
		
		
		<?php // quote_currency ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'quote_currency' ); ?>"><?php _e( 'Quote Currency:', 'text_domain' ); ?></label>
			<select name="<?php echo $this->get_field_name( 'quote_currency' ); ?>" id="<?php echo $this->get_field_id( 'quote_currency' ); ?>" class="widefat">
			<?php
			// Your options array
			$options = array(
				'BRL' => __( 'BRL - Brazilian Reais', 'text_domain' ),
				'EUR' => __( 'EUR - Euros', 'text_domain' ),
				'USD' => __( 'USD - U.S. Dollars', 'text_domain' ),
			);

			// Loop through options and add each one to the select dropdown
			foreach ( $options as $key => $name ) {
				echo '<option value="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" '. selected( $quote_currency, $key, false ) . '>'. $name . '</option>';

			} ?>
			</select>
		</p>
		
		
		<?php // refreshIntervalMs ?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'refreshIntervalMs' ) ); ?>"><?php _e( 'Refresh Interval:', 'text_domain' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'refreshIntervalMs' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'refreshIntervalMs' ) ); ?>" type="text" value="<?php echo esc_attr( $refreshIntervalMs ); ?>" />
		</p>

	<?php }

	// Update widget settings
	public function update( $new_instance, $old_instance ) {
		
		$instance = $old_instance;
		$instance['locale']    = isset( $new_instance['locale'] ) ? wp_strip_all_tags( $new_instance['locale'] ) : '';
		$instance['operation']    = isset( $new_instance['operation'] ) ? wp_strip_all_tags( $new_instance['operation'] ) : '';
		$instance['base_currency']    = isset( $new_instance['base_currency'] ) ? wp_strip_all_tags( $new_instance['base_currency'] ) : '';
		$instance['quote_currency']    = isset( $new_instance['quote_currency'] ) ? wp_strip_all_tags( $new_instance['quote_currency'] ) : '';
		$instance['refreshIntervalMs']    = isset( $new_instance['refreshIntervalMs'] ) ? wp_strip_all_tags( $new_instance['refreshIntervalMs'] ) : '';
		return $instance;
	}

	// Display the widget
	public function widget( $args, $instance ) {

		extract( $args );

		// Check the widget options
		$locale     		= isset( $instance['locale'] )				? $instance['locale']				: '';
		$operation     		= isset( $instance['operation'] )			? $instance['operation']			: '';
		$base_currency     	= isset( $instance['base_currency'] )		? $instance['base_currency']		: '';
		$quote_currency     = isset( $instance['quote_currency'] )		? $instance['quote_currency']		: '';
		$refreshIntervalMs	= isset( $instance['refreshIntervalMs'] )	? $instance['refreshIntervalMs']	: '';
		
		// WordPress core before_widget hook (always include )
		echo $before_widget;

		// Display the widget
		echo '<div id="biscointWidget"></div>';
		echo '<script type="text/javascript" src="https://staging-app.biscoint.io/widget/widget.js"></script>';
		echo '<script type="text/javascript">';
		echo   'BiscointWidget.init({';
		echo     'locale: "' . $locale . '",';
		echo     'op: "' . $operation . '",';
		echo     'base: "' . $base_currency. '",';
		echo     'quote: "' . $quote_currency . '",';
		echo     'refreshIntervalMs: ' . $refreshIntervalMs . ',';
		echo   '});';
		echo '</script>';

		// WordPress core after_widget hook (always include )
		echo $after_widget;

	}

}

// Register the widget
function register_biscoint_widget() {
	register_widget( 'Biscoint_Widget' );
}
add_action( 'widgets_init', 'register_biscoint_widget' );