<div class="container-fluid" style="margin-bottom: 30px;">
    <{if $now_op|default:false}>
        <{include file="$xoops_rootpath/modules/tad_merge/templates/op_`$now_op`.tpl"}>
    <{/if}>
</div>

<script language="JavaScript" type="text/javascript">
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>