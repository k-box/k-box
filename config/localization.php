<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Supported Locales for JS based localization
    |--------------------------------------------------------------------------
    |
    | This defines the languages that the JS environment should receive.
    | Fallback language (app.fallback_locale) is always considered even if not
    | mentioned here
    |
    */

    'supported_locales' => ['ru', 'tg', 'fr', 'de', 'ky'],

    /*
    |--------------------------------------------------------------------------
    | What strings should be exported to JS localization
    |--------------------------------------------------------------------------
    |
    | This defines what keys from lang files the Javascript environment will
    | receive.
    | The same strings will be exported for all the supported_locales and the
    | fallback_locale
    | Here use the dot notation like if you call the `trans` method.
    |
    */
    'exports' => [
        'actions.cancel',
        'actions.saving',
        'actions.deleting',
        'actions.restoring',
        'actions.cleaning_trash',
        'actions.cleaning_trash_wait',
        'actions.not_available',
        'actions.selection.at_least_one_document',
        'actions.selection.at_least_one',
        'actions.selection.only_one',
        'actions.clipboard.copied_title',
        'actions.clipboard.copied_link_text',
        'actions.clipboard.not_copied_title',
        'actions.clipboard.not_copied_link_text',
        'actions.create_collection_btn',
        'actions.publish_documents',
        'actions.trash_btn',
        'actions.trash_btn_alt',
        'actions.edit',
        'actions.details',
        'actions.forcedelete_btn_alt',
        'actions.open',

        'actions.dialogs.cancel_btn',
        'actions.dialogs.cancel_btn_alt',
        'actions.dialogs.yes_btn',
        'actions.dialogs.no_btn',
        'actions.dialogs.ok_btn',
        'actions.dialogs.delete_btn',
        'actions.dialogs.trash_btn',
        'actions.dialogs.remove_btn',
        'actions.dialogs.move_btn',
        'actions.dialogs.input_required',

        'documents.bulk.adding_title',
        'documents.bulk.adding_message',
        'documents.bulk.added_to_collection',
        'documents.bulk.some_added_to_collection',
        'documents.bulk.add_to_error',

        'documents.restore.restore_dialog_title',
        'documents.restore.restore_dialog_title_count',
        'documents.restore.restore_dialog_text',
        'documents.restore.restore_version_dialog_text',
        'documents.restore.restore_dialog_text_count',
        'documents.restore.restore_dialog_yes_btn',
        'documents.restore.restore_dialog_no_btn',
        'documents.restore.restore_success_title',
        'documents.restore.restore_error_title',
        'documents.restore.restoring',
        'documents.restore.restore_error_text_generic',
        'documents.restore.restore_version_error_text_generic',

        'documents.delete.dialog_title',
        'documents.delete.dialog_title_alt',
        'documents.delete.dialog_title_count',
        'documents.delete.dialog_text',
        'documents.delete.dialog_text_count',
        'documents.delete.deleted_dialog_title',
        'documents.delete.deleted_dialog_title_alt',
        'documents.delete.cannot_delete_dialog_title',
        'documents.delete.cannot_delete_dialog_title_alt',
        'documents.delete.cannot_delete_general_error',

        'documents.permanent_delete.dialog_title',
        'documents.permanent_delete.dialog_title_alt',
        'documents.permanent_delete.dialog_title_count',
        'documents.permanent_delete.dialog_text',
        'documents.permanent_delete.dialog_text_count',
        'documents.permanent_delete.deleted_dialog_title',
        'documents.permanent_delete.deleted_dialog_title_alt',
        'documents.permanent_delete.cannot_delete_dialog_title',
        'documents.permanent_delete.cannot_delete_dialog_title_alt',
        'documents.permanent_delete.cannot_delete_general_error',

        'documents.trash.clean_title',
        'documents.trash.empty_all_text',
        'documents.trash.empty_selected_text',
        'documents.trash.yes_btn',
        'documents.trash.no_btn',
        'documents.trash.cleaned',
        'documents.trash.cannot_clean',
        'documents.trash.cannot_clean_general_error',

        'documents.update.removed_from_title',
        'documents.update.removed_from_text',
        'documents.update.removed_from_text_alt',
        'documents.update.cannot_remove_from_title',
        'documents.update.cannot_remove_from_general_error',

        'documents.upload.folders_dragdrop_not_supported',
        'documents.upload.error_dialog_title',
        'documents.upload.upload_dialog_title',
        'documents.upload.max_uploads_reached_title',
        'documents.upload.max_uploads_reached_text',
        'documents.upload.all_uploaded',
        'documents.upload.dragdrop_not_supported',
        'documents.upload.dragdrop_not_supported_text',
        'documents.upload.remove_btn',
        'documents.upload.cancel_btn',
        'documents.upload.cancel_question',
        'documents.upload.empty_file_error',
        'documents.messages.drag_hint',

        'groups.delete.dialog_title',
        'groups.delete.dialog_title_alt',
        'groups.delete.dialog_text',
        'groups.delete.dialog_text_alt',
        'groups.delete.deleted_dialog_title',
        'groups.delete.deleted_dialog_title_alt',
        'groups.delete.cannot_delete_dialog_title',
        'groups.delete.cannot_delete_dialog_title_alt',
        'groups.delete.cannot_delete_general_error',

        'groups.move.error_title',
        'groups.move.error_title_alt',
        'groups.move.error_text_generic',
        'groups.move.moved',
        'groups.move.moved_alt',
        'groups.move.moved_text',
        'groups.move.error_not_collection',
        'groups.move.error_same_collection',
        'groups.move.move_to_project_title',
        'groups.move.move_to_project_title_alt',
        'groups.move.move_to_project_text',
        'groups.move.move_to_title',
        'groups.move.move_to_personal_title',
        'groups.move.move_to_personal_text',

        'groups.people.group_name_already_exists',
        'groups.people.create_group_dialog_title',
        'groups.people.create_group_dialog_text',
        'groups.people.create_group_dialog_placeholder',
        'groups.people.create_group_error_title',
        'groups.people.create_group_generic_error_text',
        'groups.people.cannot_add_user_dialog_title',
        'groups.people.cannot_add_user_dialog_text',
        'groups.people.user_already_exists',
        'groups.people.delete_dialog_title',
        'groups.people.delete_dialog_text',
        'groups.people.delete_error_title',
        'groups.people.delete_generic_error_text',
        'groups.people.remove_user_dialog_title',
        'groups.people.remove_user_dialog_text',
        'groups.people.remove_user_error_title',
        'groups.people.remove_user_generic_error_text',
        'groups.people.rename_dialog_title',
        'groups.people.rename_dialog_text',
        'groups.people.rename_error_title',
        'groups.people.rename_generic_error_text',

        'share.with_label',
        'share.dialog.share_created',
        'share.dialog.collection_shared',
        'share.dialog.collection_shared_text',
        'share.dialog.document_shared',
        'share.dialog.document_shared_text',
        'share.dialog.publishing',
        'share.dialog.unpublishing',
        'share.dialog.published',
        'share.dialog.not_published',
        'share.dialog.publishing_failed',
        'share.dialog.publish_already_in_progress',
        'share.unshare',
        'share.unsharing',
        'share.remove',
        'share.removing',
        'share.removed',
        'share.remove_error',
        'share.dialog.select_users',

        'errors.dragdrop.not_permitted_title',
        'errors.dragdrop.not_permitted_text',
        'errors.dragdrop.link_not_permitted_title',
        'errors.dragdrop.link_not_permitted_text',
        'errors.generic_text',
        'errors.generic_text_alt',
        'errors.generic_title',
        'errors.preference_not_saved_title',
        'errors.preference_not_saved_text',
        'errors.413_text',

        'panels.loading_message',
        'panels.load_error',
        'panels.load_error_title',

        'validation.custom.document.required',
        'validation.custom.document.required_alt',

        'share.share_btn',

        // Network related

        'networks.make_public',
        'networks.publish_to_short',
        'networks.publish_to_long',
        'networks.making_public_title',
        'networks.making_public_text',
        'networks.make_public_error',
        'networks.make_public_error_title',
        'networks.make_public_success_text_alt',
        'networks.make_public_success_title',
        'networks.make_public_change_title_not_available',
        'networks.make_public_all_collection_dialog_text',
        'networks.make_public_inside_collection_dialog_text',
        'networks.make_public_dialog_title',
        'networks.make_public_dialog_title_alt',
        'networks.publish_btn',
        'networks.make_public_empty_selection',
        'networks.make_public_dialog_text',
        'networks.make_public_dialog_text_count',

        'projects.labels.search_member_not_found',

        // upload page

        'upload.start',
        'upload.remove',
        'upload.open_file_location',
        'upload.cancel',
        'upload.cancel_question',
        'upload.status.started',
        'upload.status.queued',
        'upload.status.uploading',
        'upload.status.completed',
        'upload.status.cancelled',
        'upload.status.failed',

        'documents.duplicates.processing',
        'documents.duplicates.errors.title',
        'documents.duplicates.errors.generic',
        'documents.duplicates.errors.already_resolved',
        'documents.duplicates.errors.resolve_with_trashed_document',

        // preview
        'preview.map.lat_lng_location_hint',
        'preview.map.value_label',
    ],

];
