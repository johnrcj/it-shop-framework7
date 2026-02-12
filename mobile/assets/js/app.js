// Dom7
let $$ = Dom7;

// Init App
var app = new Framework7({
    id: 'kr.co.conpang',
    root: '#app',
    theme: 'md',
    pushState: false,
    swipePanel: 'left',
    cache: false,
    data: function() {
        return {
            CONST: {},
            lang: {},
            login_user: {},
            fcm_token: "",
            prev_page_name: "",
            app_version: "",
            current_obj: null,
        }
    },
    view: {
        stackPages: true
    },
    dialog: {},
    routes: routes,
    methods: {
        navigate: function (params) {
            var view = app.view.get('.view-main');

            view.router.navigate(params)
        },
        back: function () {
            var view = app.view.get('.view-main');
            view.router.back();
        },
    }
});

var backpressed_status = false;

$$(document).on("page:init", function (e, page) {
    backpressed_status = false;
    var page_name = $('.page-current').attr("data-name");
    switch (page_name) {
        case "login":   //intro
            break;
        default:
            break;
    }
});

var loadingOverlay = function () {
    $('.loading-overlay').show();
};

var loadingOverlayRemove = function () {
    $('.loading-overlay').hide();
};


function isIOS() {
    var userAgent = navigator.userAgent.toLowerCase();

    var isIOS = ((userAgent.search("iphone") > -1) || (userAgent.search("ipod") > -1) || (userAgent.search("ipad") > -1));
    if (isIOS == true && userAgent.search("safari") > -1) {
        return false;
    }

    return isIOS;
}

function isAndroid() {
    var ua = navigator.userAgent.toLowerCase();
    var isAndroid = ua.indexOf("android") > -1;
    if (isAndroid == true && typeof conpangBridge !== 'undefined') {
        isAndroid = true;
    } else {
        isAndroid = false;
    }
    return isAndroid;
}

