<?php
header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json");
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include_once('../includes/crud.php');

$db = new Database();
$db->connect();
include_once('../includes/functions.php');
$fn = new functions;
$fn->monitorApi('appupdate');

$date = date('Y-m-d');
$datetime = date('Y-m-d H:i:s');
$old_device_id = (isset($_POST['device_id']) && $_POST['device_id'] != "") ? $db->escapeString($_POST['device_id']) : "";
$user_id = (isset($_POST['user_id']) && $_POST['user_id'] != "") ? $db->escapeString($_POST['user_id']) : "";
$fcm_id = (isset($_POST['fcm_id']) && $_POST['fcm_id'] != "") ? $db->escapeString($_POST['fcm_id']) : "";
$latitude = (isset($_POST['latitude']) && $_POST['latitude'] != "") ? $db->escapeString($_POST['latitude']) : "";
$longtitude = (isset($_POST['longtitude']) && $_POST['longtitude'] != "") ? $db->escapeString($_POST['longtitude']) : "";
$app_version = (isset($_POST['app_version']) && $_POST['app_version'] != "") ? $db->escapeString($_POST['app_version']) : 0;
$sql = "SELECT * FROM settings";
$db->sql($sql);
$set = $db->getResult();
$res = array();
if($user_id != ''){
    $sql = "SELECT code_generate_time,total_referrals,withdrawal,last_updated,device_id,datediff('$date', joined_date) AS history_days,datediff('$datetime', last_updated) AS days,code_generate,withdrawal_status,status,joined_date,today_codes,refer_balance,trial_expired,task_type,champion_task_eligible,trial_count,mcg_timer,security,ongoing_sa_balance,salary_advance_balance,sa_refer_count  FROM users WHERE id = $user_id ";
    $db->sql($sql);
    $res = $db->getResult();
    $history_days = $fn->get_leave($user_id);
    $res[0]['history_days'] = $history_days;
    $device_id = $res[0]['device_id'];
    $today_codes = $res[0]['today_codes'];
    $task_type = $res[0]['task_type'];
    $code_generate_time = $res[0]['code_generate_time'];
    $res[0]['joined_date'] = $fn->get_joined_date($user_id);;

    

    $champion_task = $set[0]['champion_task'];
    

    $sql = "UPDATE `users` SET app_version = $app_version WHERE `id` = $user_id";
    $db->sql($sql);

    if($latitude != ''){
        $sql = "UPDATE `users` SET latitude = '$latitude',longtitude = '$longtitude' WHERE `id` = $user_id";
        $db->sql($sql);

    }


    


    if(!empty($fcm_id)){
        $sql = "UPDATE `users` SET  `fcm_id` = '$fcm_id' WHERE `id` = $user_id";
        $db->sql($sql);
    
    }
    if(isset($_POST['device_id']) && ($device_id != $old_device_id)){
        $sql = "UPDATE `users` SET  `status` = 2 WHERE `id` = $user_id";
        $db->sql($sql);

    }

    if(($task_type == 'champion' && $code_generate_time <= 5)  || ($history_days >= 7 && $today_codes > 500 && $code_generate_time <= 5)){
        $sql = "UPDATE `users` SET  `code_generate_time` = 5 WHERE `id` = $user_id";
        $db->sql($sql);

    }
    if($user_id == 13406){
        $sql = "SELECT *,1000 AS champion_search_count FROM settings";
        $db->sql($sql);
        $set = $db->getResult();

    }


}
$sql = "SELECT * FROM app_settings";
$db->sql($sql);
$appres = $db->getResult();
$response['success'] = true;
$response['message'] = "App Update listed Successfully";
$response['data'] = $appres;
$response['settings'] = $set;
$response['user_details'] = $res;
print_r(json_encode($response));

?>