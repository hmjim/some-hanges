<?php

/** File for Antons Code */ 


namespace {

	
	function sendNotification($title, $subject, $link) {
		  if (!class_exists('WonderPushAdmin')) {
			return;
		  }
		  $client = get_wonderpush_client();
		  $client->deliveries()->create(array(
			'notification' => array(
			  'alert' => array(
				'title' => $title,
				'text' => $subject,
				'targetUrl' => $link,
			  ),
			),
			// Option 1: Target all your users
			'targetSegmentIds' => '@ALL',
			// Option 2: Target specific user by ids
			//'targetUserIds' => array('johndoe', 'janedoe'),
			// Option 3: Target by tags
			//'targetTags' => array('news'),
			// More options are available, read our Management API documentation at: https://docs.wonderpush.com/reference/post-deliveries
		  ));
		}
	
	
	/* ADD GTM TO HEAD AND BELOW OPENING BODY */

add_action('wp_head', 'google_tag_manager_head', 20);
function google_tag_manager_head() { ?>
	<!-- Google Tag Manager -->
	<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
	new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
	j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
	'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
	})(window,document,'script','dataLayer','GTM-TCJRQST');</script>
	<!-- End Google Tag Manager -->
	<!-- Meta Pixel Code -->
	<script>
	!function(f,b,e,v,n,t,s)
	{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
	n.callMethod.apply(n,arguments):n.queue.push(arguments)};
	if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
	n.queue=[];t=b.createElement(e);t.async=!0;
	t.src=v;s=b.getElementsByTagName(e)[0];
	s.parentNode.insertBefore(t,s)}(window, document,'script',
	'https://connect.facebook.net/en_US/fbevents.js');
	fbq('init', '580897210652370');
	fbq('track', 'PageView');
	</script>
	<noscript><img height="1" width="1" style="display:none"
	src="https://www.facebook.com/tr?id=580897210652370&ev=PageView&noscript=1"
	/></noscript>
	<!-- End Meta Pixel Code -->
	<script>
		window.addEventListener('load', function() {
		  // Check if the page URL contains the hash "#recover_account"
		  if (window.location.hash === "#recover_account") {
			var recoverAccountLink = document.querySelector('a[href="#recover_account"]');
			if (recoverAccountLink) {
			  recoverAccountLink.click();
			}
		  }
		  if (window.location.hash === "#sign_up") {
			var SignUpLink = document.querySelector('a[href="#sign_up"]');
			if (SignUpLink) {
			  SignUpLink.click();
			}
		  }
		});
	</script>

<?php 
	//	Temporary remove elements for app approval 
	if (stripos($_SERVER['HTTP_USER_AGENT'], 'index-ios-app') !== false) {
         ?>
    <style> 
    /*		.dashboard_current_plan,
		.menu-item-5174,
		.ts-social-connect,
		.elementor-element-5849dbae, 
		.elementor-element-80694e3,
		#cmplz-cookiebanner-container { display: none !important; } */
		
    </style>
	
    <?php
    }

								   
								   
								   
								   }

add_action('wp_body_open', 'google_tag_manager_body', 100);
function google_tag_manager_body() { ?>
	<!-- Google Tag Manager (noscript) -->
	<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TCJRQST"
	height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
	<!-- End Google Tag Manager (noscript) -->
	<!-- Meta Pixel Code -->
	<noscript><img height="1" width="1" style="display:none"
	src="https://www.facebook.com/tr?id=285348897347958&ev=PageView&noscript=1"
	/></noscript>
	<!-- End Meta Pixel Code -->


<?php
}

//INDEX Saving Users Last Login AK 17.07.2023

