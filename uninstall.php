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

foreach($plugins_to_delete as $plugin) {
    uninstall_plugin($plugin);
}

//// Get active plugins
// $active_plugins=get_option('active_plugins');
// // Loop through each active plugin
// foreach($active_plugins as $listofplugins)
// {
//     // Check if current plugin is the plugin to be uninstalled
//     if(!in_array($listofplugins, $plugins_to_delete))
//     {
//         // Add plugin to the new active plugin
//         $array_newplugin[]=$listofplugins;
//     }
//     // Update active plugins with the ultimate-member, advanced-custom-fields, advanced-custom-fields-pro, and wild-apricot-login removed
//     update_option('active_plugins',$array_newplugin);
// }

// // Get list of files in plugin destination
// $plugindireactory = scandir($destination);
// // Loop through each file
// foreach($plugindireactory as $pluginslist)
// {
//     // Check if the current plugin should be deleted
//     if(in_array($pluginslist,$check_to_delete))
//     {
//         // Iterate through to the folder to be deleted
//         $dir = $destination.$pluginslist;
//         $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
//         $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
//         // Loop through each file
//         foreach($files as $file) {
//             // Check if file is directory
//             if ($file->isDir()){
//                 // Remove directory
//                 rmdir($file->getRealPath());
//             } else { // Not a directory --> is a file
//                 // Delete file
//                 unlink($file->getRealPath());
//             }
//         }
//         // Remove directory
//         rmdir($dir);
//     }
// }

// Delete options
delete_option('acf_exists');
delete_option('wal_exists');
