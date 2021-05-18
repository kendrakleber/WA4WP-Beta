<?php
/**
 * Plugin Name: Wild Apricot for WordPress (WA4WP)
 * Plugin URI: https://www.newpathconsulting.com/wild-apricot-for-wordpress
 * Description: The Wild Apricot for WordPress (WA4WP) plugin seamlessly integrates your existing Wild Apricot account with your WordPress website!
 * Version: Beta
 * Author: NewPath Consulting
 * Author URI: https://newpathconsulting.com/
**/

function phpAlert($msg) {
    echo '<script type="text/javascript">alert("' . $msg . '")</script>';
}

function echo_log( $what )
{
    echo '<pre>'.print_r( $what, true ).'</pre>';
}

function my_log_file( $msg, $name = '' )
{
    // Print the name of the calling function if $name is left empty
    $trace=debug_backtrace();
    $name = ( '' == $name ) ? $trace[1]['function'] : $name;

    $error_dir = '/Applications/MAMP/logs/php_error.log';
    $msg = print_r( $msg, true );
    $log = $name . "  |  " . $msg . "\n";
    error_log( $log, 3, $error_dir );
}

/* Plugin css file */
// Register actions to enqueue stylesheets
add_action('admin_enqueue_scripts', 'wawp_selectively_enqueue_admin_script');

function wawp_selectively_enqueue_admin_script($hook) {
    if ($hook == 'toplevel_page_wa4wp') {
        wp_enqueue_style('wawp_custom_script', plugin_dir_url(__FILE__) . 'assets/css/wawp-style.css', array(), '1.0');
    }
    wp_enqueue_style('wawp_custom_script_user', plugin_dir_url(__FILE__) . 'assets/css/wawp-custom.css', array(), '1.0');
}

add_action('wp_enqueue_scripts', 'wawp_selectively_enqueue_menu_css');

function wawp_selectively_enqueue_menu_css($hook) {
    if (!is_user_logged_in()) {
        wp_enqueue_style('wa4wp_style_menu', plugin_dir_url(__FILE__) . 'assets/css/wawp-style-menu.css', array(), '1.0');
    }

}

// Register action to render WAWP menu pages
add_action('admin_menu', 'wawp_admin_menu');

function wawp_admin_menu() {
    add_menu_page('WA4WP', 'WA4WP', 'manage_options', 'wa4wp', 'wawp_admin_page', 'dashicons-businesswoman', 6);
    add_submenu_page('wa4wp', 'WA4WP', 'WA4WP', 'manage_options', 'wa4wp');
    add_submenu_page('wa4wp', 'Global Access', 'Global Access', 'manage_options', 'globalaccess', 'wawp_global_page');
}

