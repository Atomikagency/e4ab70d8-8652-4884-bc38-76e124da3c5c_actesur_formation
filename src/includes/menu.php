<?php

add_shortcode('main_menu', function ($atts) {

    $atts = shortcode_atts([
        'menu' => 'MainMenu', // on passe ici le nom du menu directement
    ], $atts, 'main_menu');

    if (empty($atts['menu'])) {
        return '<!-- Aucun nom de menu fourni -->';
    }

    // On récupère l'objet menu par son nom
    $menu_obj = wp_get_nav_menu_object($atts['menu']);

    if (!$menu_obj) {
        return '<!-- Menu non trouvé -->';
    }

    $menu_items = wp_get_nav_menu_items($menu_obj->term_id);

    if (!$menu_items) {
        return '<!-- Pas d\'éléments -->';
    }
// On reconstruit l'arborescence simple
    $menu_tree = [];

    foreach ($menu_items as $item) {
        if ($item->menu_item_parent == 0) {
            $menu_tree[$item->ID] = [
                'item' => $item,
                'children' => [],
            ];
        } else {
            $menu_tree[$item->menu_item_parent]['children'][] = $item;
        }
    }

// Construction du HTML
    $output = '<nav id="actesur-top-menu">
    <img class="actesur-logo" decoding="async" src="/wp-content/uploads/2025/06/logo-actesur.webp" alt="logo-actesur" title="logo-actesur" srcset="/wp-content/uploads/2025/06/logo-actesur.webp 553w, /wp-content/uploads/2025/06/logo-actesur-480x208.webp 480w" sizes="(min-width: 0px) and (max-width: 480px) 480px, (min-width: 481px) 553px, 100vw" class="wp-image-75">
    <ul id="actesur-main-menu" class="main-menu">';

    foreach ($menu_tree as $entry) {
        $item = $entry['item'];
        $title = esc_html($item->title);
        $url = esc_url($item->url);

        $output .= '<li>';

        $output .= '<a href="' . $url . '">' . $title;

        if (!empty($entry['children']) || strtolower($title) === 'formations') {
            $output .= '<i class="fa fa-angle-right" aria-hidden="true"></i>';
        }

        $output .= '</a>';

        // Si le label est Formations, on injecte dynamiquement
        if (strtolower($title) === 'formations') {
            $output .= render_formations_submenu();
        }

        // Affichage des sous-items classiques existants
        if (!empty($entry['children'])) {
            $output .= '<ul><button class="menu-return-button"><i class="fa fa-angle-left" aria-hidden="true"></i>Retour</button>';
            foreach ($entry['children'] as $child) {
                $output .= '<li><a href="' . esc_url($child->url) . '">' . esc_html($child->title) . '</a></li>';
            }
            $output .= '</ul>';
        }

        $output .= '</li>';
    }

    $output .= '</ul>
    <a href="" class="espace-adherent" data-icon=""><span>Espace adhérent</span></a>
    <button id="actesur-menu-toggle">
        <i class="fa fa-bars" aria-hidden="true"></i>
    </button>
    </nav>';

    return $output;
});

function render_formations_submenu() {

    $categories = get_formation_categories();

    if (empty($categories)) return '';

    // Récupérer toutes les formations en une seule fois (évite les requêtes répétées)
    $formations = get_posts([
        'post_type' => 'formation',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'meta_key' => '_formation_categorie',
    ]);

    // On organise les formations par catégorie (slug)
    $formations_by_categorie = [];
    
    foreach ($formations as $formation) {
        $cat_slug = get_post_meta($formation->ID, '_formation_categorie', true);
        if (!isset($formations_by_categorie[$cat_slug])) {
            $formations_by_categorie[$cat_slug] = [];
        }
        $formations_by_categorie[$cat_slug][] = $formation;
    }

    $output = '<ul class="submenu-formations"><button class="menu-return-button"><i class="fa fa-angle-left" aria-hidden="true"></i>Retour</button>';

    $order = [
        ["prevention-des-risques-pro", "ergonomie", "les-risques-psychosociaux"],
        ["formations-caces", "habilitations-electriques", "travail-en-hauteur-et-echafaudages"],
        ["incendie", "sauveteur-secouriste-du-travail", "interventions-en-milieux-a-risques", "environnement"]
    ];

    $columns = [];

    foreach ($order as $idx => $keys) {
        $columns[$idx] = [];
        foreach ($keys as $k) {
            if (array_key_exists($k, $categories)) {
                // On reconstruit l’associatif dans l’ordre voulu
                $columns[$idx][$k] = $categories[$k];
            }
        }
    }
    foreach ($columns as $categories){
        $output .= '<div class="submenu-formations-column">';
        foreach ($categories as $slug => $label) {
            $url = site_url('/formations/?categorie=' . $slug);
            $image_url = ACTESUR_FORMATION_PLUGIN_URL.'images/'.$slug . '.svg';
            $output .= '<li>';
            $output .= '<a href="' . esc_url($url) . '"><img src="'. esc_url($image_url) .'" /><span>' . esc_html($label) . '</span><i class="fa fa-angle-right" aria-hidden="true"></i></a>';

            // Si la catégorie contient des formations, on les affiche
            if (!empty($formations_by_categorie[$slug])) {
                $output .= '<ul class="submenu-formations-items">';
                foreach ($formations_by_categorie[$slug] as $formation) {
                    $formation_url = get_permalink($formation);
                    $formation_title = get_the_title($formation);
                    $output .= '<li><a href="' . esc_url($formation_url) . '"><img src="' . esc_url(ACTESUR_FORMATION_PLUGIN_URL.'images/plus.svg') .'" />' . esc_html($formation_title) . '</a></li>';
                }
                $output .= '</ul>';
            }

            $output .= '</li>';
        }
        $output .= '</div>';
    }

    $output .= '</ul>';
    return $output;
}

