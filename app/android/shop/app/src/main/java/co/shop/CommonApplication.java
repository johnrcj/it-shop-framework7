package kr.co.conpang;

import android.app.Application;
import android.content.Context;

import kr.co.conpang.pref.PrefMgr;

//import com.facebook.FacebookSdk;
//import com.kakao.auth.KakaoSDK;

/**
 * Created by Thomas on 7/19/2019.
 */

public class CommonApplication extends Application {
    private static CommonApplication instance = null;
    public PrefMgr mPrefMgr = null;

    // FCM 토큰값
    public String strFCMToken = "";

    /**
     * singleton 애플리케이션 객체를 얻는다.
     *
     * @return singleton 애플리케이션 객체
     */
    public static CommonApplication getGlobalApplicationContext( ) {
        if (instance == null)
            throw new IllegalStateException("this application does not inherit com.kakao.GlobalApplication");
        return instance;
    }

    @Override
    protected void attachBaseContext(Context context) {
        super.attachBaseContext(context);
    }

    @Override
    public void onCreate( ) {
        super.onCreate();
        instance = this;
        mPrefMgr = new PrefMgr();

//        KakaoSDK.init(new KakaoSDKAdapter());
//        FacebookSdk.sdkInitialize(getApplicationContext());
    }

    /**
     * 애플리케이션 종료시 singleton 어플리케이션 객체 초기화한다.
     */
    @Override
    public void onTerminate( ) {
        super.onTerminate();
        instance = null;
    }

    public static CommonApplication getInstance() {
        return instance;
    }

    public PrefMgr getPreference() {
        return mPrefMgr;
    }
}
