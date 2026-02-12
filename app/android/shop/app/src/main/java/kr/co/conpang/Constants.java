package kr.co.conpang;

/**
 * Created by StarThomas.
 */

public interface Constants {
    String SERVER_URL = "http://192.168.0.70:81/ConpangMobile/";

    public static String SDCARD_FOLDER = "Conpang";
    boolean IS_TEST = true;
    int ANDROID = 1;
    String BROADCAST_EVENT_SET_PUSH_TOKEN = "BROADCAST_EVENT_SET_PUSH_TOKEN";


    int REQUEST_PERMISSION_CODE = 0x11;
    int REQUEST_GALLERY_CODE = 0x12;
    int FILE_PICK_REQUEST = 0x13;


    String ARG_FILEPATH = "ARG_FILEPATH";
    String ARG_FILENAME = "ARG_FILENAME";
}
