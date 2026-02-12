package kr.co.conpang.util;

import static kr.co.conpang.Constants.SERVER_URL;

import android.Manifest;
import android.app.Activity;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.pm.PackageInfo;
import android.content.pm.PackageManager;
import android.graphics.Bitmap;
import android.graphics.Point;
import android.net.Uri;
import android.os.Build;
import android.provider.Settings;
import android.support.annotation.NonNull;
import android.telephony.TelephonyManager;
import android.text.Spannable;
import android.text.SpannableStringBuilder;
import android.text.style.AbsoluteSizeSpan;
import android.util.DisplayMetrics;
import android.util.Log;
import android.util.TypedValue;
import android.webkit.MimeTypeMap;
import android.widget.Toast;

import com.loopj.android.http.AsyncHttpClient;
import com.loopj.android.http.BuildConfig;
import com.loopj.android.http.JsonHttpResponseHandler;
import com.loopj.android.http.RequestParams;

import org.json.JSONException;
import org.json.JSONObject;

import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.OutputStream;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.Date;
import java.util.Locale;
import java.util.UUID;

import cz.msebera.android.httpclient.Header;
import kr.co.conpang.MainActivity;
import kr.co.conpang.R;


public class Util {
    public static ProgressDialog m_dlgProgress = null;
    private static int m_nProgressCnt = 0;

    public static int dpToPixel(Context context, float dp) {
        float px = TypedValue.applyDimension(TypedValue.COMPLEX_UNIT_DIP, dp, context.getResources().getDisplayMetrics());
        return (int) px;
    }

    public static int pixelToDp(Context context, int pixel) {
        DisplayMetrics metrics = context.getResources().getDisplayMetrics();
        float dp = pixel / (metrics.densityDpi / 160f);
        return (int) dp;
    }

    public static void showToast(Context context, int resId) {
        showToast(context, context.getString(resId));
    }

    public static void showToast(Context context, String MESSAGE) {
        Toast.makeText(context, MESSAGE, Toast.LENGTH_SHORT).show();
    }

    // apply diffrent styles to text
    public static SpannableStringBuilder setTextStyle(String szTxt, int nStyle,
                                                      Object nColor, int nSize) {

        SpannableStringBuilder ssb = new SpannableStringBuilder();
        ssb.clear();

        if (szTxt == null || szTxt.equals("")) {
            return ssb;
        }

        ssb.append(szTxt); // attention : 본문에 "."이 들어가면 행바꾸기됩니다.
        try {
            ssb.setSpan(nStyle /*new StyleSpan(nStyle)*/, 0, szTxt.length(),
                    Spannable.SPAN_COMPOSING); // nStyle : Typeface.BOLD_ITALIC
            ssb.setSpan(nColor, 0, szTxt.length(),
                    Spannable.SPAN_EXCLUSIVE_EXCLUSIVE);
            ssb.setSpan(new AbsoluteSizeSpan(nSize), 0, szTxt.length(),
                    Spannable.SPAN_EXCLUSIVE_EXCLUSIVE);

        } catch (Exception e) {
            Log.d("", "setTextStyle() -->  " + e.getMessage());
        }

        return ssb;
    }

    // apply diffrent styles to text
    public static SpannableStringBuilder setTextStyle(String szTxt, int nStyle,
                                                      Object nColor) {

        SpannableStringBuilder ssb = new SpannableStringBuilder();
        ssb.clear();

        if (szTxt == null || szTxt.equals("")) {
            return ssb;
        }

        ssb.append(szTxt); // attention : 본문에 "."이 들어가면 행바꾸기됩니다.
        try {
            ssb.setSpan(nStyle /*new StyleSpan(nStyle)*/, 0, szTxt.length(),
                    Spannable.SPAN_COMPOSING); // nStyle : Typeface.BOLD_ITALIC
            ssb.setSpan(nColor, 0, szTxt.length(),
                    Spannable.SPAN_EXCLUSIVE_EXCLUSIVE);

        } catch (Exception e) {
            Log.d("", "setTextStyle() -->  " + e.getMessage());
        }

        return ssb;
        // textView.append(setTextStyle("테스트.스타일", Typeface.BOLD_ITALIC,
        // Color.RED, 22));
    }

    // get current date-time string name
    public static String getDateTimeString( ) {
        return String.format("%d%d%d%d%d%d", Calendar.getInstance().get(Calendar.YEAR), Calendar.getInstance().get(Calendar.MONTH) + 1, Calendar.getInstance().get(Calendar.DAY_OF_MONTH),
                Calendar.getInstance().get(Calendar.HOUR_OF_DAY), Calendar.getInstance().get(Calendar.MINUTE), Calendar.getInstance().get(Calendar.SECOND));
    }

