<?php
   /*
   Plugin Name: Content Expire Scheduler
   Plugin URI: https://wordpress.org/plugins/content-expire-scheduler
   Description: Content Expire Scheduler to automatically expire Post and Pages after selected date, and display custom message to front-end user.
   Version: 1.0
   Author: Zayed Baloch
   Author URI: http://www.radlabs.biz/
   License: GPL2
   */

defined('ABSPATH') or die("No script kiddies please!");
define( 'RLCES_VERSION',   '1.0' );
define( 'RLCES_URL', plugins_url( '', __FILE__ ) );
define( 'RADLABS_TEXTDOMAIN',  'rl_content_expire_scheduler' );

require_once('ces-settings.php');

function rl_content_expire_scheduler() {
  load_plugin_textdomain( RADLABS_TEXTDOMAIN );
}
add_action( 'init', 'rl_content_expire_scheduler' );

add_action( 'post_submitbox_misc_actions', 'rl_ces_add_expiry_calendar');
add_action( 'save_post', 'rl_ces_save_data');

function rl_ces_add_expiry_calendar() {
  global $post;

  if( ! empty( $post->ID ) ) {
    $expires = get_post_meta( $post->ID, 'rl_ces_expiry', true );
    $ces_message = get_post_meta( $post->ID, 'rl_ces_expiry_message', true );
  }

  $label = ! empty( $expires ) ? date_i18n( 'Y-n-d', strtotime( $expires ) ) : __( 'Never', RADLABS_TEXTDOMAIN );
  $date  = ! empty( $expires ) ? date_i18n( 'Y-n-d', strtotime( $expires ) ) : '';
  ?>

  <div id="rl-content-expire-scheduler-wrap" class="misc-pub-section">
    <span>
      <span class="wp-media-buttons-icon dashicons dashicons-calendar"></span>&nbsp;
      <?php _e( 'Expires:', RADLABS_TEXTDOMAIN ); ?>
      <b id="rl-content-expire-scheduler-label"><?php echo $label; ?></b>
    </span>
    <a href="#" id="rl-content-expire-scheduler-edit" class="rl-content-expire-scheduler-edit hide-if-no-js">
      <span aria-hidden="true"><?php _e( 'Edit', RADLABS_TEXTDOMAIN ); ?></span>&nbsp;
      <span class="screen-reader-text"><?php _e( 'Edit date and time', RADLABS_TEXTDOMAIN ); ?></span>
    </a>
    <div id="rl-content-expire-scheduler-field" class="hide-if-js">
      <p>
        <input type="text" name="rl-content-expire-scheduler-date" id="rl-content-expire-scheduler-date" value="<?php echo esc_attr( $date ); ?>" placeholder="yyyy-mm-dd"/>
      </p>
      <p>
      <label>Custom Message</label><br/>
      <input type="text" name="rl-content-expire-scheduler-message" id="rl-content-expire-scheduler-message" value="<?php echo esc_attr( $ces_message ); ?>"/>
      </p>
      <p>
        <a href="#" class="rl-content-expire-scheduler-hide button secondary"><?php _e( 'OK', RADLABS_TEXTDOMAIN ); ?></a>
        <a href="#" class="rl-content-expire-scheduler-hide cancel"><?php _e( 'Cancel', RADLABS_TEXTDOMAIN ); ?></a>
      </p>
    </div>
    <?php wp_nonce_field( 'rl_content_expire_scheduler_edit', 'rl_content_expire_scheduler_none' ); ?>
  </div>
  <?php
}

function rl_ces_save_data( $post_id = 0 ) {
  if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ( defined( 'DOING_AJAX') && DOING_AJAX ) || isset( $_REQUEST['bulk_edit'] ) ) {
    return;
  }
  if( ! empty( $_POST['rl_content_expire_scheduler_edit'] ) ) {
    return;
  }
  if( ! wp_verify_nonce( $_POST['rl_content_expire_scheduler_none'], 'rl_content_expire_scheduler_edit' ) ) {
    return;
  }
  if( ! current_user_can( 'edit_post', $post_id ) ) {
    return;
  }

  $expiration = ! empty( $_POST['rl-content-expire-scheduler-date'] ) ? sanitize_text_field( $_POST['rl-content-expire-scheduler-date'] ) : false;

  $ces_message_save = ! empty( $_POST['rl-content-expire-scheduler-message'] ) ? sanitize_text_field( $_POST['rl-content-expire-scheduler-message'] ) : false;

  if( $expiration ) {
    // Save the expiration
    update_post_meta( $post_id, 'rl_ces_expiry', $expiration );
    update_post_meta( $post_id, 'rl_ces_expiry_message', $ces_message_save );
  } else {
    // Remove any existing expiration date
    delete_post_meta( $post_id, 'rl_ces_expiry' );
    delete_post_meta( $post_id, 'rl_ces_expiry_message' );
  }
}

function rl_ces_is_content_expired( $post_id = 0 ) {
  $expires = get_post_meta( $post_id, 'rl_ces_expiry', true );
  if( ! empty( $expires ) ) {
  // Get the current time and post's expiration date
  $current_time = current_time( 'timestamp' );
  $expiration   = strtotime( $expires, $current_time );
  if( $current_time >= $expiration ) {
      return true;
    }
  }
  return false;
}

function rl_ces_content_title( $title = '', $post_id = 0 ) {
  if( rl_ces_is_content_expired( $post_id ) ) {
    $prefix = get_option( 'rl_ces_prefix', __( 'Expired:', RADLABS_TEXTDOMAIN ) );
    $title  = $prefix . ' ' . $title;
    add_filter( 'the_content', 'rl_ces_expiry_content' );
    add_filter( 'comments_open', 'close_comments', 10, 2 );
  }
  return $title;
}
add_filter( 'the_title', 'rl_ces_content_title', 100, 2);

function rl_ces_expiry_content($content) {
  global $post;
  $expires_msg = get_post_meta( $post->ID, 'rl_ces_expiry_message', true );
  $ces_expiry_message = get_option('ces_expiry_message');

  if (empty($expires_msg)) {
    $content = $ces_expiry_message;
  }else{
    $content = $expires_msg;
  }

  return $content;
}

function close_comments ( $open, $post_id ) {
    // if not open, than back
    if ( ! $open )
      return $open;

    $post = get_post( $post_id );
    if ( $post -> post_type ) // all post types
      return FALSE;

    return $open;
  }



function rl_ces_script() {
  wp_enqueue_style( 'jquery-ui-css', RLCES_URL . '/assets/css/jquery-ui-fresh.min.css', array(), RLCES_VERSION, 'all' );
  wp_enqueue_script( 'jquery-ui-datepicker' );
  wp_enqueue_script( 'jquery-ui-slider' );
  wp_enqueue_script( 'rl_ces_script', RLCES_URL . '/assets/js/edit.js', array( 'jquery' ), RLCES_VERSION, true );
}

add_action( 'load-post-new.php', 'rl_ces_script');
add_action( 'load-post.php', 'rl_ces_script');

require_once('ces-settings.php');