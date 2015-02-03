<?php
// Content Expire Scheduler Setting Page

function ces_registration_settings() {
  add_option( 'ces_expiry_message', 'Page has Expired: This page is no longer available. Please contact Webmaster for content.');
  register_setting( 'default', 'ces_expiry_message' );
}
add_action( 'admin_init', 'ces_registration_settings' );

function ces_settings_page() {
  add_options_page('Conten Expire Scheuler', 'Content Expire Scheduler', 'manage_options', 'ces-options', 'ces_option_page');
}
add_action('admin_menu', 'ces_settings_page');

function ces_option_page() {
?>
  <div class="wrap">
    <h2> Content Expire Scheduler</h2>

    <form method="post" action="options.php" class="form-table">
      <?php settings_fields( 'default' ); ?>
        <table class="form-table">
          <tr valign="top">
            <th scope="row">
              <label for="ces_expiry_message">Content Expiry Message</label>
            </th>
            <td>
              <textarea id="ces_expiry_message" name="ces_expiry_message" rows="5" cols="55"><?php echo get_option('ces_expiry_message'); ?></textarea>
            </td>
          </tr>
        </table>
      <?php submit_button(); ?>
    </form>
    <p style="margin-top: 40px;"><small><?php _e('If you experience some problems with this plugin please let me know about it on <a href="https://wordpress.org/support/plugin/content-expire-scheduler#postform">Plugin\'s support</a>. If you think this plugin is awesome please vote on <a href="https://wordpress.org/support/view/plugin-reviews/content-expire-scheduler/">Wordpress plugin page</a>. Thanks!', RADLABS_TEXTDOMAIN ); ?></small></p>
  </div>
<?php
}
?>