function update_last_login($user_login) {
    $user = get_user_by('login', $user_login);

    // Update the user's last login meta field
    update_user_meta($user->ID, 'last_login', current_time('mysql'));
}
add_action('wp_login', 'update_last_login', 10, 1);



	// function index_get_related_posts($post_id,$rel_key,$rel_type) {
		// global $wpdb;

		// if ( $rel_type == 'has_one' || $rel_type == 'has_many') {
			// $rows = $wpdb->get_col( $wpdb->prepare( <<<SQL
				// SELECT child_id
				// FROM {$wpdb->prefix}voxel_relations
				// WHERE parent_id = %d AND relation_key = %s
				// ORDER BY `order` ASC
			// SQL, $post_id, $rel_key ) );
		// } else {
			// $rows = $wpdb->get_col( $wpdb->prepare( <<<SQL
				// SELECT parent_id
				// FROM {$wpdb->prefix}voxel_relations
				// WHERE child_id = %d AND relation_key = %s
				// ORDER BY `order` ASC
			// SQL, $post_id, $rel_key ) );
		// }

		// $ids = array_map( 'absint', (array) $rows );

		// $is_multiple = in_array( $rel_type, [ 'has_many', 'belongs_to_many' ], true );
		// if ( ! $is_multiple && ! empty( $ids ) ) {
			// $ids = [ array_shift( $ids ) ];
		// }

		// return $ids;
	// }
	
	// function index_create_relation($parent_id,$child_id,$rel_key) { 
		
		// global $wpdb;


		// $wpdb->delete( $wpdb->prefix.'voxel_relations', [
			// 'parent_id' => $parent_id,
			// 'relation_key' => $rel_key,
		// ] );


			

			// $query = "INSERT INTO {$wpdb->prefix}voxel_relations (`parent_id`, `child_id`, `relation_key`, `order`) VALUES ";
			// $query .= $wpdb->prepare( '(%d,%d,%s,%d)', $parent_id, $child_id, $rel_key, 0 );
			// $insert_id = $wpdb->query( $query );



		// $cache_key = sprintf( 'relations:%s:%d:%s', $rel_key, $child_id, 'child_id' );
		// wp_cache_delete( $cache_key, 'voxel' );
		
		// return $insert_id; 
		
	// }
	
	
// INDEX Different Number Format €354 500 (no decimals)
function index_modify_number_format_i18n_defaults($formatted, $number, $decimals) { 
    $formatted = number_format($number, 0,'.','&nbsp;');
    return $formatted; 
}
add_filter( "number_format_i18n", "index_modify_number_format_i18n_defaults", 10, 3 );
	
	
	
 // function index_listing_save($post_id, $post, $update)
    // {
	 	
	 	// $author_id = get_post_field( 'post_author', $post_id );
	 	// $profile_id = get_the_author_meta( 'voxel:profile_id', $author_id );
	 	// $agency_id = index_get_related_posts($profile_id,'agency-agents-relation','belongs_to_one');

	 	// if(isset($agency_id[0]) && is_array($agency_id[0])) {
			// index_create_relation($agency_id[0],$post_id,'listing-company-rel');
			// $rel_id = index_get_related_posts($post_id,'listing-company-rel','belongs_to_one');
		// } else { 			
			// $rel_id = 'none';	
		// }
	 
	 	// update_post_meta($post_id,'author_relates_to',$agency_id);
	 	// update_post_meta($post_id,'author_profile_id',$profile_id);
	 	// update_post_meta($post_id,'author_rel_id',$rel_id);
	 	
	 
        //wp_mail('anton.karb@gmail.com','VAT',$post_id);
        //
        //
        //
        //
	
	 
	 	/*$price = get_post_meta($post_id, 'price');
	 
	 	$price_history = get_post_meta($post_id, 'price_history');
	 	
		$price = $price[0];
	 	
	 	$price_history[] = array_push($price_history, array('time'=> time(), 'price' => $price));
	 
        update_post_meta($post_id, 'price_history', $price_history);
	 
	 
		/*
        if (get_post_type($post_id) !== 'sale') return;

        $price = get_post_meta($post_id, 'price');
        $area = get_post_meta($post_id, 'covered-area');

        $vat_max = round($price[0] * 0.19);

        $price_per_sqm = round($price[0] / $area[0]);

        if ($area[0] <= 150) {

            $vat_min = round($price[0] * 0.05);

        } else {


            $area19 = $area[0] - 150;

            $vat_min = round(($price[0] / $area[0] * $area19 * 0.19) + ($price[0] / $area[0] * 150 * 0.05));


        }

        $vat_estimation = $vat_min . '&euro; or ' . $vat_max . '&euro;';


        update_post_meta($post_id, 'vat_min', $vat_min);
        update_post_meta($post_id, 'vat_max', $vat_max);
        update_post_meta($post_id, 'vat_estimation', $vat_estimation);
        update_post_meta($post_id, 'price_per_sqm', $price_per_sqm);

        $lowPrice = $middlePrice = $highPrice = 0;
        $lowPrice = $price[0];
        if ($lowPrice > LEVEL1) {
            $middlePrice = $lowPrice - LEVEL1;
            $lowPrice = LEVEL1;
        }
        if ($middlePrice > LEVEL2) {
            $highPrice = $middlePrice - LEVEL2;
            $middlePrice = LEVEL2;
        }
        $title_transfer_fee = ($lowPrice * LOWPRICE_PERCENT / 100 + $middlePrice * MIDDLEPRICE_PERCENT / 100 + $highPrice * HIGHPRICE_PERCENT / 100) / 2;
        $title_transfer_fee = sprintf("%01.2f", $title_transfer_fee);
        update_post_meta($post_id, 'title_transfer_fee', $title_transfer_fee);\
		*/
		
    // }

 //   add_action('save_post', 'index_listing_save', 199, 3);
    // add_action('pmxi_saved_post', 'index_listing_save', 199, 3);
	

