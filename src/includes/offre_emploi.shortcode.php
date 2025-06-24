<?php
if (!defined('ABSPATH')) {
    exit;
}

function offre_emploi_meta_shortcode($atts) {
    $atts = shortcode_atts(array(
        'field' => '',
        'label' => '',
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

    $output = '';
    
    if (!empty($atts['label'])) {
        $output = '<strong>'.esc_html($atts['label']) . '</strong> ';
    }

    switch ($field_name) {
        case 'type_contrat':
            $output .= esc_html($value);
            break;
            
        case 'localisation':
            $output .= esc_html($value);
            break;
            
        case 'salaire':
            $output .= esc_html($value);
            break;
            
        case 'date_limite':
            $output .= esc_html($value);
            break;
            
        default:
            $output .= esc_html($value);
            break;
    }

    return $output;
}

add_shortcode('offre_emploi_meta', 'offre_emploi_meta_shortcode');