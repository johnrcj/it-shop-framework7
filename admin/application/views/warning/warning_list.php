<div class="row" style="margin-top: 20px;">
    <div class="col-md-12">
        <div class="width-100" style="text-align: right">
            <a href="<?=site_url("Warning/warning_detail?mode=1")?>" class="btn btn-primary"><i class="fa fa-plus"></i>&nbsp;<?=$this->lang->line("register")?></a>
        </div>

        <table id="tbl_warning" class="table table-bordered">
            <thead>
            <tr>
                <td class="center title_td" width="130px"><?= $this->lang->line("warning_kind") ?></td>
                <td class="center title_td" width="130px"><?= $this->lang->line("create_date") ?></td>
                <td class="center title_td" width="130px"><?= $this->lang->line("modify_date") ?></td>
                <td class="center title_td" width="60px"><?= $this->lang->line("edit") ?></td>
                <td class="center title_td" width="60px"><?= $this->lang->line("delete") ?></td>
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
        $('#left_menu_warning_manage').addClass("active");
        $('#left_menu_warning_manage').children("a:eq(0)").children("span:eq(1)").addClass("selected");
        $('#page_title').html("<?=$this->lang->line('warning_manage')?>");
    })

    $(function () {
        oTable = $('#tbl_warning').DataTable({
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
                "url": "<?=site_url('Warning/ajax_table')?>", // ajax URL
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
                {"orderable": false},
                {"orderable": false},
                {"orderable": false},
                {"orderable": false},
                {"orderable": false},
            ],

            "createdRow": function (row, data, dataIndex) {
                $('td:eq(0), td:eq(4)', row).attr("style", "cursor: pointer");
                $('td:eq(0)', row).bind("click", function () {
                    location.href = '<?=site_url("Warning/warning_detail")?>?uid=' + data['uid'] + '&mode=0';
                });
                $('td:eq(3)', row).html("<a href='<?=site_url("Warning/warning_detail")?>?uid=" + data['uid'] + "&mode=2'><i class='fa fa-edit' style='color: blue'></i></a>");
                $('td:eq(4)', row).html('<a onclick="onRemove(' + data['uid'] + ')"><i class="fa fa-trash" style="color: red"></i></a>');
            },

            "order": [],

            buttons: [],

            // pagination control
            "lengthMenu": [],

            // set the initial value
            "pageLength": -1,
            "paginate": false,
            "pagingType": 'bootstrap_full_number', // pagination type
            "dom": "<'row' <'col-md-12'B>><'row'<'col-md-6 col-sm-12'><'col-md-6 col-sm-12'>><'table-scrollable't><'row'<'col-md-4 col-sm-12'><'col-md-3 col-sm-12'>>", // horizobtal scrollable datatable
            "fnDrawCallback": function (oSettings) {
            }
        });
    })

    function onRemove(uid) {
        showConfirmDlg("<?=$this->lang->line('do_you_want_to_delete')?>", function () {
            ajaxRequest('<?=site_url("Warning/delete_warning")?>', {
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
</script>