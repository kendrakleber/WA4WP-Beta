<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

// Get destination of plugins to be uninstalled
$destination = ABSPATH . '/wp-content/plugins/';

// Get active plugins
$active_plugins=get_option('active_plugins');
// Loop through each active plugin
foreach($active_plugins as $listofplugins)
{
    // Check if current plugin is the plugin to be uninstalled
    if($listofplugins!='ultimate-member/ultimate-member.php'  && $listofplugins!='advanced-custom-fields-pro/acf.php' && $listofplugins!='advanced-custom-fields/acf.php' && $listofplugins!='wild-apricot-login/wild-apricot-login.php' )
    {
        // Add plugin to the new active plugin
        $array_newplugin[]=$listofplugins;
    }
    // Update active plugins with the ultimate-member, advanced-custom-fields, advanced-custom-fields-pro, and wild-apricot-login removed
    update_option('active_plugins',$array_newplugin);
}

// Get list of files in plugin destination
$plugindireactory = scandir($destination);
// Loop through each file
foreach($plugindireactory as $pluginslist)
{
    // checktodelete holds the four plugins to be deleted
    $checktodelete=array('wild-apricot-login','ultimate-member','advanced-custom-fields-pro', 'advanced-custom-fields');
    // Check if the current plugin should be deleted
    if(in_array($pluginslist,$checktodelete))
    {
        // Iterate through to the folder to be deleted
        $dir = $destination.$pluginslist;
        $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
        // Loop through each file
        foreach($files as $file) {
            // Check if file is directory
            if ($file->isDir()){
                // Remove directory
                rmdir($file->getRealPath());
            } else { // Not a directory --> is a file
                // Delete file
                unlink($file->getRealPath());
            }
        }
        // Remove directory
        rmdir($dir);
    }
}
