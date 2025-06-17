<?php

add_shortcode('formation_list', function ($atts) {
    $atts = shortcode_atts([
        'categorie' => '',
    ], $atts, 'formation_list');

    $args = [
        'post_type' => 'formation',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC'
    ];

    if (!empty($atts['categorie'])) {
        $args['meta_query'] = [
            [
                'key' => '_formation_categorie',
                'value' => $atts['categorie'],
                'compare' => '='
            ]
        ];
    }

    $formations = get_posts($args);
    $categories = get_formation_categories();

    if (empty($formations)) {
        return '<p>Aucune formation trouvée.</p>';
    }
    // print("<pre>".print_r($formations,true)."</pre>");

    $formations_by_categorie = [];
    
    foreach ($formations as $formation) {
        $cat_slug = get_post_meta($formation->ID, '_formation_categorie', true);
        if (!isset($formations_by_categorie[$cat_slug])) {
            $formations_by_categorie[$cat_slug] = [];
        }
        $formations_by_categorie[$cat_slug][] = $formation;
    }

    $output = '<div class="actesur-categorie-list">';
    foreach($formations_by_categorie as $slug => $categorie){
        $url = site_url('/formations/?categorie=' . $slug);
        $image_url = ACTESUR_FORMATION_PLUGIN_URL.'images/'.$slug . '.svg';
        $output .= '<div class="categorie-item">';
        $output .= '<h3><img src="' . esc_url($image_url) . '"/><a href="' . esc_url($url) . '">' . esc_html($categories[$slug]) . '</a></h3>';

        foreach ($categorie as $formation) {
            $formation_url = get_permalink($formation);
            $formation_title = get_the_title($formation);
            $output .= '<div class="formation-item">';
            $output .= '<a href="' . esc_url($formation_url) . '">' . esc_html($formation_title) . '<img src="' . esc_url(ACTESUR_FORMATION_PLUGIN_URL.'images/arrow-right.svg') .'" /></a>';
            $output .= '</div>';
        }

        $output .= '</div>';
    }
    
    $output .= '</div>';
    
    return $output;
});