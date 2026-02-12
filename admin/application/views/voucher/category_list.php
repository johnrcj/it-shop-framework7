<div class="row" style="margin-top: 20px;">
    <div class="col-md-12" style="display: flex">
        <a class="btn btn-success" style="width: 200px;"><?= $this->lang->line("voucher_category") ?></a>
        <a href="<?= site_url("Voucher/voucher_list") ?>" class="btn btn-default" style="width: 200px;"><?= $this->lang->line("voucher_voucher") ?></a>
        <a href="<?= site_url("Voucher/refund_list") ?>" class="btn btn-default" style="width: 200px;"><?= $this->lang->line("voucher_refund") ?></a>
    </div>

    <div class="col-md-12">
        <div class="width-100" style="text-align: right; margin-bottom: 10px">
            <a id="btn_multi_delete" href="javascript:void(0)" class="hide btn btn-danger" onclick="onMultiRemove()"><i class="fa fa-trash"></i>&nbsp;<?=$this->lang->line("delete")?></a>
            <a href="<?=site_url("Voucher/category_detail?mode=1")?>" class="btn btn-primary"><i class="fa fa-plus"></i>&nbsp;<?=$this->lang->line("register")?></a>
        </div>

        <table id="tbl_category" class="table table-bordered">
            <thead>
            <tr>
                <td class="center title_td" style="width: 30px;"><label for="ipt_all_check"></label><input id="ipt_all_check" name="checkall" type="checkbox" class="checkall" value="ON"/></td>
                <td class="center title_td"><?= $this->lang->line("title") ?></td>
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
    var firstLoad = false;

    $(document).ready(function () {
        $('#left_menu_voucher_manage').addClass("active");
        $('#left_menu_voucher_manage').children("a:eq(0)").children("span:eq(1)").addClass("selected");
        $('#page_title').html("<?=$this->lang->line('voucher_manage')?>");
    })

    $(function () {
        oTable = $('#tbl_category').DataTable({
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
                "url": "<?=site_url('Voucher/ajax_category_table')?>", // ajax URL
                "type": "POST",
                "data": function (data) {
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
            ],

            "createdRow": function (row, data, dataIndex) {
                $('td:eq(1)', row).attr("style", "cursor: pointer");
                $('td:eq(1)', row).bind("click", function () {
                    location.href = '<?=site_url("Voucher/category_detail")?>?uid=' + data['uid'] + '&mode=0';
                });

                $('td:eq(2)', row).html('<a onclick="onRemove(' + data['uid'] + ')"><i class="fa fa-trash" style="color: red"></i></a>');
            },

            "order": [],

            buttons: [],

            // pagination control
            "lengthMenu": [
                [10, 30, 50, 100],
                [10, 30, 50, 100],
            ],

            // set the initial value
            "pageLength": 4,
            "paginate": true,
            "pagingType": 'bootstrap_full_number', // pagination type
            "dom": "<'row' <'col-md-12'B>><'row'<'col-md-6 col-sm-12'i><'col-md-6 col-sm-12'l>r><'table-scrollable't><'row'<'col-md-4 col-sm-12'><'col-md-3 col-sm-12'p>>", // horizobtal scrollable datatable
            "fnDrawCallback": function (oSettings) {
            }
        });

        $('#tbl_category_length').addClass("hidden");
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

    function onRemove(uid) {
        showConfirmDlg("<?=$this->lang->line('do_you_want_to_delete')?>", function () {
            ajaxRequest('<?=site_url("Voucher/delete_category")?>', {
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
            showConfirmDlg("<?=$this->lang->line("do_you_want_to_delete")?>", function () {
                ajaxRequest('<?=site_url("Voucher/delete_multi_categories")?>', {
                    uids: uIDs
                }, function (data) {
                    if(data === "success") {
                        showAlertDlg("<?=$this->lang->line('has_been_deleted')?>", "btn-primary", function () {
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