<?php

// Hook sur init pour déclarer le CPT
add_action('init', function() {
    register_post_type('offre_emploi', [
        'label' => 'Offres d\'emploi',
        'public' => true,
        'menu_position' => 6,
        'menu_icon' => 'dashicons-businessman',
        'supports' => ['title', 'editor'],
        'has_archive' => true,
    ]);
});

// Ajouter les metaboxes
add_action('add_meta_boxes', function() {
    add_meta_box('offre_emploi_infos', 'Informations de l\'offre', 'render_offre_emploi_metabox', 'offre_emploi', 'normal', 'default');
});

function get_types_contrat() {
    return [
        'cdi' => 'CDI',
        'cdd' => 'CDD',
        'stage' => 'Stage',
        'alternance' => 'Alternance',
        'freelance' => 'Freelance',
    ];
}

// Affichage des champs de la metabox
function render_offre_emploi_metabox($post) {
    $type_contrat = get_post_meta($post->ID, '_offre_type_contrat', true);
    $localisation = get_post_meta($post->ID, '_offre_localisation', true);
    $salaire = get_post_meta($post->ID, '_offre_salaire', true);
    $date_limite = get_post_meta($post->ID, '_offre_date_limite', true);

    $types_contrat = get_types_contrat();

    wp_nonce_field('save_offre_emploi_meta', 'offre_emploi_meta_nonce');
    ?>
    <p>
        <label for="offre_type_contrat"><strong>Type de contrat</strong></label><br>
        <select name="offre_type_contrat" id="offre_type_contrat" class="widefat">
            <option value="">-- Sélectionnez --</option>
            <?php foreach ($types_contrat as $key => $label) : ?>
                <option value="<?php echo esc_attr($key); ?>" <?php selected($type_contrat, $key); ?>>
                    <?php echo esc_html($label); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </p>
    <p>
        <label for="offre_localisation"><strong>Localisation</strong></label><br>
        <input type="text" name="offre_localisation" id="offre_localisation" value="<?php echo esc_attr($localisation); ?>" class="widefat" />
    </p>
    <p>
        <label for="offre_salaire"><strong>Salaire</strong></label><br>
        <input type="text" name="offre_salaire" id="offre_salaire" value="<?php echo esc_attr($salaire); ?>" class="widefat" placeholder="Ex: 35k - 45k €" />
    </p>
    <p>
        <label for="offre_date_limite"><strong>Date limite de candidature</strong></label><br>
        <input type="date" name="offre_date_limite" id="offre_date_limite" value="<?php echo esc_attr($date_limite); ?>" class="widefat" />
    </p>
    <?php
}

// Sauvegarde des champs
add_action('save_post', function($post_id) {
    if (!isset($_POST['offre_emploi_meta_nonce']) || !wp_verify_nonce($_POST['offre_emploi_meta_nonce'], 'save_offre_emploi_meta')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    update_post_meta($post_id, '_offre_type_contrat', sanitize_text_field($_POST['offre_type_contrat']));
    update_post_meta($post_id, '_offre_localisation', sanitize_text_field($_POST['offre_localisation']));
    update_post_meta($post_id, '_offre_salaire', sanitize_text_field($_POST['offre_salaire']));
    update_post_meta($post_id, '_offre_date_limite', sanitize_text_field($_POST['offre_date_limite']));
});