var App_Dlg = function () {
    return {
        alert: function (text, button, callback, background_drop = true) {
            var html = `<div class="row">
                            <div class="alert-content">` + text + `</div>
                            <div class="seperator"></div>
                            <div class="width-100 alert-button-panel">
                                <a class="link button-confirm width-100">` + button + `</a>
                            </div>
                        </div>`;

            app.dialog.create({
                cssClass: 'dialog-alert-panel',
                title: '',
                content: html,
                closeByBackdropClick: background_drop,
            }).on("opened", function (dlg) {
                $('.button-confirm', dlg.$el).on('click', function () {
                    dlg.close();
                    if (callback) {
                        callback();
                    }
                });
            }).on("close", function (dlg) {
                // if (callback) {
                //     callback();
                // }
            }).on("closed", function (dlg) {

            }).open();
        },

        confirm: function (text, okBtn, cancelBtn, callbackOk, callbackCancel, callbackClose) {
            var html = `<div class="row">
                            <div class="alert-content">` + text + `</div>
                            <div class="seperator"></div>
                            <div class="width-100 alert-button-panel">
                                <a class="link button-cancel">` + cancelBtn + `</a>
                                <a class="link button-confirm">` + okBtn + `</a>
                            </div>
                        </div>`;

            app.dialog.create({
                cssClass: 'dialog-alert-panel',
                title: '',
                content: html,
                closeByBackdropClick: true,
            }).on("opened", function (dlg) {
                $('.button-cancel', dlg.$el).on('click', function () {
                    dlg.close();
                    if (callbackCancel) {
                        callbackCancel();
                    }
                });
                $('.button-confirm', dlg.$el).on('click', function () {
                    dlg.close();
                    if (callbackOk) {
                        callbackOk();
                    }
                });
            }).on("close", function (dlg) {
                if (callbackClose) {
                    callbackClose();
                }
            }).on("closed", function (dlg) {

            }).open();
        },

        main_popup: function (info, callbackOk, callbackCancel) {
            var html = `<div class="row">
                            <div class="row popup-main-content">
                                <img class="image" data-target="` + info.link_uid + `" data-type="` + info.link_type + `" src="` + info.image_url + `">
                                <label class="content">` + info.content + `</label>
                            </div>
                            <div class="seperator"></div>
                            <div class="width-100 alert-button-panel">
                                <a class="link button-cancel">` + app.data.lang['dont_watch_today'] + `</a>
                                <a class="link button-confirm">` + app.data.lang['close'] + `</a>
                            </div>
                        </div>`;

            app.dialog.create({
                cssClass: 'dialog-alert-panel bigger',
                title: '',
                content: html,
                closeByBackdropClick: true,
            }).on("opened", function (dlg) {
                $('.image', dlg.$el).on('click', function () {
                    let link_uid = $(this).attr("data-target");
                    let link_type = $(this).attr("data-type");
                    if(parseInt(link_type) == 1) {
                        dlg.close();
                        app.methods.navigate({
                            path: '/event_detail',
                            query: {
                                event_uid: link_uid
                            }
                        });
                    } else if(parseInt(link_type) == 2) {
                        dlg.close();
                        app.methods.navigate({
                            path: '/notice_detail',
                            query: {
                                notice_uid: link_uid
                            }
                        });
                    }
                });

                $('.button-cancel', dlg.$el).on('click', function () {
                    dlg.close();
                    if (callbackCancel) {
                        callbackCancel(info.uid);
                    }
                });
                $('.button-confirm', dlg.$el).on('click', function () {
                    dlg.close();
                    if (callbackOk) {
                        callbackOk(info.uid);
                    }
                });
            }).on("close", function (dlg) {
            }).on("closed", function (dlg) {

            }).open();
        },

        picker_popup: function (str_title, arr_str_list, selected_index, callback) {
            var str_content = '<div class="popup picker-popup-panel width-100">\n' +
                '                        <div class="row">\n' +
                '                            <div class="row center picker-popup-panel-header">\n' +
                '                                <label class="center picker-popup-panel-header-title">' + str_title + '</label>\n' +
                '            <a class="popup-close picker-popup-panel-button-close link">\n' +
                '                <img src="assets/images/btn_close.png">\n' +
                '            </a>\n' +
                '                            </div>\n' +
                '                            <div class="row">\n' +
                '                                <div class="list picker-list width-100" style="margin: 0px;">\n' +
                '                                    <ul>\n';

            for (var i = 0; i < arr_str_list.length; i++) {
                if (i == selected_index) {
                    str_content += '<li class="active">\n' +
                        '               <a class="item-link popup-close picker-item" data_target="' + i + '">' + arr_str_list[i] + '</a>\n' +
                        '           </li>\n';
                } else {
                    str_content += '<li>\n' +
                        '               <a class="item-link popup-close picker-item" data_target="' + i + '">' + arr_str_list[i] + '</a>\n' +
                        '           </li>\n';
                }
            }
            str_content += '                        </ul>\n' +
                '                                </div>\n' +
                '                            </div>\n' +
                '                        </div>\n' +
                '                    </div>';

            app.popup.create({
                content: str_content,
            }).on("opened", function (dlg) {
                $(dlg.el).find('.picker-item').bind("click", function () {
                    var data_target = $(this).attr("data_target");
                    if (callback != undefined) {
                        callback(data_target);
                    }
                })
            }).open();
        },

        uploadImage: function (callback) {
            var str_content = '<div style="background-color: white;margin: auto;display: block;position: relative;padding: 0vw 3.33vw 0vw 3.33vw;">\n' +
                '                   <a style="height: 15.28vw;line-height: 15.28vw;font-size: 3.61vw !important;border-bottom: 1px solid #f3f3f3;color: #686868 !important;display: block; text-align: center" class="btn_gallery light_normal_black_font width-100 center popup-close">' + app.data.lang['gallery'] + '</a>\n' +
            '                   <a style="height: 15.28vw;line-height: 15.28vw;font-size: 3.61vw !important;color: #686868 !important;display: block; text-align: center" class="btn_camera light_normal_black_font width-100 center popup-close">' + app.data.lang['camera'] + '</a>\n' +
            '                </div>';
            app.dialog.create({
                cssClass: 'dialog-alert-panel',
                content: str_content,
                closeByBackdropClick: true,
            }).on("opened", function (dlg) {
                $(dlg.el).find('.btn_camera').bind("click", function () {
                    dlg.close();
                    callback(0);
                })

                $(dlg.el).find('.btn_gallery').bind("click", function () {
                    dlg.close();
                    callback(1);
                })
            }).open();
        },
    }
}();

