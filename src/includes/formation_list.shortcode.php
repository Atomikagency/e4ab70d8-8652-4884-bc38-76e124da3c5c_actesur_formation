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

    if (empty($formations)) {
        return '<p>Aucune formation trouvée.</p>';
    }

    $output = '<div class="formation-list">';
    
    foreach ($formations as $formation) {
        $categorie = get_post_meta($formation->ID, '_formation_categorie', true);
        $description_courte = get_post_meta($formation->ID, '_formation_description_courte', true);
        $categories = get_formation_categories();
        $categorie_label = isset($categories[$categorie]) ? $categories[$categorie] : $categorie;
        
        $output .= '<div class="formation-item">';
        $output .= '<h3><a href="' . get_permalink($formation->ID) . '">' . esc_html($formation->post_title) . '</a></h3>';
        
        if ($categorie) {
            $image_url = ACTESUR_FORMATION_PLUGIN_URL . 'images/' . $categorie . '.svg';
            $output .= '<div class="formation-categorie">';
            $output .= '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($categorie_label) . '" style="width:30px;height:auto;"> ';
            $output .= '<span>' . esc_html($categorie_label) . '</span>';
            $output .= '</div>';
        }
        
        if ($description_courte) {
            $output .= '<div class="formation-description">' . nl2br(esc_html($description_courte)) . '</div>';
        }
        
        $output .= '</div>';
    }
    
    $output .= '</div>';
    
    return $output;
});