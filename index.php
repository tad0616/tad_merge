<?php
use Xmf\Request;
use XoopsModules\Tadtools\SweetAlert;
use XoopsModules\Tadtools\TadDataCenter;
use XoopsModules\Tadtools\TadUpFiles;
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
if (!Utility::power_chk('tad_merge', '1', $xoopsModule->mid())) {
    redirect_header('readme.php', 3, _MD_TADMERGE_NO_PERMISSION);
}
$uid = $xoopsUser->uid();
$TadUpFiles = new TadUpFiles('tad_merge', "/{$uid}");
$TadDataCenter = new TadDataCenter('tad_merge');

/*-----------功能函數區----------*/

//
function upload_form()
{
    global $uid, $TadUpFiles, $xoopsTpl, $TadDataCenter;

    $SweetAlert = new SweetAlert();
    $SweetAlert->render("del_file", "index.php?op=del_file&files_sn=", 'files_sn');

    // 列出目前用戶的上傳資料
    $TadUpFiles->set_col('data', $uid);
    $data_files = $TadUpFiles->get_file();
    $file_count = [];
    foreach ($data_files as $data_file_sn => $file) {
        // 取得用戶的資料陣列
        $data_file_arr[$data_file_sn] = $file['show_file_name'] . " ({$file['upload_date']})";

        $TadDataCenter->set_col('data_file_sn', $data_file_sn);
        $temp_file_sn_arr = $TadDataCenter->getData('temp_file_sn');
        $temp_files = [];
        foreach ($temp_file_sn_arr['temp_file_sn'] as $sort => $temp_file_sn) {
            $f = $TadUpFiles->get_file($temp_file_sn);
            $down_dir = XOOPS_ROOT_PATH . "/uploads/tad_merge/$uid/down_{$data_file_sn}/{$temp_file_sn}";
            $fi = new FilesystemIterator($down_dir, FilesystemIterator::SKIP_DOTS);
            $f[$temp_file_sn]['count'] = iterator_count($fi);
            $temp_files[] = $f;

        }
        $file['temp_files'] = $temp_files;

        $my_data_files[$data_file_sn] = $file;
    }
    // Utility::dd($my_data_files);
    // 取得目前用戶的樣板陣列
    $TadUpFiles->set_col('temp', $uid);
    $temp_files = $TadUpFiles->get_file();
    $temp_file_arr = [];
    foreach ($temp_files as $files_sn => $file) {
        $temp_file_arr[$files_sn] = $file['show_file_name'] . " ({$file['upload_date']})";
    }
    $xoopsTpl->assign('my_data_files', $my_data_files);
    $xoopsTpl->assign('data_file_arr', $data_file_arr);
    $xoopsTpl->assign('temp_file_arr', $temp_file_arr);
    $xoopsTpl->assign('file_count', $file_count);
}

//
function upload($data_sn = '', $temp_sn = '')
{
    global $uid, $TadUpFiles, $TadDataCenter;
    if ($_FILES['xlsx']['name']) {
        $TadUpFiles->set_var('tag', 'data');
        $TadUpFiles->set_col('data', $uid);
        $data_file_sn = $TadUpFiles->upload_one_file($_FILES['xlsx']['name'], $_FILES['xlsx']['tmp_name'], $_FILES['xlsx']['type'], $_FILES['xlsx']['size'], null, null, null, null, true, false, 'xlsx');
    } elseif (!empty($data_sn)) {
        $data_file_sn = $data_sn;
    }

    $down_dir = XOOPS_ROOT_PATH . "/uploads/tad_merge/$uid/down_{$data_file_sn}";
    Utility::mk_dir(dirname($down_dir));
    Utility::mk_dir($down_dir);

    if ($_FILES['odt']['name']) {
        $TadUpFiles->set_var('tag', 'temp');
        $TadUpFiles->set_col('temp', $uid);
        $temp_file_sn = $TadUpFiles->upload_one_file($_FILES['odt']['name'], $_FILES['odt']['tmp_name'], $_FILES['odt']['type'], $_FILES['odt']['size'], null, null, null, null, true, false, 'odt');
    } elseif (!empty($temp_sn)) {
        $temp_file_sn = $temp_sn;
    }
    $down_dir = XOOPS_ROOT_PATH . "/uploads/tad_merge/$uid/down_{$data_file_sn}/{$temp_file_sn}";
    Utility::mk_dir($down_dir);
    $temp_dir = XOOPS_ROOT_PATH . "/uploads/tad_merge/$uid/temp_{$temp_file_sn}";
    Utility::mk_dir($temp_dir);

    $TadDataCenter->set_col('data_file_sn', $data_file_sn);
    $TadDataCenter->saveCustomData(['temp_file_sn' => [$temp_file_sn]], 'append');
}