function initDatePicker(id, date, callback) {
    app.calendar.create({
        inputEl: '#' + id,
        dateFormat: 'yyyy-mm-dd',
        monthNames: app.data.lang["monthnames"].split(','),
        monthNamesShort: app.data.lang["monthnames_short"].split(','),
        dayNames: app.data.lang["daynames"].split(','),
        dayNamesShort: app.data.lang["daynames_short"].split(','),
        rotateEffect: true,
        value: [
            date
        ],

        on: {
            change: function (picker, values, displayValues) {
            },
            close: function (picker) {
                callback(picker.value);
            }
        }
    });
}

function initTimePicker(id, time_type, hour, minute, callback) {
    app.picker.create({
        inputEl: '#' + id,
        cssClass: 'time_picker',
        renderToolbar: function () {
            return '<div class="toolbar">' +
                '<div class="toolbar-inner">' +
                '<div class="left">' +
                '<a href="#" class="link sheet-close popover-close btn_picker_close">Cancel</a>' +
                '</div>' +
                '<div class="right">' +
                '<a href="#" class="link sheet-close popover-close select_data_complete btn_picker_done">Select</a>' +
                '</div>' +
                '</div>' +
                '</div>';
        },
        rotateEffect: true,
        value: [
            time_type,
            hour,
            minute,
        ],

        formatValue: function (values, displayValues) {
//                        return values[0] + '.' + getFormatDayString((parseInt(values[1]) + 1)) + '.' + getFormatDayString(values[2]);
        },

        cols: [
            // time_type
            {
                values: ["AM", "PM"],
            },
            // hour
            {
                values: ('01 02 03 04 05 06 07 08 09 10 11 12').split(' '),
            },
            // Divider
            {
                divider: true,
                content: ':'
            },
            // minute
            {
                values: (function () {
                    var arr = [];
                    for (var i = 0; i <= 59; i++) {
                        arr.push(getFormatDayString(i));
                    }
                    return arr;
                })(),
            },
        ],
        on: {
            open: function (picker) {
                picker.$el.find('.select_data_complete').on('click', function () {
                    if (callback != undefined) {
                        callback(picker.cols[0].value + ' ' + picker.cols[1].value + ":" + picker.cols[3].value, picker.cols[1].value + ":" + picker.cols[3].value, picker.cols[0].value);
                    }
                });
            },

            change: function (picker, values, displayValues) {
            },
        }
    });
}

function initSelectPicker(id, values, displayValues, defValue, callback) {
    if (defValue !== null) {
        app.picker.create({
            inputEl: '#' + id,
            toolbar: false,
            rotateEffect: true,
            value: [
                defValue
            ],
            cols: [
                {
                    textAlign: 'center',
                    values: displayValues,
                    keyValues: values,
                }
            ],
            on: {
                open: function (picker) {
                    picker.$el.find('.select_data_complete').on('click', function () {
                        if (callback !== undefined && picker.cols.length > 0) {
                            callback(picker.cols[0].keyValues[picker.cols[0].activeIndex], picker.cols[0].values[picker.cols[0].activeIndex]);
                        }
                    });
                },

                change: function (picker, values, keyValues) {
                    if (callback !== undefined && picker.cols.length > 0) {
                        callback(picker.cols[0].keyValues[picker.cols[0].activeIndex], picker.cols[0].values[picker.cols[0].activeIndex]);
                    }
                },
            }
        });
    } else {
        app.picker.create({
            inputEl: '#' + id,
            toolbar: false,
            rotateEffect: true,
            cols: [
                {
                    textAlign: 'center',
                    values: displayValues,
                    keyValues: values,
                }
            ],
            on: {
                open: function (picker) {
                    picker.$el.find('.select_data_complete').on('click', function () {
                        if (callback !== undefined && picker.cols.length > 0) {
                            callback(picker.cols[0].keyValues[picker.cols[0].activeIndex], picker.cols[0].values[picker.cols[0].activeIndex]);
                        }
                    });
                },

                change: function (picker, values, keyValues) {
                    if (callback !== undefined && picker.cols.length > 0) {
                        callback(picker.cols[0].keyValues[picker.cols[0].activeIndex], picker.cols[0].values[picker.cols[0].activeIndex]);
                    }
                },
            }
        });
    }
}