// Render content for Global Access settings page
function wawp_global_page() {
    // Get WA current membership status, default to "Pending Renewal" if no such membership status exists
    $memvalue = get_option('globalmembershipstatus');
    if (empty($memvalue)) {
        $memvalue = array("PendingRenewal", "active");
    } else {
        $memvalue = unserialize($memvalue);
    }

    // Renders checkbox list of possible membership statuses and a textarea to enter a global restriction message
    ?>
    <form  id="formsubmit" action="" method="post">
    <h2  class="globaltext-heading"><strong>Membership Status</strong></h2>
    <input type="checkbox" id="memberstats" name="membershipstatus[]"    <?php checked(in_array('PendingRenewal', $memvalue));?> value="PendingRenewal">
    <label for="vehicle1">Pending Renewal</label><br>
    <input type="checkbox" id="memberstats2" name="membershipstatus[]" <?php checked(in_array('Lapsed', $memvalue));?> value="Lapsed">
    <label for="memberstats2">Lapsed</label><br>
    <input type="checkbox" id="memberstats3" name="membershipstatus[]" <?php checked(in_array('PendingLevel', $memvalue));?> value="PendingLevel">
    <label for="memberstats3">Pending Level Change</label><br>
    <input type="checkbox" id="memberstats4" name="membershipstatus[]" <?php checked(in_array('PendingNew', $memvalue));?> value="PendingNew">
    <label for="memberstats4">Pending New</label><br>
    <input type="checkbox" id="memberstats5" name="membershipstatus[]" <?php checked(in_array('Active', $memvalue));?> value="Active">
    <label for="memberstats5">Active</label>
    <?php
    wp_nonce_field('_rohitink_meta_nonce', 'rohitink_meta_nonce');?>
            <h2 class="globaltext-heading"><label for="rohitink_meta_content"><?php _e('Global Restriction Message', 'text-domain');?></label></h2>
    <?php
    $meta_content_withslashes = get_option('globalrestrict_message'); //wpautop( rohitink_get_meta( 'rohitink_meta_content' ),true);
    $meta_content = stripcslashes($meta_content_withslashes);
    wp_editor($meta_content, 'meta_content_editor', array(
        'wpautop' => true,
        'media_buttons' => false,
        'textarea_name' => 'rohitink_meta_content',
        'textarea_rows' => 10,
        'teeny' => true,
    ));
    ?>
    <input type="submit" class="button button-primary button-large" id="savewp" name="save" value="Save">
    </form>
    <?php

    // On message save, get the message content and membership status and add or update both settings
    if (isset($_POST['save'])) {
        $wysyiwig_editor = trim($_POST['rohitink_meta_content']);
        $membershipstatus = $_POST['membershipstatus'];
        $globalmembership = serialize($membershipstatus);
        $globalmembershipstatus = get_option('globalmembershipstatus');

        if (empty($globalmembershipstatus)) {
            add_option('globalmembershipstatus', $globalmembership);
        } else {
            update_option('globalmembershipstatus', $globalmembership);
        }
        $global_restrict_message = get_option('globalrestrict_message');
        if (empty($global_restrict_message)) {
            add_option('globalrestrict_message', $wysyiwig_editor);
            update_option('globalrestrict_message', $wysyiwig_editor);
        } else {
            update_option('globalrestrict_message', $wysyiwig_editor);
        }
        ?>
        <script>
        location.reload();
        </script>
        <?php
    }
}

// Render content for main WAWP settings page
function wawp_admin_page() {
    ?>
    <section id="bundle_top_section">
        <div class="container">
            <div class="steps_section">
                <div class="steps_des bg">
                    <h3 class="title_section">Wild Apricot</h3>
                    <p><strong><span>Step 1: </span></strong>&nbsp;&nbsp;Activate the Wild Apricot plugin if it is not activated.</p>
                    <p><strong><span>Step 2: </span></strong>&nbsp;&nbsp;Go to Settings --> Wild Apricot Login to configure your Wild Apricot login settings. </p>
                </div>
                <div class="line"></div>
                <div class="steps_des">
                    <h3 class="title_section">Advanced Custom Fields</h3>
                    <p><strong><span>Step 1: </span></strong>&nbsp;&nbsp;Activate the Advanced Custom Fields plugin if it is not activated.</p>

                    <p><strong><span>Step 2: </span></strong>&nbsp;&nbsp;Import file if it is not imported. Go to Custom Fields -> Tools -> Import Fields Groups -> Choose file -> Import.
                    <?php $json_downloadpath = site_url() . '/wp-content/plugins/wa4wp/acf-wa4wp.json'; ?>
                    <a download href="<?php echo $json_downloadpath; ?>">Click here to download the custom fields file</a>.</p>
                </div>
                <div class="line"></div>
            </div>
        </div>
    </section>
  <?php
}

// // Wild Apricot and Advanced Custom Fields
// $destination = ABSPATH . '/wp-content/plugins/';
// $add_our_plugin = scandir($destination);
// phpAlert("add_our_plugin" . $add_our_plugin);
// require_once ABSPATH . 'wp-admin/includes/plugin.php';
// $check_plugins_exist = array('advanced-custom-fields', 'wild-apricot-login');
// $number_of_plugins_count = count(array_intersect($check_plugins_exist, $add_our_plugin));
// // Check which plugins exist
// $acf_exists = in_array('advanced-custom-fields/acf.php', $add_our_plugin, true);
// // phpAlert("acf_exists: ". var_export($acf_exists, true));
// $wal_exists = in_array('wild-apricot-login/wild-apricot-login.php', $add_our_plugin, true);
// // phpAlert("wal_exists: ". var_export($wal_exists, true));
// add_option('add_our_plugin', $add_our_plugin);
// add_option('acf_exists', var_export($acf_exists, true));
// add_option('wal_exists', var_export($wal_exists, true));

