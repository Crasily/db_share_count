<?php

include 'cronjob.php';


add_filter( 'cron_schedules', 'five_minutes' );
register_activation_hook(__FILE__, 'activate_cronjob');
register_deactivation_hook(__FILE__, 'dbsc_deactivation');
add_action('dbsc_cron_event', 'dbsc_cronjob');
add_action( 'admin_menu', 'dbsc_plugin_menu' );

function five_minutes( $schedules ) {
    $schedules['five_minutes'] = array(
        'interval' => 300,
        'display'  => esc_html__( 'Every Five Minutes' ),
    );
    return $schedules;
}

function activate_cronjob() {
  if ( ! wp_next_scheduled( 'dbsc_cron_event' ) ) {
    wp_schedule_event( time(), 'five_minutes', 'dbsc_cron_event' );
  }
}

function dbsc_deactivation() {
	wp_clear_scheduled_hook('dbsc_cron_event');
}

function dbsc_plugin_menu() {
  add_options_page( 'DB Share Count', 'DB Share Count', 'manage_options', 'dbsc_admin_options', 'dbsc_settings_page' );
	add_action( 'admin_menu', 'dbsc_plugin_menu' );
  add_action( 'admin_init', 'dbsc_register_settings' );
}
function dbsc_register_settings() {
  register_setting( 'dbsc_settings', 'dbsc_settings', 'dbsc_options_validate' );
  add_settings_section('dbsc_main', 'Display Settings', 'dbsc_section_text', 'dbsc-main-settings');
  add_settings_field('dbsc_min_count_display', 'Minimum count to display', 'dbsc_min_count_display', 'dbsc-main-settings', 'dbsc_main');
}

function dbsc_section_text() {
  echo '<p>Configure DB Share Count display options</p>';
}

function dbsc_min_count_display() {
  $options = get_option('dbsc_settings');
  $minCount = dbsc_get_min_count();
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