    public static String getStringTimeFormat_00_00_00(int second) {
        int h = second / 3600;
        int m = (second % 3600) / 60;
        int s = second % 60;

        if (h == 0) {
            return String.format(Locale.getDefault(), "%02d : %02d", m, s);
        } else {
            return String.format(Locale.getDefault(), "%02d:%02d:%02d", h, m, s);
        }
    }

    // get appversion
    public static String getVersion(Context context) {
        try {
            PackageInfo pi = context.getPackageManager().getPackageInfo(context.getPackageName(), 0);
            return pi.versionName;
        } catch (PackageManager.NameNotFoundException e) {
            return null;
        }
    }


    //
    // device related
    //
    public static String getDeviceId(Context context) {
        String deviceId = "";

        final TelephonyManager tm = (TelephonyManager) context.getSystemService(Context.TELEPHONY_SERVICE);

        final String tmDevice, tmSerial, androidId;
        tmDevice = "" + tm.getDeviceId();
        tmSerial = "" + tm.getSimSerialNumber();
        androidId = "" + Settings.Secure.getString(context.getContentResolver(), Settings.Secure.ANDROID_ID);

        UUID deviceUuid = new UUID(androidId.hashCode(), ((long) tmDevice.hashCode() << 32) | tmSerial.hashCode());
        deviceId = deviceUuid.toString();

        return deviceId;
    }

    public static String getDeviceModel(Context context) {
        String deviceModel = Build.MODEL;
        return deviceModel;
    }

    public static void goToAppSettings(Activity activity) {
        Intent intent = new Intent(Settings.ACTION_APPLICATION_DETAILS_SETTINGS,
                Uri.fromParts("package", activity.getPackageName(), null));
        intent.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
        activity.startActivity(intent);
    }

    // url = file path or whatever suitable URL you want.
    public static String getMimeType(String url) {
        String type = null;
        String extension = MimeTypeMap.getFileExtensionFromUrl(url);
        if (extension != null) {
            type = MimeTypeMap.getSingleton().getMimeTypeFromExtension(extension);
        }
        return type;
    }

    public static void hideProgress( ) {
        m_nProgressCnt--;
        if (m_nProgressCnt > 0)
            return;
        if (m_dlgProgress != null && m_dlgProgress.isShowing()) {
            m_dlgProgress.dismiss();
            m_dlgProgress.hide();
        }
        m_dlgProgress = null;
    }

    public static int getScreenWidth(@NonNull Context context) {
        Point size = new Point();
        ((Activity) context).getWindowManager().getDefaultDisplay().getSize(size);
        return size.x;
    }

    /**
     * 서버API 요청.(POST방식)
     * <p/>
     * p_context : ProgressDialog를 현시하기 위하여 필요.
     * p_nFuncId : 요청하려는 API함수 식별상수(ex : f_searchUsers).
     * p_params  : 요청파라미터.(GET)
     * p_post_params : POST요청파라미터.
     * p_responseListener : 응답핸들러.
     */
    public static void doRequest(boolean p_bShowProgressDialog, final Context p_context, final String p_strFuncId, RequestParams p_postParams, final OnResponseListener p_responseListener) {
        final AsyncHttpClient w_httpClient = new AsyncHttpClient();

        final ProgressDialog w_dlgProgress = p_bShowProgressDialog ? ProgressDialog.show(p_context, null, p_context.getString(R.string.wait)) : null;
        if (w_dlgProgress != null) {
            w_dlgProgress.setCancelable(true);
            w_dlgProgress.setCanceledOnTouchOutside(false);
            w_dlgProgress.setOnCancelListener(new DialogInterface.OnCancelListener() {
                @Override
                public void onCancel(DialogInterface dialogInterface) {
                    if (w_httpClient != null)
                        w_httpClient.cancelAllRequests(true);
                }
            });
        }

        String w_strFuncUrl = SERVER_URL + p_strFuncId;
        if (BuildConfig.DEBUG) {
            Log.d("Net HTTP >>> ", w_strFuncUrl);
            Log.d("Net param >>>", p_postParams == null ? "null" : p_postParams.toString());
        }

        w_httpClient.setTimeout(AsyncHttpClient.DEFAULT_SOCKET_TIMEOUT);
        w_httpClient.post(w_strFuncUrl, p_postParams, new AlwaysAsyncJsonHttpResponseHandler() {
            @Override
            public void onSuccess(int p_nStatusCode, Header[] p_headers, JSONObject p_jsonResponse) {

                if (BuildConfig.DEBUG) {
                    Log.d("Net Response(0) >>> " + p_strFuncId, p_jsonResponse.toString());
                }

                if (w_dlgProgress != null)
                    w_dlgProgress.dismiss();

                //
                // 결과 파싱.
                //
                try {
                    //
                    // 서버응답결과를 리턴.
                    //
                    if (p_responseListener != null)
                        p_responseListener.onSuccess(p_jsonResponse);
                } catch (JSONException e) {
                    if (BuildConfig.DEBUG) {
                        Log.w("Net Response(1) >>> ", e.getMessage());
                    }
                    //
                    // 실패를 리턴.
                    //
                    String w_strRsMsg = p_context.getString(R.string.msg_jsonparse_error);
                    if (p_responseListener != null)
                        p_responseListener.onFailure(w_strRsMsg);
                }
            }

            @Override
            public void onFailure(int p_nStatusCode, Header[] p_headers, Throwable error, JSONObject response) {
                try {
                    if (w_dlgProgress != null)
                        w_dlgProgress.dismiss();

                    if (BuildConfig.DEBUG) {
                        Log.w("Net Response(2) >>> " + p_strFuncId, error.getMessage());
                    }

                    //
                    // 실패를 리턴.
                    //
                    String w_strRsMsg = p_context.getString(R.string.msg_network_error);
                    if (p_responseListener != null)
                        p_responseListener.onFailure(w_strRsMsg);
                } catch (Exception e) {

                }
            }

            @Override
            public void onFailure(int p_nStatusCode, Header[] p_headers, String p_data, Throwable p_error) {
                try {
                    if (w_dlgProgress != null)
                        w_dlgProgress.dismiss();

                    if (BuildConfig.DEBUG) {
                        Log.d("Net Response(3) >>>  " + p_strFuncId, p_data + "\n" + p_error.getMessage());
                    }

                    //
                    // 실패를 리턴.
                    //
                    String w_strRsMsg = p_context.getString(R.string.msg_api_error);
                    if (p_responseListener != null)
                        p_responseListener.onFailure(w_strRsMsg + p_error.getLocalizedMessage());
                } catch (Exception e) {

                }
            }

            @Override
            public void onProgress(long bytesWritten, long totalSize) {
                super.onProgress(bytesWritten, totalSize);
                if (p_responseListener != null)
                    p_responseListener.onProgress(bytesWritten, totalSize);
            }
        });
    }

