
var bridgeCallback;

function bridgeStart() {
    if (isIOS() === true) {
        window.webkit.messageHandlers.bridgeLoginInfo.postMessage({});
    } else if (typeof window.conpangBridge != 'undefined') {//(isAndroid() == true) {
        window.conpangBridge.bridgeLoginInfo();
    }
}

function bridgeSnsInfo(type, callback) {  //type=2: kakao,
    bridgeCallback = callback;

    if (isRunningOnWeb()) {
        callback("thomas@kakao.com", "thomaskim");
        return;
    }

    if (typeof window.conpangBridge != 'undefined') {
        window.conpangBridge.bridgeSnsInfo(type);
    }

    if (typeof window.webkit != 'undefined') {
        window.webkit.messageHandlers.bridgeSnsInfo.postMessage({
            type: type,
        });
    }
}

function bridgeAppleInfo(callback) {  //type=3 apple login
    bridgeCallback = callback;

    if (isRunningOnWeb()) {
        callback("thomas@apple.com", "thomaskim");
        return;
    }

    if (typeof window.webkit != 'undefined') {
        window.webkit.messageHandlers.bridgeAppleInfo.postMessage({});
    }
}

function bridgeLoginSuccess(usr_type, email, pwd) {
    if (isIOS()) {
        window.webkit.messageHandlers.bridgeLoginSuccess.postMessage({
            usr_type: parseInt(usr_type),
            email: email,
            pwd: pwd
        });
    } else if (isAndroid() === true) {
        window.conpangBridge.bridgeLoginSuccess(parseInt(usr_type), email, pwd);
    }
}

function bridgeUploadImage(type, callback) {
    bridgeCallback = callback;

    if (typeof window.conpangBridge != 'undefined') {
        window.conpangBridge.bridgeUploadImage(type);
    }
    if (typeof window.webkit != 'undefined') {
        window.webkit.messageHandlers.bridgeUploadImage.postMessage({
            type: type
        });
    }

    if (isRunningOnWeb()) {
        $('#f_upload_test').trigger("click");
        return;
    }
}

function bridgeUploadImages(callback) {
    bridgeCallback = callback;

    if (typeof window.conpangBridge != 'undefined') {
        window.conpangBridge.bridgeUploadImages();
    }
    if (typeof window.webkit != 'undefined') {
        window.webkit.messageHandlers.bridgeUploadImages.postMessage({
        });
    }

    if (isRunningOnWeb()) {
        $('#f_upload_test').trigger("click");
        return;
    }
}

function bridgeLogout() {
    if (typeof window.conpangBridge != 'undefined') {
        window.conpangBridge.bridgeLogout();
    }
    if (typeof window.webkit != 'undefined') {
        window.webkit.messageHandlers.bridgeLogout.postMessage({});
    }
}

function bridgeGoUrl(url) {
    if (typeof window.conpangBridge != 'undefined') {
        window.conpangBridge.bridgeGoUrl(url);
    }
    if (typeof window.webkit != 'undefined') {
        window.webkit.messageHandlers.bridgeGoUrl.postMessage({
            "url": url
        });
    }
}

function bridgeInviteFriends(url) {
    if (typeof window.conpangBridge != 'undefined') {
        window.conpangBridge.bridgeInviteFriends(url);
    }
    if (typeof window.webkit != 'undefined') {
        window.webkit.messageHandlers.bridgeInviteFriends.postMessage({
            url: url
        });
    }
}

function bridgeFetchVersion(callback) {
    bridgeCallback = callback;

    if (typeof window.conpangBridge != 'undefined') {
        window.conpangBridge.bridgeFetchVersion();
    }
    if (typeof window.webkit != 'undefined') {
        window.webkit.messageHandlers.bridgeFetchVersion.postMessage({
        });
    }

    if (isRunningOnWeb()) {
        callback("0.0.1");
        return ;
    }
}

function bridgeSendVoucher(image_urls, callback) {
    bridgeCallback = callback;

    if (typeof window.conpangBridge != 'undefined') {
        window.conpangBridge.bridgeSendVoucher(image_urls);
    }

    if (typeof window.webkit != 'undefined') {
        window.webkit.messageHandlers.bridgeSendVoucher.postMessage({
            urls: image_urls,
        });
    }

    if (isRunningOnWeb()) {
        callback("1");
        return ;
    }
}

function bridgeFinishApp() {
    if (typeof window.conpangBridge != 'undefined') {
        window.conpangBridge.bridgeFinishApp();
    }
    if (typeof window.webkit != 'undefined') {
        window.webkit.messageHandlers.bridgeFinishApp.postMessage({});
    }
}

function bridgeResetMonthFlag() {
    if (typeof window.conpangBridge != 'undefined') {
        window.conpangBridge.bridgeResetMonthFlag();
    }
    if (typeof window.webkit != 'undefined') {
        window.webkit.messageHandlers.bridgeResetMonthFlag.postMessage({});
    }
}

function bridgeCall(phone) {
    if (typeof window.conpangBridge != 'undefined') {
        window.conpangBridge.bridgeCall(phone);
    }
    if (typeof window.webkit != 'undefined') {
        window.webkit.messageHandlers.bridgeCall.postMessage({
            "phone": phone
        });
    }
}

function bridgeGetLocation(callback) {
    if (isRunningOnWeb()) {
        callback(JSON.stringify({latitude: '30.11', longitude: '31.111'}));
        return;
    }

    bridgeCallback = callback;
    if (typeof window.conpangBridge != 'undefined') {
        window.conpangBridge.bridgeGetLocation();
    }
    if (typeof window.webkit != 'undefined') {
        window.webkit.messageHandlers.bridgeGetLocation.postMessage({});
    }
}

function bridgeRunSms(phone) {
    if (typeof window.conpangBridge != 'undefined') {
        window.conpangBridge.bridgeRunSms(phone);
    }
    if (typeof window.webkit != 'undefined') {
        window.webkit.messageHandlers.bridgeRunSms.postMessage({
            "phone": phone
        });
    }
}

function bridgeBackPressed() {
    back();
}