// 合併
function merge($data_sn, $temp_sn)
{
    global $uid, $TadUpFiles, $TadDataCenter;

    $temp_file = $TadUpFiles->get_file($temp_sn);
    require_once __DIR__ . '/class/dunzip2/dUnzip2.inc.php';
    require_once __DIR__ . '/class/dunzip2/dZip.inc.php';
    $zip = new dUnzip2($temp_file[$temp_sn]['physical_file_path']);
    $zip->getList();

    $down_dir = XOOPS_ROOT_PATH . "/uploads/tad_merge/$uid/down_{$data_sn}/{$temp_sn}";
    Utility::delete_directory($down_dir);
    Utility::mk_dir(dirname($down_dir));
    Utility::mk_dir($down_dir);

    $temp_dir = XOOPS_ROOT_PATH . "/uploads/tad_merge/$uid/temp_{$temp_sn}";
    Utility::delete_directory($temp_dir);
    Utility::mk_dir(dirname($temp_dir));
    Utility::mk_dir($temp_dir);
    $zip->unzipAll($temp_dir);

    $temp_xml = file_get_contents("{$temp_dir}/content.xml");

    $data_file = $TadUpFiles->get_file($data_sn);

    require_once XOOPS_ROOT_PATH . '/modules/tadtools/vendor/phpoffice/phpexcel/Classes/PHPExcel/IOFactory.php';
    $reader = PHPExcel_IOFactory::createReader('Excel2007');
    $PHPExcel = $reader->load($data_file[$data_sn]['physical_file_path']); // 檔案名稱
    $sheet = $PHPExcel->getSheet(0); // 讀取第一個工作表(編號從 0 開始)
    $maxCell = $PHPExcel->getActiveSheet()->getHighestRowAndColumn(); // 取得總列數
    $highestRow = $maxCell['row'];
    $zero_count = strlen($highestRow);
    $columnNum = letters_to_num($maxCell['column']);

    // 讀出標題列，以便取代
    for ($column = 0; $column <= $columnNum; $column++) {
        $var[$column] = '{' . trim($sheet->getCellByColumnAndRow($column, 1)->getCalculatedValue()) . '}';
    }

    for ($row = 2; $row <= $highestRow; $row++) {
        $val = [];
        for ($column = 0; $column <= $columnNum; $column++) {
            if (PHPExcel_Shared_Date::isDateTime($sheet->getCellByColumnAndRow($column, $row))) {
                $val[$column] = PHPExcel_Shared_Date::ExcelToPHPObject($sheet->getCellByColumnAndRow($column, $row)->getValue())->format('Y-m-d');
            } else {
                $val[$column] = $sheet->getCellByColumnAndRow($column, $row)->getCalculatedValue();
            }
        }
        $new_xml = str_replace($var, $val, $temp_xml);
        file_put_contents("{$temp_dir}/content.xml", $new_xml);
        mk_odt($temp_dir, $down_dir, $row, $temp_file[$temp_sn]['show_file_name'], $zero_count);
    }
    redirect_header('index.php', 3, _MD_TADMERGE_MERGER_COMPLETED);
}

//下載
function mk_odt($from = "", $to = "", $i = "", $filename = '', $zero_count = 3)
{
    global $xoopsDB, $xoopsModuleConfig;

    // $msg = shell_exec("zip -r -j {$to}/{$i}.zip {$from}");

    include_once 'class/pclzip.lib.php';
    Utility::mk_dir($to);
    setlocale(LC_ALL, 'zh_TW.utf8');
    $f = pathinfo($filename);
    $i = sprintf("%'.0{$zero_count}d", $i);
    $zipfile = new PclZip("{$to}/{$f['filename']}_{$i}.{$f['extension']}");
    $zipfile->create($from, PCLZIP_OPT_REMOVE_PATH, $from);
}

//下載
function download_all($data_sn, $temp_sn)
{
    global $xoopsDB, $uid, $TadUpFiles;

    $temp_file = $TadUpFiles->get_file($temp_sn);
    setlocale(LC_ALL, 'zh_TW.utf8');
    $f = pathinfo($temp_file[$temp_sn]['show_file_name']);
    $down_dir = XOOPS_ROOT_PATH . "/uploads/tad_merge/$uid/down_{$data_sn}/{$temp_sn}";
    $down_file = XOOPS_ROOT_PATH . "/uploads/tad_merge/$uid/" . _MD_TADMERGE_MERGED . "_{$f['filename']}.zip";
    $down_url = XOOPS_URL . "/uploads/tad_merge/$uid/" . _MD_TADMERGE_MERGED . "_{$f['filename']}.zip";

    include_once 'class/pclzip.lib.php';
    $zipfile = new PclZip($down_file);
    $zipfile->create($down_dir, PCLZIP_OPT_REMOVE_PATH, $down_dir);
    header("location:$down_url");
    exit;
}

function letters_to_num($letters)
{
    $num = 0;
    $arr = array_reverse(str_split($letters));

    for ($i = 0; $i < count($arr); $i++) {
        $num += (ord(strtolower($arr[$i])) - 96) * (pow(26, $i));
    }
    return $num;
}