function initAlarmPicker(id, day, noon, hour, callback) {
    daystr = app.data.lang["prev_day"];
    hourstr = app.data.lang["hour"];
    app.picker.create({
        inputEl: '#' + id,
        rotateEffect: true,
        openIn: 'sheet',
        renderToolbar: function () {
            return '<div class="toolbar">' +
                '<div class="toolbar-inner">' +
                // '<span>' + app.data.lang["alarm_question"] + '</span>' +
                '<div class="left">' +
                '<a href="#" class="link cancel sheet-close popover-close">' + app.data.lang["cancel"] + '</a>' +
                '</div>' +
                '<div class="right">' +
                '<a href="#" class="link confirm sheet-close popover-close">' + app.data.lang["confirm"] + '</a>' +
                '</div>' +
                '</div>' +
                '</div>';
        },
        value: [
            (day + 1).toString() + daystr,
            noon === 0 ? app.data.lang["am"] : app.data.lang["pm"],
            hour.toString() + hourstr,
        ],
        cols: [
            {
                values: [
                    '1' + daystr,
                    '2' + daystr,
                    '3' + daystr,
                    '4' + daystr,
                    '5' + daystr,
                    '6' + daystr,
                    '7' + daystr,
                    '8' + daystr,
                    '9' + daystr,
                    '10' + daystr,
                ],
            },
            {
                values: [
                    app.data.lang["am"],
                    app.data.lang["pm"],
                ],
                textAlign: 'center',
            },
            {
                values: [
                    '0' + hourstr,
                    '1' + hourstr,
                    '2' + hourstr,
                    '3' + hourstr,
                    '4' + hourstr,
                    '5' + hourstr,
                    '6' + hourstr,
                    '7' + hourstr,
                    '8' + hourstr,
                    '9' + hourstr,
                    '10' + hourstr,
                    '11' + hourstr,
                ],
            },
        ],
        on: {
            open: function (picker) {
                picker.$el.find('.confirm').on('click', function () {
                    if (callback !== undefined) {
                        callback(picker.cols[0].activeIndex, picker.cols[1].activeIndex, picker.cols[2].activeIndex);
                    }
                });

                picker.$el.find('.cancel').on('click', function () {
                });
            },
        }
    })
}

function getFormatDayString(value) {
    if (parseInt(value) < 10) {
        return "0" + value;
    } else {
        return value;
    }
}

function request(url, data, successCallback, errorCallback, method='post') {
    app.request({
        method: method,
        url: app.data.CONST.API_URL + url,
        data: data,
        dataType: "json",
        beforeSend: function beforeSend(xhr) {
            app.preloader.show();
        },
        error: function error(xhr) {
            //   $formEl.trigger('formajax:error', { data: data, xhr: xhr });
            //   app.emit('formAjaxError', $formEl[0], data, xhr);
            console.log(xhr);
            showToast(xhr.statusText);
            if (typeof errorCallback != 'undefined') {
                errorCallback();
            }
//            alert(app.data.lang["unknown_error"]);
        },
        complete: function complete(xhr) {
            //   $formEl.trigger('formajax:complete', { data: data, xhr: xhr });
            //   app.emit('formAjaxComplete', $formEl[0], data, xhr);
            app.preloader.hide();
        },
        success: function success(response, status, xhr) {
            console.log(response);
            //   $formEl.trigger('formajax:success', { data: data, xhr: xhr });
            //   app.emit('formAjaxSuccess', $formEl[0], data, xhr);
            //     response = JSON.parse(response);
            if (response.code === app.data.CONST.RES_ERROR_NO_SESSION) {
                showToast(response.msg);
                let timeoutID = setTimeout(() => {
                    app.methods.navigate('/login/');
                }, 500);
                return;
            }
            if (!successCallback(response)) {
                return;
            }

            console.log(response.code, app.data.CONST.RES_SUCCESS);
            if (response.code !== app.data.CONST.RES_SUCCESS) {
                showToast(response.msg);
            }
        },
    });
}

function isRunningOnWeb() {
    if (typeof window.conpangBridge != 'undefined') {
        return false;
    }
    return typeof window.webkit == 'undefined';
}

function waitUntilPageRefresh(page, callback) {
    let intervalID = setInterval(function () {
        clearInterval(intervalID);

        if ($(page.target).hasClass('page-current')) {
            callback();
        }
    }, 1000);
}


