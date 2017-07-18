<?php
/**
 * Fired when admin page menu revision ajax called.
 *
  *
 * @since      1.0.0
 * @package    Multidots Advance Menu Manager
 * @subpackage advance-menu-manager/includes/classes
 * @author     Multidots Solutions Pvt. Ltd. <info@multidots.com>
 */

class md_admin_menu_revision_ajax_action {


	function __construct() {}

	function my_action_for_add_new_menu_item_html_filter_own(){
		$return_data = array();
		if ('my_action_for_add_new_menu_item_html_filter' ==  $_POST['action'] && !empty($_POST['page_no']) && !empty($_POST['post_type'])){

			global $_nav_menu_placeholder, $nav_menu_selected_id;

			$total_page_count =0;
			$page_html = '';
			
			$post_per_page = get_option('amm_'.$_POST['post_type']);
			if(empty($post_per_page)){
				$post_per_page = get_option('amm_post_perpage_default');
			}
			if(empty($post_per_page)){
				$post_per_page = 50;
			}



			if(empty($_POST['amm_menu_query'])){

				$post_type_name = $_POST['post_type'];
				$per_page = $post_per_page;
				$args = array(
				'paged' => $_POST['page_no'],
				'order' => 'ASC',
				'orderby' => 'title',
				'posts_per_page' => $per_page,
				'post_type' => $post_type_name,
				'suppress_filters' => true,
				'update_post_term_cache' => false,
				'update_post_meta_cache' => false,
				'post_status'   => 'publish',
				);


				if(!empty($_POST['filter_author']) && 'all' != $_POST['filter_author'] ){
					$args['author'] = $_POST['filter_author'];

				}
				if ( 'page' == $post_type_name ) {
					if(!empty($_POST['filter_template']) && 'all' != $_POST['filter_template']) {
						$args['meta_query'] = array(
						array(
						'key' => '_wp_page_template',
						'value' => $_POST['filter_template'],
						'compare' => '='
						)
						);

					}
				}else{

					if(!empty($_POST['filter_category']) && 'all' != $_POST['filter_category']) {

						//$args['category__in']=$cat_id;
						$args['category_name']=$_POST['filter_category'];

					}
				}
				if(!empty($_POST['filter_textbox'])){ $args['s'] 	= $_POST['filter_textbox'];}

				if($_POST['filter_menu_item'] == 'on'){
					$curent_menu_id = array();
					$recently_edited = absint( get_user_option( 'nav_menu_recently_edited' ) );
					$menu_items = wp_get_nav_menu_items($recently_edited);
					for ( $amm=0; $amm < count($menu_items); $amm++ ) {
						$curent_menu_id[] = $menu_items[$amm]->object_id;
					}
					//$args['post__not_in']=array(35,2,13);
					$args['post__not_in']=$curent_menu_id;

				}

				$removed_args = array(
				'action',
				'customlink-tab',
				'edit-menu-item',
				'menu-item',
				'page-tab',
				'_wpnonce',
				);


				// @todo transient caching of these results with proper invalidation on updating of a post of this type
				$get_posts = new WP_Query;
				$posts = $get_posts->query( $args );
				$get_posts_for_count = new WP_Query($args);
				$total_page_count = $get_posts_for_count->found_posts;

				if ( ! $get_posts->post_count ) {
					$page_html .= '<li class="no_record">No Record found</li>';

				}else{

					$db_fields = false;
					if ( is_post_type_hierarchical( $post_type_name ) ) {
						$db_fields = array( 'parent' => 'post_parent', 'id' => 'ID' );
					}
					//$walker = new Walker_Nav_Menu_Checklist( $db_fields );
					$walker = new Walker_Nav_Menu_Checklist_md( $db_fields );

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


					$posts = apply_filters( "nav_menu_items_{$post_type_name}", $posts, $args, $post_type );
					$checkbox_items = walk_nav_menu_tree( array_map('wp_setup_nav_menu_item', $posts), 0, (object) $args );
					$page_html .= $checkbox_items;
				}
			}else{
				//amm_menu_query * @param string $taxonomy The taxonomy object.

				$taxonomy_name = $_POST['post_type'];
				$per_page = $post_per_page;
				$pagenum = isset( $_REQUEST['page_no'] ) ? absint( $_REQUEST['page_no'] ) : 1;
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
				//'paged'=>$_REQUEST['page_no'],
				);

				$terms = get_terms( $taxonomy_name, $args );

				if ( ! $terms || is_wp_error($terms) ) {
					$page_html .= '<li class="no_record">No Record found</li>';
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

				$args['walker'] = $walker;
				$page_html .= walk_nav_menu_tree( array_map('wp_setup_nav_menu_item', $terms), 0, (object) $args );
			}


			$return_data['sucess'] = $page_html;
			$return_data['total_page'] = $total_page_count;

		}else{
			$return_data['error'] = 'Please try again later.';
		}

		echo json_encode($return_data);
		exit();

	}


	/**
	 * Pagination post per page feature	 
	 * 
	 * 
	 * @version		1.0.3	 
	 */

	function my_action_for_add_pagination_post_per_page_limit_method(){
		$return_data = array();
		if ('my_action_for_add_pagination_limit' ==  $_POST['action'] && !empty($_POST['amm_option_key'])){

			update_option($_POST['amm_option_key'],$_POST['page_per_post']);
			$return_data['sucess'] = 'ok';

		}else{
			$return_data['error'] = 'Please try again later.';
		}
		echo json_encode($return_data);
		exit();

	}


}