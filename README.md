Wild Apricot for Wordpress (WA4WP) Documentation

Version 2.0 - May 19, 2021

# WordPress Administrators Guide

## Installing and Configuring the WA4WP Plugin

In the left Wordpress menu, navigate to Plugins > Add New. Next to the page header, click the Upload Plugin button. Choose the [zip file](https://github.com/NewPath-Consulting/WA4WP-Beta/archive/refs/heads/master.zip) of the plugin from your computer. You will need to download this this if you have not already. Click Install Now, then activate it.

This will automatically install and activate the Wild Apricot Login, Advanced Custom Fields, and Short Code in Menus plugins if these were not active previously. It will also import an ACF configuration file with 3 field groups to allow content viewing controls.

Future: The functionality of these additional plugins will be built directly into the WA4WP plugin.

To configure the WA4WP plugin, it must be connected to the Wild Apricot API.

### Create an Authorized Application in Wild Apricot

Using a full Wild Apricot administrator account, create your WordPress site as an external application using the [detailed instructions on authorization external applications.](https://www.google.com/url?q=https://gethelp.wildapricot.com/en/articles/199-integrating-with-wordpress%23authorizing&sa=D&source=editors&ust=1615306111122000&usg=AOvVaw2021mFF2bb930o6DAXmylq) These instructions are also included below in a slightly modified form. 

In the Wild Apricot web administration settings, view authorized applications.

<img src="https://user-images.githubusercontent.com/458134/110492511-4125bb00-80c0-11eb-9d74-2c89befc392e.png" alt="settings > authorized applications" width="400"/> 

Click Settings (1) in the administration menu to display a settings menu. Click Authorized applications (2) to display the list of authorized applications.

![authorize application](https://user-images.githubusercontent.com/458134/110492565-4e42aa00-80c0-11eb-8ccb-4566f3c19fa6.png)

Click Authorized application (1) to begin the authorization process.

Select the Server application type as shown below.

![correct type](https://user-images.githubusercontent.com/458134/110492648-6286a700-80c0-11eb-82af-f28bd0d684bf.png)

Select WordPress (1) and click Continue (2) to advance to the next form.

Fill in the application details as shown below. Copy the API key for the plugin configuration. Save the new authorized application.

<img src="https://user-images.githubusercontent.com/458134/110492722-73cfb380-80c0-11eb-89ed-5c41d6468479.png" alt="image5" width="600"/>

Enter an application name (1). This can be anything. Copy the API key (2). Select WordPress access to allow the WA4WP plugin to connect to Wild Apricot. Copy the Client ID (3) and Client Secret (4) keys.

<img src="https://user-images.githubusercontent.com/458134/110493016-88ac4700-80c0-11eb-8be8-844fb80542ec.png" alt="image9" width="700"/>
You can include some introductory text in (3). Add the formal fully qualified domain names of your WordPress website(s) that will allow SSO in (4). Click Save to save the application authorization.

### Add API Keys into WA4WP Plugin

Once you have created an API key and Client ID and Client Secret strings, in the Wordpress left menu, go to Settings > Wild Apricot Login and copy and paste the strings into the appropriate fields. Make sure to save these changes. 

![image17](https://user-images.githubusercontent.com/458134/110493303-9b268080-80c0-11eb-9628-d0cb5bd43a9f.png)

**IMPORTANT: If your WordPress site shares an admin email address with a user on your Wild Apricot site, you MUST change the email address of the existing WordPress user. You can do this in the Users menu in the WordPress dashboard. You can login with your Wild Apricot email, and elevate that user to an administrator in WordPress if required. Having the same email will cause "an unknown error has occurred" to display when the overlapping user tries to login on the Wordpress site.**

## Updating functions.php

In the left hand menu, go to Appearance > Theme Editor then under theme files on the right hand menu, select `functions.php`. Add the code below. It is recommended that this is instead done within a child theme to ensure the code is preserved even if the theme is updated.

```
function get_user_role() {
    global $current_user;
    $user_roles = $current_user->roles;
    $user_role = array_shift($user_roles);
    return $user_role;
    }

//Add role in body class
add_filter('body_class','my_class_names');
function my_class_names($classes) {
    $classes[] = get_user_role();
    return $classes;
    }

//Hide admin bar for all users except administrators
add_action('after_setup_theme', 'remove_admin_bar');
function remove_admin_bar() {
if (!current_user_can('administrator') && !is_admin()) {
       show_admin_bar(false);
       }
  }
```

The plugin is set up. The WordPress administrators can now manage access to pages and posts based on Wild Apricot membership level and membership group.

<br>

***
<br>

## WA4WP Global Access Settings

### Setting Membership Status Restrictions

To set which membership status can access pages and posts, use the Global Access menu under the WA4WP dashboard icon.

![image18](https://user-images.githubusercontent.com/458134/110493489-a7aad900-80c0-11eb-8f17-90701491afeb.png)


Set the membership statuses that will be allowed to view restricted posts or pages.

![image10](https://user-images.githubusercontent.com/458134/110493595-c4471100-80c0-11eb-879c-598b7c9db7a4.png)


### Set Global Restriction Message

You can show a default restricted message to visitors who are trying to access pages which they do not have access to. This message will be displayed to logged in members who do not have access to a restricted page.

![image11](https://raw.githubusercontent.com/kendrakleber/files/4d962aa6ccc87546a4aac22574fd9541b2916c02/globalrestriction.jpg)


For your convenience:
```
<strong>This page is available for active Digital Nova Scotia members only. </strong>

If your account is lapsed, please log in and renew your membership under member profile.

If you don't have a password, click the Login/Reset Password button below. On the login screen, click forgot password, and enter your email to receive instructions on setting your password. If you don't receive an email, check your junk/spam email, and make sure you input the correct address.


[wa_login login_label="Login/Reset Password" logout_label="Logout" redirect_page="/membership/member-hub/"]
```

To display the logout button, be sure to include the short code. 

## Per Page and Post Access Settings

### Setting a Custom Page/Post Restricted Message

Each page and post has a restricted message in a box called "Restrict individual Page and Post". This box appears under the main content and can float down the page depending on what page builder is in use, if any.

![image3](https://user-images.githubusercontent.com/458134/110493742-e771c080-80c0-11eb-99eb-e9b3c0408109.png)

IMPORTANT: To save the custom restricted message, make sure to save or publish the page or post.

### Setting per Page/Post Access Control

On every page you can select which member levels can view the content of the page. Access control is set by the box on the right side of the page's or post's edit screen. Look for the Member Access box to the right hand side of the page editor.

![image1](https://user-images.githubusercontent.com/458134/110493795-f2c4ec00-80c0-11eb-9339-885ac90f5c70.png)


You can select one or more membership levels to restrict which levels have access. Contacts without membership level are called "WA non-member contacts". You can restrict pages to non-members and make sure a non-logged-in visitor cannot see those pages.

### Membership Groups

You can also set access to one or more membership groups using the Select All Group Levels options. You can select zero or more membership groups which will allow members in those Wild Apricot membership groups to access the page. Selecting a group will allow all users of that group to view the page, even if their membership level was not explicitly checked.

The levels and groups are set inclusively -- that means that if a member is in one of the configured levels OR they are in a configured membership group they can see the page. They will only not be able to view tha page if they don't fit *any* of the criteria. Membership levels and groups can be unchecked to provide a wider level of access.

## Website Menu Management

### Showing Member-Only Menus

To turn on the CSS Classes box, go to Screen option at the top of the page and check the CSS classes checkbox:

![image13](https://user-images.githubusercontent.com/458134/110493874-0708e900-80c1-11eb-904d-e92e5f844725.png)


With the CSS Classes, administrators can control which menus are displayed for members by adding the class: wawp-menu-hide to each menu's CSS class.

![image19](https://user-images.githubusercontent.com/458134/110493939-16883200-80c1-11eb-80e1-4f708e7b1397.png)

## Embedding Wild Apricot Content into WordPress

On this site, the page "Membership Profile" (/member-profile/) was created, and contains a Wild Apricot "widget" that is inserted from Wild Apricot using "widget" code. [Detailed documentation on widgets available to be embedded is available on the Wild Apricot help website.](https://gethelp.wildapricot.com/en/articles/222)

![image2](https://user-images.githubusercontent.com/458134/110494055-391a4b00-80c1-11eb-9e31-9994ff624be7.png)

The code is displayed below so that you may copy and paste it to a page on your site. Please note that the `src` values are specific to your Wild Apricot website. The code below is for the `https://members-digitalnovascotia.wildapricot.org` website. If this is not the URL of your Wild Apricot website, please replace `https://members-digitalnovascotia.wildapricot.org` in both `src` tags with the URL of your Wild Apricot website. For example, if your Wild Apricot website is `https://kendra76548.wildapricot.org/`, then the first `src` tag would become `https://kendra76548.wildapricot.org/widget/Sys/profile` and the second `src` tag would be `https://kendra76548.wildapricot.org/Common/EnableCookies.js`.

```
<!-- wp:html -->
<p><iframe src="https://members-digitalnovascotia.wildapricot.org/widget/Sys/profile" width="1250px" height="600px" frameborder="no">
</iframe></p>
<p><script type="text/javascript" language="javascript" src="https://members-digitalnovascotia.wildapricot.org/Common/EnableCookies.js">
</script></p>
<!-- /wp:html -->
```

Future: This Wild Apricot content will be directly accessed through the API and the plugin will allow customization of pages like these.

## Putting Login and Member Profile Buttons in a Menu

### Configure Menu

This example is assuming putting the login button in the top corner of the page. This is not the only option, and can be customized. In this example, the login and profile buttons will be place in a Top Level (secondary) menu. 

In the left menu select Appearance > Menus. Under the Edit Menus tab on that page, select a menu to edit, or create a new menu. This example menu will be called Top.

Using ‘Add Menu Items’ drag and drop two ‘Custom Links’. Configure the links as shown:

<img src="https://user-images.githubusercontent.com/8716690/119713801-f8f07c00-be16-11eb-9d6b-bd63ba303c7c.jpg" alt="shortcodeincustomlinks" width="400"/>

For your convenience:

#### Login

URL: ```[wa_login login_label="Member Login" logout_label="Logout" redirect_page="/"]```

Navigation Label: ```FULL HTML OUTPUT```

CSS Classes (optional): ```wa-login```


The URL field contains a custom link for displaying a login button. The login and logout labels can be anything. The redirect page is where the user is directed after logging in, "/" will keep them on the same page. The navigation label must be FULL HTML OUTPUT for the shortcode to work.

#### Profile

URL: ```/member-profile/```

Navigation Label: ```Your Profile```

CSS Classes (optional): ```profile```

All of these fields can be modified. This URL field goes to a page with the Wild Apricot member profile widget on it, this value can be anything. The Navigation Label is the text that is displayed for this menu item.

<br>
Note: If you cannot see the CSS Classes field, scroll to the top of the page, click the Screen Options drop down, and under Show advanced menu properties, make sure the CSS Classes box is checked. See the section *Showing Member-only Menus* for more detailed instructions.

You will now need to make sure that this header is displayed on your site. This can vary theme to theme. On the Menus page, and under the Edit Menus tab, scroll down to the Menu Settings options, and make sure that the correct display location is checked, in this case Top Navigation.

On the same page, switch to the Manage Locations tab and make sure your menu is assigned to the correct Theme Location.

For Avada, there are additional steps required. In hopes this is applicable to another theme, in the left menu, navigate to Avada > Options. Select Header. Make sure you are using a header layout that includes a secondary menu. In the Header Content 2 field, select that Navigation be displayed. 

### Updating Menu Button CSS

The default login display is a basic grey rectangle. If you want to change this, you will need to modify your theme CSS. 

It is recommended that instead of directly modifying your theme CSS, you instea create a child theme and modify the CSS there to ensure this code is preserved if your theme updates. 

However, the CSS may be edited directly by going to Appearance > Theme Editor, and selecting your theme. Modify the code on the style.css code, noting that code higher in the file takes priority.

Note that for Avada, this didn't work, and to modify the CSS you instead go to  Avada > Options > Custom CSS and put the code there in the visual editor. 

Below is example CSS code to improve the plugin, this can be used as a starting point for your modifications.

```
/*Secondary (top) Navigation*/
body.logged-in .secondary-menu i.icon-user.remove{display: none;}
body:not(.logged-in) .fusion-secondary-menu li.profile {
    display: none !important;
}

.fusion-secondary-menu i {font-size: 14px; margin-right:5px;}
.fusion-secondary-menu li .wa_login_shortcode form input[type="submit"] {background: none; color:#333; font-size: 14px; border:none; height:44px; padding:0 12px;}
.fusion-secondary-menu li .wa_login_shortcode form p {display: none;}
.fusion-secondary-menu li .wa_login_shortcode form input[type="submit"]:hover {color: #000; cursor: pointer;}

.fusion-secondary-menu li.wa-login {padding-left:20px;}
.fusion-secondary-menu li .wa_login_shortcode:before {font-family: "Font Awesome 5 Free";
    font-weight: 900;  content: "\f2f6"; position: absolute; left:7px; top:14px;}

/*.secondary-menu li.wa-login:before {content:'\e80d'; font-family: "fontello"; display: inline-block; width: 25px; height: 25px;}*/
.fusion-secondary-menu i.icon-user {color: #eee; margin-right: -17px;}
.fusion-secondary-menu i.icon-user:before {color:#eee;}
.fusion-secondary-menu i.icon-user:hover:before {color: #fff;}


.fusion-secondary-menu li.profile i.icon-user {margin-right:3px; display: inline-block}
body:not(.logged-in) .secondary-menu li.profile {display: none !important;}

.fusion-secondary-menu li span.dashicons {margin-top:10px; margin-right:4px;}

.fusion-secondary-menu>ul>li>a {font-weight: 400;}
.fusion-secondary-menu>ul>li {margin: 0 15px; border:none !important;}

.fusion-secondary-menu li:after {content: " ";
    position: absolute;
    top: calc(50% + 1em);
    left: 0;
    width: 100%;
    border-top-style: solid;
    transform: scaleX(0);
    transition: transform .2s ease-in-out;
	color:#235ba8;}

.fusion-secondary-menu li:hover:after {transform: scaleX(1);}
```

## Membership Level Sync

The membership levels that have been added, modified or deleted will be synced into WordPress automatically. During each member login, the membership metadata (eg status and membership level) will be updated.

## Uninstalling

To uninstall the plugin, navigate to Plugins > Installed Plugins. Search for WA4WP, then deactivate and delete it. This should also remove any dependent plugins (Wild Apricot Login, Advanced Custom Fields, and Short Code in Menus) that were not installed before the WA4WP plugin was installed. If the dependent plugins are not removed, they can be deactivated and deleted manually. 

***

# Member's Guide

## Logging In

As a Wild Apricot user you can login using the login menu item in the top right corner of the page. Once you click login you will be presented with a Wild Apricot login page.

Step 1

<img src="https://user-images.githubusercontent.com/458134/110494221-62d37200-80c1-11eb-8625-03ee6ed4b41d.png" alt="login" width="800"/> 

Step 2 Type your Wild Apricot login credentials here

<img src="https://user-images.githubusercontent.com/458134/110494160-4fc0a200-80c1-11eb-9689-3e38e6a445c7.png" alt="credentials" width="800"/> 

Step 3 If you are logged in successfully you will see a Your Profile link to the right of Logout
![image16](https://user-images.githubusercontent.com/458134/110494118-46373a00-80c1-11eb-9738-7f016405347b.png)

## Accessing Your Profile

Click Your Profile button to view your Wild Apricot Profile. You will have full access to your profile from this page including editing functions.

***


# WA4WP - Add On
Wild Apricot for WordPress - Custom Directory Plugin

This plugin makes it easy to integrate Wild Apricot member directories into a WordPress site.

# Version Control
- v0.10.6 - Fix search bar bug when using php 7.4
- v0.10.5 - initial version
