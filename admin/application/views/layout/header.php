<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<html lang="en">
<head>
    <head>
        <meta charset="utf-8" />
        <title><?=$this->lang->line("app_name")?></title>
        <link href="<?=base_url()?>assets/images/logo2.png" rel="icon">
        <link href="<?=base_url()?>assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?=base_url()?>assets/global/css/components.css" rel="stylesheet" id="style_components" type="text/css" />
        <link href="<?=base_url()?>assets/pages/css/login.css" rel="stylesheet" type="text/css" />
        <link href="<?=base_url()?>assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css" />
        <link href="<?=base_url()?>assets/layouts/layout/css/themes/darkblue.min.css" rel="stylesheet" type="text/css" id="style_color" />
        <link href="<?=base_url()?>assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <link href="<?=base_url()?>assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
        <link href="<?=base_url()?>assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
        <link href="<?=base_url()?>assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?=base_url()?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" type="text/css" />
        <link href="<?=base_url()?>assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
        <link href="<?=base_url()?>assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css" rel="stylesheet" type="text/css" />
        <link href="<?=base_url()?>assets/global/css/plugins.min.css" rel="stylesheet" type="text/css" />
        <link href="<?=base_url()?>assets/layouts/layout/css/layout.min.css" rel="stylesheet" type="text/css" />
        <link href="<?=base_url()?>assets/layouts/layout/css/custom.min.css" rel="stylesheet" type="text/css" />
        <link href="<?=base_url()?>assets/css/custom.css?time=<?=time()?>" rel="stylesheet" type="text/css" />
        <link href="<?=base_url()?>assets/global/plugins/bootstrap-summernote/summernote.css" rel="stylesheet" type="text/css" />
        <link href="<?=base_url()?>assets/global/plugins/bootstrap-toastr/toastr.min.css" rel="stylesheet" type="text/css" />
        <link href="<?=base_url()?>assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="<?=base_url()?>assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?=base_url()?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />
        <script src="<?=base_url()?>assets/global/plugins/jquery.min.js" type="text/javascript"></script>
        <script src="<?=base_url()?>assets/js/jquery.form.js" type="text/javascript"></script>
        <script src="<?=base_url()?>assets/global/plugins/bootbox/bootbox.min.js" type="text/javascript"></script>
        <link href="<?=base_url()?>assets/global/plugins/jquery-multi-select/css/multi-select.css" rel="stylesheet" type="text/css" />
        <style>
            .padding_1 {
                padding: 1px !important;
            }

            .align_left {
                text-align: left !important;
            }

            .mg_right_15 {
                margin-right: 15px;
            }

            #ms-child_category {
                width: 520px;
            }

            #ms-child_category .ms-selectable {
                float: right !important;
            }

            #ms-child_category .ms-selectable .ms-list {
                height: 300px;
            }

            #ms-child_category .ms-selection {
                float: left !important;
            }

            #ms-child_category .ms-selection .ms-list {
                height: 300px;
            }

            .single_line {
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                display: block;
            }

            .left_td {
                text-align: left !important;
            }

            .item_part {
                border: 1px solid grey;
                border-radius: 15px !important;
                padding: 5px 15px;
                margin-right: 5px;
                margin-bottom: 0 !important;
            }
        </style
    </head>
<body class="page-header-fixed page-sidebar-closed-hide-logo page-content-white  page-sidebar-fixed" id="total_body">
<div class="page-wrapper">
    <div class="page-header navbar navbar-fixed-top">
        <div class="page-header-inner ">
            <div class="page-logo" style="">
               <a style="color: white;font-weight: 600;font-size: 20px;margin: auto;height: 50px;line-height: 50px;text-decoration: unset"><?=$this->lang->line("app_name")?></a>
            </div>
            <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
                <span></span>
             </a>
            <div class="top-menu">
                <ul class="nav navbar-nav pull-right">
                    <li class="dropdown dropdown-user">
                        <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                            <span class="username username-hide-on-mobile"> <span class="headerbar_admin_id"><?=$manager_name?></span></span>
                            <i class="fa fa-angle-down"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-default">
                            <li>
                                <a onclick="ShowManagerSettingDialog()">
                                    <i class="icon-user"></i> <?=$this->lang->line("setting")?> </a>
                            </li>
                            <li>
                                <a href="<?=site_url('Login/logout')?>">
                                    <i class="icon-logout"></i> <?=$this->lang->line("logout")?> </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="clearfix"> </div>
    <div class="page-container">
        <div class="page-sidebar-wrapper">
            <div class="page-sidebar navbar-collapse collapse">
                <ul class="page-sidebar-menu  page-header-fixed " data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">
                    <li class="nav-item" id="left_menu_user_manage">
                        <a href="<?=site_url('User/member_list')?>" class="nav-link nav-toggle">
                            <i class="fa fa-genderless"></i>
                            <span class="title"><?=$this->lang->line("member_manage")?></span>
                            <span class=""></span>
                        </a>
                    </li>
                    <li class="nav-item" id="left_menu_voucher_manage">
                        <a href="<?=site_url('Voucher/category_list')?>" class="nav-link nav-toggle">
                            <i class="fa fa-genderless"></i>
                            <span class="title"><?=$this->lang->line("voucher_manage")?></span>
                            <span class=""></span>
                        </a>
                    </li>
                    <li class="nav-item" id="left_menu_notice_manage">
                        <a href="<?=site_url('Notice/notice_list')?>" class="nav-link nav-toggle">
                            <i class="fa fa-genderless"></i>
                            <span class="title"><?=$this->lang->line("notice_manage")?></span>
                            <span class=""></span>
                        </a>
                    </li>
                    <li class="nav-item" id="left_menu_qna_manage">
                        <a href="<?=site_url('Qna/qna_list')?>" class="nav-link nav-toggle">
                            <i class="fa fa-genderless"></i>
                            <span class="title"><?=$this->lang->line("qna_manage")?></span>
                            <span class=""></span>
                        </a>
                    </li>
                    <li class="nav-item" id="left_menu_terms_manage">
                        <a href="<?=site_url('Terms/terms_list')?>" class="nav-link nav-toggle">
                            <i class="fa fa-genderless"></i>
                            <span class="title"><?=$this->lang->line("terms_manage")?></span>
                            <span class=""></span>
                        </a>
                    </li>
                    <li class="nav-item" id="left_menu_warning_manage">
                        <a href="<?=site_url('Warning/warning_list')?>" class="nav-link nav-toggle">
                            <i class="fa fa-genderless"></i>
                            <span class="title"><?=$this->lang->line("warning_manage")?></span>
                            <span class=""></span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="page-bar" style="display: flex;align-items: center">
                    <ul class="page-breadcrumb">
                        <i class="fa fa-circle" style="color: #f7941d"></i>
                        <span id="page_title" style="font-weight: 700;font-size: 15px;color:#f7941d"></span>
                    </ul>

                    <div id="event_top_guide_btn_div" class="hide" style="margin-left: auto;display: flex">
                    </div>
                </div>
                