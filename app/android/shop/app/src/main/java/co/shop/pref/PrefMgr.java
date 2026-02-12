package kr.co.conpang.pref;

import android.content.Context;
import android.content.SharedPreferences;

import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.Locale;

import co.shop.CommonApplication;

public class PrefMgr {
    private static final String FCM_TOKEN = "as_fcm_token";
    private static final String USER_ID = "as_user_id";
    private static final String USER_PWD = "as_user_pwd";
    private static final String LOGIN_TYPE = "as_user_login_type";
    private static final String USR_TYPE = "as_user_type";
    private static final String LAST_LOGIN_MONTH = "as_last_login_month";

    protected SharedPreferences sharedPreferences;
    protected SharedPreferences.Editor editor;

    public PrefMgr() {
        String name = CommonApplication.getInstance().getPackageName() + "-preference";
        sharedPreferences = CommonApplication.getInstance().getSharedPreferences(name, Context.MODE_PRIVATE);
        editor = sharedPreferences.edit();
    }

    public void setUserId(String userId) {
        editor.putString(USER_ID, userId).apply();
    }

    public String getUserId() {
        return sharedPreferences.getString(USER_ID, "");
    }

    public void setUserPwd(String userPwd) {
        editor.putString(USER_PWD, userPwd).apply();
    }

    public String getUserPwd() {
        return sharedPreferences.getString(USER_PWD, "");
    }

    public void setToken(String token) {
        editor.putString(FCM_TOKEN, token).apply();
    }

    public String getToken() {
        return sharedPreferences.getString(FCM_TOKEN, "");
    }


    public void setLoginType(int type) {
        editor.putInt(LOGIN_TYPE, type).apply();
    }

    public int getLoginType() {
        return sharedPreferences.getInt(LOGIN_TYPE, 0);
    }

    public void setUsrType(int type) {
        editor.putInt(USR_TYPE, type).apply();
    }

    public int getUsrType() {
        return sharedPreferences.getInt(USR_TYPE, 1);
    }

    public int getMonthFirstFlag() {
        String last_month = sharedPreferences.getString(LAST_LOGIN_MONTH, "");
        String cur_month = new SimpleDateFormat("yyyy-MM", Locale.getDefault()).format(new Date());
        if(last_month.equals(cur_month)) {
            return 0;
        } else {
            return 1;
        }
    }

    public void setMonthFirstFlag() {
        String last_month = sharedPreferences.getString(LAST_LOGIN_MONTH, "");
        String cur_month = new SimpleDateFormat("yyyy-MM", Locale.getDefault()).format(new Date());
        editor.putString(LAST_LOGIN_MONTH, cur_month).apply();
    }
}
