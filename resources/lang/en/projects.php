<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Projects related Language Lines
	|--------------------------------------------------------------------------
	|
	|
	*/

	'page_title' => 'Projects',
	'page_title_with_name' => 'Project :name',


	'new_button' => 'New Project',
	
	'create_page_title' => 'Create New Project',
	'edit_page_title' => 'Edit Project :name',
	
	'edit_button' => 'Edit',
	'delete_button' => 'Delete',

	'labels' => array(
		'name' => 'Project Name',
		'description' => 'Project Description',
		
		'users' => 'Users',
		'users_hint' => 'Select the user to include into the Project',
		
		
		'create_submit' => 'Create Project',
		'edit_submit' => 'Save Project',
		'cancel' => 'Cancel',
	),
	
	'empty_selection' => 'Select a project to see the details',
	'empty_projects' => 'No projects. <a href=":url">Create</a> a new project',
	
	'errors' => array(

		'exception' => 'The project cannot be created. (:exception)',
		
		'prevent_edit_description' => 'The Project collection cannot be edited from here, please goto <a href=":link">Projects > Edit :name</a> to make the edits.',
		
		'prevent_delete_description' => 'The Project collection cannot be deleted.'
	),
	
	'project_created' => 'Project :name has been created',
	
	'project_updated' => 'Project :name has been updated',
	

];