function del_file($files_sn)
{
    global $TadUpFiles, $xoopsDB, $uid;

    $file = $TadUpFiles->get_file($files_sn);
    if ($file['tag'] == "data") {
        $down_dir = XOOPS_ROOT_PATH . "/uploads/tad_merge/$uid/down_{$files_sn}";
        Utility::delete_directory($down_dir);
        $sql = "delete from `" . $xoopsDB->prefix('tad_merge_data_center') . "`
        where `col_name`= 'data_file_sn' and `col_sn`= '$files_sn'";
    } else {
        $temp_dir = XOOPS_ROOT_PATH . "/uploads/tad_merge/$uid/temp_{$files_sn}";
        Utility::delete_directory($temp_dir);
        $sql = "delete from `" . $xoopsDB->prefix('tad_merge_data_center') . "`
        where `data_name`= 'temp_file_sn' and `data_value`= '$files_sn'";
    }
    $xoopsDB->queryF($sql) or Utility::web_error($sql, __FILE__, __LINE__);
    $TadUpFiles->del_files($files_sn);
}

function list_file($data_sn, $temp_sn)
{
    global $TadUpFiles, $xoopsDB, $uid;
    $down_dir = XOOPS_ROOT_PATH . "/uploads/tad_merge/$uid/down_{$data_sn}/$temp_sn";
    if (is_dir($down_dir)) {
        if ($dh = opendir($down_dir)) {
            while (($file = readdir($dh)) !== false) {
                if (substr($file, 0, 1) != '.') {
                    echo "<a href='index.php?op=download&data_sn=$data_sn&temp_sn=$temp_sn&file={$file}'>$file</a><br>";
                }

            }
            closedir($dh);
        }
    }
    exit;
}

function download($data_sn, $temp_sn, $file)
{
    global $TadUpFiles, $xoopsDB, $uid;

    $down_dir = XOOPS_ROOT_PATH . "/uploads/tad_merge/$uid/down_{$data_sn}/$temp_sn";

    $os_charset = (PATH_SEPARATOR === ':') ? 'UTF-8' : 'Big5';

    if (function_exists('mb_http_output')) {
        mb_http_output('pass');
    }

    if ($os_charset != _CHARSET) {
        //若網站和主機編碼不同，則將 $file_display (真實檔名) 轉為主機編碼，以便等一下建立檔案
        $file = iconv(_CHARSET, $os_charset, $file);
    }

    $file_dir = XOOPS_ROOT_PATH . "/uploads/tad_merge/$uid/down_{$data_sn}/$temp_sn/$file";
    $file_url = XOOPS_URL . "/uploads/tad_merge/$uid/down_{$data_sn}/$temp_sn/$file";

    header('Expires: 0');
    header('Content-Type: ' . $mimetype);
    //header('Content-Type: application/octet-stream');
    if (preg_match("/MSIE ([0-9]\.[0-9]{1,2})/", $_SERVER['HTTP_USER_AGENT'])) {
        header('Content-Disposition: inline; filename="' . $file . '"');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
    } else {
        header('Content-Disposition: attachment; filename="' . $file . '"');
        header('Pragma: no-cache');
    }
    //header("Content-Type: application/force-download");
    header('Content-Transfer-Encoding: binary');
    header('Content-Length: ' . filesize($file_dir));

    ob_clean();
    $handle = fopen($file_dir, 'rb');

    set_time_limit(0);
    while (!feof($handle)) {
        echo fread($handle, 4096);
        flush();
    }
    fclose($handle);

    die;

}

/*-----------變數過濾----------*/
$op = Request::getString('op');
$files_sn = Request::getInt('files_sn');
$data_sn = Request::getInt('data_sn');
$temp_sn = Request::getInt('temp_sn');
$file = Request::getString('file');

/*-----------執行動作判斷區----------*/
switch ($op) {
    case 'merge':
        merge($data_sn, $temp_sn);
        exit;

    case 'upload':
        upload($data_sn, $temp_sn);
        header("location:index.php");
        exit;

    case 'del_file':
        del_file($files_sn);
        header("location:index.php");
        exit;

    case 'download_all':
        download_all($data_sn, $temp_sn);
        exit;

    case 'download':
        download($data_sn, $temp_sn, $file);
        exit;

    case 'list_file':
        list_file($data_sn, $temp_sn);
        exit;

    default:
        upload_form();
        $op = 'upload_form';
        break;
}

/*-----------秀出結果區--------------*/
$xoopsTpl->assign('toolbar', Utility::toolbar_bootstrap($interface_menu));
$xoopsTpl->assign('now_op', $op);
$xoTheme->addStylesheet(XOOPS_URL . '/modules/tad_merge/css/module.css');
require_once XOOPS_ROOT_PATH . '/footer.php';