// Activation hook
register_activation_hook(__FILE__, function() {
    // Load external dependencies
    // Wild Apricot and Advanced Custom Fields
    $destination = ABSPATH . 'wp-content/plugins/';
    $existing_plugins = scandir($destination);
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
    $check_plugins_exist = array('advanced-custom-fields', 'wild-apricot-login');
    $number_of_plugins_count = count(array_intersect($check_plugins_exist, $existing_plugins));
    // Check which plugins exist
    $acf_exists = in_array('advanced-custom-fields/acf.php', $existing_plugins, true);
    // phpAlert("acf_exists: ". var_export($acf_exists, true));
    $wal_exists = in_array('wild-apricot-login/wild-apricot-login.php', $existing_plugins, true);
    // phpAlert("wal_exists: ". var_export($wal_exists, true));
    add_option('add_our_plugin', $existing_plugins);
    add_option('acf_exists', var_export($acf_exists, true));
    add_option('wal_exists', var_export($wal_exists, true));

    // $apl = get_option('active_plugins');

    // $plugins = get_plugins();

    // $active_plugins = array();

    // foreach ($apl as $p) {
    //     if (isset($plugins[$p])) {

    //         array_push($active_plugins, $plugins[$p]['TextDomain']);
    //     }
    // }

    // if ACF or WAL aren't installed, unzip corebundle and install them
    if (!in_array('advanced-custom-fields', $existing_plugins) || !in_array('wild-apricot-login', $existing_plugins)) {
        // Unzip corebundle.zip
        $upload_dir = plugin_dir_path(__FILE__) . 'corebundle.zip';
        $a = scandir($destination);
        $zip = new ZipArchive;
        $res = $zip->open($upload_dir);

        if ($res === true) {
            $zip->extractTo($destination);
            $zip->close();
        }
    }

    $active_plugins = get_option('active_plugins');
    // activate ACF and WAL if not activated
    if (!in_array('advanced-custom-fields/acf.php', $active_plugins)) {
        activate_plugin('advanced-custom-fields/acf.php');
        $add_plugin = 'advanced-custom-fields/acf.php';
        array_push($active_plugins, $add_plugin);
    }

    if (!(in_array('wild-apricot-login/wild-apricot-login.php', $active_plugins))) {
        activate_plugin('wild-apricot-login/wild-apricot-login.php');
        $add_plugin = 'wild-apricot-login/wild-apricot-login.php';
        array_push($active_plugins, $add_plugin);
    }

    update_option('active_plugins', $active_plugins);
});

// Register activation hook
add_action('activated_plugin', 'wawp_detect_activation');

function wawp_detect_activation($plugin) {

    if ($plugin == plugin_basename(__FILE__)) {
        exit(wp_redirect(admin_url('admin.php?page=wa4wp')));

    }
}

// // Load external dependencies
// // Wild Apricot and Advanced Custom Fields
// $destination = ABSPATH . '/wp-content/plugins/';
// $add_our_plugin = scandir($destination);
// require_once ABSPATH . 'wp-admin/includes/plugin.php';
// $check_plugins_exist = array('advanced-custom-fields', 'wild-apricot-login');
// $number_of_plugins_count = count(array_intersect($check_plugins_exist, $add_our_plugin));
// // Check which plugins exist
// $acf_exists = in_array('advanced-custom-fields', $add_our_plugin);
// $wal_exists = in_array('wild-apricot-login', $add_our_plugin);
// add_option('acf_exists', $acf_exists);
// add_option('wal_exists', $wal_exists);

// $apl = get_option('active_plugins');

// $plugins = get_plugins();

// $active_plugins = array();

// foreach ($apl as $p) {
//     if (isset($plugins[$p])) {

//         array_push($active_plugins, $plugins[$p]['TextDomain']);
//     }
// }

// if ($number_of_plugins_count < 2) {
//     $upload_dir = plugin_dir_path(__FILE__) . 'corebundle.zip';
//     $a = scandir($destination);
//     $zip = new ZipArchive;
//     $res = $zip->open($upload_dir);

//     if ($res === true) {
//         $zip->extractTo($destination);
//         $zip->close();
//         if (!in_array('acf', $active_plugins)) {
//             activate_plugin('advanced-custom-fields/acf.php');
//         }

