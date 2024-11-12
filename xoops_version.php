<?php
/**
 * Tad Merge module
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright  The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license    http://www.fsf.org/copyleft/gpl.html GNU public license
 * @package    Tad Merge
 * @since      2.5
 * @author     tad
 * @version    $Id $
 **/

$modversion = array();

//---模組基本資訊---//
$modversion['name'] = _MI_TADMERGE_NAME;
// $modversion['version'] = '1.2';
$modversion['version'] = $_SESSION['xoops_version'] >= 20511 ? '2.0.0-Stable' : '2.0';
$modversion['description'] = _MI_TADMERGE_DESC;
$modversion['author'] = _MI_TADMERGE_AUTHOR;
$modversion['credits'] = _MI_TADMERGE_CREDITS;
$modversion['help'] = 'page=help';
$modversion['license'] = 'GPL see LICENSE';
$modversion['image'] = "images/logo.png";
$modversion['dirname'] = basename(__DIR__);

//---模組狀態資訊---//
$modversion['release_date'] = '2024-11-18';
$modversion['module_website_url'] = 'https://tad0616.net';
$modversion['module_website_name'] = _MI_TADMERGE_AUTHOR_WEB;
$modversion['module_status'] = 'release';
$modversion['author_website_url'] = 'https://tad0616.net';
$modversion['author_website_name'] = _MI_TADMERGE_AUTHOR_WEB;
$modversion['min_php'] = '5.4';
$modversion['min_xoops'] = '2.5.10';

//---paypal資訊---//
$modversion['paypal'] = [
    'business' => 'tad0616@gmail.com',
    'item_name' => 'Donation : ' . _MI_TAD_WEB,
    'amount' => 0,
    'currency_code' => 'USD',
];

//---安裝設定---//
$modversion['onInstall'] = "include/onInstall.php";
$modversion['onUpdate'] = "include/onUpdate.php";
$modversion['onUninstall'] = "include/onUninstall.php";

//---資料表架構---//
$modversion['sqlfile']['mysql'] = "sql/mysql.sql";
$modversion['tables'] = [
    "tad_merge_data_center",
    "tad_merge_files_center",
];

//---後台使用系統選單---//
$modversion['system_menu'] = 1;

//---後台管理介面設定---//
$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = 'admin/main.php';
$modversion['adminmenu'] = 'admin/menu.php';

//---前台主選單設定---//
$modversion['hasMain'] = 1;

//---樣板設定---//
$modversion['templates'] = [
    ['file' => 'tad_merge_admin.tpl', 'description' => 'tad_merge_admin.tpl'],
    ['file' => 'tad_merge_index.tpl', 'description' => 'tad_merge_index.tpl'],
];
