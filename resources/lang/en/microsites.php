<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Microsites related Language Lines
    |--------------------------------------------------------------------------
    */

    'page_title' => 'Project Microsites',
    'page_title_with_name' => 'Project Microsite for :project',
    
    'pages' => [
        'create' => 'Create Microsite for project ":project"',
        'edit' => 'Edit Microsite for project ":project"',
    ],
    
    
    'hints' => [
        'what' => 'A microsite is a public page for your project',
        'create_for_project' => 'Create a microsite for the project',
        'for_project' => 'Create a microsite for the project',
        'delete_microsite' => 'Remove the project microsite',
        'edit_microsite' => 'Change the microsite page content and settings',
        
        'site_title' => 'The following website name will be shown to users',
        'description' => 'A short description of the purpose of your microsite used by the search engines.',
        'slug' => 'Short, descriptive sequence of terms for the user-friendly version of the website URL path. This will help users to find and remember the website address. For example a project named "Climate Change" could have "climate-change" as the slug. The resulting url will be https://organization.com/projects/climate-change.',
        'logo' => 'The website logo must be of max size 280x80 pixels and hosted somewhere with a secure connection over HTTPS',
        'default_language' => 'The default language in which the website will be shown to its visitors, unless specified otherwise by their settings',

        'content' => 'Here you can specify the microsite single page textual content and the optional navigation menu. At now you can only specify content in English and Russian versions.',
        
        'page_title' => 'The title of the page. The default value is home',
        'page_slug' => 'The user friendly version of the page URL path. This will help users find and remember the website address',
        'page_content' => 'You can insert text, links and other text. Supported format refers to the <a href="https://daringfireball.net/projects/markdown/basics" target="_blank">Markdown syntax</a>. You can also insert links and embed elements from other websites. For example you can embed an RSS content feed  by putting this code on its own line <code>@rss:https://klinktest.wordpress.com/feed/</code>. Please note that embed content will be cached to prevent high resource usage with a caching period of 1 to 4 hours (depending on the service)',
    ],
    
    'actions' => [
        'create' => 'Create Microsite',
        'edit' => 'Edit Microsite',
        'save' => 'Save Microsite settings',
        'delete' => 'Delete Microsite',
        'delete_ask' => 'You are about to delete the microsite for ":title". Are you sure to delete it?',
        'view_site' => 'View Microsite',
        'publish' => 'Publish the Microsite',
        'view_project_documents' => 'Go to Project',
        'search' => 'Search K-Link...',
        'search_project' => 'Search :project...',
    ],
    
    'messages' => [
        'created' => 'The microsite ":title" has been created and it is reachable at <a href=":site_url" target="_blank">:slug</a>',
        'updated' => 'The microsite ":title" has been updated',
        'deleted' => 'The microsite ":title" has been deleted. The public microsite url will not be reachable anymore',
    ],
    
    'errors' => [
        'create' => 'There was a problem creating the microsite. :error',
        'create_no_project' => 'Please specify a Project. A Project has not been specified to enable the microsite creation.',
        'create_already_exists' => 'A microsite for the project ":project" already exists. You cannot have more than one microsite for each project.',
        'delete' => 'There was a problem deleting the microsite. :error',
        'update' => 'There was a problem updating the microsite. :error',
        'delete_forbidden' => 'You cannot delete the microsite ":title" because you are not a project manager of the project related to the microsite.',
        'forbidden' => 'You need to be a Project Administrator to interact with the microsites.',
    ],
    
    'labels' => [
        'microsite' => 'Microsite',
        'site_title' => 'Site name',
        'slug' => 'Site human friendly slug',
        'site_description' => 'Site description',
        'logo' => 'Website logo',
        'default_language' => 'Site default language',
        'cancel_and_back' => 'Cancel',
        'publishing_box' => '',
        'content' => 'Microsite Content',
        
        'content_en' => 'English content of the site',
        'content_ru' => 'Russian content of the site',
        
        'page_title' => 'Title of the page to be created',
        'page_slug' => 'Slug of the page',
        'page_content' => 'Content of the page',
    ],
];
