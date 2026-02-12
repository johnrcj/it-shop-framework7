package co.shop.fcm;

//import com.google.firebase.iid.FirebaseInstanceId;
//import com.google.firebase.iid.FirebaseInstanceIdService;
//
//public class MyFirebaseInstanceIdService extends FirebaseInstanceIdService {
//
//    public MyFirebaseInstanceIdService() {
//    }
//
//    @Override
//    public void onTokenRefresh() {
//        String w_strToken = FirebaseInstanceId.getInstance().getToken();
//        UserApp w_app = (UserApp) this.getApplicationContext();
//        w_app.mPrefMgr.setToken(w_strToken);
//
////        if (w_app.mPrefMgr.isValid()) {
////            setServerToken(w_app.mPrefMgr.getUserUid(), w_strToken);
////        }
//    }
//
//    private void setServerToken(int userUid, String token) {
//        RequestParams param = new RequestParams();
//        param.put("id", userUid);
//        param.put("token", token);
//
//        doRequest(false, this.getApplicationContext(), "/common/set_token", param, new Util.OnResponseListener() {
//            @Override
//            public void onSuccess(JSONObject p_result) throws JSONException {
//            }
//
//            @Override
//            public void onFailure(String p_strErrorMsg) {
//            }
//
//            @Override
//            public void onProgress(long bytesWritten, long totalSize) {
//            }
//        });
//    }
//}
