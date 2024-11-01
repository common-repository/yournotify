<?php
/**
 * Yournotify Subscribe Widget
 *
 * There are two hidden inputs, where the account ID and the selected list ID are saved.
 * These two fields get populated via JS, because we get the contact lists from the API.
 *
 * @package yournotify
 */

if ( ! class_exists( 'Yournotify_Subscribe' ) ) {
	class Yournotify_Subscribe extends WP_Widget {

		/**
		 * Register widget with WordPress.
		 */
		public function __construct() {

			add_shortcode( 'yournotify', array( $this, 'yournotify_admin_shortcode' ) );

			parent::__construct(
				false, // ID, auto generate when false.
				esc_html__( 'Yournotify', 'yournotify' ),
				array(
					'description' => esc_html__( 'Display a subscribe form for collecting contacts with Yournotify.', 'yournotify'),
					'classname'   => 'yournotify-subscribe',
				)
			);

			// AJAX callback.
			add_action( 'wp_ajax_yournotify_subscribe_get_lists', array( $this, 'yournotify_get_lists' ) );
		}

		public function yournotify_admin_shortcode() {

			$options = get_option('widget_yournotify_subscribe');

			if( isset($options) && isset($options[$this->number])) {
			  //$this->number returns the unique widget id that corresponds to the database index
			  $instance_options = $options[$this->number];
			}

			$args = array();
			$instance = array();
			$instance['api_key'] = $instance_options['api_key'];
			$instance['selected_list'] = $instance_options['selected_list'];
		    the_widget( 'Yournotify_Subscribe', $instance, $args );
		}

		/**
		 * Front-end display of widget.
		 *
		 * @see WP_Widget::widget()
		 *
		 * @param array $args
		 * @param array $instance
		 */
		public function widget( $args, $instance ) {
			$api_key       = empty( $instance['api_key'] ) ? '' : $instance['api_key'];
			$selected_list = empty( $instance['selected_list'] ) ? '' : $instance['selected_list'];
			$security_string = esc_attr( $selected_list );

			$form_texts = apply_filters( 'yournotify-btn/form_texts', array(
				'name'  => esc_html__( 'Your Name', 'yournotify-btn' ),
				'email'  => esc_html__( 'Your E-mail Address', 'yournotify-btn' ),
				'telephone'  => esc_html__( 'Your Telephone', 'yournotify-btn' ),
				'submit' => esc_html__( 'Subscribe!', 'yournotify-btn' ),
			) );

			echo $args['before_widget'];
			?>
			<form action="" method="" name="yournotify-embedded-subscribe-form" class="yournotify-subscribe  validate" novalidate>
				<input type="text" value="" name="NAME" class="name  form-control  yournotify-subscribe__name-input" placeholder="<?php echo esc_html( $form_texts['name'] ); ?>" required>
				<input type="email" value="" name="EMAIL" class="email  form-control  yournotify-subscribe__email-input" placeholder="<?php echo esc_html( $form_texts['email'] ); ?>" required>
				<input type="text" value="" name="TELEPHONE" class="telephone  form-control  yournotify-subscribe__telephone-input" placeholder="<?php echo esc_html( $form_texts['telephone'] ); ?>" required>
				<input type="hidden" class="yournotify-subscribe__key-input" value="<?php echo esc_html( $api_key ); ?>" />
				<input type="hidden" class="yournotify-subscribe__list-input" value="<?php echo esc_attr( $selected_list ); ?>">
				<!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
				<div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text" name="<?php echo esc_attr( $security_string ); ?>" tabindex="-1" value=""></div>
				<input type="submit" value="<?php echo esc_html( $form_texts['submit'] ); ?>" name="subscribe" class="button  btn  btn-primary yournotify-subscribe__submit">
				<a class="js-yournotify-signature" target="_blank" href="https://yournotify.com/?utm_source=wordpress&utm_medium=subscribe&utm_campaign=form">Powered by Yournotify</a>
				<div class="js-yournotify-frontend-notice"></div>
			</form>
			<?php
			echo $args['after_widget'];
		}

		/**
		 * Sanitize widget form values as they are saved.
		 *
		 * @param array $new_instance The new options.
		 * @param array $old_instance The previous options.
		 */
		public function update( $new_instance, $old_instance ) {
			$instance = array();

			$instance['api_key']       = sanitize_text_field( $new_instance['api_key'] );
			$instance['selected_list'] = sanitize_text_field( $new_instance['selected_list'] );

			return $instance;
		}

		/**
		 * Back-end widget form.
		 *
		 * @param array $instance The widget options.
		 */
		public function form( $instance ) {
			$api_key       = empty( $instance['api_key'] ) ? '' : $instance['api_key'];
			$selected_list = empty( $instance['selected_list'] ) ? '' : $instance['selected_list'];

			?>
			<p>
				<?php esc_html_e( 'In order to use this widget, you have to: ', 'yournotify' ); ?>
			</p>

			<ol>
				<li><?php printf( esc_html__( '%1$sVisit this URL and login with your Yournotify account%2$s,', 'yournotify' ), '<a href="https://admin.yournotify.com/dashboard/account_developer.html" target="_blank">', '</a>' ); ?></li>
				<li><?php esc_html_e( 'Create an API key and paste it in the input field below,', 'yournotify' ); ?></li>
				<li><?php esc_html_e( 'Click on the Connect button, so that your existing Yournotify lists can be retrieved,', 'yournotify' ); ?></li>
				<li><?php esc_html_e( 'Select which list you want your visitors to subscribe to, from the dropdown menu below.', 'yournotify' ); ?></li>
			</ol>

			<p>
				<label for="<?php echo esc_attr($this->get_field_id( 'api_key' )); ?>"><?php esc_html_e( 'Yournotify API key:', 'yournotify' ); ?>
				</label>
				<input class="js-yournotify-api-key" id="<?php echo esc_attr($this->get_field_id( 'api_key' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'api_key' )); ?>" type="text" value="<?php echo esc_html( $api_key ); ?>" />
				<input class="js-connect-yournotify-api-key  button" type="button" value="<?php esc_html_e( 'Connect', 'yournotify' ); ?>">
			</p>

			<p class="js-yournotify-loader" style="display: none;">
				<span class="spinner" style="display: inline-block; float: none; visibility: visible; margin-bottom: 6px;" ></span> <?php esc_html_e( 'Loading ...', 'yournotify' ); ?>
			</p>

			<div class="js-yournotify-notice"></div>

			<p class="js-yournotify-list-container" style="display: none;">
				<label for="<?php echo esc_attr($this->get_field_id( 'list' )); ?>"><?php esc_html_e( 'Yournotify list:', 'yournotify' ); ?></label> <br>
				<select id="<?php echo esc_attr($this->get_field_id( 'list' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'list' )); ?>"></select>
				<input class="js-yournotify-selected-list" id="<?php echo esc_attr($this->get_field_id( 'selected_list' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'selected_list' )); ?>" type="hidden" value="<?php echo esc_attr( $selected_list ); ?>">
			</p>

			<?php if ( ! empty( $api_key ) ) : ?>
				<script type="text/javascript">
					jQuery( '.js-connect-yournotify-api-key' ).trigger( 'click' );
				</script>
			<?php endif; ?>

			<?php
		}

		/**
		 * AJAX callback function to retrieve the Yournotify lists.
		 */
		public function yournotify_get_lists() {
			check_ajax_referer( 'yournotify-ajax-verification', 'security' );

			$response = array();

			$api_key	= sanitize_text_field( $_GET['api_key'] );

			$args = array(
				'headers' => array(
					'x-access-token' => sprintf( '%1$s', $api_key ),
				),
			);

			$lists_endpoint = 'https://api.yournotify.com/lists';

			$request = wp_remote_get( $lists_endpoint, $args );

			// Error while connecting to the Yournotify server.
			if ( is_wp_error( $request ) ) {
				$response['message'] = esc_html__( 'There was an error connecting to the Yournotify servers.', 'yournotify' );

				wp_send_json_error( $response );
			}

			// Retrieve the response code and body.
			$response_code = wp_remote_retrieve_response_code( $request );
			$response_body = json_decode( wp_remote_retrieve_body( $request ), true );

			// The request was not successful.
			if ( 200 !== $response_code ) {
				$response['message'] = sprintf( esc_html__( 'Error: %1$s', 'yournotify' ), $response_body['status'] );

				wp_send_json_error( $response );
			}

			// There are no lists in this Yournotify account.
			if ( empty( $response_body['data']['results'] ) ) {
				$response['message'] = esc_html__( 'There are no lists with this API key! Please create an list in the Yournotify dashboard and try again.', 'yournotify' );

				wp_send_json_error( $response );
			}

			$lists = array();

			// Parse through the retrieved lists and collect the info we need.
			foreach ( $response_body['data']['results'] as $list ) {
				$lists[ $list['id'] ] = $list['name'];
			}

			$response['message']    = esc_html__( 'Yournotify lists were successfully retrieved!', 'yournotify' );
			$response['lists']      = $lists;

			wp_send_json_success( $response );
		}

	}

}
