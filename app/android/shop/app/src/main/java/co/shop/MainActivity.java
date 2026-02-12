package co.shop;

import static co.shop.Constants.FILE_PICK_REQUEST;
import static co.shop.Constants.REQUEST_PERMISSION_CODE;
import static co.shop.Constants.SERVER_URL;

import android.Manifest;
import android.annotation.SuppressLint;
import android.app.Activity;
import android.app.AlertDialog;
import android.content.ClipData;
import android.content.ClipboardManager;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.pm.PackageInfo;
import android.content.pm.PackageManager;
import android.graphics.Bitmap;
import android.net.Uri;
import android.os.AsyncTask;
import android.os.Build;
import android.os.Bundle;
import android.os.Environment;
import android.support.annotation.NonNull;
import android.support.v4.app.ActivityCompat;
import android.support.v4.content.ContextCompat;
import android.support.v7.app.AppCompatActivity;
import android.util.Log;
import android.view.KeyEvent;
import android.webkit.CookieManager;
import android.webkit.DownloadListener;
import android.webkit.JavascriptInterface;
import android.webkit.JsResult;
import android.webkit.WebChromeClient;
import android.webkit.WebResourceRequest;
import android.webkit.WebSettings;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.widget.Toast;

import com.loopj.android.http.BuildConfig;
import com.loopj.android.http.JsonHttpResponseHandler;
import com.loopj.android.http.RequestParams;
import com.loopj.android.http.SyncHttpClient;

import org.json.JSONException;
import org.json.JSONObject;
import org.jsoup.Jsoup;
import org.jsoup.nodes.Document;
import org.jsoup.select.Elements;

import java.io.File;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.util.ArrayList;
import java.util.regex.Pattern;

import cz.msebera.android.httpclient.Header;
import co.shop.pref.PrefMgr;
import co.shop.util.Downloader;
import co.shop.util.ImageFilePathUtil;
import co.shop.util.Util;


public class MainActivity extends AppCompatActivity {
    private final String TAG = "MainActivity";
    private WebView mWebView;
    private JSFunctions mJSFunctions = new JSFunctions();

    String strURL = "";

    String[] permissions = {
            Manifest.permission.READ_EXTERNAL_STORAGE,
            Manifest.permission.WRITE_EXTERNAL_STORAGE,
//            Manifest.permission.ACCESS_COARSE_LOCATION,
//            Manifest.permission.ACCESS_FINE_LOCATION,
            Manifest.permission.CAMERA,
            Manifest.permission.INTERNET,
//            Manifest.permission.READ_PHONE_STATE
    };

    private final static int PHONECALL_RESULTCODE = 2;

    private MediaManager mMediaManager;
    public ArrayList<String> selectedImages = new ArrayList<String>();
    public String uploadedPaths = "";

    private boolean mFinish = false;

    private int mViewType = 0;

    private static final String FILE_NAME = "coffing";
    public static double lat = 0.0;
    public static double lon = 0.0;
    public static String place = "";

    public String otherPhone = "";

    Downloader downloader = null;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
//        getWindow().setFlags(WindowManager.LayoutParams.FLAG_FULLSCREEN, WindowManager.LayoutParams.FLAG_FULLSCREEN);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        mWebView = findViewById(R.id.webview);

        downloader = new Downloader(this);

