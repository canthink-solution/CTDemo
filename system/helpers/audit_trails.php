<?php

function trailPreviousData($table, $pkValue, $pkColumn = NULL)
{
    if (empty($pkColumn)) {
        $pkColumn = db()->getPrimaryKeyColumn($table);
    }

    return db()->table($table)->where($pkColumn, $pkValue)->fetch();
}

function trail($status, $event, $table, $set = NULL, $previous_values = NULL)
{
    global $audit;

    $auditTable = $audit['audit_table'];
    $auditSession = $audit['session'];

    $config['audit_enable'] = filter_var($audit['audit_enable'], FILTER_VALIDATE_BOOLEAN);

    /*
    |--------------------------------------------------------------------------
    | Not Allowed table for auditing
    |--------------------------------------------------------------------------
    |
    | The following setting contains a list of the not allowed database tables for auditing.
    | You may add those tables that you don't want to perform audit.
    |
    */
    $config['not_allowed_tables'] = [$auditTable];

    /*
    |--------------------------------------------------------------------------
    | Enable Insert Event Track
    |--------------------------------------------------------------------------
    |
    | Set [TRUE/FALSE] to track insert event.
    |
    */
    $config['track_insert'] = filter_var($audit['track_insert'], FILTER_VALIDATE_BOOLEAN);

    /*
    |--------------------------------------------------------------------------
    | Enable Update Event Track
    |--------------------------------------------------------------------------
    |
    | Set [TRUE/FALSE] to track update event
    |
    */
    $config['track_update'] = filter_var($audit['track_update'], FILTER_VALIDATE_BOOLEAN);

    /*
    |--------------------------------------------------------------------------
    | Enable Delete Event Track
    |--------------------------------------------------------------------------
    |
    | Set [TRUE/FALSE] to track delete event
    |
    */
    $config['track_delete'] = filter_var($audit['track_delete'], FILTER_VALIDATE_BOOLEAN);

    //return without save resource
    if (!$status) return 1;  // event not performed
    if (!$config['audit_enable']) return 1; // trail not enabled
    if ($event === 'insert' && !$config['track_insert']) return 1; // insert tracking not enabled
    if ($event === 'update' && !$config['track_update']) return 1; // update tracking not enabled
    if ($event === 'delete' && !$config['track_delete']) return 1; // delete tracking not enabled
    if (in_array($table, $config['not_allowed_tables'])) return 1; // table tracking not allowed

    if ($event == 'update') {
        diff_on_update($previous_values, $set);
        //data has not been update
        if (empty($previous_values) && empty($set))
            return 1;
    }

    $old_value = null;
    if (!empty($previous_values)) $old_value = json_encode($previous_values);

    $new_value = json_encode($set); // For delete event it stores where condition

    db()->secureInput(false)->insert(
        $auditTable,
        array_merge(
            $auditSession,
            [
                'event' => $event,
                'table_name' => $table,
                'old_values' => $old_value,
                'new_values' => $new_value,
                'url' => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]",
                'ip_address' => get_client_ip(),
                'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                'created_at' => timestamp(),
            ]
        )
    );
}

function diff_on_update(&$old_value, &$new_value)
{
    $old = [];
    $new = [];
    foreach ($new_value as $key => $val) {
        if (isset($new_value[$key])) {
            if (isset($old_value[$key])) {
                if ($new_value[$key] != $old_value[$key]) {
                    $old[$key] = $old_value[$key];
                    $new[$key] = $new_value[$key];
                }
            } else {
                $old[$key] = '';
                $new[$key] = $new_value[$key];
            }
        }
    }

    $old_value = $old;
    $new_value = $new;
}

function get_client_ip()
{
    $ipaddress = '';

    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if (isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if (isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if (isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';

    return $ipaddress;
}
