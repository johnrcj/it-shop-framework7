//Check email format
function validateEmail(email) {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
}

function validateEmailPhone(phone) {
    var re = /^[\d-]+$/;
    return re.test(String(phone).toLowerCase());
}

function validatePhone(phone) {
    var re = /^(\d{3})\-(\d{4})\-(\d{4})+$/;
    return re.test(String(phone).toLowerCase());
}

function validateDate(date) {
    var re = /^(\d{4})\-(\d\d)\-(\d\d)+$/;
    return re.test(String(date).toLowerCase());
}

//Check password format
function validPwd(pwd) {
    var matchCnt = 0;
    if(pwd.length >= 8) {
        if (pwd.length > 16) {
            return false;
        }
        var re = /^(?=.*[a-zA-Z]).+$/;  // Including english
        if(re.test(pwd)) {
            matchCnt += 1;
        }
        re = /^(?=.*[0-9]).+$/; // Including number
        if(re.test(pwd)) {
            matchCnt += 1;
        }
        re = /(?=.*[!@#$%^*+=-]).+$/; // Including special character
        if(re.test(pwd)) {
            matchCnt += 1;
        }

        return matchCnt >= 2;
    } else {
        return false;
    }
}

// Communication failure alert after api communication
function showErrorResult(resultCode) {
    switch (resultCode) {
        case 1:
            showToast(AppStrings.RESULT_DB_CONNECT_ERROR);
            break;
        case 2:
            showToast(AppStrings.RESULT_DB_OP_ERROR);
            break;
        case 3:
            showToast(AppStrings.RESULT_PARAM_ERROR);
            break;
        case 4:
            showToast(AppStrings.RESULT_NO_USER_ERROR);
            break;
        case 5:
            showToast(AppStrings.RESULT_WRONG_PASSWORD_ERROR);
            break;
        case 6:
            showToast(AppStrings.RESULT_NO_EXIST_ERROR);
            break;
        case 7:
            showToast(AppStrings.RESULT_NICKNAME_DUPLICATE_ERROR);
            break;
        case 8:
            showToast(AppStrings.RESULT_ID_DUPLICATE_ERROR);
            break;
        case 9:
            showToast(AppStrings.RESULT_EMAIL_DUPLICATE_ERROR);
            break;
        case 10:
            showToast(AppStrings.RESULT_PHONE_DUPLICATE_ERROR);
            break;
        case 11:
            showToast(AppStrings.RESULT_UPLOAD_ERROR);
            break;
        case 12:
            showToast(AppStrings.RESULT_PRIVILEGE_ERROR);
            break;
        case 13:
            showToast(AppStrings.RESULT_ZIP_NO_EXIST);
            break;
        case 14:
            showToast(AppStrings.RESULT_EXIT_USER);
            break;
        default:
            showToast(AppStrings.RESULT_DB_CONNECT_ERROR);
    }
}

function showToast(str, timeout = 3000){
    app.toast.create({
        text: str,
        position: "bottom",
        closeTimeout: timeout,
    }).open();
}

function getYearOfNow() {
    var year = 0;
    date = new Date();
    if (navigator.appName == "Netscape") {
        year = date.getYear() + 1900
    } else {
        year = date.getYear();
    }
    return(year)
}

function saveSessionData(key, value) {
    localStorage.setItem(key, value);
}

function clearSessionData(key) {
    localStorage.removeItem(key);
}

function loadSessionData(key) {
    try {
        return localStorage.getItem(key);
    } catch (e) {
        return "";
    }
}

function num_format(number) {
    let num;
    if (isNaN(number)) {
        num = number;
    } else {
        let numbers = [];
        while (number > 1000) {
            let d = number % 1000;
            numbers.splice(0, 0, d >= 100 ? d : d >= 10 ? '0' + d : '00' + d);
            number = Math.floor(number / 1000);
        }
        numbers.splice(0, 0, number);
        num = numbers.join(',');
    }
    return num;
}

function get_phone_format_str(str_phone) {
    var return_str = "";
    if(str_phone.length > 3) {
        return_str += str_phone.substr(0,3) + "-";
    } else {
        return_str = str_phone;
        return return_str;
    }

    if(str_phone.length > 7) {
        return_str += str_phone.substr(3,4) + "-";
    } else {
        return_str += str_phone.substr(3);
        return return_str;
    }

    return_str += str_phone.substr(7);
    return return_str;
}

function sprintf(template, values) {
    return template.replace(/%s/g, function() {
        return values.shift();
    });
}

function get_str_surgical_part_list(str) {
    var return_arr = Array();
    let arr_list = str.split(",");
    for (var i=0; i<arr_list.length; i++) {
        if(arr_list[i]) {
            return_arr.push(arr_list[i]);
        }
    }
    return return_arr;
}

function get_recommend_status_str(status) {
    let str_return = "";
    switch (parseInt(status)) {
        case 0:
            str_return = app.data.lang["i_am_sorry"];
            break;
        case 1:
            str_return = app.data.lang["i_recommend1"];
            break;
        case 2:
            str_return = app.data.lang["i_recommend2"];
            break;
    }
    return str_return;
}

function startShakeAnim(obj) {
    $(obj).addClass('shaker')
    $(obj).one('animationend', () => {
        $(obj).removeClass('shaker')
    })
}