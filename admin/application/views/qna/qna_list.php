<div class="row" style="margin-top: 20px;">
    <div class="width-100">
        <div>
            <div class="col-md-9">
                <table class="table table-bordered" style="width: 70%">
                    <tbody>
                    <tr>
                        <td width="200px" class="padding_1">
                            <select class="form-control" id="ipt_search_range" name="search_range">
                                <option value="0"><?=$this->lang->line("all")?></option>
                                <option value="1"><?=$this->lang->line("title")?></option>
                                <option value="2"><?=$this->lang->line("contents")?></option>
                                <option value="3"><?=$this->lang->line("user_id")?></option>
                            </select>
                        </td>
                        <td width="%" class="padding_1">
                            <input class="form-control" id="ipt_search_key" placeholder="Please input search key." name="search_key">
                        </td>
                        <td class="padding_1" style="width: 100px;">
                            <a onclick="searchData()" class="btn btn-success width-100"><i
                                    class="fa fa-search"></i>&nbsp;<?= $this->lang->line("search") ?></a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div class="col-md-3" style="text-align: right;">
                <a id="btn_multi_delete" href="javascript:void(0)" class="hide btn btn-danger" onclick="onMultiRemove()"><i class="fa fa-trash"></i>&nbsp;<?=$this->lang->line("delete")?></a>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <table id="tbl_qna" class="table table-bordered">
            <thead>
            <tr>
                <td class="center title_td" style="width: 30px;"><label for="ipt_all_check"></label><input id="ipt_all_check" name="checkall" type="checkbox" class="checkall" value="ON"/></td>
                <td class="center title_td"><?= $this->lang->line("title") ?></td>
                <td class="center title_td"><?= $this->lang->line("user_id") ?></td>
                <td class="center title_td" style="width: 130px;"><?= $this->lang->line("create_date") ?></td>
                <td class="center title_td" style="width: 100px;"><?= $this->lang->line("answer_state") ?>
                <td class="center title_td" style="width: 60px;"><?= $this->lang->line("answer") ?></td>
                <td class="center title_td" style="width: 60px;"><?= $this->lang->line("delete") ?></td>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<script>
    var oTable;

    $(document).ready(function () {
        $('#left_menu_qna_manage').addClass("active");
        $('#left_menu_qna_manage').children("a:eq(0)").children("span:eq(1)").addClass("selected");
        $('#page_title').html("<?=$this->lang->line('qna_manage')?>");

        ComponentsSelect.init('ipt_search_answer');
        ComponentsSelect.init('ipt_search_range');
    })

    $(function () {
        oTable = $('#tbl_qna').DataTable({
            "stateSave": true,
            "processing": true,
            "serverSide": true,
            "autoWidth": false,

            "language": {
                // "emptyTable": "Empty table.",
                "info": "<span style='font-weight: 700'>Search</span> <span style='font-weight: 700;' class='color_white_blue'>_END_</span> counts / <span style='font-weight: 700'>All</span> _TOTAL_ counts",
                "infoEmpty": "",
                "infoFiltered": "(filtered1 from _MAX_ total entries)",
                // "lengthMenu": "_MENU_ Page length",
                "search": "Search:"
            },

            "ajax": { // define ajax settings
                "url": "<?=site_url('Qna/ajax_table')?>", // ajax URL
                "type": "POST",
                "data": function (data) {
                    setSearchParams(data);
                },
                "dataSrc": function (res) {
                    if (res.recordsTotal === 0) {
                        //showAlertDlg("<?=$this->lang->line('no_search_result')?>", "btn-primary", function () {
                        //}, "<?=$this->lang->line("confirm")?>");
                    }
                    return res.data;
                }
            },

            "columns": [
                {"orderable": false, "render":
                    function (value, AddedOn, data, type, row) {
                        return '<input type="checkbox" id="' + data['uid'] + '" class="call-checkbox" value="' + data['uid'] + '" onclick="onChecked(' + data['uid'] + ')" />';}},
                {"orderable": false},
                {"orderable": false},
                {"orderable": false},
                {"orderable": false},
                {"orderable": false},
                {"orderable": false},
            ],

            "createdRow": function (row, data, dataIndex) {
                $('td:eq(1)', row).attr("style", "cursor: pointer");
                $('td:eq(1)', row).bind("click", function () {
                    location.href = '<?=site_url("Qna/qna_detail")?>?uid=' + data['uid'] + '&mode=0';
                });
                if(data['answer_status'] === 1) {
                    $('td:eq(4)', row).html("<?=$this->lang->line("answered")?>");
                } else {
                    $('td:eq(4)', row).html("<?=$this->lang->line("not_answered")?>");
                }
                $('td:eq(5)', row).html("<a href='<?=site_url("Qna/qna_detail")?>?uid=" + data['uid'] + "&mode=2'><i class='fa fa-edit' style='color: blue'></i></a>");
                $('td:eq(6)', row).html('<a onclick="onRemove(' + data['uid'] + ')"><i class="fa fa-trash" style="color: red"></i></a>');
            },

            "order": [],

            buttons: [],

            // pagination control
            "lengthMenu": [
                [10, 30, 50, 100],
                [10, 30, 50, 100],
            ],
            // set the initial value
            "pageLength": 30,
            "pagingType": 'bootstrap_full_number', // pagination type
            "dom": "<'row' <'col-md-12'B>><'row'<'col-md-6 col-sm-12'i><'col-md-6 col-sm-12'l>r><'table-scrollable't><'row'<'col-md-4 col-sm-12'><'col-md-3 col-sm-12'p>>", // horizobtal scrollable datatable
            "fnDrawCallback": function (oSettings) {
            }
        });

        $('#tbl_qna_length').addClass("hidden");
    })

    $('.checkall').click(function(e) {
        var chk = $(this).prop('checked');
        $('input', oTable.$('tr', {"filter": "applied"} )).prop('checked',chk);

        let $checkboxes = $(".call-checkbox");
        let $checked = $checkboxes.filter(':checked')
        let $unchecked = $checkboxes.filter(':not(:checked)');

        if (!$unchecked.length) {
            $("#ipt_all_check").prop("checked", true);
            $("#btn_multi_delete").addClass("hide");

        } else {
            $("#ipt_all_check").prop("checked", false);
            $("#btn_multi_delete").removeClass("hide");
        }

        if ($checked.length > 0)
            $("#btn_multi_delete").removeClass("hide");
        else
            $("#btn_multi_delete").addClass("hide");

    })

    function onChecked(uid) {

        let $checkboxes = $(".call-checkbox");
        let $checked = $checkboxes.filter(':checked')
        let $unchecked = $checkboxes.filter(':not(:checked)');

        if(!$unchecked.length) {
            $("#ipt_all_check").prop("checked", true);
            $("#btn_multi_delete").addClass("hide");

        } else {
            $("#ipt_all_check").prop("checked", false);
            $("#btn_multi_delete").removeClass("hide");
        }

        if($checked.length > 0)
            $("#btn_multi_delete").removeClass("hide");
        else
            $("#btn_multi_delete").addClass("hide");
    }

    function setSearchParams(data) {
        data['search_answer'] = $("#ipt_search_answer").val();
        data['search_range'] = $("#ipt_search_range").val();
        data['search_key'] = $("#ipt_search_key").val();
    }

    function searchData() {
        oTable.draw(true);
    }

    function onRemove(uid) {
        showConfirmDlg("<?=$this->lang->line('do_you_want_to_delete')?>", function () {
            ajaxRequest('<?=site_url("Qna/delete_qna")?>', {
                uid: uid
            }, function (data) {
                if (data === "success") {
                    showAlertDlg("<?=$this->lang->line('has_been_deleted')?>", "btn-danger", function () {
                        oTable.draw(false);
                    }, "<?=$this->lang->line("confirm")?>");
                } else {
                    showNotification("<?=$this->lang->line('error')?>", "<?=$this->lang->line("operation_failed")?>", "error");
                }
            })

        }, false, null, "<?=$this->lang->line('delete')?>", "<?=$this->lang->line('cancel')?>")
    }

    function onMultiRemove() {
        var uIDs = $(".call-checkbox:checked").map(function(i, cb) {
            return $(cb).val();
        }).get();
        if (uIDs.length === 1) {
            this.onRemove(uIDs[0]);
        } else {
            showConfirmDlg("<?=$this->lang->line("delete_qnas_qna")?>", function () {
                ajaxRequest('<?=site_url("Qna/delete_multi_qnas")?>', {
                    uids: uIDs
                }, function (data) {
                    if(data === "success") {
                        showAlertDlg("<?=$this->lang->line('was_deleted')?>", "btn-primary", function () {
                            oTable.draw(true);
                            $("#btn_multi_delete").addClass("hide");
                            $("#ipt_all_check").prop("checked", false);
                        }, "<?=$this->lang->line("confirm")?>");
                    } else {
                        showNotification("<?=$this->lang->line('error')?>", "<?=$this->lang->line("operation_failed")?>", "error");
                    }
                })
            }, false, function () {
            }, "<?=$this->lang->line("delete")?>", "<?=$this->lang->line("cancel")?>")
        }
    }
</script>