//        $get_active_plugins = get_option('active_plugins');
//         if (!(in_array('wild-apricot-login/wild-apricot-login.php', $get_active_plugins))) {
//             $add_plugin = 'wild-apricot-login/wild-apricot-login.php';
//             array_push($get_active_plugins, $add_plugin);
//             update_option('active_plugins', $get_active_plugins);
//         }
//     }
// } else {
//     if (!in_array('acf', $active_plugins)) {
//         activate_plugin('advanced-custom-fields/acf.php');
//     }
//     $active_plugins = get_option('active_plugins');
//     if (!(in_array('wild-apricot-login/wild-apricot-login.php', $active_plugins))) {
//         $add_plugin = 'wild-apricot-login/wild-apricot-login.php';
//         array_push($active_plugins, $add_plugin);
//         update_option('active_plugins', $active_plugins);
//     }
// }

// Register deactivation hook
register_deactivation_hook(__FILE__, 'wawp_deactivate');
function wawp_deactivate() {
    // $destination = ABSPATH . 'wp-content/plugins/';
    // error_log($destination, 3, ABSPATH . 'wp-content/error.log');
    $active_plugins = get_option('active_plugins');



    $plugins_to_deactivate = array();
    // if ACF and WAL are installed w/ WAWP, we wanna deactivate them
    $acf_exists = get_option('acf_exists');
    $wal_exists = get_option('wal_exists');

    // my_log_file('acf_exists: ' . var_export($acf_exists, true));
    // my_log_file('wal_exists: ' . var_export($wal_exists, true));

    if (strcmp($acf_exists, 'false') == 0) { // equal
        // deactivate acf
        my_log_file('deactivating acf');
        $plugins_to_deactivate[] = 'advanced-custom-fields/acf.php';

    }

    if (strcmp($wal_exists, 'false') == 0) { // equal
        // deactivate wal
        my_log_file('deactivating wal');
        $plugins_to_deactivate[] = 'wild-apricot-login/wild-apricot-login.php';
    }

    // Remove the plugins to be deactivated from the active plugins array
    $new_active_plugins = array();
    $our_plugin = plugin_basename(__FILE__);
    foreach ($active_plugins as $plugin) {
        // if plugin is NOT in plugins_to_deactivate, add it to new_active_plugins
        if (!in_array($plugin, $plugins_to_deactivate) && strcmp($our_plugin, $plugin) !== 0) {
            my_log_file("not going to deactivate!: " . $plugin);
            $new_active_plugins[] = $plugin;
        }
    }

    my_log_file("plugin basename " . plugin_basename(__FILE__));
    foreach ($new_active_plugins as $plugin) {
        my_log_file("new active: " . $plugin);
    }
    foreach ($plugins_to_deactivate as $plugin) {
        my_log_file("plugins to deactivate: " . $plugin);
    }
    // my_log_file("plugins_to_deactivate: " . var_dump($plugins_to_deactivate, true));

    require_once(ABSPATH . 'wp-admin/includes/plugin.php');
    deactivate_plugins($plugins_to_deactivate);

    update_option('active_plugins', $new_active_plugins);

    // if not, we don't wanna do anything

    // foreach ($active_plugins as $listofplugins) {
    //     if ($listofplugins != 'advanced-custom-fields/acf.php' && $listofplugins != 'wild-apricot-login/wild-apricot-login.php') {
    //         $array_newplugin[] = $listofplugins;
    //     }
    //     update_option('active_plugins', $array_newplugin);
    // }
    // $plugindireactory = scandir($destination);

}

// Add meta box for content restriction on posts
add_action('add_meta_boxes', 'wawp_register_meta_box');

function wawp_register_meta_box() {
    $postpagenew = array('post', 'page');
    add_meta_box(
        'wawp_member_access_meta_box',
        __('Member Access', 'WA4WP'),
        'wawp_member_access_meta_box',
        $postpagenew,
        'side',
        'default'
    );
}

