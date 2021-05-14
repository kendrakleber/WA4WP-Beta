<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

// if (!is_plugin_active('testplugin/testplugin.php')) {
   // deactivate_plugins(array('advanced-custom-fields-pro/acf.php', 'ultimate-member/ultimate-member.php'));
// }
// else
// {
//     $result = activate_plugin('woo-checkout-field-editor-pro/checkout-form-designer.php');
//     $result = activate_plugin('vickybhaiplugin/veryfirstplugin.php');
//     $result = activate_plugin('woo-checkout-field-editor-pro/checkout-form-designer.php');
// }

// $result1 = activate_plugin('advanced-custom-fields-pro/acf.php');
// ///$result2 = activate_plugin('vickybhaiplugin/veryfirstplugin.php');
// $result3 = activate_plugin('ultimate-member/ultimate-member.php');


// $destination = $_SERVER['DOCUMENT_ROOT'] . '/testingpurpose/wp-content/plugins/';

// $getoptions = get_option('uninstall_plugins');

// print_r($getoptions);

// TODO: Ask if user wants to uninstall wild-apricot-login and advanced-custom-fields

if($_SERVER['SERVER_NAME']=='localhost')
{
  $destination = $_SERVER['DOCUMENT_ROOT'] . '/testingpurpose/wp-content/plugins/';
}
else{
    $destination = $_SERVER['DOCUMENT_ROOT'] . '/wp-content/plugins/';
}

$active_plugins=get_option('active_plugins');
foreach($active_plugins as $listofplugins)
{
    if($listofplugins!='ultimate-member/ultimate-member.php'  && $listofplugins!='advanced-custom-fields-pro/acf.php' && $listofplugins!='advanced-custom-fields/acf.php' && $listofplugins!='wild-apricot-login/wild-apricot-login.php' )
    {
        // echo "coming";
        $array_newplugin[]=$listofplugins;
    }
    update_option('active_plugins',$array_newplugin);
}

$plugindireactory = scandir($destination);
foreach($plugindireactory as $pluginslist)
{
    // echo "<br/>".$pluginslist;
    $checktodelete=array('wild-apricot-login','ultimate-member','advanced-custom-fields-pro', 'advanced-custom-fields');
    if(in_array($pluginslist,$checktodelete))
    {
        $dir =$destination.$pluginslist;
        $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($it,
             RecursiveIteratorIterator::CHILD_FIRST);
    foreach($files as $file) {
    if ($file->isDir()){
        rmdir($file->getRealPath());
    } else {
        unlink($file->getRealPath());
    }
    }
    rmdir($dir);
        //echo $pluginslist;
    }
}
// die;
