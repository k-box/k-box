<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Collections Language Lines
    |--------------------------------------------------------------------------
    |
    |
    */

    'collections' => [
        'title'        => 'Sammlungen',
        'personal_title' => 'Meine Sammlungen',
        'private_title' => 'Projekte',
        'description'   => 'Sammlungen helfen, Dokumente zu organisieren.',

        'empty_private_msg' => 'Im Moment keine Projekte.',

    ],

    'create_btn' => 'Erstellen',
    'save_btn' => 'Speichern',
    'loading' => 'Speichere Sammlung...',

    'panel_create_title' => 'Erstelle eine neue Sammlung',

    'panel_edit_title' => 'Sammlung <strong>:name</strong> bearbeiten',

    'created_on' => 'erstellt am',
    'created_by' => 'erstellt von',

    'private_badge_label' => 'Private Dokumentensammlung',

    'group_icon_label' => 'Sammlung',

    'empty_msg' => 'Keine Sammlungen. Erstellen sie eine Sammlung.',

    'form' => [
        'collection_name_placeholder' => 'Name der Sammlung',
        'collection_name_label' => 'Sammlungsname',

        'parent_label' => 'Übergeordnete Sammlung',
        'parent_project_label' => 'In der Projektsammlung',

        'make_public' => 'Mache diese Sammlung sichtbar für andere Projektmitglieder.',
        'make_private' => 'Mache diese Sammlung privat',
    ],

    'delete' => [

        'dialog_title' => ':collection löschen?',
        'dialog_title_alt' => 'Sammlung löschen?',
        'dialog_text' => 'Sie sind dabei :collection zu löschen. Dies wird nur die Sammlung entfernen. Die Dokumente bleiben bestehen.',
        'dialog_text_alt' => 'Sie sind dabei die gewählte Sammlung zu löschen. Dies wird nur die Sammlung entfernen. Die Dokumente bleiben bestehen.',

        'deleted_dialog_title' => ':collection wurde gelöscht',
        'deleted_dialog_title_alt' => 'Gelöscht',

        'cannot_delete_dialog_title' => 'Konnte ":collection" nicht löschen!',
        'cannot_delete_dialog_title_alt' => 'Löschen fehlgeschlagen!',

        'cannot_delete_general_error' => 'Konnte die gewählten Elemente nicht löschen.',

        'forbidden_delete_collection' => 'Die Sammlung :collection konnte nicht gelöscht werden. Sie haben keine Berechtigung, Aktionen an Sammlungen durchzuführen.',
        'forbidden_delete_project_collection' => 'Die Sammlung :collection konnte nicht gelöscht werden, da sie Teil eines Projektes ist in dem sie die Berechtigungen zum löschen nicht besitzen.',
    ],

    'move' => [
        'moved' => '":collection" verschoben',
        'moved_alt' => 'Verschoben',
        'moved_text' => 'Die Sammlung wurde verschoben, wir aktualisieren ihre Ansicht...',
        'error_title' => 'Konnte :collection nicht verschieben',
        'error_title_alt' => 'Konnte Sammlung nicht verschieben',
        'error_text_generic' => 'Verschieben aufgrund eines Fehlers fehlgeschlagen, bitte wenden sie sich an ihren Administrator.',
        'error_not_collection' => 'Das Verschieben wird nur auf Sammlungen angewendet.',
        'error_same_collection' => 'Sie können eine Sammlung nicht in sich selbst verschieben.',
        'move_to_title' => 'Nach ":collection" verschieben?',
        'move_to_project_title' => 'Verschieben nach ":collection"?',
        'move_to_project_title_alt' => 'In das Projekt verschieben?',
        'move_to_project_text' => 'Sie sind dabei eine Private Sammlung in ein Projekt zu verschieben. Dadurch werden ":collection" und alle untergeordneten Sammlungen sichtbar für alle Projektmitglieder.',
        'move_to_personal_title' => 'Sammlungen Privat machen?',
        'move_to_personal_text' => 'Sie sind dabei eine Projektsammlung in ihre privaten Sammlungen zu verschieben. Dadurch wird ":collection" vor anderen Projektmitgliedern versteckt.',
    ],

    'access' => [
        'forbidden' => 'Sie haben keine Berechtigungen auf ":name" zuzugreifen.',
        'forbidden_alt' => 'Aufgrund Ihrer Berechtigungen können Sie nicht auf die Sammlung zugreifen',
    ],

    'add_documents' => [
        'forbidden' => 'Sie können keine Dokumente zu ":name" hinzufügen, da Ihnen die Berechtigungen fehlen.',
        'forbidden_alt' => 'Sie können keine Dokumente zur Sammlung hinzufügen, da Ihnen die Berechtigungen fehlen.',
    ],

    'remove_documents' => [
        'forbidden' => 'Sie können keine Dokumente aus ":name" entfernen, da Ihnen die Berechtigungen fehlen.',
        'forbidden_alt' => 'Sie können keine Dokumente aus der Sammlung entfernen, da Ihnen die Berechtigungen fehlen.',
    ],

];
