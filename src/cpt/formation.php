<?php


// Hook sur init pour déclarer le CPT
add_action('init', function() {
    register_post_type('formation', [
        'label' => 'Formations',
        'public' => true,
        'menu_position' => 5,
        'menu_icon' => 'dashicons-welcome-learn-more',
        'supports' => ['title', 'editor'], // éditeur natif pour le contenu principal
        'has_archive' => true,
    ]);
});

// Ajouter les metaboxes
add_action('add_meta_boxes', function() {
    add_meta_box('formation_infos', 'Informations de la formation', 'render_formation_metabox', 'formation', 'normal', 'default');
});

function get_formation_categories() {
    return [
        'prevention-des-risques-pro' => 'Prévention des risques Pro',
        'formations-caces' => 'Formations CACES',
        'incendie' => 'Incendie',
        'ergonomie' => 'Ergonomie',
        'habilitations-electriques' => 'Habilitations électriques',
        'sauveteur-secouriste-du-travail' => 'Sauveteur secouriste du travail',
        'interventions-en-milieux-a-risques' => 'Interventions en milieux à risques',
        'les-risques-psychosociaux' => 'Les Risques Psychosociaux',
        'travail-en-hauteur-et-echafaudages' => 'Travail en hauteur et échafaudages',
        'environnement' => 'Environnement',
    ];
}


// Affichage des champs de la metabox
function render_formation_metabox($post) {
    $categorie = get_post_meta($post->ID, '_formation_categorie', true);
    $description_courte = get_post_meta($post->ID, '_formation_description_courte', true);
    $public = get_post_meta($post->ID, '_formation_public', true);

    $categories = get_formation_categories();

    wp_nonce_field('save_formation_meta', 'formation_meta_nonce');
    ?>
    <p>
        <label for="formation_categorie"><strong>Catégorie</strong></label><br>
        <select name="formation_categorie" id="formation_categorie" class="widefat">
            <option value="">-- Sélectionnez --</option>
            <?php foreach ($categories as $key => $label) : ?>
                <option value="<?php echo esc_attr($key); ?>" <?php selected($categorie, $key); ?>>
                    <?php echo esc_html($label); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </p>
    <p>
        <label for="formation_description_courte"><strong>Description courte</strong></label><br>
        <textarea name="formation_description_courte" id="formation_description_courte" rows="3" class="widefat"><?php echo esc_textarea($description_courte); ?></textarea>
    </p>
    <p>
        <label for="formation_public"><strong>Public (un élément par ligne)</strong></label><br>
        <textarea name="formation_public" id="formation_public" rows="3" class="widefat"><?php echo esc_textarea($public); ?></textarea>
    </p>
    <?php
}



// Sauvegarde des champs
add_action('save_post', function($post_id) {
    if (!isset($_POST['formation_meta_nonce']) || !wp_verify_nonce($_POST['formation_meta_nonce'], 'save_formation_meta')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    update_post_meta($post_id, '_formation_categorie', sanitize_text_field($_POST['formation_categorie']));
    update_post_meta($post_id, '_formation_description_courte', sanitize_textarea_field($_POST['formation_description_courte']));
    update_post_meta($post_id, '_formation_public', sanitize_textarea_field($_POST['formation_public']));
});


// On déclare le shortcode
add_shortcode('formation_meta', function ($atts) {
    if (!is_singular('formation')) {
        return ''; // on ne sort du shortcode que sur une formation
    }

    $atts = shortcode_atts([
        'field' => '',
    ], $atts, 'formation_meta');

    $field = $atts['field'];
    if (!$field) {
        return '';
    }

    $post_id = get_the_ID();

    // Liste des champs et leurs fonctions de rendu associées
    $field_renderers = [
        'categorie' => 'render_formation_categorie',
        'description_courte' => 'render_formation_description_courte',
        'public' => 'render_formation_public',
    ];

    if (!array_key_exists($field, $field_renderers)) {
        return '';
    }

    $renderer_function = $field_renderers[$field];
    if (function_exists($renderer_function)) {
        return call_user_func($renderer_function, $post_id);
    }

    return '';
});


// Fonction de rendu pour chaque champ :

function render_formation_categorie($post_id)
{
    $value = get_post_meta($post_id, '_formation_categorie', true);
    $categories = get_formation_categories();
    if (!$value) return '';

    $label = isset($categories[$value]) ? $categories[$value] : $value;

    // Construction du chemin vers l'image
    $image_url = ACTESUR_FORMATION_PLUGIN_URL.'images/'.$value . '.svg';

    $output  = '<div class="formation-categorie">';
    $output .= '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($label) . '" style="width:50px;height:auto;"> ';
    $output .= '</div>';

    return $output;
}

function render_formation_description_courte($post_id)
{
    $value = get_post_meta($post_id, '_formation_description_courte', true);
    if (!$value) return '';
    return '<p>' . nl2br(esc_html($value)) . '</p>';
}

function render_formation_public($post_id)
{
    $value = get_post_meta($post_id, '_formation_public', true);
    if (!$value) return '';

    $public_items = array_filter(array_map('trim', explode("\n", $value)));
    if (empty($public_items)) return '';

    $output = '<ul id="block_custom_public_formation">';
    foreach ($public_items as $item) {
        $output .= '<li><img src="'.(ACTESUR_FORMATION_PLUGIN_URL.'/images/plus.svg' ).'" alt="Plus">
' . esc_html($item) . '</li>';
    }
    $output .= '</ul>';
    return $output;
}
