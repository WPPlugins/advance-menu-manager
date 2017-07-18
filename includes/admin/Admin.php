<?php
/**
 * Fired when admin page will loaded.
 *
 * @link       http://multidots.com
 * @since      1.0.0
 *
 * @package    Multidots Advance Menu Manager
 * @subpackage advance-menu-manager/includes/classes
 */

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

require_once( ABSPATH . 'wp-admin/includes/nav-menu.php' );

$admin_interface = new md_admin_interface();
//$admin_interface->menu_container_print();


$admin_interface->menu_container_print_custom();

// Load all the nav menu interface functions
function do_accordion_sections_own( $screen, $context, $object ) {
	include('include/Popup_Add_Menu_Content.php' );
}


/**
 * Displays a metabox for the custom links menu item.
 *
 * @since 3.0.0
 *
 * @global int        $_nav_menu_placeholder
 * @global int|string $nav_menu_selected_id
 */
function wp_nav_menu_item_link_meta_box_own_md() {
	global $_nav_menu_placeholder, $nav_menu_selected_id;

	$_nav_menu_placeholder = 0 > $_nav_menu_placeholder ? $_nav_menu_placeholder - 1 : -1;

	?>
	<div class="customlinkdiv" id="customlinkdiv">
		<input type="hidden" value="custom" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-type]" />
		<p id="menu-item-url-wrap">
			<label class="howto" for="custom-menu-item-url">
				<span><?php _e('URL'); ?></span>
				<input id="custom-menu-item-url" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-url]" type="text" class="code menu-item-textbox" value="http://" />
			</label>
		</p>

		<p id="menu-item-name-wrap">
			<label class="howto" for="custom-menu-item-name">
				<span><?php _e( 'Link Text' ); ?></span>
				<input id="custom-menu-item-name" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-title]" type="text" class="regular-text menu-item-textbox input-with-default-title" title="<?php esc_attr_e('Menu Item'); ?>" />
			</label>
		</p>

		<p class="button-controls">
			<span class="add-to-menu">
				<input type="submit"<?php wp_nav_menu_disabled_check( $nav_menu_selected_id ); ?> class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e('Add to Menu'); ?>" name="add-custom-menu-item" id="submit-customlinkdiv" />
				<span class="spinner"></span>
			</span>
		</p>

	</div><!-- /.customlinkdiv -->
	<?php
}

/**
 * Displays a metabox for a post type menu item.
 *
 * @since 3.0.0
 *
 * @global int        $_nav_menu_placeholder
 * @global int|string $nav_menu_selected_id
 *
 * @param string $object Not used.
 * @param string $post_type The post type object.
 */
