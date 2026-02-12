package co.shop;

import android.app.Application;
import android.content.Context;

import co.shop.pref.PrefMgr;

//import com.facebook.FacebookSdk;
//import com.kakao.auth.KakaoSDK;

/**
 * Created by Thomas on 7/19/2019.
 */

public class CommonApplication extends Application {
    private static CommonApplication instance = null;
    public PrefMgr mPrefMgr = null;

    // FCM Token
    public String strFCMToken = "";

    /**
     * singleton Get instance of application
     *
     * @return singleton instance of application
     *
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
     * When application has finished, init the object of application
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
