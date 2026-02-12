<div class="row" style="margin-top: 20px;">
    <div class="col-md-12">
        <form id="frm_detail" method="post" action="<?= site_url('Warning/update_warning') ?>"
              enctype="multipart/form-data">
            <input id="ipt_edit_uid" class="hidden" name="edit_uid" value="<?= $edit_uid ?>">
            <table class="table table-bordered">
                <tbody style="border-color: white;">
                <tr>
                    <td class="title_td" style="width: 150px;"><?= $this->lang->line("contents") ?>
                        <?php if ($mode != 0) { ?>
                        <span style="color: red">*</span>
                        <?php } ?>
                    </td>
                    <td class="padding_1">
                        <input id="ipt_content" name="content" class="form-control" <?= $mode == 0 ? "readonly" : "" ?>
                               value="<?= $info != null ? $info->content : "" ?>">
                    </td>
                </tr>
                <?php if ($mode == 0) { ?>
                <tr>
                    <td class="title_td" style="width: 150px;"><?= $this->lang->line("create_date") ?>
                    <td class="padding_1">
                        <input class="form-control" readonly
                               value="<?= $info != null ? $info->reg_time : "" ?>">
                    </td>
                </tr>
                <?php } ?>
                <?php if ($mode == 0) { ?>
                <tr>
                    <td class="title_td" style="width: 150px;"><?= $this->lang->line("create_date") ?>
                    <td class="padding_1">
                        <input class="form-control" readonly
                               value="<?= $info != null ? $info->mod_time : "" ?>">
                    </td>
                </tr>
                <?php } ?>
                <tr>
                    <td class="title_td" style="width: 150px;"><?= $this->lang->line("warning_kind") ?>
                        <?php if ($mode != 0) { ?>
                        <span style="color: red">*</span>
                        <?php } ?>
                    <td class="padding_1">
                        <?php if ($mode == 0) { ?>
                        <input class="form-control" <?= $mode == 0 ? "readonly" : "" ?>
                               value="<?= $info != null ? $this->lang->line(_make_warning($info->kind)) : "" ?>">
                        <?php } else { ?>
                        <select id="ipt_kind" name="kind" class="form-control center">
                            <?php for ($i = 1; $i <= 4; $i++) { ?>
                            <option value="<?= $i ?>" <?= $info != null ? ($info->kind==$i ? "selected" : "") : "" ?>><?= $this->lang->line(_make_warning($i)) ?></option>
                            <?php } ?>
                        </select>
                        <?php } ?>
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
    <div class="col-md-12 center">
        <?php if ($mode == 0) { ?>
        <a class="btn btn-danger" onclick="history.go(-1)" style="width: 130px">
            <i class="fa fa-close"></i> <?= $this->lang->line("confirm") ?></a>
        <?php } else { ?>
        <a class="btn btn-danger" onclick="onBack()" style="width: 130px">
            <i class="fa fa-close"></i> <?= $this->lang->line("cancel") ?></a>
        <a onclick="onSave()" class="btn btn-primary" style="width: 130px">
            <i class="fa fa-save"></i> <?= $this->lang->line("save") ?></a>
        <?php } ?>
    </div>
</div>
<script>
    var img_selected_flag = false;

    $(document).ready(function () {
        $('#left_menu_warning_manage').addClass("active");
        $('#left_menu_warning_manage').children("a:eq(0)").children("span:eq(1)").addClass("selected");
        $('#page_title').html("<?=$this->lang->line('warning_manage')?>");
    })

    function onChangeImg() {
        $('#f_img').trigger("click");
    }

    $('#f_img').change(function () {
        var input = $("#f_img").get(0);
        var file = input.files[0];
        var fr = new FileReader();
        var img = fr.readAsDataURL(file);
        fr.onloadend = function () {
            var img = fr.result;
            $('#div_img_content').find("img").attr("src", img);
            $('#div_img_content').find("img").removeClass("hide");
            $('#div_img_content').find("label").addClass("hide");
            img_selected_flag = true;
        }
    })

    function onSave() {
        var str_content = $('#ipt_content').val();

        if (str_content === "") {
            showNotification("Warning", "Please input contents.", "warning");
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

                if (data === "success") {
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