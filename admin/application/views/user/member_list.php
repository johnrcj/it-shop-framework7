<div class="row" style="margin-top: 20px;">
    <div class="col-md-12" style="display: flex">
        <a class="btn btn-success" style="width: 200px;"><?= $this->lang->line("member_list") ?></a>
        <a href="<?= site_url("User/withdrawal_list") ?>" class="btn btn-default"
           style="width: 200px;"><?= $this->lang->line("withdrawal_member_manage") ?></a>
    </div>

    <div class="col-md-12" style="margin-top: 10px;">
        <label id="lbl_cnt_info" style="font-size: 16px;"></label>
    </div>

    <div style="margin-top: 10px;">
        <div class="col-md-9">
            <table class="table table-bordered" style="width: 70%">
                <tbody>
                <tr>
                    <td width="200px" class="padding_1">
                        <select class="form-control" id="ipt_search_range" name="search_range">
                            <option value="0"><?=$this->lang->line("all")?></option>
                            <option value="1"><?=$this->lang->line("user_id")?></option>
                            <option value="2"><?=$this->lang->line("handphone")?></option>
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
            <a id="btn_multi_delete" href="javascript:void(0)" class="hide btn btn-danger" onclick="onMultiRemove()"><i class="fa fa-trash"></i>&nbsp;<?=$this->lang->line("withdraw")?></a>
        </div>
    </div>

    <div class="col-md-12">
        <table id="tbl_user" class="table table-bordered">
            <thead>
            <tr>
                <td class="center title_td" style="width: 30px;"><label for="ipt_all_check"></label><input id="ipt_all_check" name="checkall" type="checkbox" class="checkall" value="ON"/></td>
                <td class="center title_td"><?= $this->lang->line("user_id") ?></td>
                <td class="center title_td"><?= $this->lang->line("handphone") ?></td>
                <td class="center title_td" style="width: 130px;"><?= $this->lang->line("register_date") ?></td>
                <td class="center title_td" style="width: 80px;"><?= $this->lang->line("withdraw") ?></td>
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
        $('#left_menu_user_manage').addClass("active");
        $('#left_menu_user_manage').children("a:eq(0)").children("span:eq(1)").addClass("selected");
        $('#page_title').html("<?=$this->lang->line('member_manage')?>");

        ComponentsSelect.init('ipt_search_range');
    })

    $(function () {
        var table = $('#tbl_user');
        oTable = table.DataTable({
            "stateSave": true,
            "processing": true,
            "serverSide": true,
            "autoWidth": false,

            "language": {
                // "emptyTable": "Empty table.",
                "info": "<span style='font-weight: 700'>Search</span> <span style='font-weight: 700;' class='color_white_blue'>_END_</span> members / <span style='font-weight: 700'>All</span> _TOTAL_ members",
                "infoEmpty": "",
                "infoFiltered": "(filtered1 from _MAX_ total entries)",
                // "lengthMenu": "_MENU_ Page length",
                "search": "Search:"
            },

            "ajax": { // define ajax settings
                "url": "<?=site_url('User/ajax_table')?>", // ajax URL
                "type": "POST",
                "data": function (data) {
                    setSearchParams(data);
                },
                "dataSrc": function (res) {
                    $('#lbl_cnt_info').html(res.data.member_cnt_info);
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
            ],

            "createdRow": function (row, data, dataIndex) {
                $('td:eq(4)', row).html('<a onclick="onRemove(' + data['uid'] + ')"><i class="fa fa-trash" style="color: red"></i></a>');
            },

            "order": [],

            buttons: [],

            // pagination control
            "lengthMenu": [
            ],
            // set the initial value
            "pageLength": 30,
            "paginate": false,
            "pagingType": 'bootstrap_full_number', // pagination type
            "dom": "<'row' <'col-md-12'B>><'row'<'col-md-6 col-sm-12'i><'col-md-6 col-sm-12'l>r><'table-scrollable't><'row'<'col-md-4 col-sm-12'><'col-md-3 col-sm-12'p>>", // horizobtal scrollable datatable
            "fnDrawCallback": function (oSettings) {
            }
        });

        $('#tbl_user_length').addClass("hidden");
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
        data['search_range'] = $("#ipt_search_range").val();
        data['search_key'] = $("#ipt_search_key").val();
    }

    function searchData() {
        oTable.draw(true);
    }

    function onRemove(uid) {
        showConfirmDlg("<?=$this->lang->line("withdraw_member_qna")?>", function () {
            ajaxRequest('<?=site_url("User/withdraw_user")?>', {
                uid: uid
            }, function (data) {
                if(data === "success") {
                    showAlertDlg("<?=$this->lang->line('was_withdrew')?>", "btn-primary", function () {
                        oTable.draw(true);
                        $("#btn_multi_delete").addClass("hide");
                    }, "<?=$this->lang->line("confirm")?>");
                } else {
                    showNotification("<?=$this->lang->line('error')?>", "<?=$this->lang->line("operation_failed")?>", "error");
                }
            })
        }, false, function () {
        }, "<?=$this->lang->line("withdraw")?>", "<?=$this->lang->line("cancel")?>")
    }

    function onMultiRemove() {
        var uIDs = $(".call-checkbox:checked").map(function(i, cb) {
            return $(cb).val();
        }).get();
        if (uIDs.length === 1) {
            this.onRemove(uIDs[0]);
        } else {
            showConfirmDlg("<?=$this->lang->line("withdraw_members_qna")?>", function () {
                ajaxRequest('<?=site_url("User/withdraw_multi_users")?>', {
                    uids: uIDs
                }, function (data) {
                    if(data === "success") {
                        showAlertDlg("<?=$this->lang->line('was_withdrew')?>", "btn-primary", function () {
                            oTable.draw(true);
                            $("#btn_multi_delete").addClass("hide");
                            $("#ipt_all_check").prop("checked", false);
                        }, "<?=$this->lang->line("confirm")?>");
                    } else {
                        showNotification("<?=$this->lang->line('error')?>", "<?=$this->lang->line("operation_failed")?>", "error");
                    }
                })
            }, false, function () {
            }, "<?=$this->lang->line("withdraw")?>", "<?=$this->lang->line("cancel")?>")
        }
    }
</script>