<?php
if (!defined('ABSPATH')) {
    exit;
}

function offre_emploi_meta_shortcode($atts) {
    $atts = shortcode_atts(array(
        'field' => '',
    ), $atts, 'offre_emploi_meta');

    if (empty($atts['field'])) {
        return '';
    }

    global $post;
    
    if (!$post || get_post_type($post) !== 'offre_emploi') {
        return '';
    }

    $field_name = $atts['field'];
    $meta_key = '_offre_' . $field_name;
    $value = get_post_meta($post->ID, $meta_key, true);

    if (empty($value)) {
        return '';
    }

    switch ($field_name) {
        case 'type_contrat':
            return esc_html($value);
            
        case 'localisation':
            return esc_html($value);
            
        case 'salaire':
            return esc_html($value);
            
        case 'date_limite':
            if ($value) {
                $formatted_date = date_i18n(get_option('date_format'), strtotime($value));
                return esc_html($formatted_date);
            }
            return '';
            
        default:
            return esc_html($value);
    }
}

add_shortcode('offre_emploi_meta', 'offre_emploi_meta_shortcode');