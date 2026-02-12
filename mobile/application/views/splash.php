<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no, viewport-fit=cover">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta http-equiv="Content-Security-Policy" content="default-src * 'self' 'unsafe-inline' 'unsafe-eval' data: gap:">
    <title><?= $this->lang->line('app_name') ?></title>
    <link rel="stylesheet" href="<?= base_url() ?>assets/css/framework7.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>assets/css/app.css">
</head>
<body>
<div id="app">
    <div class="view view-main view-init safe-areas" data-url="/">
        <div class="page" data-name="splash">
            <div class="page-content splash-panel">
                <label><?= $this->lang->line("splash_label") ?></label>
                <img src="<?= base_url() ?>assets/images/ic_splash_logo.png">
            </div>
        </div>

        <div class="bottom-nav hidden">
            <div onclick="onButtonMenu(1)" class="selected bottom-menu-item home link">
                <div class="child">
                    <i></i>
                    <div class="title"><?= $this->lang->line("home") ?></div>
                </div>
            </div>
            <div onclick="onButtonMenu(2)" class="bottom-menu-item refund link">
                <div class="child">
                    <i></i>
                    <div class="title"><?= $this->lang->line("refund") ?></div>
                </div>
            </div>
            <div onclick="onButtonMenu(3)" class="bottom-menu-item mypage link">
                <div class="child">
                    <i></i>
                    <div class="title"><?= $this->lang->line("myshop") ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= base_url() ?>assets/js/jquery-2.1.3.min.js"></script>
<script src="<?= base_url() ?>assets/js/framework7.js"></script>
<script src="<?= base_url() ?>assets/js/moment.min.js"></script>

<script>
    var SITE_URL = '<?=site_url()?>';
</script>

<script src="<?= base_url() ?>assets/js/routes.js?time=<?= time() ?>"></script>
<script src="<?= base_url() ?>assets/js/app.js?time=<?= time() ?>"></script>
<script src="<?= base_url() ?>assets/js/util.js"></script>
<script src="<?= base_url() ?>assets/js/bridge_action.js"></script>
<script src="<?= base_url() ?>assets/js/input_action.js"></script>

<input id="f_upload_test" class="hidden" type="file" accept="image/*">

<script>
    app.data.CONST.SITE_URL = '<?=site_url()?>';
    app.data.CONST.API_URL = app.data.CONST.SITE_URL;

    app.data.lang = JSON.parse(`<?= json_encode($this->lang->language) ?>`);

    app.data.CONST.RES_SUCCESS = parseInt('<?=RES_SUCCESS?>');
    app.data.CONST.RES_ERROR_PARAMETER = parseInt('<?=RES_ERROR_PARAMETER?>');
    app.data.CONST.RES_ERROR_DB = parseInt('<?=RES_ERROR_DB?>');
    app.data.CONST.RES_ERROR_INFO_NO_EXIST = parseInt('<?=RES_ERROR_INFO_NO_EXIST?>');
    app.data.CONST.RES_ERROR_INCORRECT = parseInt('<?=RES_ERROR_INCORRECT?>');
    app.data.CONST.RES_ERROR_DUPLICATE = parseInt('<?=RES_ERROR_DUPLICATE?>');
    app.data.CONST.RES_ERROR_PRIVILEGE = parseInt('<?=RES_ERROR_PRIVILEGE?>');
    app.data.CONST.RES_ERROR_FILE_UPLOAD = parseInt('<?=RES_ERROR_FILE_UPLOAD?>');
    app.data.CONST.RES_ERROR_NO_SESSION = parseInt('<?=RES_ERROR_NO_SESSION?>');
    app.data.CONST.RES_ERROR_EMAIL_DUP = parseInt('<?=RES_ERROR_EMAIL_DUP?>');
    app.data.CONST.RES_ERROR_PHONE_DUP = parseInt('<?=RES_ERROR_PHONE_DUP?>');
    app.data.CONST.RES_ERROR_INCORRECT_EMAIL = parseInt('<?=RES_ERROR_INCORRECT_EMAIL?>');
    app.data.CONST.RES_ERROR_INCORRECT_PWD = parseInt('<?=RES_ERROR_INCORRECT_PWD?>');
    app.data.CONST.RES_ERROR_USR_BLOCK = parseInt('<?=RES_ERROR_USR_BLOCK?>');
    app.data.CONST.RES_ERROR_USR_EXIT = parseInt('<?=RES_ERROR_USR_EXIT?>');
    app.data.CONST.RES_ERROR_UNKNOWN = parseInt('<?=RES_ERROR_UNKNOWN?>');

    var b_m_selected_index = 1;

    $(document).ready(function () {
        let intervalID = setInterval(() => {
            clearInterval(intervalID);

            if (isIOS() || isAndroid()) {
                bridgeStart();
            } else {
                app.methods.navigate('/login');
            }
        }, 1000);

        hideMainBottomMenu();
    });

    function showMainBottomMenu() {
        $('.bottom-nav').removeClass('hidden');
    }

    function hideMainBottomMenu() {
        $('.bottom-nav').addClass("hidden");
    }

    function changeBottomMenuSelected(index) {
        $('.bottom-menu-item').removeClass('selected');
        b_m_selected_index = index;
        $('.bottom-menu-item').eq(index - 1).addClass("selected");
    }

    function onButtonMenu(index) {
        if(b_m_selected_index !== index) {
            changeBottomMenuSelected(index);

            switch (index) {
                case 1:
                    $('[data-name=main]').remove();
                    app.methods.navigate('/main');
                    break;
                case 2:
                    $('[data-name=refund]').remove();
                    app.methods.navigate('/refund');
                    break;
                case 3:
                    $('[data-name=mypage]').remove();
                    app.methods.navigate('/mypage');
                    break;
                default:
                    break;
            }

            showMainBottomMenu();
        }
    }

    $('#f_upload_test').change(function() {
        var input = $("#f_upload_test").get(0);
        var file = input.files[0];

        var form_data = new FormData();
        form_data.append('img', file);

        $.ajax('<?=site_url('Intro/file_upload')?>', {
            method: 'POST',
            data: form_data,
            dataType: 'json',
            processData: false,
            contentType: false,
            beforeSend: function () {

            },
            success: function (result) {
                if (Number(result.code) === 0) {
                    bridgeCallback(result.file, result.url);
                } else {
                    showToast("File upload error.");
                }
            }
        });
    })

    function checkAutoLogin(usr_type, email, pwd, fcm_token, app_ver) {
        app.data.app_version = app_ver;
        app.data.fcm_token = fcm_token;

        request('/Intro/check_login', {
            usr_type: usr_type,
            email: email,
            pwd: pwd,
            dev_type: isAndroid() ? 1 : 2,
            fcm_token: fcm_token,
        }, function(result) {
            if(result.code === 0) {
                app.data.login_user.uid = result.usr_uid;
                app.data.login_user.usr_type = usr_type;
                app.methods.navigate('/main');
            } else {
                app.methods.navigate('/login');
            }
        })
    }
</script>
</body>
</html>
