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
  </div>
<?php
}
?>