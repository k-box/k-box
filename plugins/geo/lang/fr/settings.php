<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Language Lines for the settings page
    |--------------------------------------------------------------------------
    */

    'page_title' => 'Paramètres de l\'extension géographique',

    'description' => 'Cette page contient les paramètres pour modifier le comportement de l\'extension géographique',

    'geoserver' => [
        'title' => 'Connexion au GeoServer',
        'description' => 'Paramètres nécessaires pour la connexion au Geoserver. GeoServer est utilisé pour stocker, visualiser et convertir des fichiers gégraphiques (GIS)',

        'url' => 'Adresse du serveur Geoserver (ex. https://domain.com/geoserver/)',
        'username' => 'Nom d\'utilisateur pour le Geoserver',
        'password' => 'Mot de passe pour le Geoserver',
        'workspace' => 'GeoServer workspace(ex. kbox)',
    ],

    'gdal' => [
        'available' => 'Gdal librairie installée. (:version)',
        'not_available' => 'Gdal librairie non-disponible, certaines fonctions peuvent ne pas fonctionner.',
    ],

    'connection' => [
        'established' => 'Connexion au GeoServer (:version) réussie.',
        'failed' => 'Connexion au Geoserver échouée . :error',
    ],

    'providers' => [
        'title' => 'Map Providers',
        'description' => 'Configurez les cartes de base utilisées comme fonds de carte pour la visualisation',

        'provider_created' => 'Map Provider ":name" créé',
        'provider_updated' => 'Map Provider ":name" modifié',
        'default_provider_updated' => 'Nouveau provider par défaut ":name"',
        'providers_enabled' => 'Aucun provider activé|{1}Provider ":name" activé|[2,*]activé ":name" et :count autres providers',
        'providers_disabled' => 'Aucun provider désactivé|{1}Provider ":name" désactivé|[2,*]désactivé ":name" et :count autres providers',
        'provider_deleted' => 'Map Provider ":name" supprimé',
        'provider_delete_denied_is_default' => 'Le provider par défaut ":name" n\'a pas pu être supprimé. Veuillez d\'abord indiquer un autre provider par défaut.',

        'create_title' => 'Créer un provider',
        'create_description' => 'Créer un nouveau map provider',

        'edit_title' => 'Modifier le provider ":name"',
        'edit_description' => 'EModifier un map provider',

        'types' => [
            'tile' => 'Tiled Map Service (TMS) provider',
            'wms' => 'Web Map Service (WMS) provider',
        ],

        'attributes' => [
            'id' => 'id',

            'default' => 'défaut',
            'enabled' => 'activé',

            'subdomains' => 'Subdomains',
            'subdomains_description' => 'Pour les TMS providers, les tuiles peuvent être servies depuis différents domaines pour accélérer les temps de chargement. Dans l\'URL ceci est généralment spécifié par le symbole {s}.',

            'type' => 'Type de Map provider',
            'type_description' => 'TMS ou WMS?',

            'label' => 'Nom',
            'label_description' => 'Le nom à donner à ce provider. Doit être unique parmi tous les providers listés',

            'url' => 'Url',
            'url_description' => 'L\'URL pour charger la carte',

            'attribution' => 'Attribution',
            'attribution_description' => 'Le texte d\'attribution à afficher quand le provider est sélectionné. Cela contient en général les droits d\'auteur et les informations de license',

            'maxZoom' => 'Niveau de zoom maximum',
            'maxZoom_description' => 'Le niveau de zoom maximum supporté par ce provider',
            
            'layers' => 'Couches',
            'layers_description' => 'Les couches à utiliser, données par le map provider. Uniquement pour les WMS',
        ],
    ],
        
    'validation' => [
        'url' => [
            'regex' => 'L\'URL doit commencer avec http:// ou https://, ex. https://tile.openstreetmaps.com/{x}/{y}/{z}.png',
        ],
        'label' => [
            'not_in' => "Le nom du provider doit être unique. [:label] est déjà utilisé.",
        ],
        'id' => [
            'not_found' => 'Provider non trouvé',
        ],
        'type' => [
            'not_changeable' => "Le type du provider [:current] ne peut pas être changé en [:new]",
        ],
        'default_map' => [
            'in' => 'La carte sélectionnée n\'est pas disponible dans le système',
        ]
    ],
    
];
