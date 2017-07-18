<?php
/**
 * Plugin Name: Advanced Menu Manager
 * Plugin URI: http://www.multidots.com/
 * Description: Very Simple, easy, admin-friendly menu manege with this plugin which can be used for the customize menus like edit, sorting, delete, Add new menu etc.
 * Author: Multidots
 * Author URI: http://www.multidots.com/
 * Version: 1.6
 *
 * Copyright: (c) 2014-2015 Multidots Solutions PVT LTD (info@multidots.com)
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @author    Multidots 
 * @category  Plugin
 * @copyright Copyright (c) 2015-2016 Multidots Solutions Pvt. Ltd.
 * @license   



  /**
 *
 * If this file is called directly, abort.
 *
 * @version		1.0.0
 * @author 		Multidots
 */
if (!defined('WPINC')) {
    die;
}


/**
 * prevent direct access data leaks
 *
 * This is the condition to prevent direct access data leaks.
 *
 * @version		1.0.0
 * @author 		Multidots
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

define('PLUGIN_NAME', 'Advanced Menu Manager');
define('PLUGIN_TITLE_NAME', 'Menu Manager');
define('PLUGIN_SLUG', 'advance-menu-manager');
define('PLUGIN_BASE_PATH', WP_PLUGIN_URL . "/advance-menu-manager/includes/image/");
define('PLUGIN_PATH', WP_PLUGIN_URL . "/advance-menu-manager/includes/");

/**
 * plugin_activation function
 *
 * On activation, admin page interface will be loaded.
 *
 * @version		1.0.0
 * @author 		Multidots
 */
function plugin_activation() {
    require_once plugin_dir_path(__FILE__) . 'includes/classes/Class_Activator.php';
    global $wpdb;
    set_transient('_welcome_screen_advance_menu_manager_activation_redirect_data', true, 30);
}

register_activation_hook(__FILE__, 'plugin_activation');
add_action('admin_init', 'welcome_advance_menu_manager_screen_do_activation_redirect');
add_action('admin_menu', 'welcome_pages_screen_advance_menu_manager');

add_action('admin_menu', 'dot_store_menu_advance_menu_manager');


add_action('advance_menu_manager_other_plugins', 'advance_menu_manager_other_plugins');
add_action('advance_menu_manager_about', 'advance_menu_manager_about');
add_action('advance_menu_manager_premium_feauter', 'advance_menu_manager_premium_feauter');
add_action('admin_print_footer_scripts', 'advance_menu_manager_pointers_footer');
add_action('admin_menu', 'welcome_screen_advance_menu_manager_remove_menus', 999);

function my_enqueue($hook) {
    wp_enqueue_script('wp-pointer');
}

add_action('admin_enqueue_scripts', 'my_enqueue');

function welcome_advance_menu_manager_screen_do_activation_redirect() {
    if (!get_transient('_welcome_screen_advance_menu_manager_activation_redirect_data')) {
        return;
    }

    // Delete the redirect transient
    delete_transient('_welcome_screen_advance_menu_manager_activation_redirect_data');

    // if activating from network, or bulk
    if (is_network_admin() || isset($_GET['activate-multi'])) {
        return;
    }
    // Redirect to extra cost welcome  page
    wp_safe_redirect(add_query_arg(array('page' => 'advance-menu-manager-lite&tab=menu_advance_manager_get_started_method'), admin_url('admin.php')));
}

function welcome_pages_screen_advance_menu_manager() {
    /* add_dashboard_page(
      'Advance Menu Manager Dashboard', 'Advance Menu Manager  Dashboard', 'read', 'advance-menu-manager-lite',  'welcome_screen_content_advance_menu_manager'
      ); */
}

// dots stor landing page

function dot_store_menu_advance_menu_manager() {
    global $GLOBALS;
    if (empty($GLOBALS['admin_page_hooks']['dots_store'])) {
        add_menu_page(
                __('DotStore Plugins', 'advance-menu-manager'), 'DotStore Plugins', 'NULL', 'dots_store', 'dot_store_advance_menu', plugin_dir_url(__FILE__) . 'images/menu-icon.png', 6
        );
    }

    add_submenu_page("dots_store", "Advanced Menu Manager", "Advanced Menu Manager", "manage_options", "advance-menu-manager-lite", 'custom_advance_submenu_extra', "", 99);
}

// custom submenu for extra flate rate shipping 


function custom_advance_submenu_extra() {
    $url = site_url('wp-admin/admin.php?page=advance-menu-manager-lite&tab=menu-manager-add&section=menu-add');
    $active_tab = "menu-manager-add";
    if (!empty($_GET["tab"])) {


        if ($_GET["tab"] == "menu-manager-add") {
            dot_store_advance_menu_manager();
        }

        if ($_GET["tab"] == "menu_advance_manager_premium_method") {
            menu_advance_manager_premium_method_function();
        }
        if ($_GET['tab'] == 'menu_advance_manager_get_started_method') {
            menu_advance_manager_get_started_method_function();
        }
        if ($_GET['tab'] == 'menu_advance_manager_dotstore_contact_support_method') {
            menu_advance_manager_dotstore_contact_support_method_function();
        }
        if ($_GET['tab'] == 'dotstore_introduction_menu_advance_manager') {
            dotstore_introduction_menu_advance_manager_function();
        }
    } else {
        ?>
        <script>location.href = '<?php echo $url; ?>';</script>
    <?php }
    ?>


    <?php
}

// advance manu manager premium rate function

function menu_advance_manager_premium_method_function() {
    advance_menu_manager_admin_menu_side_header_part();

    menu_advance_manager_premium_method();
}

// advance manu manager Getting started

