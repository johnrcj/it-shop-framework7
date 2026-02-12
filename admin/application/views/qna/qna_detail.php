<div class="row" style="margin-top: 20px;">
    <div class="col-md-12">
        <form id="frm_detail" method="post" action="<?= site_url('Qna/update_qna') ?>"
              enctype="multipart/form-data">
            <input id="ipt_edit_uid" class="hidden" name="edit_uid" value="<?= $edit_uid ?>">
            <table class="table table-bordered">
                <tbody style="border-color: white;">
                <tr>
                    <td class="title_td"><?= $this->lang->line("title") ?></td>
                    <td class="align_left" colspan="3"><?= $info != null ? $info->title : "" ?></td>
                </tr>
                <tr>
                    <td class="title_td" style="width: 150px;"><?= $this->lang->line("user_id") ?></td>
                    <td class="align_left"><a style="text-decoration: underline"><?= $usr_info != null ? $usr_info->email : "" ?></a></td>
                </tr>
                <tr>
                    <td class="title_td" style="width: 150px;"><?= $this->lang->line("create_date") ?></td>
                    <td class="align_left" colspan="3"><?= $info != null ? $info->reg_time : "" ?></td>
                </tr>
                <tr>
                    <td class="title_td" style="width: 150px;"><?= $this->lang->line("question_content") ?></td>
                    <td class="align_left" colspan="3">
                        <div class="width-100">
                            <textarea class="width-100" readonly rows="5"><?= $info != null ? $info->content : "" ?></textarea>
                            <?php
                            foreach ($info->image_urls as $url) {
                                ?>
                                <img style="width: 32%; margin-bottom: 10px; margin-right: 10px;" src="<?= $url != null ? $url : "" ?>">
                                <?php
                            }
                            ?>
                        </div>
                    </td>
                </tr>
                <?php if ($mode == 0) { ?>
                <tr>
                    <td class="title_td" style="width: 150px;"><?= $this->lang->line("answer_state") ?></td>
                    <td class="align_left" colspan="3">
                        <?= $info != null ? ($info->answer_time != null ? "Answered" : "Not answered" ) : "Not answered" ?>
                    </td>
                </tr>
                <?php } ?>
                </tbody>
            </table>

            <label class="width-100"><?=$this->lang->line("register_admin_answer")?></label>

            <table class="table table-bordered">
                <tbody style="border-color: white;">
                <tr>
                    <td class="title_td" style="width: 150px;"><?= $this->lang->line("answer_content") ?></td>
                    <td class="padding_1">
                        <textarea id="ipt_answer_content" rows="6" name="answer_content" <?= $mode == 0 ? "readonly" : "" ?>
                                  class="form-control"><?= $info != null ? $info->answer_content : "" ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td class="title_td" style="width: 150px;"><?= $this->lang->line("image") ?></td>
                    <td class="padding_1">
                        <input type="file" id="f_img" class="hide" name="image" accept="image/*">
                        <div style="width: 33%;background-color: #e7ecf1;border: 1px solid grey;">
                            <div id="div_img_content" class="width-100" style="height: 200px;position: relative">
                                <?php
                                if ($info == null) {
                                    ?>
                                    <img class="hide" style="width: 100%;height: 100%;">
                                    <label style="color: grey;position: absolute;top: 50%;left:50%;transform: translate(-50%,-50%)">
                                        <?= $this->lang->line("image") ?></label>
                                    <?php
                                } else {
                                    ?>
                                    <img style="width: 100%;height: 100%;"
                                         src="<?= $info != null ? $info->answer_image_url : "" ?>">
                                    <label class="hide" style="color: grey;position: absolute;top: 50%;left:50%;transform: translate(-50%,-50%)">
                                        <?= $this->lang->line("image") ?>
                                    </label>
                                    <?php
                                }
                                ?>
                            </div>
                            <?php if ($mode != 0) { ?>
                                <div class="col-md-12 padding_none">
                                    <a onclick="onChangeImg()" class="btn btn-primary width-100"
                                       style="margin: 0"><?= $this->lang->line("change") ?></a>
                                </div>
                            <?php } ?>
                        </div>
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
        $('#left_menu_qna_manage').addClass("active");
        $('#left_menu_qna_manage').children("a:eq(0)").children("span:eq(1)").addClass("selected");
        $('#page_title').html("<?=$this->lang->line('qna_manage')?>");
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
        var str_content = $('#ipt_answer_content').val();

        if (str_content == "") {
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