function mobileAutoLoginCheck(usr_type, email, pwd, fcm_token, app_ver) {
    checkAutoLogin(usr_type, email, pwd, fcm_token, app_ver);
}


var backpressed_status = false;
function mobileBackPressed() {
    var modal_show_status = 0;
    $('.modal-in').each(function () {
        if ($(this).hasClass("toast") === false) {
            modal_show_status++;
        }
    })

    if (modal_show_status > 0) {
        backpressed_status = false;
        $('.popup-backdrop').each(function () {
            $(this).trigger("click");
        })
        $('.dialog-backdrop').each(function () {
            $(this).trigger("click");
        })
    } else {
        var page_name = $('.page-current').attr("data-name");

        if (page_name === "main" || page_name === "login") {
            if (page_name === "main") {
                var select_mode = $('.page-current').attr("select-mode");
                if (select_mode) {
                    app.data.current_obj.getOutSelectMode();
                    return;
                }
                var search_mode = $('.page-current').attr("search-mode");
                if (search_mode) {
                    app.data.current_obj.getOutSearchMode();
                    return;
                }
            }

            if (backpressed_status === false) {
                backpressed_status = true;
                showToast(app.data.lang['app_finish']);
            } else {
                var toast_show_status = 0;
                $('.toast-content').each(function () {
                    toast_show_status++;
                })

                if (toast_show_status === 0) {
                    backpressed_status = true;
                    showToast(app.data.lang['app_finish']);
                } else {
                    backpressed_status = false;
                    bridgeFinishApp();
                }
            }
        } else if (page_name === "refund" || page_name === "mypage") {
            onButtonMenu(1);
        } else if(page_name === "refund_settlement") {
        } else if(page_name === "info_modify" || page_name === "qna_write") {
            app.data.current_obj.onBack();
        } else {
            back();
        }
    }
}

/// page components
function back() {
    backPage();
}


function backPage() {
    var view = app.view.get('.view-main');
    var pageName = view.router.$el.children('.page.page-current').attr('data-name');

    var prev_page_name = $('.page-previous:last').attr("data-name");
    app.data.prev_page_name = prev_page_name;
    view.router.back();
}

function stepBack(step) {
    var view = app.view.get('.view-main');
    view.router.history.splice(view.router.history.length-step, step);

    for (let i = 0; i < step; i++) {
        $(".page:last-child").remove();
    }
}

function clearAndNavigateLoginPage() {
    var view = app.views.create('.view-main');
    $('.page').remove();

    app.methods.navigate('/login');
}

function clearAndLoadNewPage(page_url) {
    var view = app.views.create('.view-main');
    var options = {
        url: page_url,
    };

    view.router.load(options);
}

function clearAndLoadNewPageWithoutAnimate(page_url) {
    var view = app.views.create('.view-main');
    var options = {
        url: page_url,
        w: false,
    };

    view.router.load(options)
}

function loadPage(page_url) {
    var view = app.view.get('.view-main');
    var options = {
        url: page_url,
    };

    view.router.load(options);
}

function loadComponentPage(page_url) {
    var view = app.view.get('.view-main');
    view.router.removeFromXhrCache(page_url);

    var real_url = page_url.split('?')[0];

    var options = {
        componentUrl: real_url,
        url: page_url,
    };

    view.router.load(options);
}

function clearAndLoadComponentPage(page_url) {
    $('.page-previous').remove();
    // $('.page-current').remove();
    var view = app.view.get('.view-main');
    view.router.clearPreviousHistory();
    view.router.removeFromXhrCache(page_url);

    var real_url = page_url.split('?')[0];

    var options = {
        componentUrl: real_url,
        url: page_url,
    };

    view.router.load(options);
}


function replaceComponentPage(page_url) {
    $('.page-current').remove();
    var view = app.view.get('.view-main');
    view.router.removeFromXhrCache(page_url);

    var real_url = page_url.split('?')[0];

    var options = {
        componentUrl: real_url,
        url: page_url,
    };

    view.router.load(options);
}

function goPrevPage(){
    var view = app.view.get('.view-main');
    view.router.back({
        force: true,
        ignoreCache: true
    });
}

function goPrevPageWithoutRefresh() {
    var view = app.view.get('.view-main');
    view.router.back({
        force: false,
        ignoreCache: false
    });
}