// function record_price_history( $post_id ) {

    // if ( 'sale' !== get_post_type( $post_id ) && 'rent' !== get_post_type( $post_id ) ) {
        // return;
    // }


    // $current_price = get_post_meta( $post_id, 'price', true );


    // $price_history = get_post_meta( $post_id, 'price_history', true );


    // $last_recorded_price = '';
    // if ( $price_history ) {
        // $price_history = unserialize( $price_history );
        // $last_entry = end( $price_history );
        // $last_recorded_price = $last_entry['value'];
    // }


    // if ( $current_price === $last_recorded_price ) {
        // return; // Price hasn't changed, no need to update the history
    // }


    // $price_entry = array(
        // 'timestamp' => current_time( 'timestamp' ),
        // 'value'     => $current_price,
    // );


    // if ( $price_history ) {
        // $price_history[] = $price_entry;
    // } else {
        // $price_history = array( $price_entry );
    // }


    // update_post_meta( $post_id, 'price_history', serialize( $price_history ) );
// }
// add_action( 'save_post', 'record_price_history' );

function price_history_shortcode( $atts ) {
    $post_id = get_the_ID();
    $output = '';

    // Retrieve the price history array
    $price_history = get_post_meta( $post_id, 'price_history', true );

    // Unserialize the price history array
    if ( $price_history ) {
        $price_history = unserialize( $price_history );

        // Generate the output HTML
        if ( ! empty( $price_history ) ) {
            $output .= '<b>Price Change History</b><table>';

            foreach ( $price_history as $entry ) {
                $timestamp = $entry['timestamp'];
                $value     = $entry['value'];

                $output .= '<tr>';
                $output .= '<td>&euro;' . number_format_i18n($value) . '</td';
                $output .= '<td> on ' . date( 'Y-m-d', $timestamp ) . '</td>';
                $output .= '</tr>';
            }

            $output .= '</table>';
        } else {
            $output .= '<!-- No price history available. -->';
        }
    } else {
        $output .= '<!--- No price history available. -->';
    }

    return $output;
}
add_shortcode( 'price_history', 'price_history_shortcode' );
	
	
	
function index_title_link( $atts ) {
    $output = '';		
	$post_id = $atts['id'];
	
	//$post_id = get_the_id();
	
    if ( $post_id ) {
            $output .= '<a href="'.get_permalink($post_id).'">'.get_the_title($post_id).'</a>';	
	}
    return $output;
}
	
add_shortcode( 'title_link', 'index_title_link' );

	

	// custom handler for event "Agencies: New post submitted"
add_action( 'voxel/app-events/messages/user:received_message', function( $event ) {

	//wp_mail('info@index.cy','Agency created',print_r($event));

//	sendNotification('Agency created', 'Agency creation notification', 'voxel.index.cy/buy');

} );


add_filter( 'manage_complex_posts_columns', 'add_complex_custom_column' );
function add_complex_custom_column( $columns ) {
    $columns['related_developer'] = __( 'Related Developer', 'textdomain' );
    return $columns;
}

