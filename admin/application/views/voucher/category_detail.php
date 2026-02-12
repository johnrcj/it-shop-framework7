<div class="row" style="margin-top: 20px;">
    <div class="col-md-12">
        <form id="frm_detail" method="post" action="<?= site_url('Voucher/update_category') ?>"
              enctype="multipart/form-data">
            <input id="ipt_edit_uid" class="hidden" name="edit_uid" value="<?= $edit_uid ?>">
            <table class="table table-bordered">
                <tbody style="border-color: white;">
                <tr>
                    <td class="title_td" style="width: 150px;"><?= $this->lang->line("title") ?> <span
                                style="color: red">*</span></td>
                    <td class="padding_1">
                        <input id="ipt_title" name="title" class="form-control"
                               value="<?= $info != null ? $info->title : "" ?>">
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
    <div class="col-md-12 center">
        <a class="btn btn-danger" onclick="onBack()" style="width: 130px"><i
                    class="fa fa-close"></i> <?= $this->lang->line("cancel") ?></a>
        <a onclick="onSave()" class="btn btn-primary" style="width: 130px"><i
                    class="fa fa-save"></i> <?= $this->lang->line("save") ?></a>
    </div>
</div>
<script>
    $(document).ready(function () {
        $('#left_menu_voucher_manage').addClass("active");
        $('#left_menu_voucher_manage').children("a:eq(0)").children("span:eq(1)").addClass("selected");
    })

    function onSave() {
        var str_title = $('#ipt_title').val();

        if (str_title == "") {
            showNotification("Warning", "Please input title.", "warning");
            return;
        }

        showConfirmDlg("<?=$this->lang->line('do_you_want_to_save')?>", function () {
            var options = {
                success: afterSuccess,  // post-submit callback
                beforeSend: beforeSubmit,
                resetForm: false        // reset the form after successful submit
            };

            $("#frm_detail").ajaxSubmit(options);

            // $("#frm_detail").submit();

            function beforeSubmit() {
                showLoadingProgress();
            }

            function afterSuccess(data) {
                hideLoadingProgress();

                if (data == "success") {
                    history.go(-1);
                } else {
                    showNotification("Error", "Server error...", "error");
                }
            }
        }, false, null, "<?=$this->lang->line('save')?>", "<?=$this->lang->line('cancel')?>")
    }

    function onBack() {
        showConfirmDlg("<?=$this->lang->line('do_you_want_to_cancel')?>", function () {
            history.go(-1);
        }, false, null, "<?=$this->lang->line('confirm')?>", "<?=$this->lang->line('cancel')?>")
    }
</script>