function menu_advance_manager_get_started_method_function() {
    advance_menu_manager_admin_menu_side_header_part();
    $current_user = wp_get_current_user();
    if (!get_option('amm_free_plugin_notice_shown')) {
        ?>
        <div id="amm_free_dialog">
            <p><?php _e('Be the first to get latest updates and exclusive content straight to your email inbox.'); ?></p>
            <p><input type="text" id="txt_user_sub_amm" class="regular-text" name="txt_user_sub_amm" value="<?php echo $current_user->user_email; ?>"></p>
        </div>
    <?php } ?>                       
    <div class="flat-table res-cl left-container">
        <h2>Thanks For Installing Advanced Menu Manager</h2>
        <table class="table-outer">
            <tbody>
                <tr>
                    <td class="fr-2">
                        <p class="block gettingstarted"><strong>Getting Started </strong></p>
                        <p class="block textgetting">
                            <span>Advanced Menu Manager Plugins make it simpler for you and your team to effectively create and manage menus for content- heavy blogs and website.</span>
                        </p>
                        <p class="block textgetting">Create Menu and easy manage as per your needs</p>
                        <p class="block textgetting">
                            <span><strong>Step 1 :</strong> Create a menu by <a href="<?php echo site_url(); ?>/wp-admin/admin.php?page=advance-menu-manager-lite&tab=menu-manager-add&section=menu-add">"create a new menu"</a> link as per your needs. You can give a name to your menu as shown in the image. Click on the Save Menu option to save your menu.</span>
                            <span class="gettingstarted">
                                <img style="border: 2px solid #e9e9e9;margin-top: 3%;" src="<?php echo plugin_dir_url(__FILE__) . 'images/1-amm.png'; ?>">										
                            </span>
                        </p>
                        <p class="block gettingstarted textgetting">
                            <span class="spacing"><strong>Step 2 :</strong> Start adding new menu items : Use the Green + sign as shown in the image below to start adding items to your menu.</span>
                            <span class="gettingstarted">
                                <img style="border: 2px solid #e9e9e9;margin-top: 3%;" src="<?php echo plugin_dir_url(__FILE__) . 'images/2-AMM .png'; ?>">
                            </span>
                        </p>

                        <p class="block gettingstarted textgetting">
                            <span class="spacing"><strong>Step 3 :</strong> Add pages/posts : With this screen, it will provide complete details about the pages, posts, categories, tags, format and picture tag. Once you get to this step, you have the complete picture of your pages, the item id, title, item slug, author and publish date too. This is quite handy as you get all the details in one single interface. No going back and forth to check stuff.</span>
                            <span class="gettingstarted">
                                <img style="border: 2px solid #e9e9e9;margin-top: 3%;" src="<?php echo plugin_dir_url(__FILE__) . 'images/03- AMM.png'; ?>">
                            </span>
                        </p>

                        <p class="block gettingstarted textgetting">
                            <strong><a href="https://store.multidots.com/docs/plugin/advanced-menu-manager/" target="_blank">Learn more about other plugin features</a></strong>

                        </p>


                    </td>
                </tr>
            </tbody>
        </table>
    </div>


    <?php
    dotstore_advance_menu_manager_left_side_menu();
}

// advance manu manager support and contact function

function menu_advance_manager_dotstore_contact_support_method() {
    advance_menu_manager_admin_menu_side_header_part();

    menu_advance_manager_premium_method();

    dotstore_advance_menu_manager_left_side_menu();
}

// advance manu manager premium rate function

function dotstore_introduction_menu_advance_manager_function() {
    advance_menu_manager_admin_menu_side_header_part();
    ?>
    <style>
        .flat-table table.table-outer tr td {
            border: 1px solid #ddd;
        }
    </style>

    <div class="flat-table res-cl left-container">
        <h2>Quick info</h2>
        <table class="table-outer">
            <tbody>
                <tr>
                    <td class="fr-1">Product Type</td>
                    <td class="fr-2">WordPress Plugin</td>
                </tr>
                <tr>
                    <td class="fr-1">Product Name</td>
                    <td class="fr-2">Advanced Menu Manager</td>
                </tr>
                <tr>
                    <td class="fr-1">Installed Version</td>
                    <td class="fr-2"> 1.6</td>
                </tr>
                <tr>
                    <td class="fr-1">License &amp; Terms of use</td>
                    <td class="fr-2"> <a target="_blank" href=" https://store.multidots.com/terms-conditions/">Click here</a> to view license and terms of use.</td>
                </tr>
                <tr>
                    <td class="fr-1">Help &amp; Support</td>
                    <td class="fr-2">
                        <ul style="margin-left: 15px !important;list-style: inherit; ">
                            <li><a target="_blank" href="<?php echo site_url(); ?>/wp-admin/admin.php?page=advance-menu-manager-lite&tab=menu_advance_manager_get_started_method"> Quick Start Guide</a></li>
                            <li><a target="_blank" href="https://store.multidots.com/docs/plugin/advanced-menu-manager/">Documentation</a></li> 
                            <li>	<a target="_blank" href="https://store.multidots.com/dotstore-support-panel/ "> Support Forum</a></li>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <td class="fr-1">Localization</td>
                    <td class="fr-2">English ,Spanish</td>
                </tr>

            </tbody>
        </table>
    </div>

    <?php
    dotstore_advance_menu_manager_left_side_menu();
}

// dots store function

function dot_store_advance_menu_manager() {
    //require_once dirname(__FILE__) . '/includes/admin/Admin.php';

    require_once( ABSPATH . 'wp-admin/includes/nav-menu.php' );

    $admin_interface = new md_admin_interface();

    advance_menu_manager_admin_menu_side_header_part();
    ?>

    <div class="flat-table res-cl adv-menu-manager-main left-container">

        <div class="">
            <!-- menu content start --> 
            <?php
            if ($_GET["tab"] == "menu-manager-add") {

                require_once dirname(__FILE__) . '/includes/admin/Admin.php';
            }
            ?>

            <!-- Menu content -->					

        </div>		
    </div>

    <?php dotstore_advance_menu_manager_left_side_menu(); ?>
    </div>
    </div>

    </body>
    </html>

    <?php
}

