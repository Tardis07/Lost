<?php
/**
 * Theme Lost functions and definitions
 *
 * Set up the theme and provides some helper functions, which are used in the
 * theme as custom template tags. Others are attached to action and filter
 * hooks in WordPress to change core functionality.
 *
 * @author Javis <javismay@gmail.com>
 * @license MIT
 * @since 1.0
 * @version 1.0
 */

add_filter('the_content', 'lo_fancybox_replace');
function lo_fancybox_replace ($content) {
	global $post;
	$pattern = "/<a(.*?)href=('|\")([^>]*).(bmp|gif|jpeg|jpg|png|swf)('|\")(.*?)>(.*?)<\/a>/i";
	$replacement = '<a$1href=$2$3.$4$5 class="slimbox" $6>$7</a>';
	$content = preg_replace($pattern, $replacement, $content);
	return $content;
}

add_action('wp_ajax_nopriv_lo_post_love', 'lo_post_love');
add_action('wp_ajax_lo_post_love', 'lo_post_love');
function lo_post_love(){
	global $wpdb,$post;
	$id = $_POST["um_id"];
	$action = $_POST["um_action"];
	if ( $action == 'ding'){
		$love_num = get_post_meta($id,'lo_love',true);
		$expire = time() + 99999999;
		$domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false; // make cookies work with localhost
		setcookie('lo_love_'.$id,$id,$expire,'/',$domain,false);
		if (!$love_num || !is_numeric($love_num)) {
			update_post_meta($id, 'lo_love', 1);
		}
		else {
			update_post_meta($id, 'lo_love', ($love_num + 1));
		}
		echo get_post_meta($id,'lo_love',true);
	}
	die;
}

add_action('wp_head','lo_description');
function lo_description() {
    global $s, $post;
    $description = '';
    $blog_name = get_bloginfo('name');
    if ( is_singular() ) {
        $ID = $post->ID;
        $title = $post->post_title;
        $author = $post->post_author;
        $user_info = get_userdata($author);
        $post_author = $user_info->display_name;
        if (!get_post_meta($ID, "meta-description", true)) {$description = $title.' - 作者: '.$post_author.',首发于'.$blog_name;}
        else {$description = get_post_meta($ID, "meta-description", true);}
    } elseif ( is_home () )    { $description = lo_opt('index_description');
    } elseif ( is_tag() )      { $description = single_tag_title('', false) . " - ". trim(strip_tags(tag_description()));
    } elseif ( is_category() ) { $description = single_cat_title('', false) . " - ". trim(strip_tags(category_description()));
    } elseif ( is_archive() )  { $description = $blog_name . "'" . trim( wp_title('', false) ) . "'";
    } elseif ( is_search() )   { $description = $blog_name . ": '" . esc_html( $s, 1 ) . "' 的搜索結果";
    } else { $description = $blog_name . "'" . trim( wp_title('', false) ) . "'";
    }
    $description = mb_substr( $description, 0, 220, 'utf-8' );
    echo "<meta name=\"description\" content=\"$description\">\n";
}

add_action('wp_head', 'record_visitors'); 
function record_visitors(){
	if (is_singular()) {
		global $post;
		$post_ID = $post->ID;
		if($post_ID) {
			$post_views = (int)get_post_meta($post_ID, 'views', true);
			if(!update_post_meta($post_ID, 'views', ($post_views+1))) {
				add_post_meta($post_ID, 'views', 1, true);
			}
		}
	}
}
function lo_post_views($after=''){
	global $post;
	$post_ID = $post->ID;
	$views = (int)get_post_meta($post_ID, 'views', true);
	echo $views.$after;
}

