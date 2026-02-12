<!DOCTYPE html>
<!--[if IE 8]>
<html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]>
<html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->

<head>
    <meta charset="utf-8"/>
    <title><?= $this->lang->line("app_name") ?></title>
    <link href="<?= base_url() ?>assets/images/logo1.png" rel="icon">
    <link href="<?= base_url() ?>assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="<?= base_url() ?>assets/global/css/components.css" rel="stylesheet" id="style_components"
          type="text/css"/>
    <link href="<?= base_url() ?>assets/pages/css/login.css" rel="stylesheet" type="text/css"/>
    <link href="<?= base_url() ?>assets/css/custom.css" rel="stylesheet" type="text/css"/>
    <script src="<?= base_url() ?>assets/global/plugins/jquery.min.js" type="text/javascript"></script>
    <link href="<?= base_url() ?>assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet"
          type="text/css"/>
    <script src="<?= base_url() ?>assets/js/jquery.form.js" type="text/javascript"></script>
    <link href="<?= base_url() ?>assets/global/plugins/bootstrap-toastr/toastr.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="<?= base_url() ?>assets/global/plugins/font-awesome/css/font-awesome.css" rel="stylesheet"
          type="text/css"/>
    <link href="<?= base_url() ?>assets/layouts/layout/css/themes/darkblue.min.css" rel="stylesheet" type="text/css"
          id="style_color"/>

    <style>
        .blockUI {
            left: 50% !important;
            text-align: center;
            transform: translateX(-50%);
            margin-left: 0 !important;
        }
    </style>
</head>
<body>
<div class="logo">
</div>

<div class="content" style="margin-top: 200px;">
    <!-- BEGIN LOGIN FORM -->
    <form action="" method="post" style="padding: 0px;width:400px;background-color:white;margin: auto" id="login_form">
        <div class="row" style="padding: 20px 30px 30px 30px;">
            <div class="form-group col-md-12" align="center">
                <label style="font-weight: bold; font-size: 25px"><?= $this->lang->line("shop_admin") ?></label>
            </div>
            <div class="col-md-12 form-group">
                <div class="input-group width-100">
                    <input class="form-control" placeholder="<?= $this->lang->line("id") ?>" name="usrid" id="usrid">
                </div>
            </div>
            <div class="col-md-12 form-group">
                <div class="input-group width-100">
                    <input type="password" placeholder="<?= $this->lang->line("password") ?>" class="form-control"
                           name="password" id="password">
                </div>
            </div>
            <div class="row" style="margin-top: 20px;">
                <div class="col-md-12" style="display: flex;align-items: center;margin-top: 10px;">
                    <button type="submit" class="btn btn-success"
                            style="width: 150px;margin: auto"><?= $this->lang->line("login") ?></button>
                </div>
            </div>
        </div>
    </form>

    <div class="width-100" style="background-color: white;position: fixed;bottom: 0;left: 0;right: 0;padding: 20px 50px;white-space: pre-wrap"><?=$this->lang->line("bottom_menu_content")?></div>
</div>

<script>
    var validator;
    var error1;
    $(function () {
        var login_form = $('#login_form');

        validator = login_form.validate({
            doNotHideMessage: true, //this option enables to show the error/success messages on tab switch.
            errorElement: 'span', //default input error message container
            errorClass: 'help-block help-block-error', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            messages: {
                usrid: {
                    required: "<?=$this->lang->line('required_filed_error')?>"
                },
                password: {
                    required: "<?=$this->lang->line('required_filed_error')?>"
                }
            },
            rules: {
                usrid: {
                    required: true
                },
                password: {
                    required: true
                }
            },
            highlight: function (element) { // hightlight error inputs
                $(element)
                    .closest('.form-group').removeClass('has-success').addClass('has-error'); // set error class to the control group
            },

            unhighlight: function (element) { // revert the change done by hightlight
                $(element)
                    .closest('.form-group').removeClass('has-error'); // set error class to the control group
            },
            beforeSubmit: function () {

            },
            submitHandler: function (form) {
                onLogin();
            }
        });
    })

    function onLogin() {
        $.ajax({
            type: 'post',
            url: '<?=site_url("Login/login")?>',
            data: 'usrid=' + $('#usrid').val() + '&password=' + $('#password').val(),
            beforeSend: function () {
                App.blockUI({
                    animate: true,
                    target: '#login_form',
                    boxed: false
                });
            },
            success: function (data) {
                App.unblockUI('#login_form');
                if (data === "no_exist") {
                    showAlertDlg("<?=$this->lang->line('please_check_your_id_and_password_again')?>", "btn-danger", function() {
                    }, "<?=$this->lang->line("confirm")?>");
                } else if (data === 'no_password') {
                    showAlertDlg("<?=$this->lang->line('please_check_your_id_and_password_again')?>", "btn-danger", function() {
                    }, "<?=$this->lang->line("confirm")?>");
                } else {
                    location.href = "<?=site_url('User/member_list')?>";
                }
            }
        })
    }
</script>
<script src="<?= base_url() ?>assets/global/plugins/jquery-validation/js/jquery.validate.min.js"
        type="text/javascript"></script>
<script src="<?= base_url() ?>assets/global/plugins/jquery-validation/js/additional-methods.min.js"
        type="text/javascript"></script>

<script src="<?= base_url() ?>assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="<?= base_url() ?>assets/global/scripts/app.min.js" type="text/javascript"></script>
<script src="<?= base_url() ?>assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js"
        type="text/javascript"></script>
<script src="<?= base_url() ?>assets/global/plugins/bootstrap-toastr/toastr.min.js" type="text/javascript"></script>
<script src="<?= base_url() ?>assets/js/common.js" type="text/javascript"></script>

</body>
</html>