// Renders the membership roles metabox content
function wawp_member_access_meta_box($post) {
    wp_nonce_field('wawp_member_access_meta_box_nonce', 'wawp_member_access_nonce');
    global $wp_roles;
    $all_roles = $wp_roles;
    $rolegetdb = array('admin', 'edito');
    $km = get_post_meta($post->ID, 'rolecheckingcustom', true);
    $rolegetdb = unserialize($km);
    if (empty($rolegetdb)) {
        $rolegetdb = array('admin', 'edito');
    }

    foreach ($all_roles as $key => $getroles) {
        if ($key == 'role_names') {
            unset($getroles['administrator']);
            unset($getroles['editor']);
            unset($getroles['author']);
            unset($getroles['contributor']);
            unset($getroles['subscriber']);
            unset($getroles['bbp_keymaster']);
            unset($getroles['bbp_spectator']);
            unset($getroles['bbp_blocked']);
            unset($getroles['bbp_moderator']);
            unset($getroles['bbp_participant']);
            unset($getroles['employer']);
            unset($getroles['candidate']);
            //unset($getroles['digital nova scotia']);

            $countnew = 0;
            $count = 0;

            foreach ($getroles as $role => $newroles) {
                $result = substr($role, 0, 4);
                if ($result != 'grp_') {
                    $leve_role_users[] = array($role => $newroles);
                } else {
                    $grouparray_members[] = array($role => $newroles);
                }
            }
            $inc = 0;
            ?>
            <div class="wrapper-custom">
            <!-- Render checkbox list of member roles -->
            <ul>
            <?php
            foreach ($leve_role_users as $newkey => $modified_role) {
                foreach ($modified_role as $key => $modified_roles) {
                    if ($inc == 0) { ?>
                        <li style="margin:0;font-weight: 600;">
                            <label for="checkall"><input type="checkbox" value="checkall" id='selectall' name="checkall"  /> Select All Member Levels</label>
                        </li>
                        <li>
                            <label for="<?php echo $modified_roles; ?>">
                                <input type="checkbox" value="<?php echo $key; ?>" class='case' name="rolecheckingcustomvalue[]" <?php checked(in_array($key, $rolegetdb));?> />
                                <?php echo $modified_roles; ?>
                            </label>
                        </li>
                        <?php
                    } else { ?>
                        <li>
                            <label for="<?php echo $modified_roles; ?>"> <input type="checkbox" value="<?php echo $key; ?>" class='case' name="rolecheckingcustomvalue[]" <?php checked(in_array($key, $rolegetdb));?> />
                                <?php echo $modified_roles; ?>
                            </label>
                        </li>
                        <?php
                    }
                    $inc++;
                }
            }
            ?>
            </ul>

            <!-- Render checkbox list of group roles -->
            <ul>
            <?php
            $inc_forgroup = 0;
            foreach ($grouparray_members as $grp_array) {
                foreach ($grp_array as $key => $modified_roles) {
                    if ($inc_forgroup == 0) { ?>
                        <li style="margin:0;font-weight: 600;">
                            <label for="checkall"><input type="checkbox" value="checkall" id='selectallnew' name="checkall"  /> Select All Group Levels</label>
                        </li>
                        <li>
                            <label for="<?php echo $modified_roles; ?>">
                                <input type="checkbox" value="<?php echo $key; ?>" class='casenew' name="rolecheckingcustomvalue[]" <?php checked(in_array($key, $rolegetdb));?> />
                                <?php echo $modified_roles; ?>
                            </label>
                        </li>
                        <?php
                    } else { ?>
                        <li>
                            <label for="<?php echo $modified_roles; ?>">
                                <input type="checkbox" value="<?php echo $key; ?>" class='casenew' name="rolecheckingcustomvalue[]" <?php checked(in_array($key, $rolegetdb));?> />
                                <?php echo $modified_roles; ?>
                            </label>
                        </li>
                        <?php
                    }
                    $inc_forgroup++;
                }
            }
            echo "</ul></div>";
        }
    }
}

// Register action to save custom meta box
add_action('save_post', 'wawp_save_member_access_meta_box');
function wawp_save_member_access_meta_box($post_id) {
    if (!isset($_POST['wawp_member_access_nonce']) || !wp_verify_nonce($_POST['wawp_member_access_nonce'], 'wawp_member_access_meta_box_nonce')) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['rolecheckingcustomvalue'])) {
        $serialize = serialize($_POST['rolecheckingcustomvalue']);
        update_post_meta($post_id, 'rolecheckingcustom', sanitize_text_field($serialize));
    } else {
        delete_post_meta($post_id, 'rolecheckingcustom');
    }
}

