
var bridgeCallback;

function bridgeStart() {
    if (isIOS() === true) {
        window.webkit.messageHandlers.bridgeLoginInfo.postMessage({});
    } else if (typeof window.shopBridge != 'undefined') {//(isAndroid() == true) {
        window.shopBridge.bridgeLoginInfo();
    }
}

function bridgeSnsInfo(type, callback) {  //type=2: kakao,
    bridgeCallback = callback;

    if (isRunningOnWeb()) {
        callback("thomas@kakao.com", "thomaskim");
        return;
    }

    if (typeof window.shopBridge != 'undefined') {
        window.shopBridge.bridgeSnsInfo(type);
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
        window.shopBridge.bridgeLoginSuccess(parseInt(usr_type), email, pwd);
    }
}

function bridgeUploadImage(type, callback) {
    bridgeCallback = callback;

    if (typeof window.shopBridge != 'undefined') {
        window.shopBridge.bridgeUploadImage(type);
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

    if (typeof window.shopBridge != 'undefined') {
        window.shopBridge.bridgeUploadImages();
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
    if (typeof window.shopBridge != 'undefined') {
        window.shopBridge.bridgeLogout();
    }
    if (typeof window.webkit != 'undefined') {
        window.webkit.messageHandlers.bridgeLogout.postMessage({});
    }
}

function bridgeGoUrl(url) {
    if (typeof window.shopBridge != 'undefined') {
        window.shopBridge.bridgeGoUrl(url);
    }
    if (typeof window.webkit != 'undefined') {
        window.webkit.messageHandlers.bridgeGoUrl.postMessage({
            "url": url
        });
    }
}

function bridgeInviteFriends(url) {
    if (typeof window.shopBridge != 'undefined') {
        window.shopBridge.bridgeInviteFriends(url);
    }
    if (typeof window.webkit != 'undefined') {
        window.webkit.messageHandlers.bridgeInviteFriends.postMessage({
            url: url
        });
    }
}

function bridgeFetchVersion(callback) {
    bridgeCallback = callback;

    if (typeof window.shopBridge != 'undefined') {
        window.shopBridge.bridgeFetchVersion();
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

    if (typeof window.shopBridge != 'undefined') {
        window.shopBridge.bridgeSendVoucher(image_urls);
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
    if (typeof window.shopBridge != 'undefined') {
        window.shopBridge.bridgeFinishApp();
    }
    if (typeof window.webkit != 'undefined') {
        window.webkit.messageHandlers.bridgeFinishApp.postMessage({});
    }
}

function bridgeResetMonthFlag() {
    if (typeof window.shopBridge != 'undefined') {
        window.shopBridge.bridgeResetMonthFlag();
    }
    if (typeof window.webkit != 'undefined') {
        window.webkit.messageHandlers.bridgeResetMonthFlag.postMessage({});
    }
}

function bridgeCall(phone) {
    if (typeof window.shopBridge != 'undefined') {
        window.shopBridge.bridgeCall(phone);
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
    if (typeof window.shopBridge != 'undefined') {
        window.shopBridge.bridgeGetLocation();
    }
    if (typeof window.webkit != 'undefined') {
        window.webkit.messageHandlers.bridgeGetLocation.postMessage({});
    }
}

function bridgeRunSms(phone) {
    if (typeof window.shopBridge != 'undefined') {
        window.shopBridge.bridgeRunSms(phone);
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
