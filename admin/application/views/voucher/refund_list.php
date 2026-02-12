<div class="row" style="margin-top: 20px;">
    <div class="col-md-12" style="display: flex">
        <a href="<?= site_url("Voucher/category_list") ?>" class="btn btn-default" style="width: 200px;"><?= $this->lang->line("voucher_category") ?></a>
        <a href="<?= site_url("Voucher/voucher_list") ?>" class="btn btn-default" style="width: 200px;"><?= $this->lang->line("voucher_voucher") ?></a>
        <a class="btn btn-success" style="width: 200px;"><?= $this->lang->line("voucher_refund") ?></a>
    </div>

    <div style="margin-top: 10px;">
        <div class="col-md-12" style="margin-top: 10px;">
            <table class="table table-bordered" style="width: 90%">
                <tbody>
                <tr>
                    <td width="200px" class="padding_1">
                        <select class="form-control" id="ipt_search_approval" name="search_range">
                            <option value="0"><?=$this->lang->line("all")?></option>
                            <option value="1"><?=$this->lang->line("approved")?></option>
                            <option value="2"><?=$this->lang->line("not_approved")?></option>
                        </select>
                    </td>
                    <td width="200px" class="padding_1">
                        <select class="form-control" id="ipt_search_range" name="search_range">
                            <option value="0"><?=$this->lang->line("all")?></option>
                            <option value="1"><?=$this->lang->line("user_id")?></option>
                            <option value="2"><?=$this->lang->line("handphone")?></option>
                            <option value="3"><?=$this->lang->line("barcode")?></option>
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

        <div class="col-md-3" style="text-align: right; margin-top: 10px;">
            <a id="btn_multi_approve" href="javascript:void(0)" class="hide btn btn-success" onclick="onMultiApproval()"><i class="fa fa-check"></i>&nbsp;<?=$this->lang->line("approve")?></a>
            <a id="btn_multi_disapprove" href="javascript:void(0)" class="hide btn btn-danger" onclick="onMultiDisapproval()"><i class="fa fa-close"></i>&nbsp;<?=$this->lang->line("disapprove")?></a>
        </div>
    </div>

    <div class="col-md-12" style="margin-top: 10px;">
        <table id="tbl_refund" class="table table-bordered">
            <thead>
            <tr>
                <td class="center title_td" style="width: 30px;"><label for="ipt_all_check"></label><input id="ipt_all_check" name="checkall" type="checkbox" class="checkall" value="ON"/></td>
                <td class="center title_td"><?= $this->lang->line("user_id") ?></td>
                <td class="center title_td" style="width: 100px;"><?= $this->lang->line("handphone") ?></td>
                <td class="center title_td"><?= $this->lang->line("barcode") ?></td>
                <td class="center title_td"><?= $this->lang->line("name") ?></td>
                <td class="center title_td" style="width: 130px;"><?= $this->lang->line("expire_date") ?></td>
                <td class="center title_td"><?= $this->lang->line("category") ?></td>
                <td class="center title_td"><?= $this->lang->line("where_use") ?></td>
                <td class="center title_td"><?= $this->lang->line("memo") ?></td>
                <td class="center title_td" style="width: 40px;"><?= $this->lang->line("price") ?></td>
                <td class="center title_td"><?= $this->lang->line("bank") ?></td>
                <td class="center title_td"><?= $this->lang->line("account") ?></td>
                <td class="center title_td" style="width: 120px;"><?= $this->lang->line("approval") ?></td>
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
        $('#left_menu_voucher_manage').addClass("active");
        $('#left_menu_voucher_manage').children("a:eq(0)").children("span:eq(1)").addClass("selected");
        $('#page_title').html("<?=$this->lang->line('voucher_manage')?>");

        ComponentsSelect.init('ipt_search_approval');
        ComponentsSelect.init('ipt_search_range');
    })

    $(function () {
        var table = $('#tbl_refund');
        oTable = table.DataTable({
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
                "url": "<?=site_url('Voucher/ajax_refund_table')?>", // ajax URL
                "type": "POST",
                "data": function (data) {
                    setSearchParams(data);
                },
                "dataSrc": function (res) {
                    if (res.recordsTotal === 0) {
                        //showAlertDlg("<?=$this->lang->line('no_search_result')?>", "btn-primary", function () {
                        //}, "<?=$this->lang->line("confirm")?>");
                    }
                    return res.data.list;
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
                {"orderable": false},
                {"orderable": false},
                {"orderable": false},
                {"orderable": false},
                {"orderable": false},
                {"orderable": false},
            ],

            "createdRow": function (row, data, dataIndex) {
                $('td:eq(1)', row).attr("style", "cursor: pointer");
                $('td:eq(12)', row).attr("style", "padding: 1px");
                $('td:eq(12)', row).html('<select data-uid="' + data['uid'] + '" class="sb_approval form-control center">' +
                    '<option value="0" ' + (parseInt(data['approval']) == 0 ? "selected" : "") + '>Not approved</option>' +
                    '<option value="1" ' + (parseInt(data['approval']) == 1 ? "selected" : "") + '>Approved</option>' +
                    '</select>');
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
                $('.sb_approval').unbind();
                $('.sb_approval').change(function () {
                    var approval = $(this).val();
                    var refund_id = $(this).attr("data-uid");
                    if (approval === "1")
                        onApproval(refund_id);
                    else
                        onDisapproval(refund_id);
                })
            }
        });

        $('#tbl_refund_length').addClass("hidden");
    })

    $('.checkall').click(function(e) {
        var chk = $(this).prop('checked');
        $('input', oTable.$('tr', {"filter": "applied"} )).prop('checked',chk);

        let $checkboxes = $(".call-checkbox");
        let $checked = $checkboxes.filter(':checked')
        let $unchecked = $checkboxes.filter(':not(:checked)');

        if (!$unchecked.length) {
            $("#ipt_all_check").prop("checked", true);
            $("#btn_multi_approve").addClass("hide");
            $("#btn_multi_disapprove").addClass("hide");

        } else {
            $("#ipt_all_check").prop("checked", false);
            $("#btn_multi_approve").removeClass("hide");
            $("#btn_multi_disapprove").removeClass("hide");
        }

        if ($checked.length > 0) {
            $("#btn_multi_approve").removeClass("hide");
            $("#btn_multi_disapprove").removeClass("hide");
        } else {
            $("#btn_multi_approve").addClass("hide");
            $("#btn_multi_disapprove").addClass("hide");
        }
    })

    function onChecked(uid) {

        let $checkboxes = $(".call-checkbox");
        let $checked = $checkboxes.filter(':checked')
        let $unchecked = $checkboxes.filter(':not(:checked)');

        if(!$unchecked.length) {
            $("#ipt_all_check").prop("checked", true);
            $("#btn_multi_approve").addClass("hide");
            $("#btn_multi_disapprove").addClass("hide");

        } else {
            $("#ipt_all_check").prop("checked", false);
            $("#btn_multi_approve").removeClass("hide");
            $("#btn_multi_disapprove").removeClass("hide");
        }

        if($checked.length > 0) {
            $("#btn_multi_approve").removeClass("hide");
            $("#btn_multi_disapprove").removeClass("hide");
        } else {
            $("#btn_multi_approve").addClass("hide");
            $("#btn_multi_disapprove").addClass("hide");
        }
    }

    function setSearchParams(data) {
        data['search_approval'] = $("#ipt_search_approval").val();
        data['search_range'] = $("#ipt_search_range").val();
        data['search_key'] = $("#ipt_search_key").val();
    }

    function searchData() {
        oTable.draw(true);
    }

    function onApproval(uid) {
        showConfirmDlg("<?=$this->lang->line("approval_qna")?>", function () {
            ajaxRequest('<?=site_url("Voucher/change_approval")?>', {
                refund_id: uid,
                approval: 1
            }, function (data) {
                if (data === "success") {
                    showNotification("<?=$this->lang->line('success')?>", "<?=$this->lang->line("operation_success")?>", "success");
                } else {
                    showNotification("<?=$this->lang->line('error')?>", "<?=$this->lang->line("operation_failed")?>", "error");
                    oTable.draw(false);
                }
            })
        }, false, function () {
        }, "<?=$this->lang->line("approve")?>", "<?=$this->lang->line("cancel")?>")
    }

    function onDisapproval(uid) {
        showConfirmDlg("<?=$this->lang->line("disapproval_qna")?>", function () {
            ajaxRequest('<?=site_url("Voucher/change_approval")?>', {
                refund_id: uid,
                approval: 0
            }, function (data) {
                if (data === "success") {
                    showNotification("<?=$this->lang->line('success')?>", "<?=$this->lang->line("operation_success")?>", "success");
                } else {
                    showNotification("<?=$this->lang->line('error')?>", "<?=$this->lang->line("operation_failed")?>", "error");
                    oTable.draw(false);
                }
            })
        }, false, function () {
        }, "<?=$this->lang->line("disapprove")?>", "<?=$this->lang->line("cancel")?>")
    }

    function onMultiApproval() {
        var uIDs = $(".call-checkbox:checked").map(function(i, cb) {
            return $(cb).val();
        }).get();
        if (uIDs.length === 1) {
            this.onApproval(uIDs[0]);
        } else {
            showConfirmDlg("<?=$this->lang->line("multi_approval_qna")?>", function () {
                ajaxRequest('<?=site_url("Voucher/change_multi_approval")?>', {
                    uids: uIDs,
                    approval: 1
                }, function (data) {
                    if(data === "success") {
                        showAlertDlg("<?=$this->lang->line('was_approved')?>", "btn-primary", function () {
                            oTable.draw(true);
                            $("#btn_multi_approve").addClass("hide");
                            $("#btn_multi_disapprove").addClass("hide");
                            $("#ipt_all_check").prop("checked", false);
                        }, "<?=$this->lang->line("confirm")?>");
                    } else {
                        showNotification("<?=$this->lang->line('error')?>", "<?=$this->lang->line("operation_failed")?>", "error");
                    }
                })
            }, false, function () {
            }, "<?=$this->lang->line("approve")?>", "<?=$this->lang->line("cancel")?>")
        }
    }

    function onMultiDisapproval() {
        var uIDs = $(".call-checkbox:checked").map(function(i, cb) {
            return $(cb).val();
        }).get();
        if (uIDs.length === 1) {
            this.onDisapproval(uIDs[0]);
        } else {
            showConfirmDlg("<?=$this->lang->line("multi_disapproval_qna")?>", function () {
                ajaxRequest('<?=site_url("Voucher/change_multi_approval")?>', {
                    uids: uIDs,
                    approval: 0
                }, function (data) {
                    if(data === "success") {
                        showAlertDlg("<?=$this->lang->line('was_disapproved')?>", "btn-primary", function () {
                            oTable.draw(true);
                            $("#btn_multi_approve").addClass("hide");
                            $("#btn_multi_disapprove").addClass("hide");
                            $("#ipt_all_check").prop("checked", false);
                        }, "<?=$this->lang->line("confirm")?>");
                    } else {
                        showNotification("<?=$this->lang->line('error')?>", "<?=$this->lang->line("operation_failed")?>", "error");
                    }
                })
            }, false, function () {
            }, "<?=$this->lang->line("disapprove")?>", "<?=$this->lang->line("cancel")?>")
        }
    }
</script>