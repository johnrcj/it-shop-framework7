<div class="row" style="margin-top: 20px;">
    <div class="col-md-12" style="display: flex">
        <a href="<?= site_url("Voucher/category_list") ?>" class="btn btn-default" style="width: 200px;"><?= $this->lang->line("voucher_category") ?></a>
        <a class="btn btn-success" style="width: 200px;"><?= $this->lang->line("voucher_voucher") ?></a>
        <a href="<?= site_url("Voucher/refund_list") ?>" class="btn btn-default" style="width: 200px;"><?= $this->lang->line("voucher_refund") ?></a>
    </div>

    <div class="col-md-12" style="margin-top: 10px;">
        <label id="lbl_cnt_info" style="font-size: 16px;"></label>
    </div>

    <div class="col-md-12" style="margin-top: 10px;">
        <table class="table table-bordered" style="width: 70%">
            <tbody>
            <tr>
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

    <div class="col-md-12" style="margin-top: 10px;">
        <table id="tbl_voucher" class="table table-bordered">
            <thead>
            <tr>
                <td class="center title_td"><?= $this->lang->line("user_id") ?></td>
                <td class="center title_td" style="width: 100px;"><?= $this->lang->line("handphone") ?></td>
                <td class="center title_td"><?= $this->lang->line("barcode") ?></td>
                <td class="center title_td"><?= $this->lang->line("name") ?></td>
                <td class="center title_td" style="width: 130px;"><?= $this->lang->line("expire_date") ?></td>
                <td class="center title_td"><?= $this->lang->line("category") ?></td>
                <td class="center title_td"><?= $this->lang->line("where_use") ?></td>
                <td class="center title_td"><?= $this->lang->line("memo") ?></td>
                <td class="center title_td" style="width: 40px;"><?= $this->lang->line("price") ?></td>
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
    })

    $(function () {
        var table = $('#tbl_voucher');
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
                "url": "<?=site_url('Voucher/ajax_voucher_table')?>", // ajax URL
                "type": "POST",
                "data": function (data) {
                    setSearchParams(data);
                },
                "dataSrc": function (res) {
                    $('#lbl_cnt_info').html(res.data.voucher_cnt_info);
                    if (res.recordsTotal === 0) {
                        //showAlertDlg("<?=$this->lang->line('no_search_result')?>", "btn-primary", function () {
                        //}, "<?=$this->lang->line("confirm")?>");
                    }
                    return res.data.list;
                }
            },

            "columns": [
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

        $('#tbl_voucher_length').addClass("hidden");
    })

    function setSearchParams(data) {
        data['search_range'] = $("#ipt_search_range").val();
        data['search_key'] = $("#ipt_search_key").val();
    }

    function searchData() {
        oTable.draw(true);
    }
</script>