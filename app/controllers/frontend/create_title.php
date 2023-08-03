<?php

	global $wpdb;
                remove_action( 'wp_insert_post', 'vat_estimation_feild_save' );
                remove_action( 'wp_save_post', 'vat_estimation_feild_save' );
                remove_action( 'wp_update_post', 'vat_estimation_feild_save' );

    $author_id = get_post_field('post_author', $post_id);
    $profile_id = get_the_author_meta('voxel:profile_id', $author_id);
	
	$rel_type = 'belongs_to_one';
    if ($rel_type == 'has_one' || $rel_type == 'has_many') {
        $rows = $wpdb->get_col($wpdb->prepare(<<<SQL
				SELECT child_id
				FROM {$wpdb->prefix}voxel_relations
				WHERE parent_id = %d AND relation_key = %s
				ORDER BY `order` ASC
			SQL, $profile_id, 'agency-agents-relation'));
    } else {
        $rows = $wpdb->get_col($wpdb->prepare(<<<SQL
				SELECT parent_id
				FROM {$wpdb->prefix}voxel_relations
				WHERE child_id = %d AND relation_key = %s
				ORDER BY `order` ASC
			SQL, $profile_id, 'agency-agents-relation'));
    }

    $ids = array_map('absint', (array)$rows);

    $is_multiple = in_array($rel_type, ['has_many', 'belongs_to_many'], true);
    if (!$is_multiple && !empty($ids)) {
        $ids = [array_shift($ids)];
    }

	
    $agency_id = $ids;

    if (isset($agency_id[0]) && is_array($agency_id[0])) {
		
		
		$parent_id = $agency_id[0];
		$child_id = $post_id;
		$rel_key = 'listing-company-rel';
		$wpdb->delete($wpdb->prefix . 'voxel_relations', [
			'parent_id' => $parent_id,
			'relation_key' => $rel_key,
		]);

		$query = "INSERT INTO {$wpdb->prefix}voxel_relations (`parent_id`, `child_id`, `relation_key`, `order`) VALUES ";
		$query .= $wpdb->prepare('(%d,%d,%s,%d)', $parent_id, $child_id, $rel_key, 0);
		$insert_id = $wpdb->query($query);

		$cache_key = sprintf('relations:%s:%d:%s', $rel_key, $child_id, 'child_id');
		wp_cache_delete($cache_key, 'voxel');
		
		$rel_key = 'listing-company-rel';
		$rel_type = 'belongs_to_one';
    if ($rel_type == 'has_one' || $rel_type == 'has_many') {
        $rows = $wpdb->get_col($wpdb->prepare(<<<SQL
				SELECT child_id
				FROM {$wpdb->prefix}voxel_relations
				WHERE parent_id = %d AND relation_key = %s
				ORDER BY `order` ASC
			SQL, $post_id, $rel_key));
    } else {
        $rows = $wpdb->get_col($wpdb->prepare(<<<SQL
				SELECT parent_id
				FROM {$wpdb->prefix}voxel_relations
				WHERE child_id = %d AND relation_key = %s
				ORDER BY `order` ASC
			SQL, $post_id, $rel_key));
    }

    $ids = array_map('absint', (array)$rows);

    $is_multiple = in_array($rel_type, ['has_many', 'belongs_to_many'], true);
    if (!$is_multiple && !empty($ids)) {
        $ids = [array_shift($ids)];
    }
	
		$rel_id = $ids;
	
    } else {
        $rel_id = 'none';
    }

    update_post_meta($post_id, 'author_relates_to', $agency_id);
    update_post_meta($post_id, 'author_profile_id', $profile_id);
    update_post_meta($post_id, 'author_rel_id', $rel_id);


    $current_price = get_post_meta($post_id, 'price', true);


    $price_history = get_post_meta($post_id, 'price_history', true);


    $last_recorded_price = '';
    if ($price_history) {
        $price_history = unserialize($price_history);
        $last_entry = end($price_history);
        $last_recorded_price = $last_entry['value'];
    }


    $price_entry = array(
        'timestamp' => current_time('timestamp'),
        'value' => $current_price,
    );

    if ($price_history) {
        $price_history[] = $price_entry;
    } else {
        $price_history = array($price_entry);
    }

    update_post_meta($post_id, 'price_history', serialize($price_history));

    $price = get_post_meta($post_id, 'price');
    $area = get_post_meta($post_id, 'covered-area');

    if (!isset($price[0])) {
        $price[0] = 0;
    }
    if (!isset($area[0])) {
        $area[0] = 0;
    }

    if (($price[0] > 0) && ($area[0] > 0)) {
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
    }

    $lowPrice = $middlePrice = $highPrice = 0;
    $lowPrice = $price[0];
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

    $taxonomiesForTitle = [];
    $importantTaxonomies = ['sale_type', 'ad_bedrooms', 'district', 'city'];

        foreach ($importantTaxonomies as $taxonomy) {
            $taxonomiesForTitle[$taxonomy] = get_the_terms($post_id, $taxonomy);
        }
            $post_title = "";
            $post = get_post($post_id);

            $coveredArea = get_post_meta($post_id, 'covered-area');
            $totalArea = get_post_meta($post_id, 'plot-area');
            $propertyTypeLabel = get_post_type_object($post->post_type)->label;
			
            if (isset($taxonomiesForTitle['sale_type'][0]->slug)) {
                if (($taxonomiesForTitle['sale_type'][0]->slug == 'residential') || (($taxonomiesForTitle['sale_type'][1]->slug ?? false) == 'residential')) {
                    if (isset($taxonomiesForTitle['ad_bedrooms'][0]) && ($taxonomiesForTitle['sale_type'][0]->slug != 'plot-of-land-residential')) {
                        $post_title .= $taxonomiesForTitle['ad_bedrooms'][0]->name . ' bedroom';
                        $post_title .= ' ';
                    } elseif (isset($coveredArea[0]) && ($coveredArea[0] != '') && ($taxonomiesForTitle['sale_type'][0]->slug != 'plot-of-land-residential')) {
                        $post_title .= $coveredArea[0] . 'sq.m ';
                    } elseif (isset($totalArea[0]) && ($totalArea[0] != '') && ($taxonomiesForTitle['sale_type'][0]->slug == 'plot-of-land-residential')) {
                        $post_title .= $totalArea[0] . 'sq.m Residential ';
                    }
                    if (isset($taxonomiesForTitle['sale_type'][0]) && ($taxonomiesForTitle['sale_type'][0]->slug != 'residential')) {
                        $post_title .= $taxonomiesForTitle['sale_type'][0]->name . ' ';
                    } else {
                        $post_title .= 'Residential Property';
                    }
                    $post_title = trim($post_title);
                    $post_title .= ' ' . $propertyTypeLabel;
                    if (isset($taxonomiesForTitle['city'][0]->name)) {
                        $post_title .= ' in ' . $taxonomiesForTitle['city'][0]->name;
                    }
                    if (isset($taxonomiesForTitle['district'][0]) && (str_replace('_district', '', $taxonomiesForTitle['district'][0]->slug) != ($taxonomiesForTitle['city'][0]->slug ?? false)) && !str_contains(($taxonomiesForTitle['city'][0]->name ?? false), $taxonomiesForTitle['district'][0]->name)) {
                        $post_title .= ', ' . $taxonomiesForTitle['district'][0]->name . ' district';
                    }

                }
                if (($taxonomiesForTitle['sale_type'][0]->slug == 'commercial') || (($taxonomiesForTitle['sale_type'][1]->slug ?? false) == 'commercial')) {
                    if (isset($coveredArea[0]) && ($coveredArea[0] != '') && ($taxonomiesForTitle['sale_type'][1]->slug != 'plot-of-land-commercial')) {
                        $post_title .= $coveredArea[0] . 'sq.m. ';
                    } elseif (isset($totalArea[0]) && ($totalArea[0] != '') && ($taxonomiesForTitle['sale_type'][1]->slug == 'plot-of-land-commercial')) {
                        $post_title .= $totalArea[0] . 'sq.m. Commercial ';
                    }
                    if (isset($taxonomiesForTitle['sale_type'][1])) {
                        $post_title .= $taxonomiesForTitle['sale_type'][1]->name . ' ';
                        if ($taxonomiesForTitle['sale_type'][0]->name == 'Building') {
                            $post_title .= $taxonomiesForTitle['sale_type'][0]->name . ' ';
                        }
                    } else {
                        if (isset($taxonomiesForTitle['sale_type'][0]))
                            $post_title .= $taxonomiesForTitle['sale_type'][0]->name . ' ';
                        $post_title .= 'Property';
                    }

                    $post_title = trim($post_title);
                    $post_title .= ' ' . $propertyTypeLabel;
                    if (isset($taxonomiesForTitle['city'][0]))
                        $post_title .= ' in ' . $taxonomiesForTitle['city'][0]->name;
                    if (isset($taxonomiesForTitle['district'][0]) && (str_replace('_district', '', $taxonomiesForTitle['district'][0]->slug) != ($taxonomiesForTitle['city'][0]->slug ?? false)) && !str_contains(($taxonomiesForTitle['city'][0]->name ?? false), $taxonomiesForTitle['district'][0]->name)) {
                        $post_title .= ', ' . $taxonomiesForTitle['district'][0]->name . ' district';
                    }
                }

                $post_name = wp_unique_post_slug(sanitize_title($post_id . ' ' . $post_title), $post_id, $post->post_status, $post->post_type, $post->post_parent);

				if(get_the_title($post_id) !== $post_title){
					wp_update_post(['ID' => $post_id, 'post_title' => $post_title, 'post_name' => $post_name]);
				}


            }



?>