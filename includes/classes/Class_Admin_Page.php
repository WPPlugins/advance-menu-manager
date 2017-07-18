<?php

/**
 * Fired when admin page will loaded.
 *
 * This class executes when plugin admin page interface executes.
 *
 * @since      1.0.0
 * @package    Multidots Advance Menu Manager
 * @subpackage advance-menu-manager/includes/classes
 * @author     Multidots Solutions Pvt. Ltd. <info@multidots.com>
 */
class md_admin_interface {

    private static $depth_count_var = '';
    /**
     * This function runs when plugin activates. (use period)
     *
     * This function executes when plugin activates and object initialised.
     *
     * @since    1.0.0
     */

    /**
     * wordpress hook called in twise that whay we have made custom logic
     *
     */
    private $past_revision = true;

    /*
      function __construct() {}

     */

    /**
     * This function runs when menu deleted from the admin page. (use period)
     *
     * This function executes when menu deleted from the admin page.
     *
     * @since    1.0.0
     */
    function my_action_ajax_for_delete_menu() {

        global $wpdb; // this is how you get access to the database

        $delete_menu_id = intval($_POST['delete_menu_id']);

        $delete_menu_obj = wp_delete_nav_menu($delete_menu_id);
        if ($delete_menu_obj) {
            $nav_menus = wp_get_nav_menus();
            if (isset($nav_menus[0]->term_id)) {
                update_user_meta(get_current_user_id(), 'nav_menu_recently_edited', $nav_menus[0]->term_id);
            }
            echo $delete_menu_obj;
        }
        wp_die(); // this is required to terminate immediately and return a proper response
    }

    /**
     * amm_save_existing_menu function
     * 
     * This function is used to save existing menu items.
     * 
     * @version 	1.0.0
     * @author 		Multidots
     * */
    function amm_save_existing_menu($menu_items) {
        // menu name and setting update

        if (empty($_POST['menu-name'])) {
            return $messages = '<div id="message" class="error notice is-dismissible"><p>' . __('Please enter menu name.') . '</p> <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
        }

        /**
         * get location, menu id, current menu location for save location on menu 
         */
        global $wpdb;
        $current_edit_menu_id = $_REQUEST['current_edit_menu_id'];
        $locations = get_registered_nav_menus();
        $menu_locations = get_nav_menu_locations();
        $messages = '';

        // Remove menu locations that have been unchecked.
        foreach ($locations as $location => $description) {
            if (( empty($_POST['menu-locations']) || empty($_POST['menu-locations'][$location]) ) && isset($menu_locations[$location]) && $menu_locations[$location] == $current_edit_menu_id)
                unset($menu_locations[$location]);
        }

        // Merge new and existing menu locations if any new ones are set.
        if (isset($_POST['menu-locations'])) {
            $new_menu_locations = array_map('absint', $_POST['menu-locations']);
            $menu_locations = array_merge($menu_locations, $new_menu_locations);
        }

        // Set menu locations.
        set_theme_mod('nav_menu_locations', $menu_locations);

        //menu title and menu related other option will update
        $_menu_object = wp_get_nav_menu_object($current_edit_menu_id);

        $menu_title = trim(esc_html($_POST['menu-name']));
        if (!$menu_title) {
            $messages = '<div id="message" class="error notice is-dismissible"><p>' . __('Please enter a valid menu name.') . '</p> <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
            $menu_title = $_menu_object->name;
        }

        if (!is_wp_error($_menu_object)) {
            $old_menu_name = $_POST['old-menu-name'];

            if ($old_menu_name != $menu_title) {
                $menu_exists = wp_get_nav_menu_object($menu_title);
                if (empty($menu_exists)) {
                    $current_edit_menu_id = wp_update_nav_menu_object($current_edit_menu_id, array('menu-name' => $menu_title));

                    if (is_wp_error($current_edit_menu_id)) {
                        $_menu_object = $current_edit_menu_id;
                        $messages = '<div id="message" class="error notice is-dismissible"><p>Please try again later.</p><button type="button" class="notice-dismiss"></div>';
                    } else {
                        $nav_menu_selected_title = $_menu_object->name;
                    }
                } else {
                    return $messages = '<div id="message" class="error notice is-dismissible"><p>' . $menu_title . ' is already registered.</p><button type="button" class="notice-dismiss"></div>';
                }
            } else {
                $nav_menu_selected_title = $_menu_object->name;
            }
        }

        // Update menu items.
        if (!is_wp_error($_menu_object) && !empty($nav_menu_selected_title)) {
            $messages_wp = array();
            $messages_wp = array_merge($messages_wp, wp_nav_menu_update_menu_items($current_edit_menu_id, $nav_menu_selected_title));
            if (!empty($messages_wp)) {
                foreach ($messages_wp as $mesg)
                    $messages .= $mesg;
            }
        }
        if (!is_wp_error($current_edit_menu_id)) {
            update_user_meta(get_current_user_id(), 'nav_menu_recently_edited', $current_edit_menu_id);
        }
        // End menu name and setting update code
        //$menu_id = $_REQUEST['amm_menu_id'];
        $elements = array('menu-item-db-id', 'menu-item-object-id', 'menu-item-object', 'menu-item-parent-id', 'menu-item-position', 'menu-item-type');
        $menu_items_obj = explode(',', $menu_items);


        array_pop($menu_items_obj);

        $menu_item_db_id = '';

        if (!empty($_REQUEST['menu-item-db-id']))
            $menu_item_db_id = array_values($_REQUEST['menu-item-db-id']);

        $end = (1 + count($menu_item_db_id)) - 1;
        $keys = range(1, $end);

        $deleted_nodes = explode(',', $_REQUEST['delete_menu_items']);

        if (!empty($menu_item_db_id))
            $menu_item_db_id = array_combine($keys, $menu_item_db_id);

        $defaults = array(
            'menu-item-db-id' => 0,
            'menu-item-object-id' => 0,
            'menu-item-object' => '',
            'menu-item-parent-id' => 0,
            'menu-item-position' => 0,
            'menu-item-type' => 'custom',
            'menu-item-title' => '',
            'menu-item-url' => '',
            'menu-item-description' => '',
            'menu-item-attr-title' => '',
            'menu-item-target' => '',
            'menu-item-classes' => '',
            'menu-item-xfn' => '',
            'menu-item-status' => '',
        );
        $args = wp_parse_args($_REQUEST, $defaults);
        $menu_order_node = 1;
        $loop_count = 0;
        if (!empty($args['menu-item-object-id'])) {
            foreach ($args['menu-item-object-id'] as $key => $value) {
                $current_post_id = $menu_item_db_id[$menu_order_node];
                if (in_array($current_post_id, $deleted_nodes)) {
                    wp_delete_post($current_post_id);
                    delete_post_meta($current_post_id, '_menu_item_type', sanitize_key($args['menu-item-type'][$key]));
                    delete_post_meta($current_post_id, '_menu_item_menu_item_parent', strval((int) $args['menu-item-parent-id'][$key]));
                    delete_post_meta($current_post_id, '_menu_item_object_id', strval((int) $args['menu-item-object-id'][$key]));
                    delete_post_meta($current_post_id, '_menu_item_object', sanitize_key($args['menu-item-object'][$key]));
                } else {
                    if (isset($args['menu-item-attr-title'][$loop_count])) {
                        $update_my_nav_post = array('ID' => $current_post_id, 'menu_order' => sanitize_key($menu_order_node), 'post_excerpt' => sanitize_key($args['menu-item-attr-title'][$loop_count]));
                    } else {
                        $update_my_nav_post = array('ID' => $current_post_id, 'menu_order' => sanitize_key($menu_order_node));
                    }

                    wp_update_post($update_my_nav_post);
                    update_post_meta($current_post_id, '_menu_item_type', sanitize_key($args['menu-item-type'][$key]));
                    update_post_meta($current_post_id, '_menu_item_menu_item_parent', strval((int) $args['menu-item-parent-id'][$key]));
                    update_post_meta($current_post_id, '_menu_item_object_id', strval((int) $args['menu-item-object-id'][$key]));
                    update_post_meta($current_post_id, '_menu_item_object', sanitize_key($args['menu-item-object'][$key]));
                    if (!empty($args['menu-item-target'][$key]))
                        update_post_meta($current_post_id, '_menu_item_target', $args['menu-item-target'][$key]);
                }
                $menu_order_node++;
                $loop_count++;
            } // Foreach close
        }
        unset($deleted_nodes);

        //if(empty($messages)) $messages = '<div id="message" class="updated notice is-dismissible "><p><strong>'.$menu_title.'</strong> has been updated.</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
        return $messages;
    }

// Function close

