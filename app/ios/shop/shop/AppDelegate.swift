//
//  Copyright (c) 2016 Google Inc.
//
//  Licensed under the Apache License, Version 2.0 (the "License");
//  you may not use this file except in compliance with the License.
//  You may obtain a copy of the License at
//
//  http://www.apache.org/licenses/LICENSE-2.0
//
//  Unless required by applicable law or agreed to in writing, software
//  distributed under the License is distributed on an "AS IS" BASIS,
//  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
//  See the License for the specific language governing permissions and
//  limitations under the License.
//
import UIKit
import UserNotifications

//import Firebase

@UIApplicationMain
class AppDelegate: UIResponder, UIApplicationDelegate,  UNUserNotificationCenterDelegate/*, MessagingDelegate*/ {
    
    var window: UIWindow?
    
    var isForeground = true
    
    enum ENotificationType: String {
        case updateNotice    = "updateNotice"
        case restoreSuccessfulNotification = "SubscriptionServiceRestoreSuccessfulNotification"
        case failPurchase    = "failPurchase"
    }
    
    func application(_ application: UIApplication, didFinishLaunchingWithOptions launchOptions: [UIApplicationLaunchOptionsKey: Any]?) -> Bool {
        // Override point for customization after application launch.
        
        UIApplication.shared.statusBarStyle = .lightContent
        
        let user_data = UserDefaults.standard
        if user_data.string(forKey: "firebase_token") != nil {
            m_token = user_data.string(forKey: "firebase_token")!
        }
        if user_data.string(forKey: "user_id") != nil {
            m_userID = user_data.string(forKey: "user_id")!
        }
        if user_data.string(forKey: "user_pwd") != nil {
            m_userPwd = user_data.string(forKey: "user_pwd")!
        }
        
        //FirebaseApp.configure()
        registerNoti(application)
        
        return true
    }
    
    func applicationWillResignActive(_ application: UIApplication) {
        // Sent when the application is about to move from active to inactive state. This can occur for certain types of temporary interruptions (such as an incoming phone call or SMS message) or when the user quits the application and it begins the transition to the background state.
        // Use this method to pause ongoing tasks, disable timers, and invalidate graphics rendering callbacks. Games should use this method to pause the game.
    }
    
    func applicationDidEnterBackground(_ application: UIApplication) {
        // Use this method to release shared resources, save user data, invalidate timers, and store enough application state information to restore your application to its current state in case it is terminated later.
        // If your application supports background execution, this method is called instead of applicationWillTerminate: when the user quits.
        isForeground = false
        disconnectFcm()
    }
    
    func applicationWillEnterForeground(_ application: UIApplication) {
        // Called as part of the transition from the background to the active state; here you can undo many of the changes made on entering the background.
        connectFcm()
    }
    
    func applicationDidBecomeActive(_ application: UIApplication) {
        isForeground = true
        // Restart any tasks that were paused (or not yet started) while the application was inactive. If the application was previously in the background, optionally refresh the user interface.
    }
    
    func applicationWillTerminate(_ application: UIApplication) {
        // Called when the application is about to terminate. Save data if appropriate. See also applicationDidEnterBackground:.
    }
    
    //////////////////////////////////////////////////////////////////////
    // MARK: - APNS callbacks
    //////////////////////////////////////////////////////////////////////
    
    func application(_ application: UIApplication, didRegisterForRemoteNotificationsWithDeviceToken deviceToken: Data) {
        //Messaging.messaging().apnsToken = deviceToken
    }
    
    func application(_ application: UIApplication, didFailToRegisterForRemoteNotificationsWithError error: Error) {
        print(error.localizedDescription)
    }
    
    func application(_ application: UIApplication, didReceiveRemoteNotification userInfo: [AnyHashable : Any], fetchCompletionHandler completionHandler: @escaping (UIBackgroundFetchResult) -> Void) {
        phasePush(userInfo)
        completionHandler(UIBackgroundFetchResult.newData)
    }
    