add_action('admin_footer', 'wawp_script_checkbox');
// Modifies behavior of checkboxes
function wawp_script_checkbox() {
    ?> <script language="javascript">
        jQuery(function() {
            // add multiple select / deselect functionality
            jQuery("#selectall").click(function () {
                jQuery('.case').attr('checked', this.checked);
            });
            jQuery("#selectallnew").click(function () {
                jQuery('.casenew').attr('checked', this.checked);
            });

            // if all checkboxes are selected, check the selectall checkbox
            // and viceversa
            jQuery(".case").click(function() {
                if(jQuery(".case").length == jQuery(".case:checked").length) {
                    jQuery("#selectall").attr("checked", "checked");
                }
                else {
                    jQuery("#selectall").removeAttr("checked");
                }
            });
            jQuery(".casenew").click(function() {
                if(jQuery(".casenew").length == jQuery(".casenew:checked").length) {
                    jQuery("#selectallnew").attr("checked", "checked");
                }
                else {
                    jQuery("#selectallnew").removeAttr("checked");
                }
            });
        });
    </script> <?php
}

// Register filter for role-based content restriction
add_filter('the_content', 'wawp_restrict_content', 10, 1);

function wawp_restrict_content($content) {
    // for updating the role based restriction
    $postidnew = get_the_ID();
    $km = get_post_meta($postidnew, 'rolecheckingcustom', true);
    $privatepagevalue = get_post_meta($postidnew, 'individual_page_restrict_value', true);
    $contentrestriction = get_post_meta($postidnew, 'um_content_restriction', true);
    $new_restricted_message = get_option('globalrestrict_message');
    $newkm = get_post_meta($postidnew, 'rolecheckingcustom', true);
    $user = wp_get_current_user();
    $currentuserrole = $user->roles;
    $rolegetdb = unserialize($newkm);
    if (!current_user_can('update_core')) {
        if (!empty($rolegetdb)) {
            $checkroleacceess = array_intersect($currentuserrole, $rolegetdb);
            $countofroles = count($checkroleacceess);
            $uid = get_current_user_id();
            $current_user_info = get_user_meta($uid);
            $check_status_db = $current_user_info['userstatus_new'][0];
            if ($countofroles <= 0) {
                if ($privatepagevalue == "") {

                    $content = "<div class='vi-content-restrict'>" . wpautop(stripslashes($new_restricted_message)) . "</div>";
                } else {
                    $content = "<div class='vi-content-restrict'>" . wpautop(stripslashes($privatepagevalue)) . "</div>";
                }
            } else {
                $member_status = get_option('globalmembershipstatus');
                $member_status = unserialize($member_status);
                $uid = get_current_user_id();
                $current_user_info = get_user_meta($uid);
                $check_status_db = $current_user_info['userstatus_new'][0];
                if (!in_array($check_status_db, $member_status)) {
                    if ($privatepagevalue == "") {
                        $content = "<div class='vi-content-restrict'>" . wpautop(stripslashes($new_restricted_message)) . "</div>";
                    } else {
                        $content = "<div class='vi-content-restrict'>" . wpautop(stripslashes($privatepagevalue)) . "</div>";
                    }
                } else {
                    $content = $content;
                }
            }
        }
    }
    return $content;
}

add_action('init', 'wawp_member_information_update');

function wawp_member_information_update() {

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://oauth.wildapricot.org/auth/token',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => 'grant_type=client_credentials&scope=auto&obtain_refresh_token=true',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Basic QVBJS0VZOnkzcGJxa2JhMWVmeGN2bjNzNWJhN21oZW83YXd5Zw==',
            'Content-Type: application/x-www-form-urlencoded',
        ),
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    $response_test = json_decode($response);
    $accesstockent = $response_test->access_token;

    /// for fetching groups and adding it

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.wildapricot.org/v2.1/accounts/17980/membergroups',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Accept: application/json',
            'Authorization: Bearer ' . $accesstockent,
        ),
    ));
    $response_grp = curl_exec($curl);
    curl_close($curl);
    $responsearray = json_decode($response_grp);
    global $wp_roles;
    $roles = $wp_roles->roles;
    foreach ($roles as $key => $rolessandname) {
        $allrolesindatabse[] = $rolessandname['name'];
    }

    foreach ($responsearray as $newarray) {

        $groupmembername = $newarray->Name;
        $groupmemberid = $newarray->Id;
        $group_role_key = "grp_wa_level_" . $groupmemberid;
        if (!in_array($group_role_key, $allrolesindatabse)) {
            add_role($group_role_key, $groupmembername);
        }
    }
    // SECOND API call
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.wildapricot.org/v2.1/accounts/17980/membershiplevels',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Accept: application/json',
            'Authorization: Bearer ' . $accesstockent,
        ),
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    $response_get = json_decode($response);

    global $wp_roles;
    $roles = $wp_roles->roles;
    foreach ($roles as $key => $rolessandname) {
        $allrolesindatabse[] = $rolessandname['name'];
    }
    if ($response_get == true) {
        foreach ($response_get as $response) {
            $addrole = $response->Name;
            if (!in_array($addrole, $allrolesindatabse)) {
                $role_key = 'wa_level_' . $response->Id;
                add_role($role_key, $response->Name);
            }
        }
    }
}