function wp_nav_menu_item_post_type_meta_box_own_md( $object, $post_type ) {
	global $_nav_menu_placeholder, $nav_menu_selected_id;
	global  $gloable_all_author_array;
	global  $gloable_all_template_array;
	global  $gloable_all_category_array;

	$post_type_name = $post_type['args']->name;
        
	// Paginate browsing for large numbers of post objects.

	//post per page dynamic
	$post_per_page = get_option('amm_'.$post_type_name);
	if(empty($post_per_page)){
		$post_per_page = get_option('amm_post_perpage_default');
	}
	if(empty($post_per_page)){
		$post_per_page = 50;
	}


	$per_page = (int)$post_per_page;
	$pagenum = isset( $_REQUEST[$post_type_name . '-tab'] ) && isset( $_REQUEST['paged'] ) ? absint( $_REQUEST['paged'] ) : 1;
	$offset = 0 < $pagenum ? $per_page * ( $pagenum - 1 ) : 0;

	$args = array(
	'offset' => $offset,
	'order' => 'ASC',
	'orderby' => 'title',
	'posts_per_page' => $per_page,
	'post_type' => $post_type_name,
	'suppress_filters' => true,
	'update_post_term_cache' => false,
	'update_post_meta_cache' => false
	);
       
	if ( isset( $post_type['args']->_default_query ) )
	$args = array_merge($args, (array) $post_type['args']->_default_query );

	// @todo transient caching of these results with proper invalidation on updating of a post of this type
	$get_posts = new WP_Query;
	$posts = $get_posts->query( $args );
	if ( ! $get_posts->post_count ) {
		echo '<p>' . __( 'No items.' ) . '</p>';
		return;
	}

	$get_posts_for_count = new WP_Query($args);

	$total_page_count = $get_posts_for_count->found_posts;

	$db_fields = false;
	if ( is_post_type_hierarchical( $post_type_name ) ) {
		$db_fields = array( 'parent' => 'post_parent', 'id' => 'ID' );
	}

	//$walker = new Walker_Nav_Menu_Checklist( $db_fields );

	$walker = new Walker_Nav_Menu_Checklist_md( $db_fields );

	$current_tab = 'all';
	if ( isset( $_REQUEST[$post_type_name . '-tab'] ) && in_array( $_REQUEST[$post_type_name . '-tab'], array('all', 'search') ) ) {
		$current_tab = $_REQUEST[$post_type_name . '-tab'];
	}

	$removed_args = array(
	'action',
	'customlink-tab',
	'edit-menu-item',
	'menu-item',
	'page-tab',
	'_wpnonce',
	);
	?>
	<div id="posttype-<?php echo $post_type_name; ?>" class="posttypediv amm_item_main_wrapper">
		<?php // add new page/post html ?>
		<div class="add_mew_item_wrapper amm_deactive">		
			<div class="add_item_inner_main">
							
				<div class="add_item_submit_row_wrapper">
				    
				    <b>Want to add a <?php echo $post_type_name; ?>? Buy the Pro Version to access all the features.</b>
				    <br><br><br>
					<button type="button" class="button-secondary amm_menu_add_cancel">Cancel</button> &nbsp;&nbsp;&nbsp;
					<a href="http://codecanyon.net/item/advance-menu-manager/15275037" target="_blank" class="button-primary" >Buy Pro Version</a>
					<br><br><br>
				</div>
			</div>
		</div><?php // end new page/post html ?>
		<div class="amm_header_main">
			<div class="new_item_add_wrapper">				
				<span class="list-controls">
					<a href="<?php echo esc_url( add_query_arg(	array($post_type_name . '-tab' => 'all','selectall' => 1,),	remove_query_arg( $removed_args )));?>#posttype-<?php echo $post_type_name; ?>" class="select-all"><?php _e('Select All'); ?></a>
				</span>					
				<span class="allready-menu-item"><input type="checkbox" name='curent-menu_item' class="curent-menu_item" value="" data-selector="<?php echo $post_type_name."checklist";?>"/>Hide existing menu items </span>
				<?php
				$newpage_lable = 'Add New '.$post_type_name;
				if ( 'page' != $post_type_name && 'post' != $post_type_name) {
					if(!empty($post_type['args']->label)) $newpage_lable = 'Add New '.$post_type['args']->label;
				}

				?>
				<span class="page-title-action"><?php echo $newpage_lable; ?></span>
			</div> 
			<div class="menu_item_search_wrapper"><input type="text" class="menu_item_search" value="" placeholder="Search menu item.."/></div> 
		</div>

		<div id="<?php echo $post_type_name; ?>-all" class="tabs-panel tabs-panel-view-all <?php echo ( 'all' == $current_tab ? 'tabs-panel-active' : 'tabs-panel-inactive' ); ?> md_popup_main_wrapper">
			
			<?php
			$filter_class_name = '';
			if ( 'page' == $post_type_name ) {
				$filter_class_name = 'md_page_filter';
			}
			?>
			<div class="amm_menu_item_main_content_wrapper">	
				<?php
				$args['walker'] = $walker;

				/*
				* If we're dealing with pages, let's put a checkbox for the front
				* page at the top of the list.
				*/
				if ( 'page' == $post_type_name ) {
					$front_page = 'page' == get_option('show_on_front') ? (int) get_option( 'page_on_front' ) : 0;
					if ( ! empty( $front_page ) ) {
						$front_page_obj = get_post( $front_page );
						$front_page_obj->front_or_home = true;
						array_unshift( $posts, $front_page_obj );
					} else{
						$_nav_menu_placeholder = ( 0 > $_nav_menu_placeholder ) ? intval($_nav_menu_placeholder) - 1 : -1;
						/*array_unshift( $posts, (object) array(
						'front_or_home' => true,
						'ID' => 0,
						'object_id' => $_nav_menu_placeholder,
						'post_content' => '',
						'post_excerpt' => '',
						'post_parent' => '',
						'post_title' => _x('Home', 'nav menu home label'),
						'post_type' => 'nav_menu_item',
						'type' => 'custom',
						'url' => home_url('/'),
						) );*/
					}
				}

				$post_type = get_post_type_object( $post_type_name );
				$archive_link = get_post_type_archive_link( $post_type_name );
				if ( $post_type->has_archive ) {
					$_nav_menu_placeholder = ( 0 > $_nav_menu_placeholder ) ? intval($_nav_menu_placeholder) - 1 : -1;
					array_unshift( $posts, (object) array(
					'ID' => 0,
					'object_id' => $_nav_menu_placeholder,
					'object'     => $post_type_name,
					'post_content' => '',
					'post_excerpt' => '',
					'post_title' => $post_type->labels->archives,
					'post_type' => 'nav_menu_item',
					'type' => 'post_type_archive',
					'url' => get_post_type_archive_link( $post_type_name ),
					) );
				}

				/**
				 * Filter the posts displayed in the 'View All' tab of the current
				 * post type's menu items meta box.
				 *
				 * The dynamic portion of the hook name, `$post_type_name`, refers
				 * to the slug of the current post type.
				 *
				 * @since 3.2.0
				 *
				 * @see WP_Query::query()
				 *
				 * @param array  $posts     The posts for the current post type.
				 * @param array  $args      An array of WP_Query arguments.
				 * @param object $post_type The current post type object for this menu item meta box.
				 */
				$posts = apply_filters( "nav_menu_items_{$post_type_name}", $posts, $args, $post_type );
				$checkbox_items = walk_nav_menu_tree( array_map('wp_setup_nav_menu_item', $posts), 0, (object) $args );

				if ( 'all' == $current_tab && ! empty( $_REQUEST['selectall'] ) ) {
					$checkbox_items = preg_replace('/(type=(.)checkbox(\2))/', '$1 checked=$2checked$2', $checkbox_items);

				}

				$filter_author = array();
				$filter_template = array();
				$filter_publish_date = array();
				$filter_cate = array();
				$template_name_array = array();
				$templates_all = get_page_templates();
				foreach ( $templates_all as $template_name => $template_filename ) {
					$template_name_array[$template_filename] = $template_filename;
					// $template_name_array[$template_name] =$template_filename;
				}

				foreach ($posts as $post_data){

					if(isset($post_data->post_author))	$filter_author[]= $post_data->post_author;

					if('page' == $post_data->post_type){
						$tamplate_name = get_post_meta( $post_data->ID,'_wp_page_template',true);
						$template_name_key = array_search($tamplate_name, $template_name_array);
						if (!empty($template_name_key)) $tamplate_name = $template_name_key;

						if(!empty($tamplate_name)){
							$filter_template[] = $tamplate_name;
						}else{
							$filter_template[] ='default';
						}
					}else{
						$category_detail = get_the_category($post_data->ID);

						if(!empty($category_detail) && count($category_detail) > 0) {
							$cate_array= array();
							foreach($category_detail as $cd){
								$filter_cate[] = $cd->cat_name;
							}
						}
					}
					//$filter_publish_date[]= get_the_date('', $post_data->ID);

				}

				if(count($filter_author) > 1 )	$filter_author = array_unique($filter_author);
				if(count($filter_template) > 1 ) $filter_template = array_unique($filter_template);
				if(count($filter_cate) > 1 ) $filter_cate = array_unique($filter_cate);
				$post_data_calss='';
				if ( 'page' != $post_type_name ) { $post_data_calss = 'post-data-show'; }
				?>
				<div class="menu_item_filter_header amm_popup_header_wrapper <?php echo $post_data_calss; ?>" amm-filter-selector="<?php echo $post_type_name; ?>checklist">
					<span class="item_ID md_walker" >Item ID</span>
					<span class="title md_walker" amm-filter-title="all" ><strong>Title</strong></span>
					<span class="item_slug md_walker" > Item Slug</span>
					 <?php
					 echo '<span class="author md_walker" amm-filter-author="all">';
					 if(count($filter_author) > 1 ){
					 	echo '<select class="filter_data" data-filter="author">';
					 	echo '<option class="" value="all"> Select Author </option>';
					 	//foreach ($filter_author as $author_id) echo '<option value="'.get_the_author_meta('display_name',$author_id).'">'.get_the_author_meta('display_name',$author_id).'</option>';
					 	foreach ($filter_author as $author_id) echo '<option value="'.$author_id.'">'.get_the_author_meta('display_name',$author_id).'</option>';
					 	echo '</select>';
					 }else{
					 	echo '<strong>Author</strong>';
					 }
					 echo '</span>';

					 if('page' == $post_type_name) {
					 	echo '<span class="template-list md_walker" amm-filter-template-list="all">';
					 	if(count($filter_template) > 1 ){
					 		echo '<select class="filter_data" data-filter="template-list">';
					 		echo '<option class="" value="all">Select template</option>';
					 		foreach ($templates_all as $template_name => $display_template_name) echo '<option class="'.$template_name.'" value="'.$display_template_name.'">'.$template_name.'</option>';
					 		echo '</select>';
					 	}else{
					 		echo '<strong>Page Template</strong>';
					 	}
					 	echo '</span>';
					 }else{
					 	echo '<span class="category-list md_walker" amm-filter-category-list="all">';
					 	if(count($filter_cate) >= 1 ){
					 		echo '<select class="filter_data" data-filter="category-list">';
					 		echo '<option class="" value="all">Select category</option>';
					 		foreach ($filter_cate as $filter_catedata) echo '<option class="'.$filter_catedata.'" value="'.$filter_catedata.'">'.$filter_catedata.'</option>';
					 		echo '</select>';
					 	}else{
					 		echo '<strong>Category</strong>';
					 	}
					 	echo '</span>';
					} ?>
		 			<span class="publish_date md_walker"><strong>Publish Date</strong></span>
		 			<span class="menu_item_existing_display_status" amm-filter-category-list="show"></span>
				</div>				
				<ul amm_menu_query='page' amm_page_count="<?php echo $total_page_count;?>" amm_post_type="<?php echo $post_type_name;?>" id="<?php echo $post_type_name; ?>checklist" data-wp-lists="list:<?php echo $post_type_name?>" class="categorychecklist form-no-clear amm_popup_header <?php echo $filter_class_name.' '.$post_data_calss;?>" amm_post_per_page = '<?php echo $post_per_page;?>'>
				<?php
				echo $checkbox_items;
				?>
				</ul>
			</div>
		</div><!-- /.tabs-panel -->
				
		<p class="button-controls">					
			<span class="add-to-menu">
				<input type="submit"<?php wp_nav_menu_disabled_check( $nav_menu_selected_id ); ?> class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e( 'Add to Menu' ); ?>" name="add-post-type-menu-item" id="<?php echo esc_attr( 'submit-posttype-' . $post_type_name ); ?>" />
				<span class="spinner"></span>
			</span>
			<span class="add-menu-item-pagelinks" amm-pagination="<?php echo $post_type_name; ?>checklist"></span>				
		</p>
		<p class="amm_list_of_page">
			<?php			

			$list_of_page_no = get_option('amm_post_perpage_option');
			if(empty($list_of_page_no)) $list_of_page_no = '50,100,200';
			
			$amm_post_perpage_array = explode(',',$list_of_page_no);
			if(count($amm_post_perpage_array) > 0) {
			 ?>
			 <label>Show items per page</label>
			 <select name="amm_post_perpage_w" class="amm_post_perpage" data_post_per_page="amm_<?php echo $post_type_name;?>" data_pagination="<?php echo $post_type_name; ?>checklist">
			 	<?php foreach ($amm_post_perpage_array as $data){
			 		if($post_per_page == $data) {
			 			echo '<option value="'.$data.'" selected>'.$data.'</option>';
			 		}else{
			 			echo '<option value="'.$data.'">'.$data.'</option>';
			 		}
			 	}
			 	?>
			 </select>
			<?php } ?>

		</p>

	</div><!-- /.posttypediv -->
	<?php
}

