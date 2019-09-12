<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Shared page Language Lines
    |--------------------------------------------------------------------------
    |
    */

    'page_title' => 'Shares',
    
    'share_btn' => 'Share',

    'share_panel_title' => 'Share :num element|Share :num elements',
    
    'share_panel_title_alt' => 'Share ":what"|Share ":what" and :count others',

    'share_created_msg' => ':num share created|:num shares created',

    'with_label' => 'Share with',

    'what_label' => 'Here is what you are sharing',

    'empty_with_me_message' => 'No one has shared anything with you :(',

    'empty_by_me_message' => 'You have not shared any document or collection yet.',

    'shared_by_me_title' => 'Shared by Me',
    'shared_by_me_count' => ':num element shared|:num elements shared',

    'shared_with_me_title' => 'Shared by Others',
    
    'shared_with_label' => 'Shared by you with',
    'shared_by_label' => 'Shared by',
    
    'bulk_destroy' => 'Shares deleted|Some shares cannot be deleted<br/>:errors',
    'removed' => 'Access removed',
    'remove_error' => 'The access cannot be removed. :error',
    'unshare' => 'Unshare',
    'unsharing' => 'Unsharing...',
    'remove' => 'Remove',
    'removing' => 'Removing...',
    
    'share_link_section' => 'Share link',
    'download_link_copy' => 'Copy download link to the clipboard',
    'document_link_copy' => 'Copy link',
    'preview_link_copy' => 'Copy preview link to the clipboard',
    'document_link_copy_multiple' => 'Copy links',
    'send_link' => 'Send link',
    'send_link_multiple' => 'Send links',
    
    'link_copied_to_clipboard' => 'The link has been copied to your clipboard. You can use CTRL+V to paste the link somewhere else.',

    'shared_on' => 'Shared on',
    
    'dialog' => [
        'title' => 'Share',
        'subtitle_single' => ':what', // only one element to share
        'subtitle_multiple' => ':what and :count other|:what and :count others', // X and 1 other|X and 2 others
        'share_created' => 'Share Created',
        'collection_shared' => 'Collection shared',
        'collection_shared_text' => 'The Collection has been shared',
        'document_shared' => 'File shared',
        'document_shared_text' => 'File has been shared',
        'multiple_selection_not_supported' => 'Multiple selection is not supported yet. We are working on it.',
        'publish_multiple_selection_not_supported' => 'Not available for multiple selections.',
        'publish_collection_not_supported' => 'Publishing a collection is not supported yet. We are working on it. Meanwhile you can select several files and use the "Publish" button to make them public.',

        'section_access_title' => 'Who has Access',
        'section_linkshare_title' => 'Link sharing',
        'section_linkshare_title_alternate' => 'Link to share',
        'section_publish_title' => 'Publish',

        'linkshare_hint' => 'Only registered users who already have access to the document can open it.',
        'linkshare_multiple_selection_hint' => 'Only registered users who already have access to the document can open it. To generate a public link, please select a document',
        'linkshare_members_only' => 'Only authenticated users listed below can access',
        'linkshare_public' => 'Anyone with the link can access. No login required.',

        'published' => 'Published on :network',
        'not_published' => 'Not published on :network',
        'publishing' => 'Publishing the document...',
        'publishing_failed' => 'Publishing failed.',
        'unpublishing' => 'Making private...',
        'publish_collection' => 'All documents in the collection will be affected.',
        'publish_already_in_progress' => 'Publication already in progress.',

        'document_is_shared' => 'File accessible by',
        'collection_is_shared' => 'Collection is accessible by',
        'users_already_has_access' => ':num user|:num users',
        'users_already_has_access_alternate' => '{0}Only you|{1}:num user|[2,*]:num users',
        'users_already_has_access_with_public_link' => '{0}Only you and whoever has the public link|{1}Only you and whoever has the public link|[2,*]:num users and whoever has the public link',
        'document_already_accessible_by_all_users' => 'File already accessible by all K-Box users.',
        'collection_already_accessible_by_all_users' => 'Collection is already accessible by all K-Box users.',

        'add_users' => 'Add users',
        'select_users' => 'Enter username...',

        'access_by_direct_share' => 'Direct access',
        'access_by_project_membership' => 'Project ":project"',
        'access_by_project_membership_hint' => 'Member of ":project"',
    ],
    'publiclinks' => [
        'public_link' => 'Public Link',
        'already_exist' => 'Public link for :name already exists.',
        'delete_forbidden_not_your' => 'Only the creator of the public link can delete it.',
        'edit_forbidden_not_your' => 'Only the creator of the public link can change it.',
    ],
];
