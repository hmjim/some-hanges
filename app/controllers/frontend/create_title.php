<?php

global $wpdb;

remove_action('wp_insert_post', 'index_save_post_manipulations');
remove_action('wp_save_post', 'index_save_post_manipulations');
remove_action('wp_update_post', 'index_save_post_manipulations');

// Check if it's a revision, if it is we don't want to continue
if (wp_is_post_revision($post_id)) return;

// Get post type, if it's not rent or sale, no need to continue
$post_type = get_post_type($post_id);
if ($post_type != 'sale' && $post_type != 'rent') return;

// Meta data
$post = get_post($post_id);
$price = get_post_meta($post_id, 'price', true);
$area = $coveredArea = get_post_meta($post_id, 'covered-area', true);
$price_history = get_post_meta($post_id, 'price_history', true);
$plot_area = get_post_meta($post_id, 'plot-area', true);
$propertyTypeLabel = get_post_type_object($post_type)->label;

// Terms
$sale_type_terms = wp_get_post_terms($post_id, 'sale_type', array('orderby' => 'parent', 'order' => 'ASC'));
$bedrooms_terms = wp_get_post_terms($post_id, 'ad_bedrooms');
$district_terms = wp_get_post_terms($post_id, 'district');
$city_terms = wp_get_post_terms($post_id, 'city');

// Check for errors and empty terms
$sale_type_parent = (!is_wp_error($sale_type_terms) && !empty($sale_type_terms)) ? $sale_type_terms[0]->name : '';
$sale_type_child = (isset($sale_type_terms[1])) ? $sale_type_terms[1]->name : '';
$sale_type_child_slug = (isset($sale_type_terms[1])) ? $sale_type_terms[1]->slug : '';

$bedrooms = (!is_wp_error($bedrooms_terms) && !empty($bedrooms_terms)) ? $bedrooms_terms[0]->name : '';
$district = (!is_wp_error($district_terms) && !empty($district_terms)) ? $district_terms[0]->name : '';
$city = (!is_wp_error($city_terms) && !empty($city_terms)) ? $city_terms[0]->name : '';


//Connecting listing to authors business
$profile_id = get_the_author_meta('voxel:profile_id', $post->post_author);

$agency_id = index_get_related_posts($profile_id, 'agency-agents-relation', 'belongs_to_one');

if (isset($agency_id[0]) && is_array($agency_id)) {

    update_post_meta($post_id, 'author_rel_parent_id', $agency_id[0]);
    $rel_id = index_create_relation($agency_id[0], $post_id, 'listing-company-rel');

} else {

    $rel_id = 'none';

}

update_post_meta($post_id, 'author_relates_to', $agency_id);
update_post_meta($post_id, 'author_profile_id', $profile_id);
update_post_meta($post_id, 'author_rel_id', $rel_id);


//Saving price change of the listing.

$last_recorded_price = '';
if ($price_history) {
    $price_history = unserialize($price_history);
    $last_entry = end($price_history);
    $last_recorded_price = $last_entry['value'];
}

$price_entry = array(
    'timestamp' => current_time('timestamp'),
    'value' => $price,
);

if ($price_history) {
    $price_history[] = $price_entry;
} else {
    $price_history = array($price_entry);
}

update_post_meta($post_id, 'price_history', serialize($price_history));


// Estimating VAT and Price per SQ.M.


if (!isset($price)) {
    $price = 0;
}
if (!isset($coveredArea)) {
    $coveredArea = 0;
}

//Checking if the price is there
if ($price > 0) {


    $vat_max = round($price * 0.19);
    update_post_meta($post_id, 'vat_max', $vat_max);


    //Checking is covered area is there
    if ($coveredArea > 0) {

        $price_per_sqm = round($price / $coveredArea);

        if ($coveredArea <= 150) {
            $vat_min = round($price * 0.05);
        } else {
            $coveredArea19 = $coveredArea - 150;
            $vat_min = round(($price / $coveredArea * $coveredArea19 * 0.19) + ($price / $coveredArea * 150 * 0.05));
        }

        $vat_estimation = $vat_min . '&euro; or ' . $vat_max . '&euro;';

        update_post_meta($post_id, 'vat_min', $vat_min);
        update_post_meta($post_id, 'vat_estimation', $vat_estimation);
        update_post_meta($post_id, 'price_per_sqm', $price_per_sqm);
    }


    // Calculating and Saving Title Transfer Fee

    $lowPrice = $middlePrice = $highPrice = 0;
    $lowPrice = $price;

    if ($lowPrice > 85000) {
        $middlePrice = $lowPrice - 85000;
        $lowPrice = 85000;
    }
    if ($middlePrice > 85000) {
        $highPrice = $middlePrice - 85000;
        $middlePrice = 85000;
    }
    $title_transfer_fee = ($lowPrice * 3 / 100 + $middlePrice * 5 / 100 + $highPrice * 8 / 100) / 2;
    $title_transfer_fee = sprintf("%01.2f", $title_transfer_fee);
    update_post_meta($post_id, 'title_transfer_fee', $title_transfer_fee);


}


// Generating Post Title  ANTON's Version

$description = "";

if ($sale_type_parent == "Residential") {

    if ($bedrooms != "") {
        $description .= $bedrooms . " Bedroom ";
    } elseif ($coveredArea != "") {
        $description .= $coveredArea . "sq.m. ";
    }
    $description .= ($sale_type_child != "") ? $sale_type_child . " " : "Residential Property ";


} elseif ($sale_type_parent == "Commercial") {
    if ($coveredArea != "") {
        $description .= $coveredArea . "sq.m. ";
    }

    $description .= ($sale_type_child != "") ? $sale_type_child . " " : "Commercial Property ";

}

// Last part of title: for sale in moni, limassol district

$description .= $propertyTypeLabel . " ";
$description .= ($city != "") ? "in " . $city . " " : "";


if ($district != "" && !str_contains($city, $district)) {
    $description .= $district . " district";
}

// If property type is Plot of land, use Plot Area instead of Covered Area
if (str_contains($sale_type_child_slug, "plot")) {
    $description = str_replace($coveredArea . " m2 ", $plot_area . " m2 ", $description);
}

// Prepare new title
$post_title = $description;

// Post Slug
$post_name = wp_unique_post_slug(sanitize_title($post_id . ' ' . $post_title), $post_id, $post->post_status, $post->post_type, $post->post_parent);


// Updating Post
if (get_the_title($post_id) !== $post_title) {
    wp_update_post(['ID' => $post_id, 'post_title' => $post_title, 'post_name' => $post_name]);
}

?>