function advance_menu_manager_admin_menu_side_header_part() {
    $plugin_name = "Advanced Menu Manager";
    $plugin_version = "1.6";
    ?>

    <html>
        <head>
            <meta charset="UTF-8" />
            <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1, maximum-scale=1">

            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css">
            <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i" rel="stylesheet">
            <link rel="stylesheet" type="text/css" href="<?php echo plugin_dir_url(__FILE__) . 'includes/admin/css/webkit.css'; ?>">
            <link rel="stylesheet" type="text/css" href="<?php echo plugin_dir_url(__FILE__) . 'includes/admin/css/style.css'; ?>">
            <link rel="stylesheet" type="text/css" href="<?php echo plugin_dir_url(__FILE__) . 'includes/admin/css/media.css'; ?>">
            <script type="text/javascript" src="<?php echo PLUGIN_PATH . 'js/custom.js'; ?>"></script>
        </head>

        <body>
            <div id="main">
                <div class="all-pad main-container">
                    <header>
                        <div class="logo-main">
                            <img  src="<?php echo plugin_dir_url(__FILE__) . 'images/amm-logo.png'; ?>" width="    width: 70px;">
                        </div>
                        <div class="header-right">
                            <div class="logo-detail">
                                <strong><?php echo $plugin_name; ?></strong>
                                <span> Free Version <?php echo $plugin_version; ?></span>


                            </div>
                            <div class="button-dots">
                                <span class="support_dotstore_image"><a href="https://store.multidots.com/advance-menu-manager-wordpress/" target="_blank"><img  src="<?php echo plugin_dir_url(__FILE__) . 'includes/admin/images/upgrade_new.png'; ?>"> </a></span>
                                <span class="support_dotstore_image"><a  target="_blank" href="https://store.multidots.com/dotstore-support-panel/" > <img   src="<?php echo plugin_dir_url(__FILE__) . 'includes/admin/images/support_new.png'; ?>"></a></span>
                            </div>
                        </div>
                    </header>
                    <?php
                    $lite_flate_rate_methode = '';
                    $menu_advance_manager_premium_method = '';
                    $menu_advance_manager_get_started_method = '';
                    $wc_lite_extra_shipping_dotstore_contact_support_method = '';
                    $dotstore_introduction_menu_advance_manager = '';
                    $dotstore_setting_menu_enable = '';
                    $dotpremium_setting_menu_enable = '';
                    $add_new_menu_manager = '';

                    if ((!empty($_GET['tab']) && $_GET['tab'] != '' && !empty($_GET['section']) && $_GET['tab'] == 'menu-manager-add')) {
                        $add_new_menu_manager = "active";
                    }
                    if (( empty($_GET['extra_method']) && !empty($_GET['section']) && $_GET['section'] == 'wc_lite_extra_shipping_method')) {
                        $lite_flate_rate_methode = "active";
                    }

                    if (!empty($_GET['tab']) && $_GET['tab'] == 'menu_advance_manager_premium_method') {
                        $menu_advance_manager_premium_method = "active";
                    }
                    if (!empty($_GET['tab']) && $_GET['tab'] == 'menu_advance_manager_get_started_method') {
                        $menu_advance_manager_get_started_method = "active";
                    }
                    if (!empty($_GET['tab']) && $_GET['tab'] == 'wc_lite_extra_shipping_dotstore_contact_support_method') {
                        $wc_lite_extra_shipping_dotstore_contact_support_method = "active";
                    }
                    if (!empty($_GET['tab']) && $_GET['tab'] == 'dotstore_introduction_menu_advance_manager') {

                        $dotstore_introduction_menu_advance_manager = "active";
                    }

                    $site_url = "admin.php?page=advance-menu-manager-lite&tab=";
                    ?>
                    <div class="menu-main">
                        <nav>
                            <ul>
                                <li>
                                    <a class="dotstore_plugin <?php echo $add_new_menu_manager; ?>"  href="<?php echo $site_url . '&tab=menu-manager-add&section=menu-add'; ?>">Menus</a>
                                </li>

                                <li>
                                    <a class="dotstore_plugin <?php echo $menu_advance_manager_premium_method; ?>"  href="<?php echo $site_url . '&tab=menu_advance_manager_premium_method'; ?>">Premium Version</a>
                                </li>
                                <li>
                                    <a class="dotstore_plugin <?php echo $menu_advance_manager_get_started_method; ?> <?php echo $dotstore_introduction_menu_advance_manager; ?>"  href="<?php echo $site_url . 'menu_advance_manager_get_started_method'; ?>">About Plugin</a>
                                    <ul class="sub-menu">
                                        <li><a  class="dotstore_plugin <?php echo $menu_advance_manager_get_started_method; ?>" href="<?php echo $site_url . 'menu_advance_manager_get_started_method'; ?>">Getting Started</a></li>
                                        <li><a class="dotstore_plugin <?php echo $dotstore_introduction_menu_advance_manager; ?>" href="<?php echo $site_url . 'dotstore_introduction_menu_advance_manager'; ?>">Quick info</a></li>
                                        <li><a class="dotstore_plugin" href=" https://store.multidots.com/suggest-a-feature/" target="_blank">Suggest A Feature</a></li>

                                    </ul>

                                </li>

                                <li>
                                    <a class="dotstore_plugin <?php echo $wc_lite_extra_shipping_dotstore_contact_support_method; ?>"  href="#">Dotstore</a>
                                    <ul class="sub-menu">
                                        <li><a target="_blank" href="https://store.multidots.com/woocommerce-plugins/">WooCommerce Plugins</a></li>
                                        <li><a target="_blank" href="https://store.multidots.com/wordpress-plugins/">Wordpress Plugins</a></li><br>
                                        <li><a target="_blank" href="https://store.multidots.com/free-woocommerce-plugins/">Free Plugins</a></li>
                                        <li><a target="_blank" href="https://store.multidots.com/themes/">Free Themes</a></li>
                                        <li><a target="_blank" href="https://store.multidots.com/dotstore-support-panel/">Contact Support</a></li>
                                    </ul>
                                </li>

                            </ul>

                            </li>
                            </ul>
                        </nav>
                    </div>
                    <?php
                }

                function menu_advance_manager_premium_method() {
                    ?>
                    <style type="text/css">
                        td.fr-2 {text-align: center;}
                        .flat-table {width: 69%;display: block;box-sizing: border-box;margin: 0 auto; float: none !important;}
                        .flat-table h2 {font-size: 19px;font-weight: bold;color: #333;border: 1px solid #ddd;padding: 16px;border-bottom: 0;background: #e9e9e9;text-align: center !important;}

                        .tab-dot { background : #ffffff; width: 69%;     margin: 0 auto; border: 1px solid #e5e5e5; border-radius: 8px;}
                        .flat-table { width: 100%; }
                        .tab-dot table.table-outer { width: 90%; margin: 30px auto; border: 1px solid #e5e5e5;}
                        .wrapper { width: 100%;   margin: 0 auto;}
                        .tab-dot table.table-outer tr.blue th:first-child {border-radius: 0px 0 0 0;}
                        .tab-dot table.table-outer tr.blue th:nth-child(3) { border-radius: 0 0px 0 0; }
                        .tab-dot table.table-outer tr.dark.radius-s td.pad { border-radius: 0 0 0 0px; }
                        .tab-dot table.table-outer tr td.green.red { border-radius: 0px 0px 0px 0; }


                    </style>
                    <div id="main-tab">
                        <div class="wrapper">
                            <div class="tab-dot">
                                <div class="flat-table res-cl">
                                    <h2>Free vs Premium </h2>

                                    <table class="table-outer premium-free-table" align="center">
                                        <thead>
                                            <tr class="blue">
                                                <th>KEY FEATURES LIST</th>
                                                <th>FREE</th>
                                                <th>PREMIUM</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="dark">
                                                <td class="pad">You can create a view, add, edit and manage your pages and posts to make your job easier.</td>
                                                <td><img src="<?php echo plugin_dir_url(__FILE__) ?>includes/admin/images/check-mark.png"></td>
                                                <td><img src="<?php echo plugin_dir_url(__FILE__) ?>includes/admin/images/check-mark.png"></td>
                                            </tr>
                                            <tr>
                                                <td class="pad">Easy search option of page/posts for when you have hundreds of pages/posts</td>
                                                <td><img src="<?php echo plugin_dir_url(__FILE__) ?>includes/admin/images/check-mark.png"></td>
                                                <td><img src="<?php echo plugin_dir_url(__FILE__) ?>includes/admin/images/check-mark.png"></td>
                                            </tr>
                                            <tr class="dark">
                                                <td class="pad">Detailed view of pages/posts including page id, slug, author name, template names. </td>
                                                <td><img src="<?php echo plugin_dir_url(__FILE__) ?>includes/admin/images/check-mark.png"></td>
                                                <td><img src="<?php echo plugin_dir_url(__FILE__) ?>includes/admin/images/check-mark.png"></td>
                                            </tr>
                                            <tr>
                                                <td class="pad">Filter pages/posts which are already there in your menu.</td>
                                                <td><img src="<?php echo plugin_dir_url(__FILE__) ?>includes/admin/images/check-mark.png"></td>
                                                <td><img src="<?php echo plugin_dir_url(__FILE__) ?>includes/admin/images/check-mark.png"></td>
                                            </tr>
                                            <tr class="dark">
                                                <td class="pad">Track, compare, restore all your changes with Menu Revisions</td>
                                                <td><img src="<?php echo plugin_dir_url(__FILE__) ?>includes/admin/images/trash.png"></td>
                                                <td><img src="<?php echo plugin_dir_url(__FILE__) ?>includes/admin/images/check-mark.png"></td>
                                            </tr>
                                            <tr>
                                                <td class="pad">Advanced interface to add menu item in your menu</td>
                                                <td><img src="<?php echo plugin_dir_url(__FILE__) ?>includes/admin/images/trash.png"></td>
                                                <td><img src="<?php echo plugin_dir_url(__FILE__) ?>includes/admin/images/check-mark.png"></td>
                                            </tr>

                                            <tr class="dark">
                                                <td class="pad">Put the menu anywhere on your site/blog with short-code</td>
                                                <td><img src="<?php echo plugin_dir_url(__FILE__) ?>includes/admin/images/trash.png"></td>
                                                <td><img src="<?php echo plugin_dir_url(__FILE__) ?>includes/admin/images/check-mark.png"></td>
                                            </tr>
                                            <tr>
                                                <td class="pad">Menu Lock Functionality. you can lock particular Menu for other users</td>
                                                <td><img src="<?php echo plugin_dir_url(__FILE__) ?>includes/admin/images/trash.png"></td>
                                                <td><img src="<?php echo plugin_dir_url(__FILE__) ?>includes/admin/images/check-mark.png"></td>
                                            </tr>
                                            <tr class="dark">
                                                <td class="pad">Create new pages within the menu without leaving your add menu item screen.</td>
                                                <td><img src="<?php echo plugin_dir_url(__FILE__) ?>includes/admin/images/trash.png"></td>
                                                <td><img src="<?php echo plugin_dir_url(__FILE__) ?>includes/admin/images/check-mark.png"></td>
                                            </tr>
                                            <tr>
                                                <td class="pad">Edit whole page/post from the menu.</td>
                                                <td><img src="<?php echo plugin_dir_url(__FILE__) ?>includes/admin/images/trash.png"></td>
                                                <td><img src="<?php echo plugin_dir_url(__FILE__) ?>includes/admin/images/check-mark.png"></td>
                                            </tr>
                                            <tr class="dark">
                                                <td class="pad">View page / post attributes withing menu.</td>
                                                <td><img src="<?php echo plugin_dir_url(__FILE__) ?>includes/admin/images/trash.png"></td>
                                                <td><img src="<?php echo plugin_dir_url(__FILE__) ?>includes/admin/images/check-mark.png"></td>
                                            </tr>

                                            <tr class="pad radius-s">
                                                <td class="pad"></td>
                                                <td></td>
                                                <td class="green red"><a href="https://store.multidots.com/advance-menu-manager-wordpress/" target="_blank">UPGRADE TO <br> PREMIUM VERSION </a></td>
                                            </tr>
                                        </tbody>
                                    </table>

                                </div>

                            </div>
                        </div>
                    </div>
                    <?php
                }

                function dotstore_advance_menu_manager_left_side_menu() {
                    $site_url = "admin.php?page=advance-menu-manager-lite&tab=";
                    ?>
                    <div class="dotstore_plugin_supports_feature">
                        <div class="right-sec">
                            <div class="right-box">
                                <div class="woo-top">
                                    <span>discover features</span>
                                </div>

                                <div class="discover">
                                    <div class="video">
                                        <iframe width="100%" height="232" src="https://www.youtube.com/embed/DkKx4EuWjsA" frameborder="0" allowfullscreen></iframe>
                                    </div>
                                    <div class="video-detail">
                                        <ul>
                                            <li><img src="<?php echo plugin_dir_url(__FILE__) . 'includes/admin/images/right_click.png'; ?>">
                                                <span >Enhanced Admin Menu UI </span>
                                            </li>
                                            <li><img src="<?php echo plugin_dir_url(__FILE__) . 'includes/admin/images/right_click.png'; ?>">
                                                <span>Track,Compare,Restore Menu</span>
                                            </li>
                                            <li><img src="<?php echo plugin_dir_url(__FILE__) . 'includes/admin/images/right_click.png'; ?>">
                                                <span>Menu Lock Functionality</span> 
                                            </li>
                                            <li><img src="<?php echo plugin_dir_url(__FILE__) . 'includes/admin/images/right_click.png'; ?>">
                                                <span >Quick Add/Edit Page within Menu</span>
                                            </li>

                                            <li><img src="<?php echo plugin_dir_url(__FILE__) . 'includes/admin/images/right_click.png'; ?>">
                                                <span >Compare Menu Revisions </span>
                                            </li>
                                            <li><img src="<?php echo plugin_dir_url(__FILE__) . 'includes/admin/images/right_click.png'; ?>">
                                                <span >Menu Shortcode features </span>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="view-button">
                                        <a  class="view_button_dotstore" target="_blank" href="<?php echo $site_url . 'menu_advance_manager_premium_method'; ?>" >view details</a>
                                        <a  class="live_button_dotstore" target="_blank" href="http://admenumanagerforwordpress.demo.store.multidots.com">live demo</a>
                                    </div>
                                </div>
                            </div>
                            <div class="right-box">
                                <div class="dotstore-blog">
                                    <img width="100%" src="<?php echo plugin_dir_url(__FILE__) . 'includes/admin/images/Discount.png'; ?>">			
                                </div>
                                <div class="upgrader_pro_version_button">
                                    <a href="https://store.multidots.com/go/amm-lite-new-interface-header-button-upgradetopro" target="_blank">
                                        <img  src="<?php echo plugin_dir_url(__FILE__) . 'includes/admin/images/upgrade_new.png'; ?>"> 
                                    </a>
                                </div>
                            </div>
                            <div class="right-box">
                                <div class="dotstore-important-link">
                                    <h2><span class="dotstore-important-link-title">Important link</span></h2>
                                    <div class="video-detail important-link">
                                        <ul>
                                            <li><img src="<?php echo plugin_dir_url(__FILE__) . 'includes/admin/images/right_click.png'; ?>"><a target="_blank" href="https://store.multidots.com/docs/plugin/advanced-menu-manager/">Plugin documentation</a></li> 
                                            <li><img src="<?php echo plugin_dir_url(__FILE__) . 'includes/admin/images/right_click.png'; ?>"><a target="_blank" href="https://store.multidots.com/dotstore-support-panel/">Support platform</a></li>
                                            <li><img src="<?php echo plugin_dir_url(__FILE__) . 'includes/admin/images/right_click.png'; ?>"><a target="_blank" href="https://store.multidots.com/suggest-a-feature/">Suggest A Feature</a></li>
                                            <li><img src="<?php echo plugin_dir_url(__FILE__) . 'includes/admin/images/right_click.png'; ?>"><a target="_blank" href="https://store.multidots.com/docs/plugin/advanced-menu-manager/">Changelog</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <?php
                }

                function welcome_screen_advance_menu_manager_remove_menus() {
                    remove_submenu_page('index.php', 'advance-menu-manager-lite');
                }

                function welcome_screen_content_advance_menu_manager() {
                    $current_user = wp_get_current_user();
                    wp_enqueue_script('jquery-ui-dialog');
                    wp_enqueue_style('wp-pointer');
                    wp_enqueue_script('wp-pointer');
                    ?>

                    <div class = "wrap about-wrap">
                        <h1 style = "font-size: 2.1em;"><?php printf(__('Welcome to Advanced Menu Manager', 'advance-menu-manager'));
                    ?></h1>

                        <div class="about-text woocommerce-about-text">
                            <?php
                            $message = '';
                            printf(__('%s Very Simple, easy, admin-friendly menu manege with this plugin which can be used for the customize menus like edit, sorting, delete, Add new menu etc', 'advance-menu-manager'), $message);
                            ?>
                            <img class="version_logo_img" src="<?php echo plugin_dir_url(__FILE__) . 'images/advance-menu-manager.png'; ?>">
                        </div>

                        <?php
                        $setting_tabs_wc = apply_filters('advance_menu_manager_setting_tab', array("about" => "Overview", "other_plugins" => "Checkout our other plugins", "premium_feauter" => "Premium Feature"));
                        $current_tab_wc = (isset($_GET['tab'])) ? $_GET['tab'] : 'general';
                        $aboutpage = isset($_GET['page'])
                        ?>
                        <h2 id="woo-extra-cost-tab-wrapper" class="nav-tab-wrapper">
                            <?php
                            foreach ($setting_tabs_wc as $name => $label)
                                echo '<a  href="' . home_url('wp-admin/index.php?page=advance-menu-manager-lite&tab=' . $name) . '" class="nav-tab ' . ( $current_tab_wc == $name ? 'nav-tab-active' : '' ) . '">' . $label . '</a>';
                            ?>
                        </h2>
                        <?php
                        foreach ($setting_tabs_wc as $setting_tabkey_wc => $setting_tabvalue) {
                            switch ($setting_tabkey_wc) {
                                case $current_tab_wc:
                                    do_action('advance_menu_manager_' . $current_tab_wc);
                                    break;
                            }
                        }
                        ?>
                        <hr />
                        <div class="return-to-dashboard">
                            <a href="<?php echo home_url('/wp-admin/themes.php?page=advance-menu-manager'); ?>"><?php _e('Go to Advance Menu Manager Settings', 'advance-menu-manager'); ?></a>
                        </div>
                    </div>

                    <?php
                }

                function advance_menu_manager_about() {
                    $current_user = wp_get_current_user();
                    ?> 

                    <div class="changelog">
                        </br>
                        <style type="text/css">
                            p.advance_menu_manager_overview {max-width: 100% !important;margin-left: auto;margin-right: auto;font-size: 15px;line-height: 1.5;}.advance_menu_manager_content_ul ul li {margin-left: 3%;list-style: initial;line-height: 23px;}
                        </style>  
                        <div class="changelog about-integrations">
                            <div class="wc-feature feature-section col three-col">
                                <div>
                                    <p class="advance_menu_manager_overview"><?php _e('The Menu makes it difficult for you to manage hundreds of menu items including sub-menu items if you run a complex content management system. Plus, when you have a blog integrated with it, you need to know what item is a page or a post. The existing WP Menu Feature makes it difficult for you and your team to add, manage, delete menu items, posts, pages etc. What`s more, you cannot waste your time dragging and dropping menu items. It increases the chance of human error and is cumbersome too. The Advanced Menu Manager has been built for websites with elaborate menu structure. It allows you to add pages and posts parallely.', 'advance-menu-manager'); ?></p>

                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }

                function advance_menu_manager_other_plugins() {
                    global $wpdb;
                    $url = 'http://www.multidots.com/store/wp-content/themes/business-hub-child/API/checkout_other_plugin.php';
                    $response = wp_remote_post($url, array('method' => 'POST',
                        'timeout' => 45,
                        'redirection' => 5,
                        'httpversion' => '1.0',
                        'blocking' => true,
                        'headers' => array(),
                        'body' => array('plugin' => 'advance-flat-rate-shipping-method-for-woocommerce'),
                        'cookies' => array()));

                    $response_new = array();
                    $response_new = json_decode($response['body']);
                    $get_other_plugin = maybe_unserialize($response_new);

                    $paid_arr = array();
                    ?>

                    <div class="plug-containter">
                        <div class="paid_plugin">
                            <h3>Paid Plugins</h3>
                            <?php
                            foreach ($get_other_plugin as $key => $val) {
                                if ($val['plugindesc'] == 'paid') {
                                    ?>


                                    <div class="contain-section">
                                        <div class="contain-img"><img src="<?php echo $val['pluginimage']; ?>"></div>
                                        <div class="contain-title"><a target="_blank" href="<?php echo $val['pluginurl']; ?>"><?php echo $key; ?></a></div>
                                    </div>	


                                    <?php
                                } else {

                                    $paid_arry[$key]['plugindesc'] = $val['plugindesc'];
                                    $paid_arry[$key]['pluginimage'] = $val['pluginimage'];
                                    $paid_arry[$key]['pluginurl'] = $val['pluginurl'];
                                    $paid_arry[$key]['pluginname'] = $val['pluginname'];
                                    ?>


                                    <?php
                                }
                            }
                            ?>
                        </div>
                        <?php if (isset($paid_arry) && !empty($paid_arry)) { ?>
                            <div class="free_plugin">
                                <h3>Free Plugins</h3>
                                <?php foreach ($paid_arry as $key => $val) { ?>  	
                                    <div class="contain-section">
                                        <div class="contain-img"><img src="<?php echo $val['pluginimage']; ?>"></div>
                                        <div class="contain-title"><a target="_blank" href="<?php echo $val['pluginurl']; ?>"><?php echo $key; ?></a></div>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                        </div>

                    </div>

                    <?php
                }

                function advance_menu_manager_premium_feauter() {
                    ?>

                    <div class="changelog">
                        </br>
                        <style type="text/css">
                            p.advance_menu_manager_overview {max-width: 100% !important;margin-left: auto;margin-right: auto;font-size: 15px;line-height: 1.5;}.advance_menu_manager_content_ul ul li {margin-left: 3%;list-style: initial;line-height: 23px;}.other_feature {margin-top: 3%;}
                        </style>  
                        <div class="changelog about-integrations">
                            <div class="wc-feature feature-section col three-col">
                                <div>
                                    <p class="advance_menu_manager_overview"><strong>Advanced Menu Manager For WordPress </strong></p>  
                                    <p class="advance_menu_manager_overview">Need even more? upgrade to <a href="https://codecanyon.net/item/advance-menu-manager/15275037?s_rank=1" rel="nofollow">Advanced Menu Manager For WordPress</a> and get all the features available in Advanced Menu Manager For WordPress</p> 

                                    <p class="advance_menu_manager_overview">Advanced Menu Manager for WordPress makes it simpler for website admins to effectively create and manage menu for content-heavy wordpress blogs and websites. When the site has hundreds of menu items, it becomes complex task to add new item, drag to top etc. AMM helps you to improve productivity while managing the menus.</p>


                                    <p class="advance_menu_manager_overview"><strong>Key Features of WooCommerce Save For Later: </strong></p>

                                    <p></p>

                                    <div class="advance_menu_manager_picture_entry_content">
                                        <div class="advance_menu_manager_picture">
                                            <div class="picture-content"><img src="https://store.multidots.com/wp-content/uploads/2016/08/2-1-640x427.png"></div>
                                        </div>
                                        <div class="advance_menu_manager_content">
                                            <h3 class="h4 cactus-post-title entry-title">Effective User Interface to manage menu</h3>
                                            <div class="excerpt">

                                                <div>
                                                    With hundreds of pages in your menu, you need a tailored UI that offers a complete view of menu, the parent pages and their child (sub-items). Advanced Menu Manager has that user-interface. Besides offering a complete picture of your menu, the clutter-free UI also allows you to view, add, edit and manage your pages and posts to make your job easier.
                                                </div>

                                            </div>
                                        </div>	

                                        <div class="cactus-last-child"></div> 

                                    </div>



                                    <div class="advance_menu_manager_picture_entry_content">
                                        <div class="advance_menu_manager_picture_fourth">
                                            <div class="picture-content"><img src="https://store.multidots.com/wp-content/uploads/2016/08/2-2-640x427.png"></div>
                                        </div>
                                        <div class="advance_menu_manager_content_fourth">
                                            <h3 class="h4 cactus-post-title entry-title">Advanced interface to add menu item in your menu</h3>
                                            <div class="excerpt">

                                                <div>
                                                    Advanced menu manager offers many great features which can enhance your productivity and accuracy while adding menu items. You can take advantage of features like-
                                                </div> 

                                                <div class="advance_menu_manager_content_ul">
                                                    <ul>
                                                        <li>Easy search option of page/posts specially when you have hundreds of pages/posts.</li>
                                                        <li>Detailed view of pages/posts including page id, slug, author name, template names etc.</li>
                                                        <li>Filter pages/posts which are already there in your menu.</li>
                                                        <li>You can create new pages within the menu without leaving your add menu item screen.</li>
                                                    </ul>
                                                </div>

                                            </div>
                                        </div>	

                                        <div class="cactus-last-child"></div> 

                                    </div>

                                    <div class="advance_menu_manager_picture_entry_content">
                                        <div class="advance_menu_manager_picture">
                                            <div class="picture-content"><img src="https://store.multidots.com/wp-content/uploads/2016/08/6-640x427.png"></div>
                                        </div>
                                        <div class="advance_menu_manager_content">
                                            <h3 class="h4 cactus-post-title entry-title">Track, compare, restore all your changes with Menu Revisions</h3>
                                            <div class="excerpt">

                                                <div>
                                                    As a site owner, it is common for you to go back to an earlier version of the menu you created. WordPress doesn't support tracking menu revision histories as of now. The Advanced Menu Manager plugin keeps a revision for each change you have made in your menu. Also it allows you to compare your current menu with a revision in the past. So, in case you made a mistake while editing the menu, you do not need to worry. You can always restore your entire menu back from previously stored revisions.
                                                </div> 
                                            </div>
                                        </div>	

                                        <div class="cactus-last-child"></div> 

                                    </div>

                                    <div class="advance_menu_manager_picture_entry_content">

                                        <div class="advance_menu_manager_content_fourth">
                                            <h3 class="h4 cactus-post-title entry-title">Menu Lock Functionality.</h3>
                                            <div class="excerpt">

                                                <div>
                                                    Using this feature you can lock particular Menu for other users. This feature can be handy when you want to restrict other admin users from editing the main navigation of your site. If you have many admin users in your wordpress site, and you want sure that not everyone can edit the main menu. Only selected users can change the main menu. In that case you can utilize the Menu Lock Functionality.
                                                </div> 
                                            </div>
                                        </div>	

                                        <div class="advance_menu_manager_picture_fourth">
                                            <div class="picture-content"><img src="https://store.multidots.com/wp-content/uploads/2016/08/3-2-640x427.png"></div>
                                        </div>

                                        <div class="cactus-last-child"></div> 

                                    </div>


                                    <div class="other_feature">
                                        <p class="advance_menu_manager_overview"><strong>Other features you will love:</strong></p>

                                        <div class="advance_menu_manager_content_ul">
                                            <ul>
                                                <li>Add, edit, manage and delete pages from a single interface, no going back to pages and posts section to create menu items.</li>
                                                <li>Add a menu item as a child category without going back and forth. Next, simply drag the item to make it a sub-item. No scrolling down to find the item and dragging it all the long way. This helps minimize your efforts and chance of human error.</li>
                                                <li>Easy to use page/post search functionality so that you know you have selected the correct menu item.</li>
                                                <li>View and compare previous menu revisions made. You can even restore to a previous version.</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php
                }

                function advance_menu_manager_pointers_footer() {
                    $admin_pointers = custom_advance_menu_manager_admin_pointers();
                    ?>
                    <script type="text/javascript">
        /* <![CDATA[ */
        jQuery.noConflict();
        (function($) {
    <?php
    foreach ($admin_pointers as $pointer => $array) {
        if ($array['active']) {
            ?>
                    $('<?php echo $array['anchor_id']; ?>').pointer({
                        content: '<?php echo $array['content']; ?>',
                        position: {
                            edge: '<?php echo $array['edge']; ?>',
                            align: '<?php echo $array['align']; ?>'
                        },
                        close: function() {
                            $.post(ajaxurl, {
                                pointer: '<?php echo $pointer; ?>',
                                action: 'dismiss-wp-pointer'
                            });
                        }
                    }).pointer('open');
            <?php
        }
    }
    ?>
        })(jQuery);
        /* ]]> */
                    </script>
                    <?php
                }

                function custom_advance_menu_manager_admin_pointers() {
                    $dismissed = explode(',', (string) get_user_meta(get_current_user_id(), 'dismissed_wp_pointers', true));
                    $version = '1_0'; // replace all periods in 1.0 with an underscore
                    $prefix = 'custom_advance_menu_manager_admin_pointers' . $version . '_';

                    $new_pointer_content = '<h3>' . __('Welcome to Advance Menu Manager') . '</h3>';
                    $new_pointer_content .= '<p>' . __('Very Simple, easy, admin-friendly menu manege with this plugin which can be used for the customize menus like edit, sorting, delete, Add new menu etc') . '</p>';

                    return array(
                        $prefix . 'custom_advance_menu_manager_admin_pointers' => array(
                            'content' => $new_pointer_content,
                            'anchor_id' => '#toplevel_page_woocommerce',
                            'edge' => 'left',
                            'align' => 'left',
                            'active' => (!in_array($prefix . 'custom_advance_menu_manager_admin_pointers', $dismissed) )
                        )
                    );
                }

                /**
                 * plugin_deactivation function
                 *
                 * This function will run when someone deactivate the plugin and all admin interface will be disabled.
                 *
                 * @version		1.0.0
                 * @author 		Multidots 
                 */
                function plugin_deactivation() {
                    require_once plugin_dir_path(__FILE__) . 'includes/classes/Class_Deactivator.php';
                }

                register_deactivation_hook(__FILE__, 'plugin_deactivation');

                function generate_menu_template() {
                    //add_theme_page('Advance Menu Manager', 'Advance Menu Manager', 'edit_theme_options', 'advance-menu-manager', 'generate_menu_page', 5);	
                }

                //add_action( 'admin_menu', 'generate_menu_template' );

                /**
                 * This function runs when plugin activates. (use period)
                 *
                 * This function executes when plugin activates and object initialised.
                 *
                 * @since    1.0.0
                 */
                function generate_menu_page() {

                    wp_enqueue_script('amm_script_fancy', plugins_url('advance-menu-manager/includes/js/custom.js'));
                    wp_enqueue_script('jquery-ui-dialog');
                    global $gloable_all_author_array;
                    global $gloable_all_template_array;
                    global $gloable_all_category_array;
                    global $gloable_all_current_menu_id;
                    $current_user = wp_get_current_user();
                    ?>
                    <link type="text/css" rel="stylesheet" href="<?php echo plugin_dir_url(__FILE__) . '/includes/css/style.css?ver=1.0.4'; ?>" >
                    <script type="text/javascript" src="<?php echo PLUGIN_PATH . 'js/custom.js'; ?>"></script>
                    <?php
                    include('includes/admin/Admin.php');
                }

                /**
                 * amm_plugin_scripts function
                 *
                 * This function will run when admin menu hook called then it is used to enqueue the styles and scripts.
                 *
                 * @version		1.0.0
                 * @author 		Multidots   
                 */
                function AMM_add_scripts_styles_admin_init() {
                    /* Register our script. */
                    wp_enqueue_style('wp-jquery-ui-dialog');
                    wp_enqueue_style('amm_style', plugins_url('advance-menu-manager/includes/css/style.css'));
                    wp_enqueue_style('amm_style_fancy', plugins_url('advance-menu-manager/includes/css/fancy_alert.css'));
                    wp_enqueue_style('amm_style');
                    wp_enqueue_style('amm_style_fancy');
                }

                add_action('admin_init', 'AMM_add_scripts_styles_admin_init');

                add_action('wp_enqueue_scripts', 'enqueue_scripts');

                function enqueue_scripts() {
                    wp_enqueue_script('custom-js-amm', plugin_dir_url(__FILE__) . 'includes/js/amm_custom.js', array('jquery'), false);
                }

                global $wpdb, $woocommerce;
                if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {

                    add_filter('woocommerce_paypal_args', 'paypal_bn_code_filter_advance_menu_manager', 99, 1);

                    /**
                     * BN code added 
                     */
                    function paypal_bn_code_filter_advance_menu_manager($paypal_args) {
                        $paypal_args['bn'] = 'Multidots_SP';
                        return $paypal_args;
                    }

                }

                /**
                 * spl_autoload_register function
                 *
                 * This function will run admin panel loades.
                 *
                 * @version		1.0.0
                 * @author 		Multidots  
                 */
                function amm_autoloader($name) {
                    require_once plugin_dir_path(__FILE__) . 'includes/classes/Class_Admin_Page.php';
                    require_once plugin_dir_path(__FILE__) . 'includes/classes/Class_Admin_Menu_Walker.php';
                    require_once plugin_dir_path(__FILE__) . 'includes/classes/Class_Menu_Ajax_Action.php';
                }

                spl_autoload_register('amm_autoloader');

                add_action('wp_ajax_my_action_delete_menu', array('md_admin_interface', 'my_action_ajax_for_delete_menu'));
                add_action('wp_ajax_my_action_create_menu_ajax', array('md_admin_interface', 'my_action_ajax_for_create_menu'));

                /*                 * * popup content *** */
                add_action('wp_ajax_my_action_for_add_new_menu_item_html_filter', array('md_admin_menu_revision_ajax_action', 'my_action_for_add_new_menu_item_html_filter_own'));

                /**
                 * Pagination post per page feature
                 *
                 * @version		1.0.1
                 */
                add_action('wp_ajax_my_action_for_add_pagination_limit', array('md_admin_menu_revision_ajax_action', 'my_action_for_add_pagination_post_per_page_limit_method'));

                add_action('wp_ajax_add_plugin_user_amm', 'wp_add_plugin_userfn_amm_free');
                add_action('wp_ajax_nopriv_add_plugin_user_amm', 'wp_add_plugin_userfn_amm_free');

                function wp_add_plugin_userfn_amm_free() {


                    $email_id = (isset($_POST["email_id"]) && !empty($_POST["email_id"])) ? $_POST["email_id"] : '';
                    $log_url = $_SERVER['HTTP_HOST'];
                    $cur_date = date('Y-m-d');
                    $request_url = 'http://www.multidots.com/store/wp-content/themes/business-hub-child/API/wp-add-plugin-users.php';
                    if (!empty($email_id)) {
                        $request_response = wp_remote_post($request_url, array('method' => 'POST',
                            'timeout' => 45,
                            'redirection' => 5,
                            'httpversion' => '1.0',
                            'blocking' => true,
                            'headers' => array(),
                            'body' => array('user' => array('plugin_id' => '18', 'user_email' => $email_id, 'plugin_site' => $log_url, 'status' => 1, 'activation_date' => $cur_date)),
                            'cookies' => array()));
                    }
                    update_option('amm_free_plugin_notice_shown', 'true');
                }
                ?>
