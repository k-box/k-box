<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Projects related Language Lines
	|--------------------------------------------------------------------------
	|
	|
	*/

	'page_title' => 'Проекты',
	'page_title_with_name' => 'Проект :name',


	'new_button' => 'Новый проект',
	
	'create_page_title' => 'Создать новый проект',
	'edit_page_title' => 'Изменить :name',
	
	'edit_button' => 'Изменить',
	'delete_button' => 'Удалить',
	'close_edit_button' => 'Выйти',

	'labels' => array(
		'name' => 'Название',
		'description' => 'Описание',
		'project_details' => 'Детали Проекта',
		
		'users' => 'Пользователи',
		'add_users' => 'Добавить пользователей',
		'add_users_button' => 'Добавить',
		'users_placeholder' => 'Выбрать',
		'users_hint' => 'Начните печатать или выберите пользователя из выпадающего меню',
		
		
		'create_submit' => 'Создать',
		'edit_submit' => 'Сохранить',
		'cancel' => 'Отменить',

		'users_in_project' => 'Добавленных пользователей (:count)',
	),

	'remove_user_hint' => 'Снять пользователя с Проекта',

	'removing_wait_title' => 'Снятие...',
	'removing_wait_text' => 'Снимаю пользователя...',

	'no_user_available' => 'Невозможно добавить пользователей. Скорее всего вы добавили всех в списке K-DMS.',
	
	'no_members' => 'Нет пользователей. Начните добавлять.',
	
	'empty_selection' => 'Выберите проект для просмотра описания',
	'empty_projects' => 'Нет проектов. <a href=":url">Создать</a> новый проект',
	
	'errors' => array(

		'exception' => 'Невозможно создать проект. (:exception)',
		
		'prevent_edit_description' => 'Изменение проекта возможно в <a href=":link">Проекты > Изменить :name</a>',
		
		'prevent_delete_description' => 'Эта проектная коллекция не может быть удалена.'
	),
	
	'project_created' => 'Проект :name создан',
	
	'project_updated' => 'Проект :name обновлен',
	

];
