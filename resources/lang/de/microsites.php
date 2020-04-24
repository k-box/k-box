<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Microsites related Language Lines
    |--------------------------------------------------------------------------
    */

    'page_title' => 'Projekt Microsites',
    'page_title_with_name' => 'Projekt Microsite für :project',

    'pages' => [
        'create' => 'Erstelle eine Microsite für Projekt ":project"',
        'edit' => 'Bearbeite Microsite für ":project"',
    ],


    'hints' => [
        'what' => 'Eine Micosite erlaubt es, eine Öffentliche Website für Ihr Projekt anzulegen',
        'create_for_project' => 'Erstelle eine Microsite für das Projekt',
        'for_project' => 'Erstelle eine Microsite für das Projekt',
        'delete_microsite' => 'Entferne die Projekt-Microsite',
        'edit_microsite' => 'Ändere den Inhalt und die Einstellungen der Microsite',

        'site_title' => 'Der Name der Website, welcher dem Nutzer angezeigt wird',
        'slug' => 'Die Nutzerfreundliche Version der Netzwerkadresse. Diese hilft Nutzern, sich die Adresse zu merken. Slugs können nicht mit "create" beginnen.',
        'logo' => 'Das Logo der Website, maximal 280x80 Pixel. Das Bild muss über das HTTPS Protokoll erreichbar sein',
        'default_language' => 'Die Sprache in welcher die Website dem Nutzer angezeigt wird, wenn keine andere Sprache ausgewählt wird',

        'content' => 'Hier kann der Textinhalt und ein optionales Navigationsmenü definiert werden. Im moment wird nur Inhalt in Englisch oder Russisch unterstützt.',

        'page_title' => 'Der Titel der Seite (Standardtitel ist "Home")',
        'page_slug' => 'Die Nutzerfreundliche version des Seitentitels. Das kann es den Nutzern erleichtern, sich die Addresse zu merken',
        'page_content' => 'Sie können <a href="https://daringfireball.net/projects/markdown/basics" target="_blank">Markdown-formatierten</a> Text verwenden. Weiterhin können links und Elemente von anderen Webseiten eingefügt werden. Zum Beispiel kann ein RSS-Feed eingebunden werden, indem folgender code in einer neuen Zeile eingefügt wird: <code>@rss:https://klinktest.wordpress.com/feed/</code>. Bitte beachten Sie das eingefügte Elemente 1–4 Stunden zwischengespeichert werden, um hohe Ressourcennutzung zu vermeiden (abhängig vom Dienst)',
    ],

    'actions' => [
        'create' => 'Microsite erstellen',
        'edit' => 'Microsite bearbeiten',
        'save' => 'Microsite Einstellungen speichern',
        'delete' => 'Microsite löschen',
        'delete_ask' => 'Sie sind dabei die Microsite für ":title" zu entfernen. Sind sie sich sicher, das sie gelöscht werden soll?',
        'view_site' => 'Microsite betrachten',
        'publish' => 'Microsite veröffentlichen',
        'view_project_documents' => 'Projekt öffnen',
        'search' => 'K-Link Durchsuchen...',
        'search_project' => 'Durchsuche :project...',
    ],

    'messages' => [
        'created' => 'Die Microsite ":title" wurde erstellt und ist hier aufrufbar: <a href=":site_url" target="_blank">:slug</a>',
        'updated' => 'Die Microsite ":title" wurde erstellt',
        'deleted' => 'Die Microsite ":title" wurde erstellt. Die öffentliche Addresse wird nicht mehr erreichbar sein',
    ],

    'errors' => [
        'create' => 'Fehler beim erstellen der Microseite. :error',
        'create_no_project' => 'Bitte ein Projekt angeben. Zum erstellen einer Microsite muss ein Projekt ausgewählt werden.',
        'create_already_exists' => 'Eine Microsite für das Projekt ":project" existiert bereits. Es kann nicht mehr als eine Microsite pro Projekt angelegt werden.',
        'delete' => 'Es gab ein Problem beim Löschen der Microsite. :error',
        'update' => 'Es gab ein Problem beim Aktualisieren der Microsite. :error',
        'delete_forbidden' => 'Sie können die Microsite ":title" nicht löschen, da sie kein Projektmanager des Projektes der Microsite sind.',
        'forbidden' => 'Sie müssen ein Projektadministrator sein, um mit Microsites zu interagieren.',
        'user_not_affiliated_to_an_institution' => 'Sie sind keiner Institution zugehörig, bitte überprüfen sie ihr Profil vor dem erstellen einer Microsite.',
    ],

    'labels' => [
        'microsite' => 'Microsite',
        'site_title' => 'Seitenname',
        'slug' => 'Nutzerfreundlicher Slug',
        'site_description' => 'Seitenbeschreibung',
        'logo' => 'Seitenlogo, (HTTPS URL zu einem Bild)',
        'default_language' => 'Standard Seitensprache',
        'cancel_and_back' => 'Abbrechen und zurück zum Projekt',
        'publishing_box' => 'Veröffentlichen',
        'content' => 'Microsite Inhalt',

        'content_en' => 'Englische Version des Inhalts',
        'content_ru' => 'Russische Version des Inhalts',

        'page_title' => 'Der Titel der zu erstellenden Seite',
        'page_slug' => 'Der Slug der Seite',
        'page_content' => 'Der Inhalt der Seite',
    ],
];