/**
 * Displays a metabox for a taxonomy menu item.
 *
 * @since 3.0.0
 *
 * @global int|string $nav_menu_selected_id
 *
 * @param string $object Not used.
 * @param string $taxonomy The taxonomy object.
 */
function wp_nav_menu_item_taxonomy_meta_box_own_md( $object, $taxonomy ) {
	global $nav_menu_selected_id;
	$taxonomy_name = $taxonomy['args']->name;

	// Paginate browsing for large numbers of objects.
	$post_per_page = get_option('amm_'.$taxonomy_name);
	if(empty($post_per_page)){
		$post_per_page = get_option('amm_post_perpage_default');
	}
	
	if(empty($post_per_page)){
		$post_per_page = 50;
	}
	
	
	
	$per_page = (int) $post_per_page;
	$pagenum = isset( $_REQUEST[$taxonomy_name . '-tab'] ) && isset( $_REQUEST['paged'] ) ? absint( $_REQUEST['paged'] ) : 1;
	$offset = 0 < $pagenum ? $per_page * ( $pagenum - 1 ) : 0;

	$args = array(
	'child_of' => 0,
	'exclude' => '',
	'hide_empty' => false,
	'hierarchical' => 1,
	'include' => '',
	'number' => $per_page,
	'offset' => $offset,
	'order' => 'ASC',
	'orderby' => 'name',
	'pad_counts' => false,
	);

	$terms = get_terms( $taxonomy_name, $args );

	if ( ! $terms || is_wp_error($terms) ) {
		echo '<p>' . __( 'No items.' ) . '</p>';
		return;
	}

	$num_pages = ceil( wp_count_terms( $taxonomy_name , array_merge( $args, array('number' => '', 'offset' => '') ) ) / $per_page );

	$total_page_count= wp_count_terms( $taxonomy_name , array_merge( $args, array('number' => '', 'offset' => '') ) );
	$post_type_name = $taxonomy_name;



	$db_fields = false;
	if ( is_taxonomy_hierarchical( $taxonomy_name ) ) {
		$db_fields = array( 'parent' => 'parent', 'id' => 'term_id' );
	}

	$walker = new Walker_Nav_Menu_Checklist_md( $db_fields );


	$removed_args = array(
	'action',
	'customlink-tab',
	'edit-menu-item',
	'menu-item',
	'page-tab',
	'_wpnonce',
	);

	?>
	<div id="taxonomy-<?php echo $taxonomy_name; ?>" class="taxonomydiv amm_item_main_wrapper">
		<div id="tabs-panel-<?php echo $taxonomy_name; ?>-all" class="tabs-panel tabs-panel-view-all tabs-panel-active md_popup_main_wrapper">
			<div class="amm_header_main">
				<div class="new_item_add_wrapper">
					<span class="list-controls amm_taxonomy"> <a href="<?php echo esc_url(add_query_arg(array($taxonomy_name . '-tab' => 'all','selectall' => 1,),remove_query_arg($removed_args)));?>#taxonomy-<?php echo $taxonomy_name; ?>" class="select-all"><?php _e('Select All'); ?></a></span>
					<!--<span class="allready-menu-item"><input type="checkbox" name='curent-menu_item' class="curent-menu_item" value="" data-selector="<?php echo $taxonomy_name; ?>checklist"/>Hide existing menu items </span>
					<!--span class="page-title-action" style="opacity: 0;">Add New</span -->
				</div>
				<!--<div class="menu_item_search_wrapper"><input type="text" class="menu_item_search" value="" placeholder="Search menu item.."/></div>-->
			</div>			
			
			<div class="menu_item_filter_header amm_popup_header_wrapper taxonomy_item_list" amm-filter-selector="<?php echo $taxonomy_name; ?>checklist">
				<span class="title md_walker" amm-filter-title="all" ><strong>Title</strong></span>
				<span class="taxonomy_slug md_walker"><strong>Slug</strong></span>
		 		<span class="taxomomy_content md_walker"><strong>Description</strong></span>
			</div>
			
			<ul amm_menu_query='taxonomy' amm_page_count="<?php echo $total_page_count;?>" amm_post_type="<?php echo $post_type_name;?>" id="<?php echo $taxonomy_name; ?>checklist" data-wp-lists="list:<?php echo $taxonomy_name?>" class="categorychecklist form-no-clear amm_popup_header taxonomy_item_list" amm_post_per_page = '<?php echo $post_per_page;?>'>
				<?php
				$args['walker'] = $walker;
				echo walk_nav_menu_tree( array_map('wp_setup_nav_menu_item', $terms), 0, (object) $args );
				?>
			</ul>			
		</div><!-- /.tabs-panel -->
		<p class="button-controls">
			<span class="add-to-menu">
				<input type="submit"<?php wp_nav_menu_disabled_check( $nav_menu_selected_id ); ?> class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e( 'Add to Menu' ); ?>" name="add-taxonomy-menu-item" id="<?php echo esc_attr( 'submit-taxonomy-' . $taxonomy_name ); ?>" />
				<span class="spinner"></span>
			</span>
			<span class="add-menu-item-pagelinks" amm-pagination="<?php echo $taxonomy_name; ?>checklist"></span>
		</p>
		<p class="amm_list_of_page">
			<?php			 
			 
			$list_of_page_no = get_option('amm_post_perpage_option');
			if(empty($list_of_page_no)) $list_of_page_no = '50,100,200';
			$amm_post_perpage_array = explode(',',$list_of_page_no);
			if(count($amm_post_perpage_array) > 0) {
			 ?>
			 <label>Show items per page</label>
			 <select name="amm_post_perpage_w" class="amm_post_perpage" data_post_per_page="amm_<?php echo $taxonomy_name;?>" data_pagination = "<?php echo $taxonomy_name; ?>checklist"">
			 	<?php foreach ($amm_post_perpage_array as $data){
			 		if($post_per_page ==$data) {
			 			echo '<option value="'.$data.'" selected>'.$data.'</option>';
			 		}else{
			 			echo '<option value="'.$data.'">'.$data.'</option>';
			 		}
			 	}
			 	?>
			 </select>
			<?php } ?>
		</p>

	</div><!-- /.taxonomydiv -->
	<?php
}
?>