        ActivityCompat.requestPermissions(this, permissions, REQUEST_PERMISSION_CODE);
    }

    @Override
    public void onActivityResult(int requestCode, int resultCode, Intent data) {
        switch (requestCode) {
            case MediaManager.SET_CAMERA_IMAGE:
            case MediaManager.SET_CAMERA_VIDEO:
            case MediaManager.CROP_IMAGE:
                mMediaManager.onActivityResult(requestCode, resultCode, data);
                break;
            case MediaManager.SET_GALLERY_IMAGE:
            case MediaManager.SET_GALLERY_VIDEO:
//                selectedImages.clear();
//                if (data.getData() != null) {
//                    Uri mImageUri = data.getData();
//                    String path = ImageFilePathUtil.getPath(this, mImageUri);
//                    selectedImages.add(path);
//                    Log.d(TAG, "single image: " + path);
//                    addImage();
//                }
                mMediaManager.onActivityResult(requestCode, resultCode, data);
                break;
            case MediaManager.SET_MULTI_GALLERY_IMAGE:
                if (resultCode == RESULT_OK) {
                    selectedImages.clear();
                    // retrieve a collection of selected images
                    if (data.getClipData() != null) {
                        ClipData clipData = data.getClipData();
                        Uri[] uris = new Uri[clipData.getItemCount()];
                        for (int i = 0; i < uris.length; i++) {
                            String path = ImageFilePathUtil.getPath(this, clipData.getItemAt(i).getUri());
                            selectedImages.add(path);
                            Log.d(TAG, "multi images: " + path);
                        }
                        addImages();
                    } else if (data.getData() != null) {
                        Uri mImageUri = data.getData();
                        String path = ImageFilePathUtil.getPath(this, mImageUri);
                        selectedImages.add(path);
                        Log.d(TAG, "single image: " + path);
                        addImage();
                    } else {
                        Log.d(TAG, "single image no");
                        mJSFunctions.uploadedFiles("");
                    }
                } else {
                    Log.d(TAG, "single image no");
                    mJSFunctions.uploadedFiles("");
                }
                break;
            case FILE_PICK_REQUEST:
                if (resultCode == RESULT_OK) {
                    Uri uri = data.getData();
                    String filePath = ImageFilePathUtil.getPath(this, uri);

                    if (filePath != null) {
                        selectedImages.clear();
                        selectedImages.add(filePath);
                        addImage();

                    } else { // android 8.0 에서 이상한 파일들이 recent 목록에 추가되는 현상이 있는것과 관련하여
                        Toast.makeText(MainActivity.this, R.string.no_real_file, Toast.LENGTH_SHORT).show();
                    }
                }
            default:
                break;
        }
    }

    @Override
    public void onRequestPermissionsResult(int requestCode, @NonNull String[] permissions, @NonNull int[] grantResults) {
        super.onRequestPermissionsResult(requestCode, permissions, grantResults);

        switch (requestCode) {
            case REQUEST_PERMISSION_CODE:
                if (isAllPermissionGranted(grantResults)) {
                    initApp();
                } else {
                    Toast.makeText(MainActivity.this, "You must agree to permission setting to use this app.", Toast.LENGTH_SHORT).show();
                    finish();
                }
                break;
            case MediaManager.REQUEST_PERMISSION_ALBUM:
            case MediaManager.REQUEST_PERMISSION_CAMERA:
                if (isAllPermissionGranted(grantResults)) {
                    mMediaManager.onRequestPermissionsResult(requestCode, permissions, grantResults);
                } else {
                    Toast.makeText(MainActivity.this, "You must agree to permission setting to use this app.", Toast.LENGTH_SHORT).show();
                    finish();
                }
                break;
            case Downloader.PERMISSION_WRITE_EXTERNAL_STORAGE:
                if (grantResults.length > 0) {
                    if (downloader != null) {
                        downloader.startDownload();
                    }
                }
        }
    }

    @Override
    public boolean onKeyDown(int keyCode, KeyEvent event) {
        if (keyCode == KeyEvent.KEYCODE_BACK) {
            mJSFunctions.backPressed();
            return false;
        }

        return super.onKeyDown(keyCode, event);
    }

    @Override
    public void onBackPressed() {
//        super.onBackPressed();
        mJSFunctions.backPressed();
    }