    /**
     * menu_container_print function
     * 
     * This function is used to print the menu container in the plugin page.
     * 
     * @version 	1.0.0
     * @author 		Multidots
     * */
    function menu_container_print() {

        global $gloable_all_author_array;
        global $gloable_all_template_array;
        global $gloable_all_category_array;
        global $gloable_all_current_menu_id;
        //set all author globaly
        $allUsers = get_users('orderby=ID&order=ASC');
        foreach ($allUsers as $currentUser) {
            if (!in_array('subscriber', $currentUser->roles)) {
                $gloable_all_author_array[] = $currentUser;
            }
        }

        // set all template globaly
        $get_templates_all = get_page_templates();
        foreach ($get_templates_all as $template_name => $template_filename) {
            $gloable_all_template_array[$template_name] = $template_filename;
        }

        // set all category by globaly		
        $all_category = get_categories('orderby=name&hide_empty=0');
        foreach ($all_category as $cat_data) {
            $gloable_all_category_array[$cat_data->cat_ID] = $cat_data->cat_name;
        }
        $form_submited_messages = isset($_REQUEST ['save_menu']) ? md_admin_interface::amm_save_existing_menu($_REQUEST ['total_menu_items']) : false;

        wp_nav_menu_post_type_meta_boxes();
        wp_nav_menu_taxonomy_meta_boxes();
        wp_enqueue_script('nav-menu');

        if (wp_is_mobile())
            wp_enqueue_script('jquery-touch-punch');

        $nav_menu_selected_id = isset($_REQUEST['menu']) ? (int) $_REQUEST['menu'] : 0;

        // Get recently edited nav menu.
        $recently_edited = absint(get_user_option('nav_menu_recently_edited'));
        if (empty($recently_edited) && is_nav_menu($nav_menu_selected_id))
            $recently_edited = $nav_menu_selected_id;

        // Use $recently_edited if none are selected.
        if (empty($nav_menu_selected_id) && !isset($_GET['menu']) && is_nav_menu($recently_edited))
            $nav_menu_selected_id = $recently_edited;


        if (empty($nav_menu_selected_id) && !empty($nav_menus) && !$add_new_screen) {
            // if we have no selection yet, and we have menus, set to the first one in the list.
            $nav_menu_selected_id = $nav_menus[0]->term_id;
        }

        // Update the user's setting.
        if ($nav_menu_selected_id != $recently_edited && is_nav_menu($nav_menu_selected_id))
            update_user_meta(get_current_user_id(), 'nav_menu_recently_edited', $nav_menu_selected_id);

        //if menu hase change on dropdwon  and save menu than recently menu option will update
        if (!empty($_POST['page_on_front'])) {
            $nav_menu_selected_id = (int) $_POST['page_on_front'];
            $user_id = get_current_user_id();
            update_user_meta($user_id, 'nav_menu_recently_edited', $nav_menu_selected_id);
        }


        $locations = get_registered_nav_menus();
        $menu_locations = get_nav_menu_locations();
        $num_locations = count(array_keys($locations));
        //$nav_menu_selected_id = 27;
        //submit themes location form
        if (isset($_GET['action']) && 'locations' == $_GET['action'] && isset($_POST['menu-locations'])) {
            if (isset($_POST['menu-locations'])) {
                check_admin_referer('save-menu-locations');

                $new_menu_locations = array_map('absint', $_POST['menu-locations']);
                $menu_locations = array_merge($menu_locations, $new_menu_locations);
                // Set menu locations
                set_theme_mod('nav_menu_locations', $menu_locations);
                $messages[] = '<div id="message" class="updated notice is-dismissible"><p>' . __('Menu locations updated.') . '</p></div>';
            }
        }

        $menu_revision_tab = '';
        if (isset($_GET['action']) && 'locations' == $_GET['action']) {
            $menu_location_flag = $_GET['action'];
        } else {
            $menu_location_flag = '';
        }
        $locations_screen = ( isset($_GET['action']) && 'locations' == $_GET['action'] ) ? true : false;

        // Get all nav menus.
        $nav_menus = wp_get_nav_menus();
        $menu_count = count($nav_menus);

        wp_nav_menu_setup();
        wp_initial_nav_menu_meta_boxes();
        ?>


        <script type="text/javascript" src="<?php echo PLUGIN_PATH . 'js/fancy_alert.js'; ?>"></script>
        <script type="text/javascript" src="<?php echo PLUGIN_PATH . 'js/md_pagination.js'; ?>"></script>
        <div id="amm_support"><a id="support_btn" href="mailto:wordpress@multidots.in?subject=">Support</a></div>
        <div class="wrap"> 
            <h2><?php echo PLUGIN_NAME; ?> </h2>				
        </div>
        <div id="Advance_menu_manager_messages">
            <?php if (!empty($form_submited_messages)) echo $form_submited_messages; ?>				
        </div>	
        <div id="wpbody">			
            <div style="overflow: hidden;" id="wpbody-content" aria-label="Main content" tabindex="0">
                <div class="wrap">
                    <h2 class="nav-tab-wrapper">
                        <a href="<?php echo site_url(); ?>/wp-admin/themes.php?page=advance-menu-manager" class="nav-tab <?php if ("locations" != $menu_location_flag) echo 'nav-tab-active'; ?> "> <?php _e('Menus'); ?></a>
                        <a href="<?php echo site_url(); ?>/wp-admin/themes.php?page=advance-menu-manager&action=locations" class="nav-tab <?php if ("locations" == $menu_location_flag) echo 'nav-tab-active'; ?>"><?php _e('Manage Locations'); ?></a>					
                    </h2>				
                    <?php
                    if ($locations_screen) :

                        if (1 == $num_locations) {
                            echo '<p>' . __('Your theme supports one menu. Select which menu you would like to use.') . '</p>';
                        } else {
                            echo '<p>' . sprintf(_n('Your theme supports %s menu. Select which menu appears in each location.', 'Your theme supports %s menus. Select which menu appears in each location.', $num_locations), number_format_i18n($num_locations)) . '</p>';
                        }
                        ?>
                        <div id="menu-locations-wrap">
                            <form method="post" action="">
                                <table class="widefat fixed" id="menu-locations-table">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="manage-column column-locations"><?php _e('Theme Location'); ?></th>
                                            <th scope="col" class="manage-column column-menus"><?php _e('Assigned Menu'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody class="menu-locations">
                                        <?php foreach ($locations as $_location => $_name) { ?>
                                            <tr class="menu-locations-row">
                                                <td class="menu-location-title"><label for="locations-<?php echo $_location; ?>"><?php echo $_name; ?></label></td>
                                                <td class="manu-manager-plus-locations-container">
                                                    <select name="menu-locations[<?php echo $_location; ?>]" id="locations-<?php echo $_location; ?>">
                                                        <option value="0"><?php printf('&mdash; %s &mdash;', esc_html__('Select a Menu')); ?></option>
                                                        <?php foreach ($nav_menus as $menu) : ?>
                                                            <?php $selected = isset($menu_locations[$_location]) && $menu_locations[$_location] == $menu->term_id; ?>
                                                            <option <?php if ($selected) echo 'data-orig="true"'; ?> <?php selected($selected); ?> value="<?php echo $menu->term_id; ?>">
                                                                <?php echo wp_html_excerpt($menu->name, 40, '&hellip;'); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <div class="locations-row-links">
                                                        <?php if (isset($menu_locations[$_location]) && 0 != $menu_locations[$_location]) : ?>
                                                            <span class="">
                                                                <a href="<?php echo esc_url(add_query_arg(array('page' => 'advance-menu-manager', 'menu' => $menu_locations[$_location]), admin_url('themes.php'))); ?>">
                                                                    <span aria-hidden="true"><?php _ex('Edit', 'menu'); ?></span><span class="screen-reader-text"><?php _e('Edit selected menu'); ?></span>
                                                                </a>
                                                            </span>
                                                        <?php endif; ?>													
                                                    </div><!-- .locations-row-links -->
                                                </td><!-- .menu-location-menus -->
                                            </tr><!-- .menu-locations-row -->
                                        <?php } // foreach  ?>
                                    </tbody>
                                </table>
                                <p class="button-controls"><?php submit_button(__('Save Changes'), 'primary left', 'nav-menu-locations', false); ?></p>
                                <?php wp_nonce_field('save-menu-locations'); ?>
                                <input type="hidden" name="menu" id="nav-menu-meta-object-id" value="<?php echo esc_attr($nav_menu_selected_id); ?>" />
                            </form>
                        </div><!-- #menu-locations-wrap -->
                        <?php
                        /**
                         * Fires after the menu locations table is displayed.
                         *
                         * @since 3.6.0
                         */
                        //do_action( 'after_menu_locations_table' ); 
                        ?>
                    <?php else : ?>
                        <input type="hidden" name="menu" id="nav-menu-meta-object-id" value="<?php echo esc_attr($nav_menu_selected_id); ?>" />				
                        <div class="manage-menus" <?php if (!empty($menu_location_flag)) echo 'style="display:none"'; ?>>			
                            <form action="" name="menu_select" method="POST" >
                                <label for="menu" class="selected-menu"><?php _e('Select a menu to edit:'); ?></label>						

                                <select name="page_on_front" id="page_on_front">
                                    <?php
                                    $current_selected_menu_name = '';

                                    //$nav_menu_object = get_terms( 'nav_menu', array( 'hide_empty' => true ) );
                                    foreach ($nav_menus as $list_nav_menus) {
                                        ?>
                                        <option value='<?php echo $list_nav_menus->term_id; ?>' <?php
                                        if ($nav_menu_selected_id == $list_nav_menus->term_id) {
                                            echo 'selected=selected';
                                        }
                                        ?> class='level-0'>
                                                <?php
                                                _e($list_nav_menus->name);

                                                if (!empty($menu_locations) && in_array($list_nav_menus->term_id, $menu_locations)) {
                                                    $locations_assigned_to_this_menu = array();
                                                    foreach (array_keys($menu_locations, $list_nav_menus->term_id) as $menu_location_key) {
                                                        if (isset($locations[$menu_location_key])) {
                                                            $locations_assigned_to_this_menu[] = $locations[$menu_location_key];
                                                        }
                                                    }

                                                    /**
                                                     * Filter the number of locations listed per menu in the drop-down select.
                                                     *
                                                     * @since 3.6.0
                                                     *
                                                     * @param int $locations Number of menu locations to list. Default 3.
                                                     */
                                                    $assigned_locations = array_slice($locations_assigned_to_this_menu, 0, absint(apply_filters('wp_nav_locations_listed_per_menu', 3)));

                                                    // Adds ellipses following the number of locations defined in $assigned_locations.
                                                    if (!empty($assigned_locations)) {
                                                        printf(' (%1$s%2$s)', implode(', ', $assigned_locations), count($locations_assigned_to_this_menu) > count($assigned_locations) ? ' &hellip;' : ''
                                                        );
                                                    }
                                                }
                                                ?>
                                        </option>
                                        <?php
                                        if ($nav_menu_selected_id == $list_nav_menus->term_id) {
                                            $current_selected_menu_name = $list_nav_menus->name;
                                        }
                                    }
                                    ?>
                                </select>
                                <span class="submit-btn"><input id="menu_submit_button" class="button-secondary" value="Select" type="submit">
                                    <label for="menu" class="selected-menu_mymenu"><?php _e('or'); ?>&nbsp;&nbsp;<span class="add-new-menu-action_custom"><?php _e('create a new menu'); ?></span></span></label>
                            </form>
                        </div><!-- /manage-menus -->
                        <?php
                        if ($menu_count <= 0) {
                            $display_none_property = 'style="display:none"';
                            $display_block_property = 'style="display:block"';
                        } else {
                            $display_none_property = '';
                            $display_block_property = '';
                        }
                        ?>
                        <div class="manage-menus" id="manage-menus_add_new_menu" <?php
                        if (!empty($menu_location_flag)) {
                            echo 'style="display:none"';
                        } echo $display_block_property;
                        ?>>
                            <label for="menu" class="selected-menu_custom"><?php _e('Menu Name'); ?> &nbsp;</label>
                            <input name="custom-new-menu-name" id="custom-new-menu-name" class="menu-name regular-text menu-item-textbox input-with-default-title" placeholder="<?php _e('Enter menu name here'); ?>" value="" type="text">
                            <span class="submit-btn_save_custom_menu">
                                <button name="save_custom_menu" id="save_menu_custom" class="button button-primary menu-save"><?php _e('Create Menu'); ?></button></span>
                        </div><!-- manage-menus add new menu -->

                        <div id="nav-menus-frame" class="menu_manager_plus" <?php echo $display_none_property; ?>>							
                            <div id="menu-management">
                                <form action="" method="post" enctype="multipart/form-data" id="md_amm_menu_form">

                                    <div id="nav-menu-header">
                                        <div class="major-publishing-actions">
                                            <label class="menu-name-label howto open-label" for="menu-name">
                                                <span>Menu Name</span>
                                                <input name="menu-name" id="menu-name" type="text" class="menu-name regular-text menu-item-textbox" title="Enter menu name here" value="<?php if (!empty($current_selected_menu_name)) echo $current_selected_menu_name; ?>">
                                                <input type="hidden"  name="old-menu-name" value="<?php if (!empty($current_selected_menu_name)) echo $current_selected_menu_name; ?>">
                                            </label>
                                            <div class="publishing-action">
                                                <input type="submit" name="save_menu" id="save_menu_header" class="button button-primary menu-save" value="Save Menu">
                                            </div><!-- END .publishing-action -->
                                        </div><!-- END .major-publishing-actions -->
                                    </div><!-- end nav-menu-header -->
                                    <?php
                                    $menu_items = wp_get_nav_menu_items($nav_menu_selected_id);
                                    $data = array();
                                    $mydata = $menu_items;
                                    ?>
                                    <div id="menu_container" <?php
                                    if (empty($menu_location_flag))
                                        echo 'style="display:block"';
                                    else {
                                        echo 'style="display:none"';
                                    }
                                    ?>>
                                        <div class="manage-menus"  id="menu_list_id">
                                            <div id="nav_menu_frame">

                                                <div id="menu-management-liquid"> 
                                                    <div id="" style="background:none;">								    		
                                                        <h3><?php _e('Menu Structure'); ?></h3>
                                                        <div class="drag-instructions post-body-plain"><?php _e('Drag each item into the order you prefer. Click the arrow on the right of the item to reveal additional configuration options.'); ?></div>
                                                        <?php if (!empty($menu_items)) { ?>
                                                            <div class="amm_top_menu_wrapper">
                                                                <!--p id="amm_menu_item_id" title="Toggle Menu item id">&nbsp;</p -->					

                                                                <p id="nestable-menu">
                                                                    <span class="toggle_plus" id="toggle_plus_action" data-action="expand-all" title="Expand child menu item" style="font-weight: bold;">Expand</span> |
                                                                    <span class="toggle_minus" id="toggle_minus_action" data-action="collapse-all" title="Collapse child menu item">Collapse</span>
                                                                </p>
                                                            </div>
                                                        <?php } ?>
                                                        <?php
                                                        static $depth = 0;
                                                        $depth1 = 0;
                                                        $flag_array = array();
                                                        $flag_array_one = array();
                                                        $menu_item_ids = '';
                                                        if (empty($menu_items)) {
                                                            echo '<div class="myh2"><h3>No Menu Found</h3></div>';
                                                            echo '<div class="myh2"><span class="add_first_menu_item"></span></div>';
                                                            echo '<ul id="menu-to-edit" class="menu ui-sortable menu-manager-plus-menu-wrapper"></ul>';
                                                        } else {
                                                            ?>

                                                            <ul id="menu-to-edit" class="menu ui-sortable menu-manager-plus-menu-wrapper">
                                                                <?php
                                                                for ($amm = 0; $amm < count($menu_items); $amm++) {
                                                                    // add menu id as globle
                                                                    $gloable_all_current_menu_id[] = $menu_items[$amm]->object_id;

                                                                    if ($menu_items[$amm]->menu_item_parent == 0) {
                                                                        $depth = 0;
                                                                        unset($flag_array);
                                                                    }
                                                                    if (0 != $amm) {
                                                                        if ($menu_items[$amm]->menu_item_parent == $menu_items[$amm - 1]->ID) {
                                                                            $depth++;
                                                                        }
                                                                    }

                                                                    $menu_item_ids .= $menu_items[$amm]->object_id . ",";

                                                                    if (!empty($menu_items[$amm + 1]->menu_item_parent) && $menu_items[$amm]->ID == $menu_items[$amm + 1]->menu_item_parent) {
                                                                        $flag_array[] = $menu_items[$amm]->ID;
                                                                    }

                                                                    $current_menu_item_id = $menu_items[$amm]->db_id;
                                                                    $current_menu_item_type = $menu_items[$amm]->type;
                                                                    $current_menu_item_url = $menu_items[$amm]->url;
                                                                    ?> 
                                                                    <li id="menu-item-<?php if (isset($menu_items[$amm]->db_id)) echo $menu_items[$amm]->db_id; ?>" class="menu-item menu-item-depth-<?php echo $depth; ?> menu-item-page menu-item-edit-inactive" data-depth="<?php echo $depth; ?>" >
                                                                        <div class="menu-item-bar">
                                                                            <div class="menu-item-handle ui-sortable-handle">
                                                                                <span class="item-title">
                                                                                    <?php
                                                                                    $menu_not_exist = '';
                                                                                    if ('post_type' == $menu_items[$amm]->type) {
                                                                                        $post_status = get_post_status($menu_items[$amm]->object_id);
                                                                                        if ('publish' != $post_status) {
                                                                                            //post exist or not
                                                                                            $menu_not_exist = 'post_item_deleted';
                                                                                        }
                                                                                        ?>
                                                                                        <span class="menu-item-title <?php echo $menu_not_exist; ?> ">
                                                                                            <?php _e($menu_items[$amm]->title); ?>
                                                                                            <span class="amm_main_menu_item_edit <?php echo $menu_not_exist; ?>" title="Edit this item">&nbsp;</span>
                                                                                        </span>
                                                                                    <?php } else { ?>
                                                                                        <span class="menu-item-title"><?php _e($menu_items[$amm]->title); ?></span>
                                                                                    <?php } ?>																						
                                                                                    <span class="is-submenu"><?php if ($menu_items[$amm]->menu_item_parent <> 0) _e('sub item'); ?></span>
                                                                                </span>
                                                                                <span class="item-controls"> 
                                                                                    <span class="view_menu_id">#menu-item-<?php echo $menu_items[$amm]->db_id; ?> </span>
                                                                                    <span class="menu_item_type"><?php echo $menu_items[$amm]->type_label; ?></span>
                                                                                    <span class="menu_sub_details" title="View Attributes">&nbsp;</span>
                                                                                    <span data-attr-menu-item='<?php echo $menu_items[$amm]->db_id; ?>' class="delete_node" title="Delete this item">X</span> 
                                                                                </span>
                                                                            </div>
                                                                            <span class="my-menu-controls">
                                                                                <span class="my-menu-controls-groups">
                                                                                    <?php
                                                                                    $menu_exisit_class_name = '';
                                                                                    if (!empty($menu_items[$amm + 1]->menu_item_parent) && !empty($menu_items[$amm]->ID)) {
                                                                                        if ($menu_items[$amm]->ID == $menu_items[$amm + 1]->menu_item_parent) {
                                                                                            $menu_exisit_class_name = 'chiled-hide';
                                                                                        }
                                                                                    }
                                                                                    ?>
                                                                                    <span id="" class="click block_hide_show <?php echo $menu_exisit_class_name; ?>" title="Hide/Show child menu item"></span>
                                                                                    <span id="" class="child_items" title="" ></span><span class="amm_highlighter" >&nbsp;</span>
                                                                                </span>
                                                                                <span class="add_menu_item_in_nav_menu" title="Add new menu item below this menu item">&nbsp;</span>
                                                                            </span>
                                                                        </div>									
                                                                        <div id="menu-item-settings-<?php echo $menu_items[$amm]->db_id; ?>" class="menu-item-settings menu-manager-plus-setting">
                                                                            <?php if ('custom' == $current_menu_item_type) : ?>
                                                                                <p class="field-url description description-wide">
                                                                                    <label for="edit-menu-item-url-<?php echo $current_menu_item_id; ?>">
                                                                                        <?php _e('URL'); ?><br />
                                                                                        <input type="text" id="edit-menu-item-url-<?php echo $current_menu_item_id; ?>" class="widefat code edit-menu-item-url" name="menu-item-url[<?php echo $current_menu_item_id; ?>]" value="<?php echo esc_attr($current_menu_item_url); ?>" />
                                                                                    </label>
                                                                                </p>
                                                                            <?php endif; ?>
                                                                            <p class="description description-wide">
                                                                                <label for="edit-menu-item-title-<?php echo $current_menu_item_id; ?>">
                                                                                    <?php _e('Navigation Label'); ?><br />
                                                                                    <input type="text" id="edit-menu-item-title-<?php echo $current_menu_item_id; ?>" class="widefat edit-menu-item-title" name="menu-item-title[<?php echo $current_menu_item_id; ?>]" value="<?php echo esc_attr($menu_items[$amm]->title); ?>" />
                                                                                </label>
                                                                            </p>
                                                                            <p class="field-title-attribute description description-wide">
                                                                                <label for="edit-menu-item-attr-title-<?php echo $current_menu_item_id; ?>">
                                                                                    <?php _e('Title Attribute'); ?><br />
                                                                                    <input type="text" id="edit-menu-item-attr-title-<?php echo $current_menu_item_id; ?>" class="widefat edit-menu-item-attr-title" name="menu-item-attr-title[<?php echo $current_menu_item_id; ?>]" value="<?php echo esc_attr($menu_items[$amm]->post_excerpt); ?>" />
                                                                                </label>
                                                                            </p>
                                                                            <p class="field-link-target description">
                                                                                <label for="edit-menu-item-target-<?php echo $current_menu_item_id; ?>">
                                                                                    <input type="checkbox" id="edit-menu-item-target-<?php echo $current_menu_item_id; ?>" value="_blank" name="menu-item-target[<?php echo $current_menu_item_id; ?>]"<?php checked($menu_items[$amm]->target, '_blank'); ?> />
                                                                                    <?php _e('Open link in a new window/tab'); ?>
                                                                                </label>
                                                                            </p>
                                                                            <p class="field-css-classes description description-thin">
                                                                                <label for="edit-menu-item-classes-<?php echo $current_menu_item_id; ?>">
                                                                                    <?php _e('CSS Classes (optional)'); ?><br />
                                                                                    <input type="text" id="edit-menu-item-classes-<?php echo $current_menu_item_id; ?>" class="widefat code edit-menu-item-classes" name="menu-item-classes[<?php echo $current_menu_item_id; ?>]" value="<?php echo esc_attr(implode(' ', $menu_items[$amm]->classes)); ?>" />
                                                                                </label>
                                                                            </p>
                                                                            <p class="field-xfn description description-thin">
                                                                                <label for="edit-menu-item-xfn-<?php echo $current_menu_item_id; ?>">
                                                                                    <?php _e('Link Relationship (XFN)'); ?><br />
                                                                                    <input type="text" id="edit-menu-item-xfn-<?php echo $current_menu_item_id; ?>" class="widefat code edit-menu-item-xfn" name="menu-item-xfn[<?php echo $current_menu_item_id; ?>]" value="<?php echo esc_attr($menu_items[$amm]->xfn); ?>" />
                                                                                </label>
                                                                            </p>
                                                                            <p class="field-description description description-wide">
                                                                                <label for="edit-menu-item-description-<?php echo $current_menu_item_id; ?>">
                                                                                    <?php _e('Description'); ?><br />
                                                                                    <textarea id="edit-menu-item-description-<?php echo $current_menu_item_id; ?>" class="widefat edit-menu-item-description" rows="3" cols="20" name="menu-item-description[<?php echo $current_menu_item_id; ?>]"><?php echo esc_html($menu_items[$amm]->description); // textarea_escaped      ?></textarea>
                                                                                    <span class="description"><?php _e('The description will be displayed in the menu if the current theme supports it.'); ?></span>
                                                                                </label>
                                                                            </p>												
                                                                            <div class="menu-item-actions description-wide submitbox">																						
                                                                                <a href="#" class="item-delete submitdelete deletion submitdelete menu_manager-plus-setting-delete">Remove </a> <span class="meta-sep hide-if-no-js"> | </span> <a href="#" class="item-cancel submitcancel hide-if-no-js menu_manager-plus-setting-cancel">Cancel</a>
                                                                            </div>

                                                                            <input class="menu-item-data-db-id" name="menu-item-db-id[<?php echo $menu_items[$amm]->db_id; ?>]" value="<?php echo esc_attr($menu_items[$amm]->db_id); ?>" type="hidden">
                                                                            <input class="menu-item-data-object-id" name="menu-item-object-id[<?php echo $menu_items[$amm]->db_id; ?>]" value="<?php echo esc_attr($menu_items[$amm]->object_id); ?>" type="hidden">

                                                                            <input class="menu-item-data-object" name="menu-item-object[<?php echo $menu_items[$amm]->db_id; ?>]" value="<?php echo esc_attr($menu_items[$amm]->object); ?>" type="hidden">
                                                                            <input class="menu-item-data-parent-id" name="menu-item-parent-id[<?php echo $menu_items[$amm]->db_id; ?>]" value="<?php echo esc_attr($menu_items[$amm]->menu_item_parent); ?>" type="hidden">
                                                                            <input class="menu-item-data-position" name="menu-item-position[<?php echo $menu_items[$amm]->db_id; ?>]" value="<?php echo esc_attr($menu_items[$amm]->menu_order); ?>" type="hidden">
                                                                            <input class="menu-item-data-type" name="menu-item-type[<?php echo $menu_items[$amm]->db_id; ?>]" value="<?php echo esc_attr($menu_items[$amm]->type); ?>" type="hidden">
                                                                        </div>
                                                                        <ul class="menu-item-transport"></ul>
                                                                        <div style="display: none;" class="amm_block_highlight"></div>
                                                                    </li>
                                                                    <?php
                                                                    if (!empty($menu_items[$amm + 1]->menu_item_parent) && $menu_items[$amm + 1]->menu_item_parent <> $menu_items[$amm]->ID) {
                                                                        if (in_array($menu_items[$amm + 1]->menu_item_parent, $flag_array)) {
                                                                            $a = array_search($menu_items[$amm + 1]->menu_item_parent, $flag_array);
                                                                            //$depth = $a - ($i-1);																			
                                                                            $depth = $a - (-1);
                                                                        }
                                                                    }
                                                                } // for loop end 
                                                                ?>									
                                                            </ul>
                                                            <?php
                                                        } // No menus found
                                                        ?>														
                                                    </div>
                                                </div><!-- menu-management-liquid-->
                                            </div>
                                        </div><!--- Manage menus -->												


                                        <div class="menu-settings" <?php if (!empty($one_theme_location_no_menus)) { ?>style="display: none;"<?php } ?>>
                                            <h3><?php _e('Menu Settings'); ?></h3>
                                            <?php
                                            if (!isset($auto_add)) {
                                                $auto_add = get_option('nav_menu_options');
                                                if (!isset($auto_add['auto_add']))
                                                    $auto_add = false;
                                                elseif (false !== array_search($nav_menu_selected_id, $auto_add['auto_add']))
                                                    $auto_add = true;
                                                else
                                                    $auto_add = false;
                                            }
                                            ?>

                                            <dl class="auto-add-pages">
                                                <dt class="howto"><?php _e('Auto add pages'); ?></dt>
                                                <dd class="checkbox-input"><input type="checkbox"<?php checked($auto_add); ?> name="auto-add-pages" id="auto-add-pages" value="1" /> <label for="auto-add-pages"><?php printf(__('Automatically add new top-level pages to this menu'), esc_url(admin_url('edit.php?post_type=page'))); ?></label></dd>
                                            </dl>

                                            <?php if (current_theme_supports('menus')) : ?>						
                                                <dl class="menu-theme-locations">
                                                    <dt class="howto"><?php _e('Theme locations'); ?></dt>
                                                    <?php foreach ($locations as $location => $description) : ?>
                                                        <dd class="checkbox-input">
                                                            <input type="checkbox"<?php checked(isset($menu_locations[$location]) && $menu_locations[$location] == $nav_menu_selected_id); ?> name="menu-locations[<?php echo esc_attr($location); ?>]" id="locations-<?php echo esc_attr($location); ?>" value="<?php echo esc_attr($nav_menu_selected_id); ?>" /> <label for="locations-<?php echo esc_attr($location); ?>"><?php echo $description; ?></label>
                                                            <?php if (!empty($menu_locations[$location]) && $menu_locations[$location] != $nav_menu_selected_id) : ?>
                                                                <span class="theme-location-set"> <?php printf(__("(Currently set to: %s)"), wp_get_nav_menu_object($menu_locations[$location])->name); ?> </span>
                                                            <?php endif; ?>
                                                        </dd>
                                                    <?php endforeach; ?>
                                                </dl>					
                                            <?php endif; ?>					
                                        </div>										
                                        <div class="submit-btn_save_menu">
                                            <input type="hidden" name="current_edit_menu_id" value="<?php echo esc_attr($nav_menu_selected_id); ?>" />
                                            <input type="hidden" name="delete_menu_items" value="" id="delete_menu_items">
                                            <input type="hidden" name="total_menu_items" value="<?php echo $menu_item_ids; ?>" id="total_menu_items">
                                            <!--<input type="hidden" name="amm_menu_id" value="<?php //echo $nav_menu_object->term_id;     ?>" id="amm_menu_id"> -->
                                            <span class="submit-btn_save_menu_span">
                                                <input id="save_menu_header" name="save_menu" class="button button-primary menu-save" class="custom" value="<?php _e('Save Menu'); ?>" type="submit">
                                            </span>
                                            <span id="amm_menu_delete" class="submit_delete"><?php _e('Delete Menu'); ?> </span>													
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <!-- / menu-management -->	
                            <div id="amm_menu_revision">		
                                <img src="<?php echo PLUGIN_BASE_PATH; ?>pro_revision.jpg" align="amm_menu_revision">										
                            </div>
                            <div class="AMM_pro_btn">
                                <a href="http://codecanyon.net/item/advance-menu-manager/15275037" target="_blank" class="menu_pro" > Get access to the Menu Revision Functionality with the Pro Version. </a>
                            </div>									
                            <div id="amm_menu_shortcode_wrapper">
                                <img src="<?php echo PLUGIN_BASE_PATH; ?>pro_menu_shortcode.jpg" align="amm_menu_revision">
                            </div>
                            <div class="AMM_pro_btn">
                                <a href="http://codecanyon.net/item/advance-menu-manager/15275037" target="_blank" class="menu_pro" > Get access to the Short Code Functionality with the Pro Version. </a>
                            </div>



                        </div> <!-- / menu_manager_plus -->		
                    </div><!-- / wrap-->
                </div><!-- /#wpbody-content -->
            </div><!-- /wpbody -->
            <div id="amm_back_to_content">					
                <span class="back-to-top" title="Go to Top of page"></span>
            </div>


            <!-- popup -->
            <div id="menu_manager_popup">
                <div id="menu_manager_popup_container">
                    <div id="mm_cancel">X</div>
                    <div id="menu_manager_popup_main-wrapper" class="menu_manager_plus">
                        <div class="menu_page_cat_tag_form_wrapper">
                            <div id="nav-menus-frame">
                                <div id="menu-settings-column" class="metabox-holder<?php
                                if (isset($_GET['menu']) && '0' == $_GET['menu']) {
                                    echo ' metabox-holder-disabled';
                                }
                                ?>">
                                    <div class="clear"></div>							
                                    <form id="nav-menu-meta" class="nav-menu-meta" method="post" enctype="multipart/form-data">
                                        <input type="hidden" name="menu" id="nav-menu-meta-object-id" value="<?php echo esc_attr($nav_menu_selected_id); ?>" />
                                        <input type="hidden" name="action" value="add-menu-item" />
                                        <?php wp_nonce_field('add-menu_item', 'menu-settings-column-nonce'); ?>
                                        <?php
                                        //do_accordion_sections( 'nav-menus', 'side', null ); 
                                        do_accordion_sections_own('nav-menus', 'side', null);
                                        //wp_nav_menu_item_link_meta_box();
                                        ?>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>				
                </div>
            </div>
            <!-- end popup -->
        <?php
        endif;
    }

    /**
     * my_action_ajax_for_create_menu function
     *
     * This function will add your newly entered menu name in textbox.
     *
     * @version		1.0.0
     * @author 		Multidots  
     */
    function my_action_ajax_for_create_menu() {
        // Check if the menu exists
        $menu_name = $_POST['new_menu_name'];
        $menu_exists = wp_get_nav_menu_object($menu_name);

        // If it doesn't exist, let's create it.
        if (!$menu_exists) {
            $menu_id = wp_create_nav_menu($menu_name);
            $user_details = wp_get_current_user();
            update_user_meta($user_details->ID, 'nav_menu_recently_edited', $menu_id);

            echo 1;
        } else {
            /* New menu allreday there */
            echo 2;
        }
        exit();
    }

    // function for dotstore

    function menu_container_print_custom() {

        global $gloable_all_author_array;
        global $gloable_all_template_array;
        global $gloable_all_category_array;
        global $gloable_all_current_menu_id;
        //set all author globaly
        $allUsers = get_users('orderby=ID&order=ASC');
        foreach ($allUsers as $currentUser) {
            if (!in_array('subscriber', $currentUser->roles)) {
                $gloable_all_author_array[] = $currentUser;
            }
        }

        // set all template globaly
        $get_templates_all = get_page_templates();
        foreach ($get_templates_all as $template_name => $template_filename) {
            $gloable_all_template_array[$template_name] = $template_filename;
        }

        // set all category by globaly		
        $all_category = get_categories('orderby=name&hide_empty=0');
        foreach ($all_category as $cat_data) {
            $gloable_all_category_array[$cat_data->cat_ID] = $cat_data->cat_name;
        }
        $form_submited_messages = isset($_REQUEST ['save_menu']) ? md_admin_interface::amm_save_existing_menu($_REQUEST ['total_menu_items']) : false;

        wp_nav_menu_post_type_meta_boxes();
        wp_nav_menu_taxonomy_meta_boxes();
        wp_enqueue_script('nav-menu');

        if (wp_is_mobile())
            wp_enqueue_script('jquery-touch-punch');

        $nav_menu_selected_id = isset($_REQUEST['menu']) ? (int) $_REQUEST['menu'] : 0;

        // Get recently edited nav menu.
        $recently_edited = absint(get_user_option('nav_menu_recently_edited'));
        if (empty($recently_edited) && is_nav_menu($nav_menu_selected_id))
            $recently_edited = $nav_menu_selected_id;

        // Use $recently_edited if none are selected.
        if (empty($nav_menu_selected_id) && !isset($_GET['menu']) && is_nav_menu($recently_edited))
            $nav_menu_selected_id = $recently_edited;


        if (empty($nav_menu_selected_id) && !empty($nav_menus) && !$add_new_screen) {
            // if we have no selection yet, and we have menus, set to the first one in the list.
            $nav_menu_selected_id = $nav_menus[0]->term_id;
        }

        // Update the user's setting.
        if ($nav_menu_selected_id != $recently_edited && is_nav_menu($nav_menu_selected_id))
            update_user_meta(get_current_user_id(), 'nav_menu_recently_edited', $nav_menu_selected_id);

        //if menu hase change on dropdwon  and save menu than recently menu option will update
        if (!empty($_POST['page_on_front'])) {
            $nav_menu_selected_id = (int) $_POST['page_on_front'];
            $user_id = get_current_user_id();
            update_user_meta($user_id, 'nav_menu_recently_edited', $nav_menu_selected_id);
        }


        $locations = get_registered_nav_menus();
        $menu_locations = get_nav_menu_locations();
        $num_locations = count(array_keys($locations));
        //$nav_menu_selected_id = 27;
        //submit themes location form
        if (isset($_GET['action']) && 'locations' == $_GET['action'] && isset($_POST['menu-locations'])) {
            if (isset($_POST['menu-locations'])) {
                check_admin_referer('save-menu-locations');

                $new_menu_locations = array_map('absint', $_POST['menu-locations']);
                $menu_locations = array_merge($menu_locations, $new_menu_locations);
                // Set menu locations
                set_theme_mod('nav_menu_locations', $menu_locations);
                $messages[] = '<div id="message" class="updated notice is-dismissible"><p>' . __('Menu locations updated.') . '</p></div>';
            }
        }

        $menu_revision_tab = '';
        if (isset($_GET['action']) && 'locations' == $_GET['action']) {
            $menu_location_flag = $_GET['action'];
        } else {
            $menu_location_flag = '';
        }
        $locations_screen = ( isset($_GET['action']) && 'locations' == $_GET['action'] ) ? true : false;

        // Get all nav menus.
        $nav_menus = wp_get_nav_menus();
        $menu_count = count($nav_menus);

        wp_nav_menu_setup();
        wp_initial_nav_menu_meta_boxes();
        ?>


        <script type="text/javascript" src="<?php echo PLUGIN_PATH . 'js/fancy_alert.js'; ?>"></script>
        <script type="text/javascript" src="<?php echo PLUGIN_PATH . 'js/md_pagination.js'; ?>"></script>


        <div id="Advance_menu_manager_messages">
            <?php if (!empty($form_submited_messages)) echo $form_submited_messages; ?>				
        </div>	
        <div id="wpbody">			
            <div style="overflow: hidden;" id="wpbody-content" aria-label="Main content" tabindex="0">
                <div class="wrap">

                    <?php
                    if ($locations_screen) :

                        if (1 == $num_locations) {
                            echo '<p>' . __('Your theme supports one menu. Select which menu you would like to use.') . '</p>';
                        } else {
                            echo '<p>' . sprintf(_n('Your theme supports %s menu. Select which menu appears in each location.', 'Your theme supports %s menus. Select which menu appears in each location.', $num_locations), number_format_i18n($num_locations)) . '</p>';
                        }
                        ?>
                        <div id="menu-locations-wrap">
                            <form method="post" action="">

                                <p class="button-controls"><?php submit_button(__('Save Changes'), 'primary left', 'nav-menu-locations', false); ?></p>
                                <?php wp_nonce_field('save-menu-locations'); ?>
                                <input type="hidden" name="menu" id="nav-menu-meta-object-id" value="<?php echo esc_attr($nav_menu_selected_id); ?>" />
                            </form>
                        </div><!-- #menu-locations-wrap -->
                        <?php
                        /**
                         * Fires after the menu locations table is displayed.
                         *
                         * @since 3.6.0
                         */
                        //do_action( 'after_menu_locations_table' ); 
                        ?>
                    <?php else : ?>
                        <input type="hidden" name="menu" id="nav-menu-meta-object-id" value="<?php echo esc_attr($nav_menu_selected_id); ?>" />				
                        <div class="manage-menus" <?php if (!empty($menu_location_flag)) echo 'style="display:none"'; ?>>			
                            <form action="" name="menu_select" method="POST" >
                                <label for="menu" class="selected-menu"><?php _e('Select a menu to edit:'); ?></label>						

                                <select name="page_on_front" id="page_on_front">
                                    <?php
                                    $current_selected_menu_name = '';

                                    //$nav_menu_object = get_terms( 'nav_menu', array( 'hide_empty' => true ) );
                                    foreach ($nav_menus as $list_nav_menus) {
                                        ?>
                                        <option value='<?php echo $list_nav_menus->term_id; ?>' <?php
                                        if ($nav_menu_selected_id == $list_nav_menus->term_id) {
                                            echo 'selected=selected';
                                        }
                                        ?> class='level-0'>
                                                <?php
                                                _e($list_nav_menus->name);

                                                if (!empty($menu_locations) && in_array($list_nav_menus->term_id, $menu_locations)) {
                                                    $locations_assigned_to_this_menu = array();
                                                    foreach (array_keys($menu_locations, $list_nav_menus->term_id) as $menu_location_key) {
                                                        if (isset($locations[$menu_location_key])) {
                                                            $locations_assigned_to_this_menu[] = $locations[$menu_location_key];
                                                        }
                                                    }

                                                    /**
                                                     * Filter the number of locations listed per menu in the drop-down select.
                                                     *
                                                     * @since 3.6.0
                                                     *
                                                     * @param int $locations Number of menu locations to list. Default 3.
                                                     */
                                                    $assigned_locations = array_slice($locations_assigned_to_this_menu, 0, absint(apply_filters('wp_nav_locations_listed_per_menu', 3)));

                                                    // Adds ellipses following the number of locations defined in $assigned_locations.
                                                    if (!empty($assigned_locations)) {
                                                        printf(' (%1$s%2$s)', implode(', ', $assigned_locations), count($locations_assigned_to_this_menu) > count($assigned_locations) ? ' &hellip;' : ''
                                                        );
                                                    }
                                                }
                                                ?>
                                        </option>
                                        <?php
                                        if ($nav_menu_selected_id == $list_nav_menus->term_id) {
                                            $current_selected_menu_name = $list_nav_menus->name;
                                        }
                                    }
                                    ?>
                                </select>
                                <span class="submit-btn"><input id="menu_submit_button" class="button-secondary" value="Select" type="submit">
                                    <label for="menu" class="selected-menu_mymenu"><?php _e('or'); ?>&nbsp;&nbsp;<span class="add-new-menu-action_custom"><?php _e('create a new menu'); ?></span></span></label>
                            </form>
                        </div><!-- /manage-menus -->
                        <?php
                        if ($menu_count <= 0) {
                            $display_none_property = 'style="display:none"';
                            $display_block_property = 'style="display:block"';
                        } else {
                            $display_none_property = '';
                            $display_block_property = '';
                        }
                        ?>
                        <div class="manage-menus" id="manage-menus_add_new_menu" <?php
                        if (!empty($menu_location_flag)) {
                            echo 'style="display:none"';
                        } echo $display_block_property;
                        ?>>
                            <label for="menu" class="selected-menu_custom"><?php _e('Menu Name'); ?> &nbsp;</label>
                            <input name="custom-new-menu-name" id="custom-new-menu-name" class="menu-name regular-text menu-item-textbox input-with-default-title" placeholder="<?php _e('Enter menu name here'); ?>" value="" type="text">
                            <span class="submit-btn_save_custom_menu">
                                <button name="save_custom_menu" id="save_menu_custom" class="button button-primary menu-save"><?php _e('Create Menu'); ?></button></span>
                        </div><!-- manage-menus add new menu -->

                        <div id="nav-menus-frame" class="menu_manager_plus" <?php echo $display_none_property; ?>>							
                            <div id="menu-management">
                                <form action="" method="post" enctype="multipart/form-data" id="md_amm_menu_form">

                                    <div id="nav-menu-header">
                                        <div class="major-publishing-actions">
                                            <label class="menu-name-label howto open-label" for="menu-name">
                                                <span>Menu Name</span>
                                                <input name="menu-name" id="menu-name" type="text" class="menu-name regular-text menu-item-textbox" title="Enter menu name here" value="<?php if (!empty($current_selected_menu_name)) echo $current_selected_menu_name; ?>">
                                                <input type="hidden"  name="old-menu-name" value="<?php if (!empty($current_selected_menu_name)) echo $current_selected_menu_name; ?>">
                                            </label>
                                            <div class="publishing-action">
                                                <input type="submit" name="save_menu" id="save_menu_header" class="button button-primary menu-save" value="Save Menu">
                                            </div><!-- END .publishing-action -->
                                        </div><!-- END .major-publishing-actions -->
                                    </div><!-- end nav-menu-header -->
                                    <?php
                                    $menu_items = wp_get_nav_menu_items($nav_menu_selected_id);
                                    $data = array();
                                    $mydata = $menu_items;
                                    ?>
                                    <div id="menu_container" <?php
                                    if (empty($menu_location_flag))
                                        echo 'style="display:block"';
                                    else {
                                        echo 'style="display:none"';
                                    }
                                    ?>>
                                        <div class="manage-menus"  id="menu_list_id">
                                            <div id="nav_menu_frame">

                                                <div id="menu-management-liquid"> 
                                                    <div id="" style="background:none;">								    		
                                                        <h3><?php _e('Menu Structure'); ?></h3>
                                                        <div class="drag-instructions post-body-plain"><?php _e('Drag each item into the order you prefer. Click the arrow on the right of the item to reveal additional configuration options.'); ?></div>
                                                        <?php if (!empty($menu_items)) { ?>
                                                            <div class="amm_top_menu_wrapper">
                                                                    <!--<p id="amm_menu_item_id" title="Toggle Menu item id">Show Menu Id</p>-->

                                                                <p id="nestable-menu">
                                                                    <span class="toggle_plus" id="toggle_plus_action" data-action="expand-all" title="Expand child menu item" style="font-weight: bold;">Expand</span> |
                                                                    <span class="toggle_minus" id="toggle_minus_action" data-action="collapse-all" title="Collapse child menu item">Collapse</span>
                                                                    <span id="amm_menu_item_id" title="Toggle Menu item id">Show Menu Id</span>
                                                                </p>
                                                            </div>
                                                        <?php } ?>
                                                        <?php
                                                        static $depth = 0;
                                                        $depth1 = 0;
                                                        $flag_array = array();
                                                        $flag_array_one = array();
                                                        $menu_item_ids = '';
                                                        if (empty($menu_items)) {
                                                            echo '<div class="myh2"><h3>No Menu Found</h3></div>';
                                                            echo '<div class="myh2"><span class="add_first_menu_item"></span></div>';
                                                            echo '<ul id="menu-to-edit" class="menu ui-sortable menu-manager-plus-menu-wrapper"></ul>';
                                                        } else {
                                                            ?>

                                                            <ul id="menu-to-edit" class="menu ui-sortable menu-manager-plus-menu-wrapper">
                                                                <?php
                                                                for ($amm = 0; $amm < count($menu_items); $amm++) {
                                                                    // add menu id as globle
                                                                    $gloable_all_current_menu_id[] = $menu_items[$amm]->object_id;

                                                                    if ($menu_items[$amm]->menu_item_parent == 0) {
                                                                        $depth = 0;
                                                                        unset($flag_array);
                                                                    }
                                                                    if (0 != $amm) {
                                                                        if ($menu_items[$amm]->menu_item_parent == $menu_items[$amm - 1]->ID) {
                                                                            $depth++;
                                                                        }
                                                                    }

                                                                    $menu_item_ids .= $menu_items[$amm]->object_id . ",";

                                                                    if (!empty($menu_items[$amm + 1]->menu_item_parent) && $menu_items[$amm]->ID == $menu_items[$amm + 1]->menu_item_parent) {
                                                                        $flag_array[$depth] = $menu_items[$amm]->ID;
                                                                    }

                                                                    $current_menu_item_id = $menu_items[$amm]->db_id;
                                                                    $current_menu_item_type = $menu_items[$amm]->type;
                                                                    $current_menu_item_url = $menu_items[$amm]->url;
                                                                    ?> 
                                                                    <li id="menu-item-<?php if (isset($menu_items[$amm]->db_id)) echo $menu_items[$amm]->db_id; ?>" class="menu-item menu-item-depth-<?php echo $depth; ?> menu-item-page menu-item-edit-inactive" data-depth="<?php echo $depth; ?>" >
                                                                        <div class="menu-item-bar">
                                                                            <div class="menu-item-handle ui-sortable-handle">
                                                                                <span class="item-title">
                                                                                    <?php
                                                                                    $menu_not_exist = '';
                                                                                    if ('post_type' == $menu_items[$amm]->type) {
                                                                                        $post_status = get_post_status($menu_items[$amm]->object_id);
                                                                                        if ('publish' != $post_status) {
                                                                                            //post exist or not
                                                                                            $menu_not_exist = 'post_item_deleted';
                                                                                        }
                                                                                        ?>
                                                                                        <span class="menu-item-title <?php echo $menu_not_exist; ?> ">
                                                                                            <?php _e($menu_items[$amm]->title); ?>
                                                                                            <span class="amm_main_menu_item_edit <?php echo $menu_not_exist; ?>" title="Edit this item">&nbsp;</span>
                                                                                        </span>
                                                                                    <?php } else { ?>
                                                                                        <span class="menu-item-title"><?php _e($menu_items[$amm]->title); ?></span>
                                                                                    <?php } ?>																						
                                                                                    <span class="is-submenu"><?php if ($menu_items[$amm]->menu_item_parent <> 0) _e('sub item'); ?></span>
                                                                                </span>
                                                                                <span class="item-controls"> 
                                                                                    <span class="view_menu_id">#menu-item-<?php echo $menu_items[$amm]->db_id; ?> </span>
                                                                                    <span class="menu_item_type"><?php echo $menu_items[$amm]->type_label; ?></span>
                                                                                    <span class="menu_sub_details" title="View Attributes">&nbsp;</span>
                                                                                    <span data-attr-menu-item='<?php echo $menu_items[$amm]->db_id; ?>' class="delete_node" title="Delete this item">X</span> 
                                                                                </span>
                                                                            </div>
                                                                            <span class="my-menu-controls">
                                                                                <span class="my-menu-controls-groups">
                                                                                    <?php
                                                                                    $menu_exisit_class_name = '';
                                                                                    if (!empty($menu_items[$amm + 1]->menu_item_parent) && !empty($menu_items[$amm]->ID)) {
                                                                                        if ($menu_items[$amm]->ID == $menu_items[$amm + 1]->menu_item_parent) {
                                                                                            $menu_exisit_class_name = 'chiled-hide';
                                                                                        }
                                                                                    }
                                                                                    ?>
                                                                                    <span id="" class="click block_hide_show <?php echo $menu_exisit_class_name; ?>" title="Hide/Show child menu item"></span>
                                                                                    <span id="" class="child_items" title="" ></span><span class="amm_highlighter" >&nbsp;</span>
                                                                                </span>
                                                                                <span class="add_menu_item_in_nav_menu" title="Add new menu item below this menu item">&nbsp;</span>
                                                                            </span>
                                                                        </div>									
                                                                        <div id="menu-item-settings-<?php echo $menu_items[$amm]->db_id; ?>" class="menu-item-settings menu-manager-plus-setting">
                                                                            <?php if ('custom' == $current_menu_item_type) : ?>
                                                                                <p class="field-url description description-wide">
                                                                                    <label for="edit-menu-item-url-<?php echo $current_menu_item_id; ?>">
                                                                                        <?php _e('URL'); ?><br />
                                                                                        <input type="text" id="edit-menu-item-url-<?php echo $current_menu_item_id; ?>" class="widefat code edit-menu-item-url" name="menu-item-url[<?php echo $current_menu_item_id; ?>]" value="<?php echo esc_attr($current_menu_item_url); ?>" />
                                                                                    </label>
                                                                                </p>
                                                                            <?php endif; ?>
                                                                            <p class="description description-wide">
                                                                                <label for="edit-menu-item-title-<?php echo $current_menu_item_id; ?>">
                                                                                    <?php _e('Navigation Label'); ?><br />
                                                                                    <input type="text" id="edit-menu-item-title-<?php echo $current_menu_item_id; ?>" class="widefat edit-menu-item-title" name="menu-item-title[<?php echo $current_menu_item_id; ?>]" value="<?php echo esc_attr($menu_items[$amm]->title); ?>" />
                                                                                </label>
                                                                            </p>
                                                                            <p class="field-title-attribute description description-wide">
                                                                                <label for="edit-menu-item-attr-title-<?php echo $current_menu_item_id; ?>">
                                                                                    <?php _e('Title Attribute'); ?><br />
                                                                                    <input type="text" id="edit-menu-item-attr-title-<?php echo $current_menu_item_id; ?>" class="widefat edit-menu-item-attr-title" name="menu-item-attr-title[<?php echo $current_menu_item_id; ?>]" value="<?php echo esc_attr($menu_items[$amm]->post_excerpt); ?>" />
                                                                                </label>
                                                                            </p>
                                                                            <p class="field-link-target description">
                                                                                <label for="edit-menu-item-target-<?php echo $current_menu_item_id; ?>">
                                                                                    <input type="checkbox" id="edit-menu-item-target-<?php echo $current_menu_item_id; ?>" value="_blank" name="menu-item-target[<?php echo $current_menu_item_id; ?>]"<?php checked($menu_items[$amm]->target, '_blank'); ?> />
                                                                                    <?php _e('Open link in a new window/tab'); ?>
                                                                                </label>
                                                                            </p>
                                                                            <p class="field-css-classes description description-thin">
                                                                                <label for="edit-menu-item-classes-<?php echo $current_menu_item_id; ?>">
                                                                                    <?php _e('CSS Classes (optional)'); ?><br />
                                                                                    <input type="text" id="edit-menu-item-classes-<?php echo $current_menu_item_id; ?>" class="widefat code edit-menu-item-classes" name="menu-item-classes[<?php echo $current_menu_item_id; ?>]" value="<?php echo esc_attr(implode(' ', $menu_items[$amm]->classes)); ?>" />
                                                                                </label>
                                                                            </p>
                                                                            <p class="field-xfn description description-thin">
                                                                                <label for="edit-menu-item-xfn-<?php echo $current_menu_item_id; ?>">
                                                                                    <?php _e('Link Relationship (XFN)'); ?><br />
                                                                                    <input type="text" id="edit-menu-item-xfn-<?php echo $current_menu_item_id; ?>" class="widefat code edit-menu-item-xfn" name="menu-item-xfn[<?php echo $current_menu_item_id; ?>]" value="<?php echo esc_attr($menu_items[$amm]->xfn); ?>" />
                                                                                </label>
                                                                            </p>
                                                                            <p class="field-description description description-wide">
                                                                                <label for="edit-menu-item-description-<?php echo $current_menu_item_id; ?>">
                                                                                    <?php _e('Description'); ?><br />
                                                                                    <textarea id="edit-menu-item-description-<?php echo $current_menu_item_id; ?>" class="widefat edit-menu-item-description" rows="3" cols="20" name="menu-item-description[<?php echo $current_menu_item_id; ?>]"><?php echo esc_html($menu_items[$amm]->description); // textarea_escaped      ?></textarea>
                                                                                    <span class="description"><?php _e('The description will be displayed in the menu if the current theme supports it.'); ?></span>
                                                                                </label>
                                                                            </p>												
                                                                            <div class="menu-item-actions description-wide submitbox">																						
                                                                                <a href="#" class="item-delete submitdelete deletion submitdelete menu_manager-plus-setting-delete">Remove </a> <span class="meta-sep hide-if-no-js"> | </span> <a href="#" class="item-cancel submitcancel hide-if-no-js menu_manager-plus-setting-cancel">Cancel</a>
                                                                            </div>

                                                                            <input class="menu-item-data-db-id" name="menu-item-db-id[<?php echo $menu_items[$amm]->db_id; ?>]" value="<?php echo esc_attr($menu_items[$amm]->db_id); ?>" type="hidden">
                                                                            <input class="menu-item-data-object-id" name="menu-item-object-id[<?php echo $menu_items[$amm]->db_id; ?>]" value="<?php echo esc_attr($menu_items[$amm]->object_id); ?>" type="hidden">

                                                                            <input class="menu-item-data-object" name="menu-item-object[<?php echo $menu_items[$amm]->db_id; ?>]" value="<?php echo esc_attr($menu_items[$amm]->object); ?>" type="hidden">
                                                                            <input class="menu-item-data-parent-id" name="menu-item-parent-id[<?php echo $menu_items[$amm]->db_id; ?>]" value="<?php echo esc_attr($menu_items[$amm]->menu_item_parent); ?>" type="hidden">
                                                                            <input class="menu-item-data-position" name="menu-item-position[<?php echo $menu_items[$amm]->db_id; ?>]" value="<?php echo esc_attr($menu_items[$amm]->menu_order); ?>" type="hidden">
                                                                            <input class="menu-item-data-type" name="menu-item-type[<?php echo $menu_items[$amm]->db_id; ?>]" value="<?php echo esc_attr($menu_items[$amm]->type); ?>" type="hidden">
                                                                        </div>
                                                                        <ul class="menu-item-transport"></ul>
                                                                        <div style="display: none;" class="amm_block_highlight"></div>
                                                                    </li>
                                                                    <?php
                                                                    if (!empty($menu_items[$amm + 1]->menu_item_parent) && $menu_items[$amm + 1]->menu_item_parent <> $menu_items[$amm]->ID) {
                                                                        if (in_array($menu_items[$amm + 1]->menu_item_parent, $flag_array)) {
                                                                            $a = array_search($menu_items[$amm + 1]->menu_item_parent, $flag_array);
                                                                            //$depth = $a - ($i-1);																			
                                                                            $depth = $a - (-1);
                                                                        }
                                                                    }
                                                                } // for loop end 
                                                                ?>									
                                                            </ul>
                                                            <?php
                                                        } // No menus found
                                                        ?>														
                                                    </div>
                                                </div><!-- menu-management-liquid-->
                                            </div>
                                        </div><!--- Manage menus -->												


                                        <div class="menu-settings" <?php if (!empty($one_theme_location_no_menus)) { ?>style="display: none;"<?php } ?>>
                                            <h3><?php _e('Menu Settings'); ?></h3>
                                            <?php
                                            if (!isset($auto_add)) {
                                                $auto_add = get_option('nav_menu_options');
                                                if (!isset($auto_add['auto_add']))
                                                    $auto_add = false;
                                                elseif (false !== array_search($nav_menu_selected_id, $auto_add['auto_add']))
                                                    $auto_add = true;
                                                else
                                                    $auto_add = false;
                                            }
                                            ?>

                                            <dl class="auto-add-pages">
                                                <dt class="howto"><?php _e('Auto add pages'); ?></dt>
                                                <dd class="checkbox-input"><input type="checkbox"<?php checked($auto_add); ?> name="auto-add-pages" id="auto-add-pages" value="1" /> <label for="auto-add-pages"><?php printf(__('Automatically add new top-level pages to this menu'), esc_url(admin_url('edit.php?post_type=page'))); ?></label></dd>
                                            </dl>

                                            <?php if (current_theme_supports('menus')) : ?>						
                                                <dl class="menu-theme-locations">
                                                    <dt class="howto"><?php _e('Theme locations'); ?></dt>
                                                    <?php foreach ($locations as $location => $description) : ?>
                                                        <dd class="checkbox-input">
                                                            <input type="checkbox"<?php checked(isset($menu_locations[$location]) && $menu_locations[$location] == $nav_menu_selected_id); ?> name="menu-locations[<?php echo esc_attr($location); ?>]" id="locations-<?php echo esc_attr($location); ?>" value="<?php echo esc_attr($nav_menu_selected_id); ?>" /> <label for="locations-<?php echo esc_attr($location); ?>"><?php echo $description; ?></label>
                                                            <?php if (!empty($menu_locations[$location]) && $menu_locations[$location] != $nav_menu_selected_id) : ?>
                                                                <span class="theme-location-set"> <?php printf(__("(Currently set to: %s)"), wp_get_nav_menu_object($menu_locations[$location])->name); ?> </span>
                                                            <?php endif; ?>
                                                        </dd>
                                                    <?php endforeach; ?>
                                                </dl>					
                                            <?php endif; ?>					
                                        </div>										
                                        <div class="submit-btn_save_menu">
                                            <input type="hidden" name="current_edit_menu_id" value="<?php echo esc_attr($nav_menu_selected_id); ?>" />
                                            <input type="hidden" name="delete_menu_items" value="" id="delete_menu_items">
                                            <input type="hidden" name="total_menu_items" value="<?php echo $menu_item_ids; ?>" id="total_menu_items">
                                            <!--<input type="hidden" name="amm_menu_id" value="<?php //echo $nav_menu_object->term_id;    ?>" id="amm_menu_id"> -->
                                            <span class="submit-btn_save_menu_span">
                                                <input id="save_menu_header" name="save_menu" class="button button-primary menu-save" class="custom" value="<?php _e('Save Menu'); ?>" type="submit">
                                            </span>
                                            <span id="amm_menu_delete" class="submit_delete"><?php _e('Delete Menu'); ?> </span>													
                                        </div>
                                    </div>
                                </form>
                            </div>




                        </div> <!-- / menu_manager_plus -->		
                    </div><!-- / wrap-->
                </div><!-- /#wpbody-content -->
            </div><!-- /wpbody -->
            <div id="amm_back_to_content">					
                <span class="back-to-top" title="Go to Top of page"></span>
            </div>


            <!-- popup -->
            <div id="menu_manager_popup">
                <div id="menu_manager_popup_container">
                    <div id="mm_cancel">X</div>
                    <div id="menu_manager_popup_main-wrapper" class="menu_manager_plus">
                        <div class="menu_page_cat_tag_form_wrapper">
                            <div id="nav-menus-frame">
                                <div id="menu-settings-column" class="metabox-holder<?php
                                if (isset($_GET['menu']) && '0' == $_GET['menu']) {
                                    echo ' metabox-holder-disabled';
                                }
                                ?>">
                                    <div class="clear"></div>							
                                    <form id="nav-menu-meta" class="nav-menu-meta" method="post" enctype="multipart/form-data">
                                        <input type="hidden" name="menu" id="nav-menu-meta-object-id" value="<?php echo esc_attr($nav_menu_selected_id); ?>" />
                                        <input type="hidden" name="action" value="add-menu-item" />
                                        <?php wp_nonce_field('add-menu_item', 'menu-settings-column-nonce'); ?>
                                        <?php
                                        //do_accordion_sections( 'nav-menus', 'side', null ); 
                                        do_accordion_sections_own('nav-menus', 'side', null);
                                        //wp_nav_menu_item_link_meta_box();
                                        ?>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>				
                </div>
            </div>
            <!-- end popup -->
        <?php
        endif;
    }

}

// Class end curly.