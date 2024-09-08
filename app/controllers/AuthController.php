<?php

include_once '../../init.php';

use Sys\framework\Request;

// Check if request comes from within the application
if (!isAjax()) error_page('403');

use Sys\constants\GeneralErrorMessage;
use Sys\constants\LoginType;
use Sys\constants\GeneralStatus;

function authorize(Request $request)
{
    $username = request('username');
    $enteredPassword = request('password');

    $userData = db()->table('users')
        ->select('`users`.`id`, `users`.`name`, `users`.`email`, `users`.`user_preferred_name`, `users`.`password`, `users`.`user_status`')
        ->where('email', $username)
        ->orWhere('username', $username)
        ->fetch();

    $response = GeneralErrorMessage::LIST['AUTH']['DEFAULT']; // set default

    if (hasData($userData)) {
        $userPassword = $userData['password'];
        if (password_verify($enteredPassword, $userPassword)) {
            unset($userData['password']); // remove column password
            $response = loginSessionStart($userData, LoginType::CREDENTIAL);
        }
    }

    json($response);
}

function socialite(Request $request)
{
    $userData = db()->table('users')
        ->select('`users`.`id`, `users`.`name`, `users`.`email`, `users`.`user_preferred_name`, `users`.`user_status`')
        ->where('email', request('email'))
        ->fetch();

    $response = hasData($userData) ? loginSessionStart($userData, LoginType::SOCIALITE) : GeneralErrorMessage::LIST['AUTH']['EMAIL_NOT_VALID'];

    json($response);
}

function loginSessionStart($userData, $loginType = LoginType::CREDENTIAL)
{
    global $redirectAuth; // Need to check

    if ($userData['user_status'] == GeneralStatus::ACTIVE) {

        $users = db()->table('user_profile')
            ->select('id,user_id,role_id,is_main,profile_status')
            ->where('user_id', $userData['id'])
            ->where('is_main', 1)
            ->withOne('roles', 'master_roles', 'id', 'role_id', function () {
                db()->select('id,role_name,role_status')->where('role_status', 1)
                    ->with('permission', 'system_permission', 'role_id', 'id', function () {
                        db()->select('id,role_id,abilities_id,forbidden')->where('forbidden', 0)
                            ->withOne('abilities', 'system_abilities', 'id', 'abilities_id', function () {
                                db()->select('id,title');
                            });
                    });
            })
            ->withOne('profile_avatar', 'entity_files', 'entity_id', 'id', function () {
                db()->select('id,files_path,files_disk_storage,files_path_is_url,files_description,entity_file_type')
                    ->where('entity_type', 'User_model')
                    ->where('entity_file_type', 'PROFILE_PHOTO');
            })
            ->fetch();

        if (hasData($users)) {

            if (hasData($users, 'profile_status', true) == 1) {

                $permission = [];
                if (hasData($users, 'roles.permission', true)) {
                    foreach (hasData($users, 'roles.permission', true) as $ability) {
                        $permission[] = $ability['abilities']['title'];
                    }
                }
                
                startSession([
                    'userID'  => $userData['id'],
                    'userFullName'  => purify($userData['name']),
                    'userNickName'  => purify($userData['user_preferred_name']),
                    'userEmail'  => purify($userData['email']),
                    'roleID'  => hasData($users, 'role_id', true),
                    'roleName'  => hasData($users, 'roles.role_name', true),
                    'permission' => $permission,
                    'avatar' => isExistImage(hasData($users, 'profile_avatar.files_path', true), 'profile'),
                    'isLogIn' => TRUE
                ]);

                return ['code' => 200, 'message' => 'Login', 'redirectUrl' => url($redirectAuth ?? 'app/views/dashboard')];
            }
        }

        return GeneralErrorMessage::LIST['AUTH']['PROFILE'];
    } else  if ($userData['user_status'] == GeneralStatus::INACTIVE) {
        return GeneralErrorMessage::LIST['AUTH']['INACTIVE'];
    } else  if ($userData['user_status'] == GeneralStatus::UNVERIFIED) {
        return GeneralErrorMessage::LIST['AUTH']['VERIFY'];
    } else  if ($userData['user_status'] == GeneralStatus::DELETED) {
        return GeneralErrorMessage::LIST['AUTH']['DELETED'];
    } else {
        return GeneralErrorMessage::LIST['AUTH']['SUSPENDED'];
    }
}

function logout()
{
    session_destroy();
    json([
        'code' => 200,
        'message' => 'Logout',
        'redirectUrl' => url('app/views/auth-login'),
    ]);
}