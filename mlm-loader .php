<?php

session_start();
/*
  Plugin Name: Binary MLM Pro
  Plugin URI: http://tradebooster.com
  Description: The only Binary MLM plugin for Wordpress. Run a full blown MLM website from within your favourite CMS.
  Version: 2.9
  Author: Tradebooster
  Author URI: http://tradebooster.com
  License: GPL
 */

// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;

/** Constants **************************************************************** */
global $wpdb;

// Path and URL
if (!defined('MLM_PLUGIN_DIR'))
    define('MLM_PLUGIN_DIR', WP_PLUGIN_DIR . '/binary-mlm-pro');

if (!defined('MLM_PLUGIN_NAME'))
    define('MLM_PLUGIN_NAME', 'binary-mlm-pro');

define('MLM_URL', plugins_url('', __FILE__));

if (!defined('MYPLUGIN_VERSION_KEY'))
    define('MYPLUGIN_VERSION_KEY', 'myplugin_version');
if (!defined('MYPLUGIN_VERSION_NUM'))
    define('MYPLUGIN_VERSION_NUM', '2.0');
add_option(MYPLUGIN_VERSION_KEY, MYPLUGIN_VERSION_NUM);

//include the the core funcitons file

require_once(MLM_PLUGIN_DIR . '/mlm-constant.php');

//this file create or update database schema
require_once(MLM_PLUGIN_DIR . '/mlm_core/mlm-core-schema.php');

//include the html functions file
//this file create the registration form
require_once(MLM_PLUGIN_DIR . '/mlm_html/mlm-registration-page.php');

//this file contain the binary network building code
require_once(MLM_PLUGIN_DIR . '/mlm_html/mlm-view-network.php');

//this file contatain the overview of network sales like left, right and personal
require_once(MLM_PLUGIN_DIR . '/mlm_html/mlm-network-details.php');

//this file contaian the left leg sales
require_once(MLM_PLUGIN_DIR . '/mlm_html/mlm-left-group-details.php');

//this file contaian the right leg sales
require_once(MLM_PLUGIN_DIR . '/mlm_html/mlm-right-group-details.php');

//this file contaian the personal or direct sales
require_once(MLM_PLUGIN_DIR . '/mlm_html/mlm-personal-group-details.php');

//this file contain the grand total sales
require_once(MLM_PLUGIN_DIR . '/mlm_html/mlm-total-sales.php');

//this file contain the payouts
require_once(MLM_PLUGIN_DIR . '/mlm_html/mlm-my-payout-page.php');

//this file contain the payouts
require_once(MLM_PLUGIN_DIR . '/mlm_html/mlm-my-payout-details-page.php');

//this file contain the po editor
require_once(MLM_PLUGIN_DIR . '/mlm_html/mlm-po-file-editor.php');

//this file contatin the common functions which is used in other files
require_once(MLM_PLUGIN_DIR . '/common-functions.php');

require_once(MLM_PLUGIN_DIR . '/user-switching.php');

//this is admin file that contain the creation of the top user of the network
require_once(MLM_PLUGIN_DIR . '/mlm_html/admin-create-first-user.php');

//in this file admin setup the mlm plugin settings
require_once(MLM_PLUGIN_DIR . '/mlm_html/admin-mlm-settings.php');

//in this file payout will be run
require_once( MLM_PLUGIN_DIR . '/mlm_html/admin-pay-cycle.php' );

//this file contain the user updadation profile
require_once(MLM_PLUGIN_DIR . '/mlm_html/mlm-update-profile.php');

//this file user can reset own login passowrd
require_once(MLM_PLUGIN_DIR . '/mlm_html/mlm-change-password.php');

//in this file admin can change the user's profile details
require_once(MLM_PLUGIN_DIR . '/mlm_html/admin-user-update-profile.php');

require_once(MLM_PLUGIN_DIR . '/mlm_html/admin-user-account.php');
require_once(MLM_PLUGIN_DIR . '/mlm_html/admin-view-user-network.php');
require_once(MLM_PLUGIN_DIR . '/mlm_html/admin-member-license-setting.php');