add_action( 'manage_complex_posts_custom_column', 'fill_complex_custom_column', 10, 2 );
function fill_complex_custom_column( $column, $post_id ) {
    if ( 'related_developer' === $column ) {
        $related_posts = index_get_related_posts($post_id,'developer-complex','belongs_to_many');
        if ( !empty($related_posts) ) {
            foreach ( $related_posts as $related_post_id ) {
                $related_post = get_post( $related_post_id );
                echo '<a href="' . get_permalink($related_post) . '">' . $related_post->post_title . '</a><br>';
            }
        } else {
            _e( 'No related developer found', 'textdomain' );
        }
    }
}



//Adding Facebook Tracking Pixel code into footer (Maxim)
	
	
function add_this_script_footer(){ ?>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script>

(function($){
$(window).on('load', function(){
	    $('button').on('click', function(){
	        if($(this).html() == ' Sign up '){
	           fbq('track', 'CompleteRegistration'); 
	        	console.log('FB CompleteRegistration');
	        }
	        
	    });    
});
	$(document).ready(function(){

        setTimeout(function () {
        	    $('.ts-btn.ts-btn-2.create-btn.ts-btn-large.ts-save-changes').click(function(){
        	        console.log($(this).text());
        	        fbq('track', 'SubmitApplication');
        	        console.log('FB SubmitApplication');
        	    });
        }, 1000);

	    if($('body').hasClass('single-sale')){
    	    $('.single-sale .ts-action-con').click(function(){
	         console.log('FB Contact');
	         console.log($(this).text());
	         let vv = $(this).text();
	         console.log(vv.indexOf(substr));
    	        fbq('track', 'Contact', {
    			    content_category:'<?php foreach(get_the_terms( get_the_ID(), ["sale_type", "ad_bedrooms", "district", "city"]) as $kkk => $vvv){ echo $vvv->name. " ";};?>',
    			    content_ids:['<?php echo get_the_ID();?>'],
    			    content_name: '<?php echo get_the_title();?>',
    			    content_type: '<?php echo get_post_type( get_the_ID() );?>',
    			    contents:['<?php echo json_encode(wp_strip_all_tags(esc_html(get_the_excerpt())));?>'],
    			    currency: 'EUR',
    			    value: <?php if(get_post_meta(get_the_ID())["price"][0] !== null){
    			        echo get_post_meta(get_the_ID())["price"][0];
    			    } else {
    			        echo 0;
    			    }?>,
    	        }); 
    	    });
	    }
	    if($('body').hasClass('single-rent')){
    	    $('.single-rent .ts-action-con').click(function(){
	         console.log('FB Contact');
	         console.log($(this).text());
	         let vv = $(this).text();
	         console.log(vv.indexOf(substr));
    	        fbq('track', 'Contact', {
    			    content_category:'<?php foreach(get_the_terms( get_the_ID(), ["sale_type", "ad_bedrooms", "district", "city"]) as $kkk => $vvv){ echo $vvv->name. " ";};?>',
    			    content_ids:['<?php echo get_the_ID();?>'],
    			    content_name: '<?php echo get_the_title();?>',
    			    content_type: '<?php echo get_post_type( get_the_ID() );?>',
    			    contents:['<?php echo json_encode(wp_strip_all_tags(esc_html(get_the_excerpt())));?>'],
    			    currency: 'EUR',
    			    value: <?php if(get_post_meta(get_the_ID())["price"][0] !== null){
    			        echo get_post_meta(get_the_ID())["price"][0];
    			    } else {
    			        echo 0;
    			    }?>,
    	        }); 
    	    });
        }
		if($('body').hasClass('single-sale')){


			fbq('track', 'PageView',{
			    content_category:'<?php foreach(get_the_terms( get_the_ID(), ["sale_type", "ad_bedrooms", "district", "city"]) as $kkk => $vvv){ echo $vvv->name. " ";};?>',
			    content_ids:['<?php echo get_the_ID();?>'],
			    content_name: '<?php echo get_the_title();?>',
			    content_type: '<?php echo get_post_type( get_the_ID() );?>',
			    contents:['<?php echo json_encode(wp_strip_all_tags(esc_html(get_the_excerpt())));?>'],
			    currency: 'EUR',
			    value: <?php if(get_post_meta(get_the_ID())["price"][0] !== null){
			        echo get_post_meta(get_the_ID())["price"][0];
			    } else {
			        echo 0;
			    }?>
			    
			});

		}
	});
})(jQuery);
</script>

<?php } 

add_action('wp_footer', 'add_this_script_footer');

	
	
}// end of namespace