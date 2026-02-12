package co.shop.fcm;

//import com.google.firebase.messaging.FirebaseMessagingService;
//import com.google.firebase.messaging.RemoteMessage;
//
//public class MyFirebaseMessagingService extends FirebaseMessagingService {
//
//    private static final String TAG = "MyFirebaseMsgService";
//
//    @Override
//    public void onMessageReceived(RemoteMessage remoteMessage) {
//
//        if (remoteMessage.getNotification() != null) {
//            Log.d(TAG, "Message Notification Body: " + remoteMessage.getNotification().getBody());
//        }
//
//        String title = "";
//        String msg = "";
//        String type = "1";
//        int count = 0;
//
//        //TODO: PUSH 처리부
//        try {
//
//            title = remoteMessage.getData().get("title");
//            msg = remoteMessage.getData().get("message");
////            type = remoteMessage.getData().get("type");
////            count = Integer.parseInt(remoteMessage.getData().get("count"));
//
////            if (type.equals("chat")) {
////                UserApp.getInstance().updateBadgeCount(count);
////            }
//            sendNotification(type, title, msg);
//
//        } catch (Exception e) {
//            e.printStackTrace();
//        }
//
//    }
//
//    private void sendNotification(String type, String title, String message) {
//        Intent intent = new Intent(this, MainActivity.class);
//        intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP | Intent.FLAG_ACTIVITY_NEW_TASK);
//        intent.putExtra("type", type);
//        PendingIntent pendingIntent = PendingIntent.getActivity(this, (int) System.currentTimeMillis(), intent,
//                PendingIntent.FLAG_UPDATE_CURRENT);
//
//        Bitmap notificationLargeIconBitmap = BitmapFactory.decodeResource(
//                getApplicationContext().getResources(),
//                R.mipmap.ic_launcher);
//
//        int notifyID = 1;
//        String CHANNEL_ID = "my_channel_01";// The id of the channel.
//        CharSequence name = "meatgo";//getString(R.string.channel_name);// The user-visible name of the channel.
//        NotificationManager w_notificationManager = (NotificationManager) this.getSystemService(Context.NOTIFICATION_SERVICE);
//
//        Notification w_notification = null;
//        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
//            NotificationChannel mChannel = new NotificationChannel(CHANNEL_ID, name, NotificationManager.IMPORTANCE_HIGH);
//            w_notificationManager.createNotificationChannel(mChannel);
//            w_notification = new Notification.Builder(getApplicationContext())
//                    .setSmallIcon(R.mipmap.ic_launcher)
//                    .setLargeIcon(notificationLargeIconBitmap)
//                    .setContentIntent(pendingIntent)
//                    .setWhen(System.currentTimeMillis())
//                    .setAutoCancel(true)
//                    .setContentTitle(title)
//                    .setContentText(message)
//                    .setChannelId(CHANNEL_ID)
//                    .build();
//        } else {
//            if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.JELLY_BEAN) {
//                w_notification = new Notification.Builder(getApplicationContext())
//                        .setSmallIcon(R.mipmap.ic_launcher)
//                        .setLargeIcon(notificationLargeIconBitmap)
//                        .setContentIntent(pendingIntent)
//                        .setWhen(System.currentTimeMillis())
//                        .setAutoCancel(true)
//                        .setContentTitle(title)
//                        .setContentText(message)
//                        .build();
//            }
//        }
//
//        w_notificationManager.notify(notifyID, w_notification);
//    }
//}