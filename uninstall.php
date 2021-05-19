<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

// Get destination of plugins to be uninstalled
$destination = ABSPATH . '/wp-content/plugins/';

// Check if plugins were installed previously
$acf_was_installed = get_option('acf_exists');
$wal_was_installed = get_option('wal_exists');
// check_to_delete holds the four plugins to be deleted
$plugins_to_delete = array();
if ($acf_was_installed == 'false') {
    $plugins_to_delete[] = 'advanced-custom-fields/acf.php';
}
if ($wal_was_installed == 'false') {
    $plugins_to_delete[] = 'wild-apricot-login/wild-apricot-login.php';
}

require_once(ABSPATH . 'wp-admin/includes/plugin.php');
delete_plugins($plugins_to_delete);

// Delete options
delete_option('acf_exists');
delete_option('wal_exists');
