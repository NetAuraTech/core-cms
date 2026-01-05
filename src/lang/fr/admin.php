<?php

return [
    'actions' => 'Actions',
    'add' => 'Ajouter',
    'back' => 'Retour',
    'cache' => [
        'clear' => 'Vider le cache',
        'cleared' => 'Le cache a bien été vidé.',
    ],
    'create' => 'Créer',
    'content' => [
        'edit' => 'Éditer le contenu',
        'created' => 'Le contenu a été créé.',
        'updated' => 'Le contenu a été mis à jour.',
        'deleted' => 'Le contenu a été supprimé.',
        'description' => 'Description',
        'name' => 'Nom',
        'title' => 'Titre',
        'value' => 'Contenu',
        'slug' => 'Slug',
        'article' => [
            'value' => '{0}Articles|[1,1]un article|[2,*]les articles'
        ],
        'category' => [
            'value' => '{0}Catégories|[1,1]une catégorie|[2,*]les catégories',
            'created' => 'La catégorie a été créée.',
            'updated' => 'La catégorie a été mise à jour.',
            'deleted' => 'La catégorie a été supprimée.',
        ],
        'tag' => [
            'value' => '{0}Tags|[1,1]un tag|[2,*]les tags',
            'created' => 'Le tag a été créé.',
            'updated' => 'Le tag a été mis à jour.',
            'deleted' => 'Le tag a été supprimé.',
        ],
        'page' => [
            'value' => '{0}Pages|[1,1]une page|[2,*]les pages'
        ],
        'template' => [
            'value' => '{0}Modèles|[1,1]un modèle|[2,*]les modèles'
        ],
        'status' => [
            'archived' => 'Archivé',
            'draft' => 'Brouillon',
            'published' => 'Publié',
            'value' => 'Statut',
        ],
        'published_at' => 'Date de publication'
    ],
    'editor' => [
        'category' => [
            'layout' => 'Disposition',
            'template' => 'Modèle',
        ],
        'item' => [
            'delete' => [
                'confirmed' => 'Le composant a bien été supprimé.',
            ],
        ],
        'parse' => [
            'error' => 'Impossible de parser les données de l\'éditeur visuel.',
        ],
        'sidebar' => [
            'action' => [
                'copy' => [
                    'component' => 'Copier le composant',
                    'instructions' => 'Vous pouvez coller le composant sur une autre page (CTRL + V).',
                    'page' => 'Copier le code de la page',
                    'success' => 'Le code a été copié.',
                ],
            ],
            'close' => 'Fermer',
            'component' => [
                'add' => 'Ajouter un composant',
                'all' => 'Tous les composants',
                'delete' => 'Supprimer un composant',
                'search' => 'Rechercher un composant',
                'unknown' => 'Composant inconnu',
            ],
            'empty' => 'Vous n\'avez pas encore de contenu',
            'field' => [
                'htmltext' => [
                    'alignment' => [
                        'center' => 'Aligner au centre',
                        'justify' => 'Justifier le texte',
                        'left' => 'Aligner à gauche',
                        'right' => 'Aligner à droite',
                        'unset' => 'Réinitialiser l\'alignement du texte',
                    ],
                    'bold' => 'Gras',
                    'color' => 'Couleur',
                    'formatting' => [
                        'remove' => 'Supprimer tout le formatage',
                    ],
                    'heading' => 'Titre :nr',
                    'highlight' => 'Mise en valeur',
                    'italic' => 'Italique',
                    'link' => [
                        'unlink' => 'Retirer le lien',
                        'value' => 'Lien',
                    ],
                    'list' => [
                        'lift' => 'Descendre l\'élément de la liste',
                        'sink' => 'Monter l\'élément de la liste',
                        'value' => 'Liste',
                    ],
                    'redo' => 'Refaire',
                    'strike' => 'Barré',
                    'underline' => 'Souligner',
                    'undo' => 'Annuler',
                    'video' => 'Vidéo',
                ],
            ],
            'item' => 'Éléments',
            'mode' => [
                'responsive' => 'Vue adaptative',
            ],
            'tabs' => [
                'animation' => [
                    'delay' => 'Délai',
                    'general' => 'Général',
                    'value' => 'Animations',
                    'view-transition-name' => 'Nom de transition de la vue',
                ],
                'appearance' => 'Apparence',
                'automatic-gallery' => [
                    'row' => [
                        'height' => 'Hauteur d\'une ligne',
                    ],
                    'value' => 'Galerie automatique',
                ],
                'background' => [
                    'color' => 'Couleur de fond',
                    'image' => [
                        'position' => [
                            'bottom' => 'Bas',
                            'center' => 'Centre',
                            'left' => 'Gauche',
                            'right' => 'Droite',
                            'top' => 'Haut',
                            'x' => 'Position (X)',
                            'y' => 'Position (Y)',
                        ],
                        'repeat' => [
                            'no' => 'Pas de répétition',
                            'value' => 'Répétition',
                        ],
                        'size' => [
                            'auto' => 'Originale',
                            'contain' => 'Contenir',
                            'cover' => 'Remplir',
                            'value' => 'Taille de l\'image',
                        ],
                        'opacity' => 'Opacité',
                        'value' => 'Image de fond',
                    ],
                    'value' => 'Fond',
                ],
                'border' => [
                    'color' => 'Couleur de la bordure',
                    'radius' => [
                        'bottomleft' => 'Bas Gauche',
                        'bottomright' => 'Bas Droite',
                        'topleft' => 'Haut Gauche',
                        'topright' => 'Haut Droite',
                        'value' => 'Rayon de la bordure',
                    ],
                    'line' => [
                        'blink' => 'Clignoter',
                        'line-through' => 'Line à travers',
                        'underline' => 'Souligner',
                        'overline' => 'Surligner',
                        'value' => 'Ligne de bordure',
                    ],
                    'style' => [
                        'dashed' => 'Tiré',
                        'dotted' => 'Pointillé',
                        'solid' => 'Solide',
                        'wavy' => 'Vague',
                        'value' => 'Style de bordure',
                    ],
                ],
                'carousel' => [
                    'items-per-page' => 'Éléments par pages',
                    'value' => 'Carrousel',
                ],
                'contact' => [
                    'subject' => [
                        'option' => 'Option',
                        'value' => 'Sujet',
                    ],
                    'value' => 'Formulaire de contact',
                ],
                'content' => 'Contenu',
                'ctas' => 'Appels à l\'action',
                'even-columns' => 'Colonnes',
                'form' => [
                    'value' => 'Formulaire',
                    'sections' => [
                        'value' => 'Sections',
                        'visible' => 'Visible',
                    ],
                    'fields' => [
                        'value' => 'Champs',
                        'type' => 'Type',
                        'options' => 'Options',
                        'label' => 'Label',
                        'help' => 'Aide'
                    ]
                ],
                'grid' => [
                    'gap' => 'Espacement',
                    'item' => [
                        'size' => [
                            'min' => 'Taille min. d\'un élément',
                        ],
                    ],
                    'value' => 'Grille',
                ],
                'header' => 'En-tête',
                'hero' => 'Bannière',
                'media' => [
                    'alt' => 'Alt',
                    'width' => [
                        'help' => 'Laisser vide pour une largeur automatique.',
                        'value' => 'Largeur',
                    ],
                    'value' => 'Média',
                ],
                'medias' => 'Médias',
                'label' => [
                    'help' => 'Laisser vide pour conserver le nom de la page.',
                    'value' => 'Étiquette',
                ],
                'link' => [
                    'cta' => [
                        'type' => 'Type de bouton',
                        'primary' => 'Primaire',
                        'accent' => 'Accent',
                        'outline' => 'Outline'
                    ],
                    'home' => 'Page d\'accueil',
                    'blog' => 'Articles',
                    'login' => 'Se connecter',
                    'profile' => 'Profil',
                    'type' => [
                        'external' => 'Lien externe',
                        'internal' => 'Lien interne',
                        'value' => 'Type de lien',
                    ],
                    'url' => 'URL',
                    'value' => 'Lien',
                ],
                'links' => 'Liste de liens',
                'padding' => [
                    'block' => 'Espacement vertical',
                    'inline' => 'Espacement horizontal',
                ],
                'section' => 'Section',
                'card' => 'Carte',
                'title' => [
                    'color' => 'Couleur',
                    'level' => 'Niveau',
                    'value' => 'Titre',
                ],
            ],
            'template' => [
                'choose' => 'Choisir un modèle',
                'use' => 'Utiliser un modèle',
            ],
        ],
    ],
    'taxonomy' => 'Taxonomies',
    'dashboard' => 'Tableau de bord',
    'date' => 'Date',
    'delete' => [
        'confirm' => 'Voulez-vous vraiment supprimer ce contenu ?',
        'unable' => 'Impossible de supprimer',
        'value' => 'Supprimer',
    ],
    'edit' => 'Modifier',
    'job' => [
        'date' => 'Date',
        'delete' => [
            'confirm' => 'Voulez vous vraiment supprimer la tâche ?',
            'confirmed' => 'La tâche a bien été supprimée.',
        ],
        'failed' => 'Failed tasks',
        'relaunch' => [
            'value' => 'Relancer',
            'confirmed' => 'La tâche a bien été relancée.',
        ],
        'message' => 'Message',
        'value' => '{0}Tâches|[1,1]la tâche|[2,*]les tâches',
    ],
    'manage' => 'Gérer',
    'option' => [
        'created' => 'L\'option a été créée.',
        'updated' => 'L\'option a été mise à jour.',
        'deleted' => 'L\'option a été supprimée.',
        'cannot_deleted' => 'L\'option ne peut pas être supprimée.',
        'category' => [
            'general' => 'Général',
            'branding' => 'Image de marque',
            'content_settings' => 'Paramètres de contenu',
            'contact_emails' => 'E-mails de contact',
            'social_media' => 'Réseaux sociaux',
            'security' => 'Sécurité',
            'schedule' => 'Horaires',
            'legals' => 'Juridique',
            'seo' => 'Optimisation pour les moteurs de recherche',
            'theme' => 'Thème',
            'custom' => 'Personnalisé',
        ],
        'content' => [
            'article' => 'Article',
            'post' => 'Page',
        ],
        'value' => '{0}Options|[1,1]une option|[2,*]les options',
        'key' => 'Clé',
        'keys' => [
            // General
            'site_name' => 'Nom du site',
            'description' => 'Description du site',

            // Branding
            'favicon' => 'Favicon',
            'logo' => 'Logo',

            // Content Settings
            'header' => 'En-tête',
            'footer' => 'Pied de page',
            'homepage' => 'Page d\'accueil',

            // Contact Emails
            'contact-email' => 'Email de contact',
            'noreply-email' => 'Email sans réponse',
            'sav-email' => 'Email service client',

            // Social Media
            'facebook' => 'Facebook',
            'instagram' => 'Instagram',
            'linkedin' => 'LinkedIn',
            'twitter' => 'Twitter',
            'youtube' => 'YouTube',

            // Security
            'spam_words' => 'Mots spam',

            // SEO
            'address' => 'Adresse',
            'address_city' => 'Ville',
            'address_country' => 'Pays',
            'address_latitude' => 'Latitude',
            'address_longitude' => 'Longitude',
            'address_postal-code' => 'Code postal',
            'address_region' => 'Région',
            'area_served' => 'Zone desservie',
            'phone' => 'Téléphone',

            // Schedule
            'schedule_monday' => 'Lundi',
            'schedule_tuesday' => 'Mardi',
            'schedule_wednesday' => 'Mercredi',
            'schedule_thursday' => 'Jeudi',
            'schedule_friday' => 'Vendredi',
            'schedule_saturday' => 'Samedi',
            'schedule_sunday' => 'Dimanche',

            // Legals
            'privacy-policy' => 'Politique de confidentialité',
            'host-address' => 'Adresse de l\'hébergeur',
            'host-name' => 'Nom de l\'hébergeur',
            'host-phone' => 'Téléphone de l\'hébergeur',
            'host-website' => 'Site web de l\'hébergeur',
        ],
        'type' => [
            'value' => 'Type',
            'text' =>  'Texte',
            'number' => 'Nombre',
            'boolean' => 'Booléen',
            'media' => 'Média',
            'content' => 'Contenu',
            'template' => 'Modèle',
        ],
        'schedule' => [
            'format_hint' => 'Format : HH:MM-HH:MM ou HH:MM-HH:MM/HH:MM-HH:MM',
            'examples' => 'Exemples :',
            'continuous' => 'Journée continue : 09:00-18:00',
            'with_break' => 'Avec coupure : 09:00-12:00/14:00-18:00',
            'closed' => 'Fermé : laisser vide',
        ]
    ],
    'save' => 'Enregistrer',
    'send' => 'Envoyer',
    'statut' => 'Statut',
    'value' => 'Valeur',
    'view' => 'Voir',
    'permission' => 'Permissions',
    'role' => [
        'name' => 'Nom',
        'value' => '{0}Rôles|[1,1]un rôle|[2,*]les rôles',
        'created' => 'Le rôle a été créé.',
        'updated' => 'Le rôle a été mis à jour.',
        'deleted' => 'Le rôle a été supprimé.',
    ],
    'user' => [
        'created' => 'L\'utilisateur a été créé.',
        'updated' => 'L\'utilisateur a été mis à jour.',
        'deleted' => 'L\'utilisateur a été supprimé.',
        'email' => 'E-mail',
        'ban' => [
            'confirm' => 'Voulez-vous vraiment bannir cet utilisateur ?',
            'confirmed' => 'L\'utilisateur a été banni !',
            'value' => 'Bannir l\'utilisateur',
        ],
        'confirm' => 'Confirmer le compte utilisateur',
        'confirmed' => 'L\'utilisateur a été confirmé !',
        'impersonate' => [
            'info' => 'Vous êtes en mode d\'usurpation d\'identité.',
            'leave' => 'Quitter',
            'value' => 'Prendre le contrôle du compte',
        ],
        'password' => [
            'confirm' => 'Confirmer le mot de passe',
            'value' => 'Mot de passe',
        ],
        'registration' => 'Inscription',
        'unban' => [
            'confirm' => 'Voulez-vous vraiment débannir cet utilisateur ?',
            'confirmed' => 'L\'utilisateur a été débanni !',
            'value' => 'Débannir l\'utilisateur',
        ],
        'username' => 'Nom d\'utilisateur',
        'value' => '{0}Utilisateurs|[1,1]un utilisateur|[2,*]les utilisateurs',
    ],
];