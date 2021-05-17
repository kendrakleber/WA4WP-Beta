<?php
/**
 * Plugin Name: Wild Apricot for WordPress (WA4WP)
 * Plugin URI: https://www.newpathconsulting.com/wild-apricot-for-wordpress
 * Description: The Wild Apricot for WordPress (WA4WP) plugin seamlessly integrates your existing Wild Apricot account with your WordPress website!
 * Version: Beta
 * Author: NewPath Consulting
 * Author URI: https://newpathconsulting.com/
 **/

/* Plugin css file */
function wpdocs_selectively_enqueue_admin_script($hook)
{
    if ($hook == 'toplevel_page_wa4wp') {
        wp_enqueue_style('wawp_custom_script', plugin_dir_url(__FILE__) . 'assets/css/wawp-style.css', array(), '1.0');
    }
    wp_enqueue_style('wawp_custom_script_user', plugin_dir_url(__FILE__) . 'assets/css/wawp-custom.css', array(), '1.0');
}
add_action('admin_enqueue_scripts', 'wpdocs_selectively_enqueue_admin_script');

function wpdocs_selectively_enqueue_menu_css($hook)
{
    if (!is_user_logged_in()) {
        wp_enqueue_style('wa4wp_style_menu', plugin_dir_url(__FILE__) . 'assets/css/wawp-style-menu.css', array(), '1.0');
    }

}
add_action('wp_enqueue_scripts', 'wpdocs_selectively_enqueue_menu_css');


add_action('admin_menu', 'my_admin_menu');