////in this file withdrawals will be run
require_once( MLM_PLUGIN_DIR . '/mlm_html/admin-mlm-pending-withdrawal.php' );
require_once( MLM_PLUGIN_DIR . '/mlm_html/admin-mlm-withdrawal-process.php' );
require_once( MLM_PLUGIN_DIR . '/mlm_html/admin-mlm-sucessed-withdrawal.php' );
require_once( MLM_PLUGIN_DIR . '/mlm_html/admin-mlm-epins-reports.php' );
require_once( MLM_PLUGIN_DIR . '/mlm_html/mlm-epin-update.php');
require_once( MLM_PLUGIN_DIR . '/mlm_html/admin-reports.php');
require_once( MLM_PLUGIN_DIR . '/mlm_html/admin-mlm-payout-reports.php');
require_once( MLM_PLUGIN_DIR . '/mlm_html/mlm-join-network.php');


//HOOK INTO WORDPRESS
add_action('init', 'register_shortcodes');

/* Runs
  when plugin is activated */
register_activation_hook(__FILE__, 'mlm_install');

function mlm_install()
{

    mlm_core_install_users();
    mlm_core_install_leftleg();
    mlm_core_install_rightleg();
    mlm_core_install_country();
    mlm_core_insert_into_country();
    mlm_core_install_currency();
    mlm_core_insert_into_currency();
    mlm_core_install_bonus();
    mlm_core_install_commission();
    mlm_core_install_payout_master();
    mlm_core_install_payout();
    //mlm_core_install_epins();
    mlm_core_install_refe_comm();
    mlm_core_update_payout();
    mlm_core_update_payout_master();
    net_amount_payout();
}

//this code add the registration page
//1st agru is the TITLE & second is CONTENT
add_action('init', 'createPages');

