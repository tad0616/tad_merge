<script type="text/javascript">
    $(document).ready(function(){
        $('.view').click(function(){
            var temp_sn=$(this).data('temp_sn');
            var data_sn=$(this).data('data_sn');
            console.log(data_sn+'-'+temp_sn);
            $('#display-' + data_sn + '-' + temp_sn).toggle(0, function() {
                $.post("index.php", {op:'list_file', data_sn: data_sn, temp_sn: temp_sn },
                function(data) {
                    $('#list-' + data_sn + '-' + temp_sn).html(data);
                });
            });
        });
    });
</script>

<form action="" method="post" enctype='multipart/form-data'>
    <div class="form-group row mb-3">
        <label class="col-sm-3 col-form-label text-right text-end"><{$smarty.const._MD_TADMERGE_MERGER_UPLOAD_XLSX}></label>
        <div class="col-sm-3">
            <input type="file" name="xlsx" id="xlsx" accept=".xlsx" class="form-control">
        </div>
        <{if $data_file_arr|default:false}>
                <label class="col-sm-2 col-form-label text-right text-end"><{$smarty.const._MD_TADMERGE_MERGER_SELECT_EXISTING_DATA_FILE}></label>
                <div class="col-sm-4">
                    <select name="data_sn" class="form-select">
                        <option value=""></option>
                        <{foreach from=$data_file_arr key=data_files_sn item=filename}>
                            <option value="<{$data_files_sn|default:''}>"><{$filename|default:''}></option>
                        <{/foreach}>
                    </select>
                </div>
        <{/if}>
    </div>
    <div class="form-group row mb-3">
        <label class="col-sm-3 col-form-label text-right text-end"><{$smarty.const._MD_TADMERGE_MERGER_UPLOAD_ODT}></label>
        <div class="col-sm-3">
            <input type="file" name="odt" id="odt" accept=".odt" class="form-control">
        </div>
        <{if $temp_file_arr|default:false}>
                <label class="col-sm-2 col-form-label text-right text-end"><{$smarty.const._MD_TADMERGE_MERGER_SELECT_EXISTING_TPL_FILE}></label>
                <div class="col-sm-4">
                    <select name="temp_sn" class="form-select">
                        <option value=""></option>
                        <{foreach from=$temp_file_arr key=temp_files_sn item=filename}>
                            <option value="<{$temp_files_sn|default:''}>"><{$filename|default:''}></option>
                        <{/foreach}>
                    </select>
                </div>
        <{/if}>
    </div>
    <div class="form-group row mb-3">
        <div class="offset-sm-3 col-sm-9">
            <button type="submit" class="btn btn-primary" name="op" value="upload"><{$smarty.const._MD_TADMERGE_MERGE}></button>
        </div>
    </div>
</form>

<{if $my_data_files|default:false}>
    <table class="table table-bordered" >
        <tr class="bg-light">
            <th class="c"><{$smarty.const._MD_TADMERGE_MERGER_XLSX_FILE}></th>
            <th class="c"><{$smarty.const._MD_TADMERGE_MERGER_TPL_FILE}></th>
            <th class="c"><{$smarty.const._TAD_FUNCTION}></th>
        </tr>
        <{foreach from=$my_data_files key=data_files_sn item=file}>
            <{assign var="rowspan" value=$file.temp_files|@sizeof}>
            <{foreach from=$file.temp_files key=sort item=temp_files name=foo}>
                <{foreach from=$temp_files key=temp_files_sn item=temp_file}>
                    <tr>
                        <{if $smarty.foreach.foo.iteration==1}>
                            <td rowspan=<{$rowspan|default:''}>>
                                <a href="javascript:del_file(<{$data_files_sn|default:''}>)" class="text-danger" data-toggle="tooltip" title="<{$smarty.const._MD_TADMERGE_MERGER_DEL_DATA}>"><i class="fa fa-times" aria-hidden="true"></i></a>
                                <{$file.show_file_name}> (<{$file.upload_date}>)
                            </td>
                        <{/if}>
                        <td>
                            <a href="javascript:del_file(<{$temp_files_sn|default:''}>)" class="text-danger" data-toggle="tooltip" title="<{$smarty.const._MD_TADMERGE_MERGER_DEL_TEMP}>"><i class="fa fa-times" aria-hidden="true"></i></a> <{$smarty.const._MD_TADMERGE_MERGER_MERGE_TO|sprintf:$temp_file.show_file_name:$temp_file.upload_date}>
                        </td>
                        <td>
                            <a href="index.php?op=merge&data_sn=<{$data_files_sn|default:''}>&temp_sn=<{$temp_files_sn|default:''}>" class="btn btn-sm btn-success"><i class="fa fa-play" aria-hidden="true"></i> <{if $temp_file.count > 0 }><{$smarty.const._MD_TADMERGE_MERGER_REMERGE}><{else}><{$smarty.const._MD_TADMERGE_MERGER_RUN}><{/if}></a>
                            <{if $temp_file.count > 0 }>
                                <a href="index.php?op=download_all&data_sn=<{$data_files_sn|default:''}>&temp_sn=<{$temp_files_sn|default:''}>" class="btn btn-sm btn-info"><i class="fa fa-download" aria-hidden="true"></i> <{$smarty.const._MD_TADMERGE_MERGER_DOWNLOAD_ALL|sprintf:$temp_file.count}></a>
                                <button type="button" id="view-<{$data_files_sn|default:''}>-<{$data_files_sn|default:''}>" data-data_sn="<{$data_files_sn|default:''}>" data-temp_sn="<{$temp_files_sn|default:''}>" class="btn btn-sm btn-warning view"><i class="fa fa-search" aria-hidden="true"></i> <{$smarty.const._MD_TADMERGE_MERGER_VIEW}></button>
                            <{/if}>
                            <div id="display-<{$data_files_sn|default:''}>-<{$temp_files_sn|default:''}>" style="display:none;">
                                <div id="list-<{$data_files_sn|default:''}>-<{$temp_files_sn|default:''}>"></div>
                            </div>
                        </td>
                    </tr>
                <{/foreach}>
            <{/foreach}>
        <{/foreach}>
    </table>
<{/if}>