    public static abstract class AlwaysAsyncJsonHttpResponseHandler extends JsonHttpResponseHandler {
        @Override
        public boolean getUseSynchronousMode() {
            return false;
        }
    }

    /**
     * 웹서비스 API의 응답처리 핸들러 인터페이스.
     */
    public interface OnResponseListener {
        void onSuccess(JSONObject p_result) throws JSONException;

        void onFailure(String p_strErrorMsg);

        void onProgress(long bytesWritten, long totalSize);
    }


    public static void saveBitmapToFile(MainActivity activity, Bitmap bitmap, String filepath) {
        if (bitmap == null || filepath.isEmpty()) {
            return;
        }

        if (activity.hasPermission(Manifest.permission.WRITE_EXTERNAL_STORAGE)) {
            OutputStream fOut = null;
            File file = new File(filepath);
            try {
                fOut = new FileOutputStream(file);
            } catch (FileNotFoundException e) {

                e.printStackTrace();
            }
            bitmap.compress(Bitmap.CompressFormat.PNG, 100, fOut);

            try {
                fOut.flush();
                fOut.close();

            } catch (IOException e) {
                // TODO Auto-generated catch block
                e.printStackTrace();
            }
        } else {
            activity.requestPermission(activity, new String[]{Manifest.permission.WRITE_EXTERNAL_STORAGE}, 0x11);
        }
    }
    public static void callPhone(Context context, String phone) {
        Uri number = Uri.parse("tel:" + phone);
        Intent i = new Intent(Intent.ACTION_DIAL, number);
        context.startActivity(i);
    }

    public static double getDiffTime(String start) {
        try {
            SimpleDateFormat simpleDateFormat = new SimpleDateFormat("yyyy.MM.dd", Locale.getDefault());
            Date date = simpleDateFormat.parse(start);
            return ((new Date().getTime()) - date.getTime()) / 1000;

        } catch (ParseException e) {
            return 0;
        }
    }

    public static int getDiffMinute(String start) {
        try {

            String current_time = new SimpleDateFormat("yyyy.MM.dd HH:mm", Locale.getDefault()).format(new Date());

            SimpleDateFormat simpleDateFormat = new SimpleDateFormat("yyyy.MM.dd HH:mm", Locale.getDefault());
            Date date = simpleDateFormat.parse(start);

            return (int)((((simpleDateFormat.parse(current_time).getTime()) - date.getTime()) / 1000)/60);

        } catch (ParseException e) {
            return 0;
        }
    }

    public static void showActionViewIntent(Context context, String url) {
        if (url == null || url.isEmpty())
            return;

        if (!url.startsWith("http://") && !url.startsWith("https://"))
            url = "http://" + url;

        Uri uri = Uri.parse(url);
        Intent intent = new Intent(Intent.ACTION_VIEW, uri);
        context.startActivity(intent);
    }
}
