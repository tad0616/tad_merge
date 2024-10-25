<?php
use Xmf\Request;
use XoopsModules\Tadtools\Utility;
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

/*-----------引入檔案區--------------*/
$GLOBALS['xoopsOption']['template_main'] = 'tad_merge_adm_main.tpl';
require_once __DIR__ . '/header.php';
$_SESSION['tad_merge_adm'] = true;

/*-----------功能函數區----------*/

include_once XOOPS_ROOT_PATH . "/Frameworks/art/functions.php";
include_once XOOPS_ROOT_PATH . "/Frameworks/art/functions.admin.php";
include_once XOOPS_ROOT_PATH . '/class/xoopsform/grouppermform.php';

//取得本模組編號
$module_id = $xoopsModule->mid();

//頁面標題
$perm_page_title = _MA_TADMERGE_TAD_MERGE_PERM_TITLE;

//取得分類編號及標題
//權限項目陣列（編號超級重要！設定後，以後切勿隨便亂改。）
$item_list = array(
    '1' => _MA_TADMERGE_TAD_MERGE_PERM_FUNC,
);

//權限名稱（請設定一個英文名稱，一般用模組名稱即可）
$perm_name = 'tad_merge';

//權限描述
$perm_desc = _MA_TADMERGE_TAD_MERGE_PERM_DESC;

//建立XOOPS權限表單
$formi = new \XoopsGroupPermForm($perm_page_title, $module_id, $perm_name, $perm_desc, null, false);

//將權限項目設進表單中
foreach ($item_list as $item_id => $item_name) {
    $formi->addItem($item_id, $item_name);
}

echo $formi->render();

/*-----------變數過濾----------*/
$op = Request::getString('op');

/*-----------執行動作判斷區----------*/
switch ($op) {

}

/*-----------秀出結果區--------------*/
$xoopsTpl->assign('now_op', $op);
$xoTheme->addStylesheet('/modules/tadtools/css/font-awesome/css/font-awesome.css');
if ($_SESSION['bootstrap'] == 4) {
    $xoTheme->addStylesheet('modules/tadtools/css/xoops_adm4.css');
} else {
    $xoTheme->addStylesheet('modules/tadtools/css/xoops_adm3.css');
}
require_once __DIR__ . '/footer.php';