// Register action to schedule hourly role update
add_action('wp', 'wawp_cron_activation');

function wawp_cron_activation() {
    if (!wp_next_scheduled('wawp_event_hourly_update_member_data')) {
        wp_schedule_event(current_time('timestamp'), 'hourly', 'wawp_event_hourly_update_member_data');
    }
}

// Register hourly cron job for role update
add_action('wawp_event_hourly_update_member_data', 'wawp_update_wildapricot_member_data');

// Grabs user role data from Wild Apricot and updates WP's database
function wawp_update_wildapricot_member_data() {
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://oauth.wildapricot.org/auth/token',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => 'grant_type=client_credentials&scope=auto&obtain_refresh_token=true',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Basic QVBJS0VZOnkzcGJxa2JhMWVmeGN2bjNzNWJhN21oZW83YXd5Zw==',
            'Content-Type: application/x-www-form-urlencoded',
        ),
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    $response_test = json_decode($response);
    $accesstockent = $response_test->access_token;

    global $wp_roles;
    $all_roles = $wp_roles;
    foreach ($all_roles as $key => $getroles) {
        if ($key == 'role_names') {
            unset($getroles['administrator']);
            unset($getroles['editor']);
            unset($getroles['author']);
            unset($getroles['contributor']);
            unset($getroles['subscriber']);
            unset($getroles['bbp_keymaster']);
            unset($getroles['bbp_spectator']);
            unset($getroles['bbp_blocked']);
            unset($getroles['bbp_moderator']);
            unset($getroles['bbp_participant']);
            unset($getroles['employer']);
            unset($getroles['candidate']);
            unset($getroles['digital nova scotia']);

            foreach ($getroles as $role => $newroles) {
                $newrole[] = $role;
            }
        }
    }
    $blogusers = get_users(array('role__in' => $newrole));
    foreach ($blogusers as $user) {
        $user_email = trim($user->user_email);
        $user_email = urlencode($user_email);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.wildapricot.org/v2/accounts/17980/contacts?$async=false&$filter=\'e-mail\'%20eq%20' . $user_email,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Authorization: Bearer ' . $accesstockent,
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $apiupdate_response = json_decode($response);
        foreach ($apiupdate_response as $responsear) {
            if (isset($responsear[0]->MembershipLevel->Name)) {
                $newroleforuser = $responsear[0]->MembershipLevel->Name;
                $role_key = 'wa_level_' . $responsear[0]->MembershipLevel->Id;
                $u = new WP_User($user->ID);
                $user = get_userdata($user->ID);
                $currentuserrole = $user->roles[0];
                $u->remove_role($currentuserrole);
                $u->add_role($role_key, $newroleforuser);
            }
            if (isset($responsear[0]->status)) {
                $stausvlaue = $responsear[0]->status;
                update_user_meta($user->ID, 'userstatus_new', $stausvlaue);
            }
        }
    }

    // do something every hour
}

// Register action to import the custom fields data
add_action('init', 'wawp_import_custom_fields');

function wawp_import_custom_fields() {
    if (function_exists('get_field')) {
        $upload_dir_json = plugin_dir_path(__FILE__) . 'acf-wa4wp.json';
        $field_group_key = 'group_6005987fa2e45';
        $fields = acf_get_fields($field_group_key);
        if (!$fields) {
            $json = file_get_contents($upload_dir_json);
            $json = json_decode($json, true);
            if (!$json || !is_array($json)) {
                return acf_add_admin_notice(__("Import file empty", 'acf'), 'warning');
            }
            if (isset($json['key'])) {
                $json = array($json);
            }
            $ids = array();
            foreach ($json as $field_group) {
                $post = acf_get_field_group_post($field_group['key']);
                if ($post) {
                    $field_group['ID'] = $post->ID;
                }
                $field_group = acf_import_field_group($field_group);
                $ids[] = $field_group['ID'];
            }
        }
    }
}