    //////////////////////////////////////////////////////////////////////
    // MARK: - MessagingDelegate
    //////////////////////////////////////////////////////////////////////
    
    /*func messaging(_ messaging: Messaging, didRefreshRegistrationToken fcmToken: String) {
        print("Firebase registration token: \(fcmToken)")
    }
    
    func messaging(_ messaging: Messaging, didReceiveRegistrationToken fcmToken: String?) {
        print("Firebase registration token: \(fcmToken)")
    }*/
    
    /////////////////////////////////////////////////////////////////////
    // MARK: - UNUserNotificationCenterDelegate
    //////////////////////////////////////////////////////////////////////
    
    @available(iOS 10.0, *)
    func userNotificationCenter(_ center: UNUserNotificationCenter, willPresent notification: UNNotification, withCompletionHandler completionHandler: @escaping (UNNotificationPresentationOptions) -> Void) {
        let userInfo = notification.request.content.userInfo
        phasePush(userInfo)
        completionHandler([.alert, .badge, .sound])
    }
    
    @available(iOS 10.0, *)
    func userNotificationCenter(_ center: UNUserNotificationCenter, didReceive response: UNNotificationResponse, withCompletionHandler completionHandler: @escaping () -> Void) {
        let center = UNUserNotificationCenter.current()
        center.removeAllPendingNotificationRequests() // To remove all pending notifications which are not delivered yet but scheduled.
        center.removeAllDeliveredNotifications() // To remove all delivered notifications
        let userInfo = response.notification.request.content.userInfo
        phasePush(userInfo)
        completionHandler()
    }
    
    func registerNoti(_ application: UIApplication) {
        if #available(iOS 10.0, *) {
            // For iOS 10 display notification (sent via APNS)
            UNUserNotificationCenter.current().delegate = self
            
            let authOptions: UNAuthorizationOptions = [.alert, .badge, .sound]
            UNUserNotificationCenter.current().requestAuthorization(
                options: authOptions,
                completionHandler: {_, _ in })
            
            // For iOS 10 data message (sent via FCM)
            //Messaging.messaging().delegate = self
        } else {
            let settings: UIUserNotificationSettings =
                UIUserNotificationSettings(types: [.alert, .badge, .sound], categories: nil)
            application.registerUserNotificationSettings(settings)
        }
        
        application.registerForRemoteNotifications()
        
        if application.currentUserNotificationSettings!.types.contains(.badge) {
            application.applicationIconBadgeNumber = 0
        }
        
        //NotificationCenter.default.addObserver(self, selector: #selector(refreshToken), name: NSNotification.Name.InstanceIDTokenRefresh, object: nil)
    }
    
    func disconnectFcm() {
//        Messaging.messaging().shouldEstablishDirectChannel = false
        print("Disconnected from FCM.")
    }
    
    @objc func refreshToken(_ notification: NSNotification) {
        /*if let token = Messaging.messaging().fcmToken {
            print("FCM token: \(token)")
            m_token = token
            let userDefaults = UserDefaults.standard
            userDefaults.setValue(m_token, forKey: "firebase_token")
        }*/
        
        connectFcm()
    }
    
    func connectFcm() {
//        guard InstanceID.instanceID().token() != nil else {
//            return;
//        }
        
        // Disconnect previous FCM connection if it exists.
        //disconnectFcm()
        
//        Messaging.messaging().shouldEstablishDirectChannel = true
        print("Connected to FCM.")
    }
    
    func phasePush(_ info: [AnyHashable : Any]) {
        print("Message ID: \(info["gcm.message_id"]!)")
        print("%@", info)
        
        m_saleId = Int(info["id"] as! String)
        m_pushType = Int(info["type"] as! String)
        
        NotificationCenter.default.post(name: NSNotification.Name(rawValue: "income_push"), object: nil, userInfo: ["id":m_saleId ?? 0,"type": m_pushType ?? 0])
    }
}