function createPages()
{
    $post_id = register_page(MLM_REGISTRATION_TITLE, MLM_REGISTRATIN_SHORTCODE);

    //if post is successully inserted then post_id inserted into wp_postmeta table
    if ($post_id != 0)
        update_post_meta($post_id, 'mlm_registration_page', 'mlm_registration_page');

    //this code add the view binary network page

    $post_id = register_page(MLM_VIEW_NETWORK_TITLE, MLM_VIEW_NETWORK_SHORTCODE);

    //if post is successully inserted then post_id inserted into wp_postmeta table
    if ($post_id != 0)
    {
        update_post_meta($post_id, 'mlm_network_page', 'mlm_network_page');
        update_post_meta($post_id, '_mlm_is_members_only', 'true');
    }

    //this code add the view binary network genealogy

    $post_id = register_page(MLM_VIEW_GENEALOGY_TITLE, MLM_VIEW_GENEALOGY_SHORTCODE);

    //if post is successully inserted then post_id inserted into wp_postmeta table
    if ($post_id != 0)
    {
        update_post_meta($post_id, 'mlm_network_genealogy_page', 'mlm_network_genealogy_page');
        update_post_meta($post_id, '_mlm_is_members_only', 'true');
    }


    //this code add the network details page

    $post_id = register_page(MLM_NETWORK_DETAILS_TITLE, MLM_NETWORK_DETAILS_SHORTCODE);

    //if post is successully inserted then post_id inserted into wp_postmeta table
    if ($post_id != 0)
    {
        update_post_meta($post_id, 'mlm_network_details_page', 'mlm_network_details_page');
        update_post_meta($post_id, '_mlm_is_members_only', 'true');
    }

    //this code add the my left group details page

    $post_id = register_page(MLM_LEFT_GROUP_DETAILS_TITLE, MLM_LEFT_GROUP_DETAILS_SHORTCODE);

    //if post is successully inserted then post_id inserted into wp_postmeta table
    if ($post_id != 0)
    {
        update_post_meta($post_id, 'mlm_left_group_details_page', 'mlm_left_group_details_page');
        update_post_meta($post_id, '_mlm_is_members_only', 'true');
    }

    //this code add the my right group details page

    $post_id = register_page(MLM_RIGHT_GROUP_DETAILS_TITLE, MLM_RIGHT_GROUP_DETAILS_SHORTCODE);

    //if post is successully inserted then post_id inserted into wp_postmeta table
    if ($post_id != 0)
    {
        update_post_meta($post_id, 'mlm_right_group_details_page', 'mlm_right_group_details_page');
        update_post_meta($post_id, '_mlm_is_members_only', 'true');
    }

    //this code add the my right group details page

    $post_id = register_page(MLM_PERSONAL_GROUP_DETAILS_TITLE, MLM_PERSONAL_GROUP_DETAILS_SHORTCODE);

    //if post is successully inserted then post_id inserted into wp_postmeta table
    if ($post_id != 0)
    {
        update_post_meta($post_id, 'mlm_personal_group_details_page', 'mlm_personal_group_details_page');
        update_post_meta($post_id, '_mlm_is_members_only', 'true');
    }

    //this code add the my total consultant details page

    $post_id = register_page(MLM_MY_CONSULTANT_TITLE, MLM_MY_CONSULTANT_SHORTCODE);

    //if post is successully inserted then post_id inserted into wp_postmeta table
    if ($post_id != 0)
    {
        update_post_meta($post_id, 'mlm_consultant_details_page', 'mlm_consultant_details_page');
        update_post_meta($post_id, '_mlm_is_members_only', 'true');
    }

    //this code add the my total consultant details page

    $post_id = register_page(MLM_MY_PAYOUTS, MLM_MY_PAYOUTS_SHORTCODE);

    //if post is successully inserted then post_id inserted into wp_postmeta table
    if ($post_id != 0)
    {
        update_post_meta($post_id, 'mlm_my_payout_page', 'mlm_my_payout_page');
        update_post_meta($post_id, '_mlm_is_members_only', 'true');
    }


    //this code add the my total consultant details page

    $post_id = register_page(MLM_MY_PAYOUT_DETAILS, MLM_MY_PAYOUT_DETAILS_SHORTCODE);

    //if post is successully inserted then post_id inserted into wp_postmeta table
    if ($post_id != 0)
    {
        update_post_meta($post_id, 'mlm_my_payout_details_page', 'mlm_my_payout_details_page');
        update_post_meta($post_id, '_mlm_is_members_only', 'true');
    }



    //this code add the update profile page

    /* $post_id = register_page(MLM_UPDATE_PROFILE_TITLE, MLM_UPDATE_PROFILE_SHORTCODE);

      //if post is successully inserted then post_id inserted into wp_postmeta table
      if ($post_id != 0)
      {
      update_post_meta($post_id, 'mlm_update_profile_page', 'mlm_update_profile_page');
      update_post_meta($post_id, '_mlm_is_members_only', 'true');
      } */

    //this code add the change password page

    $post_id = register_page(MLM_CHANGE_PASSWORD_TITLE, MLM_CHANGE_PASSWORD_SHORTCODE);

    //if post is successully inserted then post_id inserted into wp_postmeta table
    if ($post_id != 0)
    {
        update_post_meta($post_id, 'mlm_change_password_page', 'mlm_change_password_page');
        update_post_meta($post_id, '_mlm_is_members_only', 'true');
    }

    /*     * ************ this block code for distributing bonus and payout **************** */
    /* $post_id = register_page(MLM_DISTRIBUTE_COMMISSION_TITLE, MLM_DISTRIBUTE_COMMISSION_SHORTCODE);

      //if post is successully inserted then post_id inserted into wp_postmeta table
      if($post_id!=0)
      {
      update_post_meta($post_id, 'mlm_distribute_commission', 'mlm_distribute_commission');
      update_post_meta($post_id, '_mlm_is_members_only', 'true');
      update_post_meta($post_id, '_mlm_restricted_to', 'administrator');
      } */

    /* 	$post_id = register_page(MLM_DISTRIBUTE_BONUS_TITLE, MLM_DISTRIBUTE_BONUS_SHORTCODE);

      //if post is successully inserted then post_id inserted into wp_postmeta table
      if($post_id!=0)
      {
      update_post_meta($post_id, 'mlm_distribute_bonus', 'mlm_distribute_bonus');
      update_post_meta($post_id, '_mlm_is_members_only', 'true');
      update_post_meta($post_id, '_mlm_restricted_to', 'administrator');
      } */
    /*     * ************ end code for distributing bonus and payout **************** */
    $post_id = register_page(MLM_EPIN_UPDATE_TITLE, MLM_EPIN_UPDATE_SHORTCODE);

    //if post is successully inserted then post_id inserted into wp_postmeta table
    if ($post_id != 0)
    {
        update_post_meta($post_id, 'mlm_epin_update_page', 'mlm_epin_update_page');
        update_post_meta($post_id, '_mlm_is_members_only', 'true');
    }
    $post_id = register_page(JOIN_NETWORK, JOIN_NETWORK_SHORTCODE);

    if ($post_id != 0)
    {
        update_post_meta($post_id, 'join_network', 'join_network');
        update_post_meta($post_id, '_mlm_is_members_only', 'true');
    }
}