//    void download(String url, String dstPath) {
//        downloader.setInfo(dstPath, url, false);
//        downloader.setDownloadListener(new Downloader.DownloadListener() {
//            @Override
//            public void success() {
//                mJSFunctions.downloadDone();
//            }
//        });
//        downloader.startDownload();
//    }

    private void initApp() {
        if (mMediaManager == null) {
            mMediaManager = new MediaManager(this);
            mMediaManager.setMediaCallback(new MediaManager.MediaCallback() {
                @Override
                public void onSelected(Boolean isVideo, File file, Bitmap bitmap, String videoPath, String thumbPath) {
                    if (file != null) {
                        selectedImages = new ArrayList<>();
                        selectedImages.add(file.getPath());
                        addImage();
                    }
                }

                @Override
                public void onFailed(int code, String err) {

                }

                @Override
                public void onDelete() {

                }
            });
        }

        initWebView();
    }

    private void initWebView() {
        mWebView.getSettings().setJavaScriptCanOpenWindowsAutomatically(true);
        mWebView.getSettings().setJavaScriptEnabled(true);

        mWebView.getSettings().setSavePassword(false);
        mWebView.getSettings().setAppCacheEnabled(false);

        mWebView.getSettings().setSupportMultipleWindows(true);
        mWebView.getSettings().setDomStorageEnabled(true);

        mWebView.getSettings().setLoadWithOverviewMode(true);
        mWebView.getSettings().setUseWideViewPort(true);
        mWebView.setScrollBarStyle(WebView.SCROLLBARS_OUTSIDE_OVERLAY);
        mWebView.setScrollbarFadingEnabled(true);

        mWebView.setFocusable(true);
        mWebView.setFocusableInTouchMode(true);
        mWebView.getSettings().setBuiltInZoomControls(false);
        mWebView.getSettings().setSupportZoom(false);

        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.LOLLIPOP) {
            WebSettings webViewSettings = mWebView.getSettings();
            webViewSettings.setMixedContentMode(WebSettings.MIXED_CONTENT_ALWAYS_ALLOW);
            CookieManager cookieManager = CookieManager.getInstance();
            cookieManager.setAcceptCookie(true);
            cookieManager.setAcceptThirdPartyCookies(mWebView, true);
        }

        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.KITKAT) {
            WebView.setWebContentsDebuggingEnabled(true);
        }

        mWebView.setDownloadListener(new DownloadListener() {
            @Override
            public void onDownloadStart(String url, String userAgent, String contentDisposition, String mimetype, long contentLength) {
//                Uri source = Uri.parse(url);
//
//                DownloadManager.Request request = new DownloadManager.Request(source);
//                String cookie = CookieManager.getInstance().getCookie(url);
//
//                request.setMimeType(mimetype);
//                request.addRequestHeader("User-Agent", userAgent);
//                request.addRequestHeader("Cookie", cookie);
//                request.allowScanningByMediaScanner();
//                request.setNotificationVisibility(DownloadManager.Request.VISIBILITY_VISIBLE_NOTIFY_COMPLETED);
//                request.setDestinationInExternalPublicDir(Environment.DIRECTORY_DOWNLOADS, URLUtil.guessFileName(url, contentDisposition, mimetype));
//
//                DownloadManager manager = (DownloadManager) getSystemService(DOWNLOAD_SERVICE);
//                manager.enqueue(request);

                String dstPath = Environment.getExternalStorageDirectory() + File.separator +
                        Environment.DIRECTORY_DOWNLOADS + File.separator +
                        url.substring(url.lastIndexOf("/") + 1);
                downloader.setInfo(dstPath, url, false);

                downloader.startDownload();
            }
        });

        mWebView.setWebViewClient(new WebViewClient() {
            @Override
            public boolean shouldOverrideUrlLoading(WebView view, WebResourceRequest request) {
                return super.shouldOverrideUrlLoading(view, request);
            }

            @Override
            public void onPageStarted(WebView view, String url, Bitmap favicon) {
                super.onPageStarted(view, url, favicon);
            }

            @Override
            public void onPageFinished(WebView view, String url) {
                super.onPageFinished(view, url);
            }
        });

        mWebView.setWebChromeClient(new WebChromeClient() {
            @Override
            public boolean onJsAlert(WebView view, String url, String message, final JsResult result) {
                if (!message.trim().isEmpty()) {
                    new android.support.v7.app.AlertDialog.Builder(MainActivity.this)
                            .setMessage(message)
                            .setPositiveButton("확인", new DialogInterface.OnClickListener() {
                                @Override
                                public void onClick(DialogInterface dialog, int which) {
                                    result.confirm();
                                }
                            })
                            .setCancelable(false)
                            .create()
                            .show();
                }
                return true;
            }

            @Override
            public boolean onJsConfirm(WebView view, String url,
                                       String message, final JsResult result) {
                if (!message.trim().isEmpty()) {
                    new android.support.v7.app.AlertDialog.Builder(MainActivity.this)
                            .setMessage(message)
                            .setPositiveButton("확인",
                                    new AlertDialog.OnClickListener() {
                                        public void onClick(
                                                DialogInterface dialog,
                                                int which) {
                                            result.confirm();

                                        }
                                    })
                            .setNegativeButton("취소",
                                    new AlertDialog.OnClickListener() {
                                        @Override
                                        public void onClick(DialogInterface dialog, int which) {
                                            result.cancel();
                                        }
                                    })
                            .setCancelable(false)
                            .create()
                            .show();
                }
                return true;
            }
        });

        mWebView.addJavascriptInterface(new ContentManager(getApplicationContext()), "shopBridge");
        mWebView.loadUrl(Constants.SERVER_URL);
    }

    public void call() {
        Intent intent = new Intent(Intent.ACTION_CALL);
        intent.setData(Uri.parse("tel:" + otherPhone));

        if (ActivityCompat.checkSelfPermission(this, Manifest.permission.CALL_PHONE) != PackageManager.PERMISSION_GRANTED) {
            if (ActivityCompat.shouldShowRequestPermissionRationale(this, Manifest.permission.CALL_PHONE)) {
                // 사용자가 임의로 권한을 취소시킨 경우
                // 권한 재요청
                ActivityCompat.requestPermissions(this, new String[]{Manifest.permission.CALL_PHONE}, PHONECALL_RESULTCODE);
            } else {
                // 최초로 권한을 요청하는 경우
                ActivityCompat.requestPermissions(this, new String[]{Manifest.permission.CALL_PHONE}, PHONECALL_RESULTCODE);
            }
        } else {
            startActivity(intent);
        }
    }

    private boolean isAllPermissionGranted(int[] grantResults) {
        if (grantResults.length > 0) {
            for (int grantResult : grantResults) {
                if (grantResult == PackageManager.PERMISSION_DENIED) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }

    public boolean hasPermission(String permission) {
        if (ContextCompat.checkSelfPermission(this, permission) != PackageManager.PERMISSION_GRANTED) {
            return false;
        } else {
            return true;
        }
    }

    private void saveToClipboard(String content) {
        ClipboardManager clipboard = (ClipboardManager) getSystemService(Context.CLIPBOARD_SERVICE);
        ClipData clip = ClipData.newPlainText("Chatting Link: ", content);
        clipboard.setPrimaryClip(clip);
        Toast.makeText(this, "Saved to clipboard.", Toast.LENGTH_LONG).show();
    }

    public void requestPermission(Activity p_context, String[] p_requiredPermissions, int requestCode) {
        ActivityCompat.requestPermissions(p_context, p_requiredPermissions, requestCode);
    }

    public void addImage() {
        if (selectedImages != null) {
            RequestParams param = new RequestParams();

            for (int i = 0; i < selectedImages.size(); i++) {
                if (new File(selectedImages.get(i)).exists()) {
                    try {
                        param.put("img", new File(selectedImages.get(i)));
                        break;
                    } catch (FileNotFoundException e) {
                        e.printStackTrace();
                    }
                }
            }

            Util.doRequest(true, this, "/Intro/file_upload", param, new Util.OnResponseListener() {
                @Override
                public void onSuccess(JSONObject p_result) throws JSONException {
                    try {
                        int errCode = p_result.getInt("code");
                        String errMsg = p_result.getString("msg");

                        if(errCode == 0) {
                            String file_url = p_result.getString("url");
                            String file_path = p_result.getString("file");
                            mJSFunctions.uploadedFile(file_url, file_path);
                        } else {
                            Toast.makeText(getApplicationContext(), errMsg, Toast.LENGTH_SHORT).show();
                        }

                    } catch (JSONException e) {
                        e.printStackTrace();
                    }
                }

                @Override
                public void onFailure(String p_strErrorMsg) {
                    Toast.makeText(getApplicationContext(), p_strErrorMsg, Toast.LENGTH_SHORT).show();
                }

                @Override
                public void onProgress(long bytesWritten, long totalSize) {
                }
            });
        }
    }

    public void addImages() {
        if (selectedImages != null) {
            new Thread(new Runnable() {
                @Override
                public void run() {
                uploadedPaths = "";

                for (int i = 0; i < selectedImages.size(); i++) {
                    if (new File(selectedImages.get(i)).exists()) {
                        try {
                            RequestParams param = new RequestParams();
                            param.put("img", new File(selectedImages.get(i)));

                            SyncHttpClient httpClient = new SyncHttpClient();
                            String funcUrl = SERVER_URL + "/Intro/file_upload";

                            httpClient.setTimeout(SyncHttpClient.DEFAULT_SOCKET_TIMEOUT);
                            httpClient.post(funcUrl, param, new JsonHttpResponseHandler() {
                                @Override
                                public synchronized void onSuccess(int statusCode, Header[] headers, JSONObject response) {
                                    try {
                                        int errCode = response.getInt("code");
                                        String errMsg = response.getString("msg");

                                        if(errCode == 0) {
                                            uploadedPaths += response.getString("file") + ";";
                                        } else {
                                            Log.w("Net Response(1) >>> ", errMsg);
                                        }
                                    } catch (JSONException e) {
                                        if (BuildConfig.DEBUG) {
                                            Log.w("Net Response(1) >>> ", e.getMessage());
                                        }
                                    }
                                }

                                @Override
                                public synchronized void onFailure(int statusCode, Header[] headers, Throwable throwable, JSONObject errorResponse) {
                                    Log.w("Net file_upload", errorResponse.toString());
                                }

                                @Override
                                public synchronized void onFailure(int statusCode, Header[] headers, String p_data, Throwable error) {
                                    Log.w("Net file_upload", error.getMessage());
                                }
                            });

                        } catch (FileNotFoundException e) {
                            e.printStackTrace();
                        }
                    }
                }

                mJSFunctions.uploadedFiles(uploadedPaths);
                }
            }).start();
        }
    }

    public void shareApp(String url) {
        Intent intent = new Intent();
        intent.setAction(Intent.ACTION_SEND);
        intent.putExtra(Intent.EXTRA_TEXT, url);
        intent.setType("text/plain");

        startActivity(Intent.createChooser(intent, "Share App Url"));
    }

    @SuppressLint("StaticFieldLeak")
    private class FetchNewVersion extends AsyncTask<Void, Void, String> {
        private final String packageName;
        private final String STORE_URL = "https://play.google.com/store/apps/details?id=com.gyeongju.user";

        public FetchNewVersion(String packageName) {
            this.packageName = packageName;
        }

        @Override
        protected void onPreExecute() {
            super.onPreExecute();
        }

        @Override
        protected String doInBackground(Void... params) {
            try {
                //TODO: YJ check online connection
                String storeUrl = "https://play.google.com/store/apps/details?id=" + packageName;

                Document doc = Jsoup.connect(storeUrl).get();
                Elements Version = doc.select(".htlgb");

                for (int i = 0; i < Version.size(); i++) {
                    String VersionMarket = Version.get(i).text();

                    if (Pattern.matches("^[0-9]{1}.[0-9]{1}$", VersionMarket)) {
                        return VersionMarket;
                    }
                }
            } catch (IOException e) {
                e.printStackTrace();
            }

            return "";
        }

        @Override
        protected void onPostExecute(String version) { //s는 Market Version입니다.
            if (version != null) {
                mJSFunctions.fetchVersion(version);
            } else {
                mJSFunctions.fetchVersion("");
            }
            super.onPostExecute(version);
        }
    }

    public class ContentManager {
        private Context context = null;

        public ContentManager(Context context) {
            this.context = context;
        }

        @JavascriptInterface
        public void bridgeLogin() {
            String id = CommonApplication.getInstance().mPrefMgr.getUserId();
            String pwd = CommonApplication.getInstance().mPrefMgr.getUserPwd();
//            String token = UserApp.getInstance().mPrefMgr.getToken();

            mJSFunctions.nativeLogin(id, pwd);
        }

        @JavascriptInterface
        public void bridgeLoginSuccess(int usr_type, String id, String pwd) {
            CommonApplication.getInstance().mPrefMgr.setUserId(id);
            CommonApplication.getInstance().mPrefMgr.setUserPwd(pwd);
            CommonApplication.getInstance().mPrefMgr.setUsrType(usr_type);
        }

        @JavascriptInterface
        public void bridgeLoginInfo() {
            String id = CommonApplication.getInstance().mPrefMgr.getUserId();
            String pwd = CommonApplication.getInstance().mPrefMgr.getUserPwd();
            String fcm_token = CommonApplication.getInstance().mPrefMgr.getToken();
            int usr_type = CommonApplication.getInstance().mPrefMgr.getUsrType();

            String verName = "0.0.0";
            try {
                PackageManager manager = context.getPackageManager();
                PackageInfo info = manager.getPackageInfo(context.getPackageName(), 0);
                verName = info.versionName;
            } catch(PackageManager.NameNotFoundException nnf) {
                nnf.printStackTrace();
            }

            mJSFunctions.mobileLoginCheck(usr_type, id, pwd, fcm_token, verName);
        }

        //TODO: YJ implement Kakao Login API
        @JavascriptInterface
        public void bridgeSnsInfo(int type) {
            String email = "";
            String name = "";
            if (type == 2) { //Kakao
                email = "user1@kakao.com"; //test code
                name = "KK User";
            }

            mJSFunctions.getSnsInfo(email, name);
        }

        //TODO: YJ implement Kakao Send API
        @JavascriptInterface
        public void bridgeSendVoucher(String[] imageUrls) {
            int result = 1; //1: send successfully, 0: send failed
            mJSFunctions.sendVoucher(result);
        }

        @JavascriptInterface
        public void bridgeResetMonthFlag() {
            CommonApplication.getInstance().mPrefMgr.setMonthFirstFlag();
        }

        @JavascriptInterface
        public void bridgeLogout() {
            CommonApplication.getInstance().mPrefMgr.setUserId("");
            CommonApplication.getInstance().mPrefMgr.setUserPwd("");
        }

        @JavascriptInterface
        public void bridgeCall(String phone) {
            Util.callPhone(getApplicationContext(), phone);
        }

        @JavascriptInterface
        public void bridgeSaveClipboard(String url) {
            MainActivity.this.saveToClipboard(url);
        }

        @JavascriptInterface
        public void go2Call(String phone) {
            otherPhone = phone;
            call();
        }

        @JavascriptInterface
        public void bridgeUploadImage(int type) {
            if (!hasPermission(Manifest.permission.READ_EXTERNAL_STORAGE)) {
                requestPermission(MainActivity.this, new String[]{Manifest.permission.READ_EXTERNAL_STORAGE}, REQUEST_PERMISSION_CODE);
                return;
            }

            if (type == 0) { // Camera mode
                mMediaManager.getMediaFromCamera(false);
            } else if (type == 1) { // gallery picker
                mMediaManager.getMediaFromGallery(false);
            }
        }

        @JavascriptInterface
        public void bridgeUploadImages() {
            if (!hasPermission(Manifest.permission.READ_EXTERNAL_STORAGE)) {
                requestPermission(MainActivity.this, new String[]{Manifest.permission.READ_EXTERNAL_STORAGE}, REQUEST_PERMISSION_CODE);
                return;
            }

            mMediaManager.getMultiImagesFromGallery(false);
        }

        @JavascriptInterface
        public void bridgeFinishApp() {
            finish();
        }

        @JavascriptInterface
        public void bridgeFetchVersion() {
            FetchNewVersion fetchVersion = new FetchNewVersion(context.getPackageName());
            fetchVersion.execute();
        }

        @JavascriptInterface
        public void bridgeInviteFriends(String url) {
            shareApp(url);
        }


        @JavascriptInterface
        public void bridgeGoUrl(String url) {
            Util.showActionViewIntent(MainActivity.this, url);
//            Intent i = new Intent(getApplicationContext(), UrlActivity.class);
//            i.putExtra("url", url);
//            startActivity(i);
        }
    }

    public class JSFunctions {
        void backPressed() {
            String function = "mobileBackPressed()";
            callJavascript(function);
        }

        void uploadedFile(String url, String path) {
            String function = "bridgeCallback('" + path + "','" + url + "')";
            callJavascript(function);
        }

        void uploadedFiles(String paths) {
            String function = "bridgeCallback('" + paths + "')";
            callJavascript(function);
        }

        void mobileLoginCheck(int usr_type, String id, String pwd, String fcm_token, String version) {
            String function = "mobileAutoLoginCheck('" + String.valueOf(usr_type) + "','" + id + "','" + pwd + "','" + fcm_token + "','" + version + "')";
            callJavascript(function);
        }

        public void setCurPosition(Double lat, Double log, String place) {
            String function = "setCurPosition('" + lat + "','" + log + "','" + place + "')";
            callJavascript(function);
        }

        void fetchVersion(String version) {
            String function = "bridgeCallback('" + version + "')";
            callJavascript(function);
        }

        void getSnsInfo(String email, String name) {
            String function = "bridgeCallback('" + email + "', '" + name + "')";
            callJavascript(function);
        }

        void sendVoucher(int result) {
            String function = "bridgeCallback('" + result + "')";
            callJavascript(function);
        }

        void shareApp(int result) {
            String function = "bridgeCallback('" + result + "')";
            callJavascript(function);
        }

        public void mobileUpdateToken() {
            PrefMgr prefMgr = CommonApplication.getInstance().getPreference();
            String token = prefMgr.getToken();
            if (token.isEmpty()) {
//                token = FirebaseInstanceId.getInstance().getToken();
            }

            String function = "mobileUpdateToken('" + token + "')";
            callJavascript(function);
        }

        void nativeLogin(String id, String pwd) {
            String function = "nativeLogin('" + id + "', '" + pwd + "')";
            callJavascript(function);
        }

        private void callJavascript(final String function) {
            mWebView.post(new Runnable() {
                @Override
                public void run() {
                    if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.KITKAT) {
                        String func = function + ";";
                        mWebView.evaluateJavascript(func, null);
                    } else {
                        String func = "javascript:" + function + ";";
                        mWebView.loadUrl(func);
                    }
                }
            });
        }
    }
}
