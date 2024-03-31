<?php

include_once '../../init.php';

use Sys\framework\Request;

// Check if request comes from within the application
if (!isAjax()) error_page('403');

function getUserByID(Request $request)
{
    $data = db()->table('users')
        ->select('`users`.`id`, `users`.`name`, `users`.`email`, `users`.`user_preferred_name`, `users`.`user_dob`, `users`.`username`, `users`.`password`, `users`.`user_status`')
        ->where('id', request('id'))
        ->with('profile', 'user_profile', 'user_id', 'id', function ($db) {
            $db->select('id,user_id,role_id,is_main,profile_status')
                ->withOne('roles', 'master_roles', 'id', 'role_id', function ($db) {
                    $db->select('id,role_name,role_status');
                });
        })->withOne('profile_avatar', 'entity_files', 'entity_id', 'id', function ($db) {
            $db->select('id,files_path,files_disk_storage,files_path_is_url,files_description,entity_file_type')
                ->where('entity_type', 'User_model')
                ->where('entity_file_type', 'PROFILE_PHOTO');
        })->withOne('profile_header', 'entity_files', 'entity_id', 'id', function ($db) {
            $db->select('id,files_path,files_disk_storage,files_path_is_url,files_description,entity_file_type')
                ->where('entity_type', 'User_model')
                ->where('entity_file_type', 'PROFILE_HEADER_PHOTO');
        })
        ->fetch();

    json($data);
}

function createUser(Request $request)
{
    $data = [
        'name' => request('name'),
        'user_preferred_name' => request('user_preferred_name'),
        'email' => request('email'),
        'user_gender' => request('user_gender'),
        'user_dob' => request('user_dob'),
        'username' => request('email'),
        // 'password' => request('password'),
        'user_status' => request('user_status')
    ];

    $validate = validate($data, _rulesDirectory('insert'), _rulesCustomErrorMessage()); // with custom error message
    $result = $validate['result'] ? db()->insert('users', $data) : $validate['error'];

    json($result);
}

function updateUser(Request $request)
{
    $data = [
        'id' => request('id'),
        'name' => request('name'),
        'user_preferred_name' => request('user_preferred_name'),
        'email' => request('email'),
        'user_gender' => request('user_gender'),
        'user_dob' => request('user_dob'),
        'username' => request('email'),
        // 'password' => request('password'),
        'user_status' => request('user_status')
    ];

    $validate = validate($data, _rulesDirectory('update')); // without custom error message
    $result = $validate['result'] ? db()->update('users', $data, ['id' => request('id')]) : $validate['error'];

    json($result);
}

function _rulesDirectory($type = 'insert')
{
    $rules = [
        'name' => 'required|string|maxLength:255',
        'user_preferred_name' => 'required|string|maxLength:20',
        'email' => 'required|email|minLength:5|maxLength:255',
        'user_gender' => 'integer|maxLength:1',
        'user_dob' => 'required|date',
        'username' => 'required|string|maxLength:255',
        // 'password' => 'required|string|maxLength:255',
        'user_status' => 'required|integer'
    ];

    if ($type == 'update')
        $rules['id'] = 'required|integer';

    return $rules;
}

function _rulesCustomErrorMessage() {
    return [
        'user_gender' => [
            'fields' => 'Jantina', // This will replace the key from user_gender to Jantina
            'integer' => 'Medan Jantina ini memerlukan data berbentuk nombor',
        ]
    ];
}