//shows custom message after plugin activation
add_action('admin_notices', 'show_message_after_plugin_activation');

/* Runs wher plugin is Uninstall */
register_uninstall_hook(__FILE__, 'mlm_remove');

function mlm_remove()
{
    mlm_core_drop_tables();
    //delete the data from wp_options table
    delete_option('wp_mlm_general_settings');
    delete_option('wp_mlm_eligibility_settings');
    delete_option('wp_mlm_payout_settings');
    delete_option('wp_mlm_bonus_settings');
    delete_option('menu_check');
    $theme_slug = get_option('stylesheet');
    delete_option("theme_mods_$theme_slug");
    //delete the menu name form wp_terms table
}

/* Runs wher plugin is deactivated */
register_deactivation_hook(__FILE__, 'mlm_deactivate');

function mlm_deactivate()
{
    //delete post from wp_posts and wp_postmeta table
    //$mlmPages contain the meta_key of the created mlm plugin pages
    $mlmPages = array('mlm_registration_page',
        'mlm_network_page',
        'mlm_network_genealogy_page',
        'mlm_network_details_page',
        'mlm_left_group_details_page',
        'mlm_right_group_details_page',
        'mlm_personal_group_details_page',
        'mlm_consultant_details_page',
        'mlm_my_payout_page',
        'mlm_my_payout_details_page',
        'mlm_update_profile_page',
        'mlm_change_password_page',
        'mlm_distribute_bonus',
        'mlm_distribute_commission',
        'mlm_epin_update_page',
        'join_network'
    );
//delete post from wp_posts and wp_postmeta table
    foreach ($mlmPages as $value)
    {
        $post_id = get_post_id($value);
        wp_delete_post($post_id, true);
    }
    delete_option('menu_check');
    $term = get_term_by('name', MENU_NAME, 'nav_menu');
    wp_delete_term($term->term_id, 'nav_menu');
}

if (is_admin())
{
    /* Call the html code */
    add_action('admin_menu', 'mlm_admin_menu');
}

require_once('mlm-access-control.php');

// this action shows on the admin section registration form's custom fields and edit
//add_action('show_user_profile', 'my_show_extra_profile_fields'); // shows on user interface
//add_action('edit_user_profile', 'my_show_extra_profile_fields'); // shows on admin interace
//add_action('personal_options_update', 'my_save_extra_profile_fields'); //apply on user interface
//add_action('edit_user_profile_update', 'my_save_extra_profile_fields'); //apply on admin interface
//create nav menu and its item


/* Array */
$mlm_settings = get_option('wp_mlm_general_settings');
$paymenntStatusArr = array(0 => 'Unpaid', 1 => 'Paid');
if (isset($mlm_settings['ePin_activate']) && $mlm_settings['ePin_activate'] == '1')
{
    $paymenntStatusArr[2] = 'Free Pin';
}

add_action('init', 'load_javascript');