function my_admin_menu()
{
    add_menu_page('WA4WP', 'WA4WP', 'manage_options', 'wa4wp', 'myplguin_admin_page', 'dashicons-businesswoman', 6);
    add_submenu_page('wa4wp', 'WA4WP', 'WA4WP', 'manage_options', 'wa4wp');
    add_submenu_page('wa4wp', 'Global Access', 'Global Access', 'manage_options', 'globalaccess', 'myplguin_global_page');
}
function myplguin_global_page()
{
    $memvalue = get_option('globalmembershipstatus');
    if (empty($memvalue)) {
        $memvalue = array("PendingRenewal", "active");
    } else {
        $memvalue = unserialize($memvalue);
    }
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
function myplguin_admin_page()
{
    ?>
<section id="bundle_top_section">
    <div class="container">
   <div class="steps_section">

       <div class="steps_des bg">
        <h3 class="title_section">Wild Apricot</h3>
        <p><strong><span>Step:1 -</span></strong>&nbsp;&nbsp;Activate the plugin if it is not activated</p>
        <p><strong><span>Step:2 -</span></strong>&nbsp;&nbsp;Go to the settings -> wild apricot login then configure the settings </p>
       </div>
      <div class="line"></div>
       <div class="steps_des">
        <h3 class="title_section">Advanced Custom Fields</h3>
        <p><strong><span>Step:1 -</span></strong>&nbsp;&nbsp;Activate the plugin if it is not activated</p>
        <?php
                $json_downloadpath = site_url() . '/wp-content/plugins/wa4wp/acf-wa4wp.json';
    ?>
        <p><strong><span>Step:2 -</span></strong>&nbsp;&nbsp;Import file if it is not imported. Go to custom fields -> Tools -> Import Fields Groups -> Choose file -> Import.<a download href="<?php echo $json_downloadpath; ?>">Click here to download file</a></p>

       </div>
       <div class="line"></div>
   </div>
    </div>
</section>

  <?php
}
//plugin Activation hook
function cyb_activation_redirect($plugin)
{
    if ($plugin == plugin_basename(__FILE__)) {
        exit(wp_redirect(admin_url('admin.php?page=wa4wp')));

    }
}
add_action('activated_plugin', 'cyb_activation_redirect');

// plugin path to extract
$destination = ABSPATH . '/wp-content/plugins/';
$add_our_plugin = scandir($destination);
require_once ABSPATH . 'wp-admin/includes/plugin.php';
$check_plugins_exist = array("advanced-custom-fields", 'wild-apricot-login');
$number_of_plugins_count = count(array_intersect($check_plugins_exist, $add_our_plugin));
$apl = get_option('active_plugins');

$plugins = get_plugins();

$active_plugins = array();
foreach ($apl as $p) {
    if (isset($plugins[$p])) {

        array_push($active_plugins, $plugins[$p]['TextDomain']);
    }
}
if ($number_of_plugins_count < 2) {


    $upload_dir = plugin_dir_path(__FILE__) . 'corebundle.zip';
    $a = scandir($destination);
    $zip = new ZipArchive;
    $res = $zip->open($upload_dir);

    if ($res === true) {
        $zip->extractTo($destination);
        $zip->close();
        if (!in_array('acf', $active_plugins)) {
            activate_plugin('advanced-custom-fields/acf.php');
        }

       $get_active_plugins = get_option('active_plugins');
        if (!(in_array('wild-apricot-login/wild-apricot-login.php', $get_active_plugins))) {
            $add_plugin = 'wild-apricot-login/wild-apricot-login.php';
            array_push($get_active_plugins, $add_plugin);
            update_option('active_plugins', $get_active_plugins);
        }
    }

} else {
    if (!in_array('acf', $active_plugins)) {
        activate_plugin('advanced-custom-fields/acf.php');
    }
    $active_plugins = get_option('active_plugins');
    if (!(in_array('wild-apricot-login/wild-apricot-login.php', $active_plugins))) {
        $add_plugin = 'wild-apricot-login/wild-apricot-login.php';
        array_push($active_plugins, $add_plugin);
        update_option('active_plugins', $active_plugins);
    }
}

// plugin deactivation hook
register_deactivation_hook(__FILE__, 'myplugin_deactivate');
function myplugin_deactivate()
{
    $active_plugins = get_option('active_plugins');
    foreach ($active_plugins as $listofplugins) {
        if ($listofplugins != 'advanced-custom-fields/acf.php' && $listofplugins != 'wild-apricot-login/wild-apricot-login.php') {
            $array_newplugin[] = $listofplugins;
        }
        update_option('active_plugins', $array_newplugin);
    }
    $plugindireactory = scandir($destination);

}
// content restriction
function kvkoolitus_prices_metabox()
{
    $postpagenew = array('post', 'page');
    add_meta_box(
        'kvkoolitus_prices_metabox',
        __('Member Access', 'kvkoolitus'),
        'kvkoolitus_prices_metabox_callback',
        $postpagenew,
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'kvkoolitus_prices_metabox');

function kvkoolitus_prices_metabox_callback($post)
{
    wp_nonce_field('kvkoolitus_prices_metabox_nonce', 'kvkoolitus_prices_nonce');?>
  <?php
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
<ul>
<?php
foreach ($leve_role_users as $newkey => $modified_role) {
                foreach ($modified_role as $key => $modified_roles) {
                    if ($inc == 0) {
                        ?>
 <li style="margin:0;font-weight: 600;">
 <label for="checkall"><input type="checkbox" value="checkall" id='selectall' name="checkall"  />
			Select All Member Levels</label></li>
     <li>
     <label for="<?php echo $modified_roles; ?>">  <input type="checkbox" value="<?php echo $key; ?>" class='case' name="rolecheckingcustomvalue[]" <?php checked(in_array($key, $rolegetdb));?> />
 <?php echo $modified_roles; ?></label>
<?php
} else {
                        ?>
                    <li>
                    <label for="<?php echo $modified_roles; ?>"> <input type="checkbox" value="<?php echo $key; ?>" class='case' name="rolecheckingcustomvalue[]" <?php checked(in_array($key, $rolegetdb));?> />
     <?php echo $modified_roles; ?></label>
    </li>
                <?php

                    }
                    $inc++;
                }
            }
            ?>
    </ul><ul>
    <?php

            $inc_forgroup = 0;
            foreach ($grouparray_members as $grp_array) {
                foreach ($grp_array as $key => $modified_roles) {
                    if ($inc_forgroup == 0) {
                        ?>
                        <li style="margin:0;font-weight: 600;">
                        <label for="checkall"><input type="checkbox" value="checkall" id='selectallnew' name="checkall"  />
                 Select All Group Levels</label>
                 </li>
                 <li>
                 <label for="<?php echo $modified_roles; ?>"> <input type="checkbox" value="<?php echo $key; ?>" class='casenew' name="rolecheckingcustomvalue[]" <?php checked(in_array($key, $rolegetdb));?> />
   <?php echo $modified_roles; ?></label>
  </li>
                <?php
} else {
                        ?>
<li>
<label for="<?php echo $modified_roles; ?>"> <input type="checkbox" value="<?php echo $key; ?>" class='casenew' name="rolecheckingcustomvalue[]" <?php checked(in_array($key, $rolegetdb));?> />
  <?php echo $modified_roles; ?></label>
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
function kvkoolitus_prices_save_meta($post_id)
{

    if (!isset($_POST['kvkoolitus_prices_nonce']) || !wp_verify_nonce($_POST['kvkoolitus_prices_nonce'], 'kvkoolitus_prices_metabox_nonce')) {
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
add_action('save_post', 'kvkoolitus_prices_save_meta');

add_action('admin_footer', 'cor_profile_subject_end');
function cor_profile_subject_end()
{
    ?>
	<script language="javascript">
jQuery(function(){
// add multiple select / deselect functionality
        jQuery("#selectall").click(function () {
            jQuery('.case').attr('checked', this.checked);
        });
        jQuery("#selectallnew").click(function () {
            jQuery('.casenew').attr('checked', this.checked);
        });

// if all checkbox are selected, check the selectall checkbox
// and viceversa
jQuery(".case").click(function(){

	if(jQuery(".case").length == jQuery(".case:checked").length) {
		jQuery("#selectall").attr("checked", "checked");
	}
	else {
		jQuery("#selectall").removeAttr("checked");
	}

});
jQuery(".casenew").click(function(){
if(jQuery(".casenew").length == jQuery(".casenew:checked").length) {
    jQuery("#selectallnew").attr("checked", "checked");
}
else {
    jQuery("#selectallnew").removeAttr("checked");
}
});
});
</script>
	<?php
}
function my_replace_content($content)
{
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

add_filter('the_content', 'my_replace_content', 10, 1);

add_action('init', 'member_information_update');
function member_information_update()
{

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

// hourly cron job for the role update
add_action('my_hourly_event', 'do_this_hourly');

// The action will trigger when someone visits your WordPress site
function my_activation()
{
    if (!wp_next_scheduled('my_hourly_event')) {
        wp_schedule_event(current_time('timestamp'), 'hourly', 'my_hourly_event');
    }
}
add_action('wp', 'my_activation');

function do_this_hourly()
{
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

// To import the custom fields data

function example_function()
{

    if (function_exists('get_field')) {
        $upload_dir_json = plugin_dir_path(__FILE__) . 'acf-export-2021-01-28.json';
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
add_action('init', 'example_function');
