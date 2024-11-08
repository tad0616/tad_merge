<?php
//判斷是否對該模組有管理權限
if (!isset($_SESSION['tad_merge_adm'])) {
    $_SESSION['tad_merge_adm'] = isset($xoopsUser) && \is_object($xoopsUser) ? $xoopsUser->isAdmin() : false;
}

$interface_menu[_MD_TADMERGE_INDEX] = "index.php";
$interface_icon[_MD_TADMERGE_INDEX] = "fa-compress";

$interface_menu[_MD_TADMERGE_MERGER_README] = "readme.php";
$interface_icon[_MD_TADMERGE_MERGER_README] = "fa-book";
