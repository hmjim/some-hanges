<?php

/** File for Antons Code */


namespace {


    function sendNotification($title, $subject, $link)
    {
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


    function index_listing_save($post_id, $post, $update)
    {

        $author_id = get_post_field('post_author', $post_id);
        $profile_id = get_the_author_meta('voxel:profile_id', $author_id);
        $agency_id = index_get_related_posts($profile_id, 'agency-agents-relation', 'belongs_to_one');

        if (isset($agency_id[0]) && is_array($agency_id[0])) {
            index_create_relation($agency_id[0], $post_id, 'listing-company-rel');
            $rel_id = index_get_related_posts($post_id, 'listing-company-rel', 'belongs_to_one');
        } else {
            $rel_id = 'none';
        }

        update_post_meta($post_id, 'author_relates_to', $agency_id);
        update_post_meta($post_id, 'author_profile_id', $profile_id);
        update_post_meta($post_id, 'author_rel_id', $rel_id);


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

    }

    // add_action('save_post', 'index_listing_save', 199, 3);  


    // custom handler for event "Agencies: New post submitted"
    add_action('voxel/app-events/messages/user:received_message', function ($event) {

        //wp_mail('info@index.cy','Agency created',print_r($event));

//	sendNotification('Agency created', 'Agency creation notification', 'voxel.index.cy/buy');

    });


}// end of namespace