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
require_once __DIR__ . '/header.php';
$GLOBALS['xoopsOption']['template_main'] = 'tad_merge_index.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';

/*-----------功能函數區----------*/

//
function readme()
{
}

/*-----------變數過濾----------*/
$op = Request::getString('op');
$files_sn = Request::getInt('files_sn');
$data_sn = Request::getInt('data_sn');
$temp_sn = Request::getInt('temp_sn');

/*-----------執行動作判斷區----------*/
switch ($op) {

    default:
        readme();
        $op = 'readme';
        break;
}

/*-----------秀出結果區--------------*/
$xoopsTpl->assign('toolbar', Utility::toolbar_bootstrap($interface_menu));
$xoopsTpl->assign('now_op', $op);
$xoTheme->addStylesheet(XOOPS_URL . '/modules/tad_merge/css/module.css');
require_once XOOPS_ROOT_PATH . '/footer.php';
