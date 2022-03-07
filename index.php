<?php
/**
 * @package Lanzou
 * @author Yuuz12
 * @version 1.2.1
 * @link https://yuuz12.top
 */
include "../count.php";
include "function.php";
header('Access-Control-Allow-Origin:*');
header('Content-type: application/json');
error_reporting(0);

$url = $_GET['url'];
$pwd = $_GET['pwd'];
$down = $_GET['down'];
$error = curl($url);

if (strpos($error,'文件取消分享了') !== false) {
    $Json = array(
    "code" => 201, 
    "msg" => '文件已被取消分享了噢！',
);
$Json = json_encode($Json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
echo stripslashes($Json);
return $Json;
}elseif ($url != null) {
    if ($pwd == NULL) {
        $b = 'com/';
        $c = '/';
        $id = GetBetween($url, $b, $c);
        $d = 'https://www.lanzoui.com/tp/' . $id;
        $lanzouo = curl($d);
        echo $lanzouo;
        
        if (strpos($lanzouo,'输入密码') !== false) {
            $Json = array(
            "code" => 202, 
            "msg" => '输入访问密码再试试吧！',
            );
        $Json = json_encode($Json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        echo stripslashes($Json);
        return $Json;
        } else {
        preg_match_all('/<div class=\"md\">(.*?)<span class=\"mtt\">/', $lanzouo, $name);
        preg_match_all('/时间:<\\/span>(.*?)<span class=\\"mt2\\">/', $lanzouo, $time);
        preg_match_all('/发布者:<\\/span>(.*?)<span class=\\"mt2\\">/', $lanzouo, $author);
        preg_match('/submit.href = \'(.*?)\'/', $lanzou, $down1);
        preg_match('/loaddown = \'(.*?)\';/', $lanzou, $down2);
        preg_match('/domianload = \'(.*?)\';/', $lanzou, $down3);
        preg_match_all('/<div class=\\"md\\">(.*?)<span class=\\"mtt\\">\\((.*?)\\)<\\/span><\\/div>/', $lanzouo, $size);
            if (!empty($down2)){ // loaddown是否为空
                $download = getRedirect($down1[1] . $down2[1]);
            }
            else{ // 如果为空就选择第二个
                $download = getRedirect($down1[1] . $down3[1]);
            }
            if ($down == true) {
                header("Location:" . $download);
            } else {
                $Json = array(
                "code" => 200, 
                "data" => array(
                    "name" => $name[1][0], 
                    "author" => $author[1][0], 
                    "time" => $time[1][0], 
                    "size" => $size[2][0], 
                    "url" => $download,
                    )
            );
            $Json = json_encode($Json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            echo stripslashes($Json);
            return $Json;
            }
        }
    }
    $b = 'com/';
    $c = '/';
    $id = GetBetween($url, $b, $c);
    $d = 'https://www.lanzoui.com/tp/' . $id;
    $lanzouo = curl($d);
    preg_match_all('/<div class=\"md\">(.*?)<span class=\"mtt\">/', $lanzouo, $name);
    preg_match_all('/时间:<\\/span>(.*?)<span class=\\"mt2\\">/', $lanzouo, $time);
    preg_match_all('/发布者:<\\/span>(.*?)<span class=\\"mt2\\">/', $lanzouo, $author);
    preg_match_all('/<div class=\\"md\\">(.*?)<span class=\\"mtt\\">\\((.*?)\\)<\\/span><\\/div>/', $lanzouo, $size);
    preg_match_all('/sign\':\'(.*?)\'/', $lanzouo, $sign);
    $post_data = array('action' => 'downprocess', 'sign' => $sign[1][0], 'p' => $pwd);
    $pwdurl = send_post('https://wwa.lanzoui.com/ajaxm.php', $post_data);
    if(strpos($pwdurl,'"zt":0') !== false) {
        $Json = array(
        "code" => 202, 
        "msg" => '访问密码错辣！',
        );
    $Json = json_encode($Json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    echo stripslashes($Json);
    return $Json;
    } else {
        $obj = json_decode($pwdurl, true);
        $download = getRedirect($obj['dom'] . '/file/' . $obj['url']);
        $Json = array(
            "code" => 200, 
            "data" => array(
                "name" => $name[1][0], 
                "author" => $author[1][0], 
                "time" => $time[1][0], 
                "size" => $size[2][0], 
                "url" => $download
            )
        );
        if ($down == true) {
            header("Location:" . $download);
        } else {
            $Json = json_encode($Json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            echo stripslashes($Json);
            return $Json;
        }
    }
} else {
    echo '请输入正确的蓝奏云分享的地址再试试！';
}