<?php
add_action( 'admin_menu', 'dbsc_plugin_menu' );

function dbsc_plugin_menu() {
  add_options_page( 'DB Share Count', 'DB Share Count', 'manage_options', 'dbsc_admin_options', 'dbsc_settings_page' );
	add_action( 'admin_menu', 'dbsc_plugin_menu' );
  add_action( 'admin_init', 'register_dbsc_settings' );
}
function register_dbsc_settings() {
  register_setting( 'dbsc_settings', 'dbsc_settings', 'dbsc_options_validate' );
  add_settings_section('dbsc_main', 'Display Settings', 'dbsc_section_text', 'dbsc-main-settings');
  add_settings_field('min_count_display', 'Minimum count to display', 'min_count_display', 'dbsc-main-settings', 'dbsc_main');
}

function dbsc_section_text() {
  echo '<p>Configure DB Share Count display options</p>';
}

function min_count_display() {
  $options = get_option('dbsc_settings');
  $minCount = get_min_count();
  echo "<input type='number' name='dbsc_settings[min_count_display]' value='{$minCount}'/>";
}

function dbsc_options_validate($input) {
  $options = get_option('dbsc_settings');
  $options[min_count_display] = absint($input[min_count_display]);
  return $options;
}

function dbsc_settings_page() {
?>
<div class="wrap">
<h2>DB Share Count</h2>

<form method="post" action="options.php">
    <?php settings_fields( 'dbsc_settings' ); ?>
    <?php do_settings_sections( 'dbsc-main-settings' ); ?>
    <?php submit_button(); ?>

</form>
</div>
<?php } ?>