function fb_redirect_2()
{
    global $current_user;
    $user_roles = $current_user->roles;
    $user_role = array_shift($user_roles);

    $post_id = get_post_id('mlm_network_details_page');
    if ($user_role == 'subscriber' && $_SESSION['ajax'] != 'ajax_check')
    {
        //if ( preg_match('#wp-admin/?(index.php)?$#', $_SERVER['REQUEST_URI']) ) 
        {
            if (function_exists('admin_url'))
            {
                wp_redirect(get_option('siteurl') . "/?page_id=$post_id");
            }
            else
            {
                wp_redirect(get_option('siteurl'));
            }
        }
    }
}

//add_action('init', 'fb_redirect_2');



add_filter("login_redirect", "mlm_login_redirect", 10, 3);

function mlm_login_redirect($redirect_to, $request, $user)
{
    //is there a user to check?
    if (!empty($user->roles))
    {
        if (is_array($user->roles))
        {
            //check for admins
            if (in_array("administrator", $user->roles))
            {
                // redirect them to the default place
                return admin_url();
            }
            else
            {
                return home_url();
            }
        }
    }
}

$run_once = get_option('menu_check');
if (!$run_once)
    add_action('init', 'createTheMlmMenu');



add_action('wp_logout', 'logout_session');

function logout_session()
{
    unset($_SESSION['search_user']);
    unset($_SESSION['session_set']);
    unset($_SESSION['userID']);
    unset($_SESSION['ajax']);
}

add_action('plugins_loaded', 'myplugin_load_textdomain');

function myplugin_load_textdomain()
{
    load_plugin_textdomain('binary-mlm-pro', NULL, '/binary-mlm-pro/languages/');
}

$new_version = '2.9';
if (get_option(MYPLUGIN_VERSION_KEY) != $new_version)
{
    add_action('plugins_loaded', 'mlm_core_update_mlm_user_master');
    add_action('plugins_loaded', 'mlm_core_install_epins');
    add_action('plugins_loaded', 'mlm_core_update_mlm_user_for_product_price');
    add_action('plugins_loaded', 'mlm_core_update_mlm_leftleg');
    add_action('plugins_loaded', 'mlm_core_update_mlm_rightleg');
    update_option(MYPLUGIN_VERSION_KEY, $new_version);
}
//
//if (get_option(MYPLUGIN_VERSION_KEY) == $new_version) {
else
{
    add_action('plugins_loaded', 'mlm_core_update_mlm_user_master');
    add_action('plugins_loaded', 'mlm_core_install_epins');
    add_action('plugins_loaded', 'mlm_core_update_mlm_user_for_product_price');
    add_action('plugins_loaded', 'mlm_core_update_mlm_leftleg');
    add_action('plugins_loaded', 'mlm_core_update_mlm_rightleg');
    update_option(MYPLUGIN_VERSION_KEY, $new_version);
}

add_action('plugins_loaded', 'mlm_core_install_epins');


github_plugin_updater_mlm_init(array(
    'slug' => plugin_basename(__FILE__),
    'proper_folder_name' => 'binary-mlm-pro',
    'api_url' => 'https://api.github.com/repos/tradebooster/binary-mlm-pro',
    'raw_url' => 'https://raw.github.com/tradebooster/binary-mlm-pro/master',
    'github_url' => 'https://github.com/tradebooster/binary-mlm-pro',
    'zip_url' => 'https://github.com/tradebooster/binary-mlm-pro/archive/master.zip',
    'sslverify' => true,
    'requires' => '3.2',
    'tested' => '3.8',
    'readme' => 'README.md',
        //'access_token' => '6b584e191bbc843976f08c2db984b2dedb2ab58a',
));


add_role('mlm_user', __('MLM User'));

add_action('init', 'update_user_role');

function update_user_role()
{
    global $wpdb, $table_prefix;
    $results = $wpdb->get_results("select user_id from {$table_prefix}mlm_users");
    foreach ($results as $result)
    {
        wp_update_user(array
            (
            'ID' => $result->user_id,
            'role' => 'mlm_user'
        ));
    }
}

?>
