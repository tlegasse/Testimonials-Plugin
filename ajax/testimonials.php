<?php
require($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');

//////////////////////////////////////////////////////////////
//        This is the ajax for the testimonial plugin.      //
//          There is some really complex stuff here.        //
//					  Buckle up, Buttercup.		jk			//
//////////////////////////////////////////////////////////////

if (isset($_POST['testimonial'])) {
	global $current_user;
	echo $current_user->user_firstname;
	var_dump($_POST);
	
	$my_post = array(
	  'post_title'    => wp_strip_all_tags($current_user->user_firstname),
	  'post_content'  => $_POST['testimonial'],
	  'post_status'   => 'pending',
	  'post_author'   => 1,
	  'post_type' 	  => 'testimonial'
	);
	
	// Insert the post into the database
	$postId = wp_insert_post( $my_post );
	add_post_meta($postId, 'stars', $_POST['rating']);
	
} else {
	global $wpdb;
	$args = array(	'posts_per_page' => 3,
					'offset' => 0,
					'order' => 'ASC',
					'orderby' => 'rand',
					'post_type' => 'testimonial',
					'post_status' => 'publish');
	$review_counter = 1;
	$reviews = get_posts($args);
	foreach ($reviews as $review) {
		if($review_counter == 2){ $margin = 'margin-top: 5%';} else{$margin = 'margin-top: 15%;';}
		$id = "bubbleNumber" . $review_counter;
		
		$i=0;
		$stars= "";
		while($i < $review->stars) {
			$stars = $stars . '<div class="star-five "></div>';
			$i++;
		}
		$class = '';
		$background = '';
		$background = substr_replace(wp_get_attachment_url( get_post_thumbnail_id($review->ID)), '', 7, 0);
		
		if (strlen($background) > 5) {
			$background = "background: url(" . $background . ");";
		} else {
			$plugindir = substr(TESTIMONIALS_FRONTEND, -37);
			$background = "background: url( http://www.camanoislandcoffee.com" . $plugindir . "/cup.jpg )";
		}
		
		echo "
				<div class='bubble' style=' $margin;' id='" . $id	 . "'>
							<div class='bubble-image $class' style='" . $background . "'></div>
						<div class='first-row'>
								<h3>$review->post_title	</h3>
						</div>
								<div class='star-row'>
									$stars
								</div>
						<div class='review-content bubble-text'>
							<p class='line-clamp '>$review->post_content</p>
						</div>
							<div class='more-button textBubbleNumber-$review_counter' style='display: none;'>  MORE  </div>
							<div class='more-button hidden-textBubbleNumber-$review_counter' style='display: none;'>  LESS  </div>
				</div>
		";
		$review_counter ++;
	}
		
return $reviews;
}
?>