function lo_pagenavi($range = 5){
	global $paged, $wp_query;
	echo '<div class="pagenvi">';
	if ( !$max_page ) {$max_page = $wp_query->max_num_pages;}
	if($max_page > 1){if(!$paged){$paged = 1;}
	if($paged>1) echo '<a href="' . get_pagenum_link($paged-1) .'">＜</a>';
    if($max_page > $range){
		if($paged < $range){for($i = 1; $i <= ($range + 1); $i++){echo "<a href='" . get_pagenum_link($i) ."'";
		if($i==$paged)echo " class='current'";echo ">$i</a>";}}
    elseif($paged >= ($max_page - ceil(($range/2)))){
		for($i = $max_page - $range; $i <= $max_page; $i++){echo "<a href='" . get_pagenum_link($i) ."'";
		if($i==$paged)echo " class='current'";echo ">$i</a>";}}
	elseif($paged >= $range && $paged < ($max_page - ceil(($range/2)))){
		for($i = ($paged - ceil($range/2)); $i <= ($paged + ceil(($range/2))); $i++){echo "<a href='" . get_pagenum_link($i) ."'";if($i==$paged) echo " class='current'";echo ">$i</a>";}}}
    else{for($i = 1; $i <= $max_page; $i++){echo "<a href='" . get_pagenum_link($i) ."'";
    if($i==$paged)echo " class='current'";echo ">$i</a>";}}
	if($paged<$max_page) echo '<a href="' . get_pagenum_link($paged+1) .'">＞</a>';
   }
   echo '</div>';
}

add_theme_support( 'post-thumbnails' );
function get_post_thumb(){
	global $post ;
	if ( has_post_thumbnail($post->ID) ){
		$image_id = get_post_thumbnail_id($post->ID);  
		$image_url = wp_get_attachment_image_src($image_id,'large');  
		$image_url = $image_url[0];
		//return $ap_image_url = str_replace(get_option('siteurl'),'', $image_url);
		return $image_url;
	}
	return false;
}

function lo_thumb($width=150,$height=150){
	global $post;
	$img = get_post_thumb();
	if($img){
		echo '<img itemprop="image" src="'.get_template_directory_uri().'/timthumb.php?src='.$img.'&amp;h='.$height.'&amp;w='.$width.'&amp;a=c" alt="'.get_the_title($post->ID).'">';
	}
	
}

function lo_get_thumb($width=150,$height=150, $img=''){
	global $post;
	if($img){
		return '<img itemprop="image" src="'.get_template_directory_uri().'/timthumb.php?src='.$img.'&amp;h='.$height.'&amp;w='.$width.'&amp;a=c" alt="'.get_the_title($post->ID).'">';
	}
	$img = get_post_thumb();
	if($img){
		return '<img itemprop="image" src="'.get_template_directory_uri().'/timthumb.php?src='.$img.'&amp;h='.$height.'&amp;w='.$width.'&amp;a=c" alt="'.get_the_title($post->ID).'">';
	}
}

function lo_breadcrumbs() {
	$name = __('Home','Lophita');
	echo '<ol id="crumbs" itemscope itemtype="http://schema.org/BreadcrumbList">';
	global $post;
	$home = get_bloginfo('url');
	echo '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item"  href="' . $home . '"><span itemprop="name">' . $name . '</span></a><meta itemprop="position" content="1" /></li>';
	$cat = get_the_category(); $cat = $cat[0];
	echo lo_get_category_parents($cat, TRUE, '',lo_get_level($cat)+1);
	echo '</ol>';
}

function lo_get_category_parents( $id, $link = false, $separator = '/', $level, $visited = array() ) {
	$chain = '';
	$parent = get_term( $id, 'category' );
	if ( is_wp_error( $parent ) )
		return $parent;
	$name = $parent->name;
	if ( $parent->parent && ( $parent->parent != $parent->term_id ) && !in_array( $parent->parent, $visited ) ) {
		$visited[] = $parent->parent;
		$chain .= lo_get_category_parents( $parent->parent, $link, $separator, $level-1,$visited );
	}
	if ( $link )
		$chain .= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . esc_url( get_category_link( $parent->term_id ) ) . '"><span itemprop="name">'.$name.'</span></a><meta itemprop="position" content="'.$level.'" /></li>' . $separator;
	else
		$chain .= $name.$separator;
	return $chain;
}

function lo_get_level($id) {
	$level = 1;
	$parent = get_term( $id, 'category' );
	$visited = array();
	while($parent->parent && ( $parent->parent != $parent->term_id ) && !in_array( $parent->parent, $visited )) {
		$visited[] = $parent->parent;
		$parent = $parent->parent;
		$level += 1;
	}
	return $level;
}

?>