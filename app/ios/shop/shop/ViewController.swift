//
//  ViewController.swift
//  Furniture
//
//  Created by Poker on 1/4/2020.
//  Copyright © 2020 Poker. All rights reserved.
//

import UIKit
import WebKit
import MessageUI
import MobileCoreServices
import CropViewController
import Alamofire
import SwiftyJSON
import Social

class ViewController: UIViewController {

    @IBOutlet weak var wvMain: WKWebView!
    @IBOutlet weak var vwLoading: UIView!
    
    let imagePicker : UIImagePickerController! = UIImagePickerController()
    var captureImage : UIImage!
    var flagImageSave = false
    
    var strFrom = "user"
    var strWhich = "camera"
    var strVersion = ""
    var strVersionCode = ""
    var m_view_type: Int!
    
    var imageFiles = ""
    var imageCount = 0
    var imageIndex = 0
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
        let browser = wvMain.configuration.userContentController
        
        browser.add(self as WKScriptMessageHandler, name: "showProgress")
        browser.add(self as WKScriptMessageHandler, name: "hideProgress")        
        browser.add(self as WKScriptMessageHandler, name: "getDeviceInfo")
        browser.add(self as WKScriptMessageHandler, name: "go2Dial")
        browser.add(self as WKScriptMessageHandler, name: "go2Msg")
        
//        browser.add(self as WKScriptMessageHandler, name: "camera")
        
        
        browser.add(self as WKScriptMessageHandler, name: "main")
        browser.add(self as WKScriptMessageHandler, name: "appVersion")
        browser.add(self as WKScriptMessageHandler, name: "save_email")
        browser.add(self as WKScriptMessageHandler, name: "get_email")

        browser.add(self as WKScriptMessageHandler, name: "bridgeCopyClipboard")
        browser.add(self as WKScriptMessageHandler, name: "bridgeResetMonthFlag")        
        browser.add(self as WKScriptMessageHandler, name: "bridgeLoginInfo")
        browser.add(self as WKScriptMessageHandler, name: "bridgeLoginSuccess")
        browser.add(self as WKScriptMessageHandler, name: "bridgeLogout")
        browser.add(self as WKScriptMessageHandler, name: "bridgeFinishApp")
        browser.add(self as WKScriptMessageHandler, name: "bridgeFetchVersion")
        browser.add(self as WKScriptMessageHandler, name: "bridgeSnsInfo")
        browser.add(self as WKScriptMessageHandler, name: "bridgeAppleInfo")
        browser.add(self as WKScriptMessageHandler, name: "bridgeUploadImage")
        browser.add(self as WKScriptMessageHandler, name: "bridgeUploadImages")
        browser.add(self as WKScriptMessageHandler, name: "bridgeSendVoucher")
        browser.add(self as WKScriptMessageHandler, name: "bridgeInviteFriends")
        browser.add(self as WKScriptMessageHandler, name: "bridgeGoUrl")
        
        let request = URLRequest(url: URL(string: m_webUrl)!)
        wvMain.load(request)
        
        wvMain.scrollView.bounces = false
        wvMain.navigationDelegate = self
        
        NotificationCenter.default.addObserver(self, selector: #selector(checkPush), name: NSNotification.Name(rawValue: "income_push"), object: nil)
        NotificationCenter.default.addObserver(self, selector: #selector(imgPick(_:)), name: NSNotification.Name(rawValue: "image_pick"), object: nil)
        NotificationCenter.default.addObserver(self, selector: #selector(keyboardWillDismiss(_:)), name: NSNotification.Name.UIKeyboardDidHide, object: nil)
    }

    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }
    
    deinit {
        NotificationCenter.default.removeObserver(self)
    }
    
    @objc func checkPush(_ notification : Notification) {
        guard let userInfo = notification.userInfo,
            let id   = userInfo["id"] as? Int,
            let type = userInfo["type"] as? Int else {
                print("No userInfo found in notification")
                return
        }
        
        if (type != 0 && type != 3 && id != 0) {
            let request = URLRequest(url: URL(string: "http://greatsoft05.appfrica.co.kr/main/sale_detail/" + String(id))!)
            wvMain.load(request)
        }
    }
    
    @objc func imgPick(_ notification : Notification) {
        let imgList = notification.object as! [UIImage]
        self.uploadImages(imgList)
    }
    
    @objc func keyboardWillDismiss(_ notification: Notification) {
        print("keyboard did hide")
        wvMain.setNeedsLayout()
        wvMain.layoutIfNeeded()
    }
    
    func pickFromCamera() {
        if UIImagePickerController.isSourceTypeAvailable(.camera) {
            flagImageSave = true
            
            imagePicker.delegate = self
            imagePicker.sourceType = .camera
            imagePicker.mediaTypes = [kUTTypeImage as String]
            imagePicker.allowsEditing = true
            
            present(imagePicker, animated: true, completion: nil)
        }
    }
    
    func pickFromGallery() {
        if UIImagePickerController.isSourceTypeAvailable(.photoLibrary) {
            flagImageSave = false
            
            imagePicker.delegate = self
            imagePicker.sourceType = .photoLibrary
            imagePicker.mediaTypes = [kUTTypeImage as String]
            imagePicker.allowsEditing = true
            
            present(imagePicker, animated: true, completion: nil)
        }
    }
    
    func getVersion() {
        strVersionCode = Bundle.main.infoDictionary!["CFBundleVersion"] as! String
        strVersion = Bundle.main.infoDictionary!["CFBundleShortVersionString"] as! String
        self.sendVersion(strVersion, strVersionCode)
    }
    
    func sendVersion(_ version: String, _ versionCode: String) {
        DispatchQueue.main.async {
            let script = "javascript:set_app_version('" + version + "','" + versionCode + "',2);"
            self.wvMain.evaluateJavaScript(script, completionHandler: nil)
        }
    }
    
    func setFileInfo(_ photo : String, thumbnail : String) {
        if (m_view_type == 1) {
            let script = "javascript:DealUploadedImages('" + photo + "', '" + thumbnail + "');"
            wvMain.evaluateJavaScript(script, completionHandler: nil)
        } else {
            let script = "javascript:bridgeCallback('" + thumbnail + "', '" + photo + "');"
            wvMain.evaluateJavaScript(script, completionHandler: nil)
        }
    }
    
    func setFilesInfo(_ photos : String) {
        let script = "javascript:bridgeCallback('" + photos + "');"
        wvMain.evaluateJavaScript(script, completionHandler: nil)
    }
    
    func uploadImage(_ imgList : [UIImage]) {
        Alamofire.upload(multipartFormData: { multipartFormData in
            for ind in 0..<imgList.count {
                multipartFormData.append(UIImageJPEGRepresentation(imgList[ind], 0.7)!, withName: "img", fileName: "image.jpg", mimeType: "image/jpeg")
            }

        }, to: m_webUrl + "Intro/file_upload") { (result) in
            switch result {
            case .success(let upload, _, _):
                
                upload.responseJSON { response in
                    let json = JSON(response.result.value ?? "")
                    
                    let code = json["code"].intValue
                    
                    if code == 0 {
                        let file_url = json["url"].stringValue
                        let file_name = json["file"].stringValue

                        self.setFileInfo(file_url, thumbnail: file_name)
                    }
                }
                
            case .failure(let encodingError):
                print(encodingError)
            }
        }
    }
    
    func uploadImages(_ imgList : [UIImage]) {
        self.imageIndex = 0
        self.imageFiles = ""
        self.imageCount = imgList.count
        
        let group = DispatchGroup()
        
        for ind in 0..<imgList.count {
            group.enter()
            
            Alamofire.upload(multipartFormData: { multipartFormData in
                multipartFormData.append(UIImageJPEGRepresentation(imgList[ind], 0.7)!, withName: "img", fileName: "image.jpg", mimeType: "image/jpeg")
            }, to: m_webUrl + "Intro/file_upload") { (result) in
                switch result {
                case .success(let upload, _, _):
                    
                    upload.responseJSON { response in
                        let json = JSON(response.result.value ?? "")
                        
                        let code = json["code"].intValue
                        
                        if code == 0 {
                            let file_name = json["file"].stringValue
                            if file_name != "" {
                                self.imageFiles += file_name + ";"
//                                print(file_name)
//                                print(self.imageFiles)
                            }
                        }
                        
                        self.imageIndex += 1
                        if self.imageIndex == self.imageCount {
                            self.setFilesInfo(self.imageFiles)
//                            print("succ: " + self.imageFiles)
                        }
                        
                        group.leave()
                    }
                    
                case .failure(let encodingError):
                    self.imageIndex += 1
                    if (self.imageIndex == self.imageCount) {
                        self.setFilesInfo(self.imageFiles)
//                        print("err:" + self.imageFiles)
                    }
                    print(encodingError)
                    
                    group.leave()
                    
                }
            }
        }
    }
    
}

extension ViewController:WKScriptMessageHandler {
    
    func userContentController(_ userContentController: WKUserContentController, didReceive message: WKScriptMessage) {
        
        let params = message.body as! NSDictionary
        
        switch message.name {
	    case "bridgeLoginSuccess":
                let email = params.value(forKey: "email") as! String
                let pwd = params.value(forKey: "pwd") as! String
                let usr_type = params.value(forKey: "usr_type") as! Int
                
                let userDefaults = UserDefaults.standard
                userDefaults.setValue(email, forKey: "usr_id")
                userDefaults.setValue(pwd, forKey: "usr_pwd")
                userDefaults.setValue(usr_type, forKey: "usr_type")
            
            case "bridgeLogout":
                m_userID = ""
                m_userPwd = ""
                m_pushType = 0
                let userDefaults = UserDefaults.standard
                userDefaults.setValue("", forKey: "usr_id")
                userDefaults.setValue("", forKey: "usr_pwd")
                userDefaults.setValue("", forKey: "usr_type")
                userDefaults.synchronize()
            
            case "bridgeLoginInfo":
                let userDefaults = UserDefaults.standard
                var email = userDefaults.value(forKey: "usr_id") as? String
                var pwd = userDefaults.value(forKey: "usr_pwd") as? String
                var usr_type = userDefaults.value(forKey: "usr_type") as? Int
                
//                let current_year = Calendar.current.component(.year, from: Date())
//                let current_month = Calendar.current.component(.month, from: Date())
//
//                let cur_day = String(current_year) + "-" + String(current_month)
//                var last_login_month = userDefaults.value(forKey: "last_login_month") as? String
//                if last_login_month == nil {
//                    last_login_month = ""
//                }
//
//                var first_start_flag = 1
//                if(last_login_month == cur_day) {
//                    first_start_flag = 0
//                }
                
                strVersionCode = Bundle.main.infoDictionary!["CFBundleVersion"] as! String
                strVersion = Bundle.main.infoDictionary!["CFBundleShortVersionString"] as! String
            
                if email == nil {
                    email = ""
                }
                
                if pwd == nil {
                    pwd = ""
                }
                                
                if usr_type == nil {
                    usr_type = 3
                }
                
                let script = "javascript:mobileAutoLoginCheck('" + String(describing: Int(usr_type!)) + "','" + email! + "','" + pwd! + "','" + m_token! + "','" + strVersion + "');"
                self.wvMain.evaluateJavaScript(script, completionHandler: nil)
            
            case "bridgeUploadImage":
                let type = params.value(forKey: "type") as! Int
                m_view_type = 0
                
                if type == 1 {
                    pickFromGallery()
                } else {
                    pickFromCamera()
                }
            
            case "bridgeUploadImages":
                let nav: UINavigationController! = self.navigationController
                let storyboard : UIStoryboard! = UIStoryboard.init(name: "Main", bundle: nil)
                let vc = storyboard.instantiateViewController(withIdentifier: "Gallery") as! GalleryViewController

                vc.multi = 1
                vc.count = 10

                nav.pushViewController(vc, animated: true)
            
            case "bridgeFetchVersion":
                let url = "https://itunes.apple.com/lookup?id=1548896978" //TODO: setting app's bundle ID
                let appVersion = Bundle.main.infoDictionary!["CFBundleShortVersionString"] as! String
                
                Alamofire.request(url, method: .get, parameters: nil, headers: nil).responseJSON { (response) in
                    if let value = response.result.value as? [String: AnyObject] {
                        let resultVersion = value["results"]?.value(forKey: "version") as? NSArray
                        if (resultVersion != nil) {
                            let script = "javascript:bridgeCallback('" + (resultVersion![0] as! String) + "');"
                            self.wvMain.evaluateJavaScript(script, completionHandler: nil)
                        } else {
                            let script = "javascript:bridgeCallback('" + appVersion + "');"
                            self.wvMain.evaluateJavaScript(script, completionHandler: nil)
                        }
                    } else {
                        let script = "javascript:bridgeCallback('" + appVersion + "');"
                        self.wvMain.evaluateJavaScript(script, completionHandler: nil)
                    }
                }
            
            case "bridgeSnsInfo":
                // TODO: YJ implement Kakao Login API
                let email = "KKUser1@kakao.com"
                let name = "KK Apple User"
                
                let script = "javascript:bridgeCallback('" + email + "', '" + name + "');"
                self.wvMain.evaluateJavaScript(script, completionHandler: nil)
            
            case "bridgeAppleInfo":
                // TODO: YJ implement Apple Login API
                let email = "AppleUser1@apple.com"
                let name = "Apple User"
                
                let script = "javascript:bridgeCallback('" + email + "', '" + name + "');"
                self.wvMain.evaluateJavaScript(script, completionHandler: nil)
            
            case "bridgeSendVoucher":
                // TODO: YJ implement Kakao Send API
                let urls = params.value(forKey: "urls") as! [String]
                
                let result = "1"
                let script = "javascript:bridgeCallback('" + result + "');"
                self.wvMain.evaluateJavaScript(script, completionHandler: nil)
            
            case "bridgeInviteFriends":
                let url = params.value(forKey: "url") as! String
                
                let sharingItems = [
                    "Conpang",
                    url
                ]
                let activityViewController = UIActivityViewController(activityItems: sharingItems.flatMap({$0}), applicationActivities: nil)
                if UIDevice.current.userInterfaceIdiom == .pad {
                    activityViewController.popoverPresentationController?.sourceView = view
                }
                present(activityViewController, animated: true, completion: nil)
            
            case "bridgeGoUrl":
                let target_url = params.value(forKey: "url") as! String
                if(!target_url.isEmpty) {
                    UIApplication.shared.open(URL(string: target_url)!)
                }

            case "bridgeFinishApp": break

	    case "main":
                if (m_pushType != 0 && m_pushType != 3 && m_saleId != 0) {
                    let request = URLRequest(url: URL(string: "http://greatsoft05.appfrica.co.kr/main/sale_detail/" + String(m_saleId))!)
                    wvMain.load(request)
                    
                    m_saleId = 0
                    m_pushType = 0
                }

            case "camera":
                strFrom = params.value(forKey: "from") as! String
                strWhich = "camera"
                pickFromCamera()

            case "appVersion":
                getVersion()
            
            case "bridgeCopyClipboard":
                let value = params.value(forKey: "value") as! String
                UIPasteboard.general.string = value
                break
            
            case "bridgeResetMonthFlag":
                let userDefaults = UserDefaults.standard
                let current_year = Calendar.current.component(.year, from: Date())
                let current_month = Calendar.current.component(.month, from: Date())
                
                let cur_day = String(current_year) + "-" + String(current_month)
                userDefaults.setValue(cur_day, forKey: "last_login_month")
                break

            case "showProgress":
                self.vwLoading.isHidden = false
            
            case "hideProgress":
                self.vwLoading.isHidden = true            
            
            case "getDeviceInfo":
                let script = "javascript:setDeviceInfo(2, '" + m_token + "');"
                wvMain.evaluateJavaScript(script, completionHandler: nil)
            
            case "go2Dial":
                let phone = params.value(forKey: "user_phone") as! String
                if let url = NSURL(string: "tel://\(phone)"), UIApplication.shared.canOpenURL(url as URL) {
                    UIApplication.shared.open(url as URL, options: [:], completionHandler: nil)
                }
            
            case "save_email":
                let content = params.value(forKey: "email") as! String
                let userDefaults = UserDefaults.standard
                userDefaults.setValue(content, forKey: "user_email")
            
            case "get_email":
                let userDefaults = UserDefaults.standard
                var user_email = userDefaults.value(forKey: "user_email") as? String
                
                if user_email == nil {
                    user_email = ""
                }
                
                let script = "javascript:mobileGetEmail('" + user_email! + "');"
                self.wvMain.evaluateJavaScript(script, completionHandler: nil)
            
            case "go2Msg":
                let phone = params.value(forKey: "user_phone") as! String
                
                if (MFMessageComposeViewController.canSendText()) {
                    let controller = MFMessageComposeViewController()
                    controller.body = ""
                    controller.recipients = [phone]
                    controller.messageComposeDelegate = self
                    self.present(controller, animated: true, completion: nil)
                }
            
            default:
                break
            
        }
        
    }
    
}

extension ViewController : MFMessageComposeViewControllerDelegate {
    
    func messageComposeViewController(_ controller: MFMessageComposeViewController, didFinishWith result: MessageComposeResult) {
        
        self.dismiss(animated: true, completion: nil)
    }
    
}

extension ViewController : WKNavigationDelegate {
    
    func webView(_ webView: WKWebView, didFinish navigation: WKNavigation!) {
        vwLoading.isHidden = true
    }
    
    func webView(_ webView: WKWebView, didStartProvisionalNavigation navigation: WKNavigation!) {
        vwLoading.isHidden = false
    }
    
    func webView(_ webView: WKWebView, didFail navigation: WKNavigation!, withError error: Error) {
        vwLoading.isHidden = true
    }
    
    func webView(_ webView: WKWebView, decidePolicyFor navigationAction: WKNavigationAction, decisionHandler: @escaping (WKNavigationActionPolicy) -> Void) {
        
        if let url = navigationAction.request.url, url.absoluteString.hasPrefix("http://pf.kakao.com") {
            UIApplication.shared.open(url, options: [:], completionHandler: nil)
            decisionHandler(.cancel)
            return
        }
        
//        self.wvMain.load(navigationAction.request)
        decisionHandler(.allow)
    }
    
}

extension ViewController : UIImagePickerControllerDelegate, UINavigationControllerDelegate {
    
    func imagePickerController(_ picker: UIImagePickerController, didFinishPickingMediaWithInfo info: [String : Any]) {
        let mediaType = info[UIImagePickerControllerMediaType] as! NSString
        
        if mediaType.isEqual(to: kUTTypeImage as NSString as String) {
            captureImage = info[UIImagePickerControllerOriginalImage] as! UIImage
            
            if flagImageSave {
                UIImageWriteToSavedPhotosAlbum(captureImage, self, nil, nil)
            }
            
            // 크롭뷰현시
            let cropViewControler = CropViewController(image: captureImage)
            cropViewControler.delegate = self
            
            if strFrom == "user" {
                cropViewControler.aspectRatioPreset = .presetSquare
            } else {
                cropViewControler.aspectRatioPreset = .preset4x3
            }
            
            cropViewControler.rotateButtonsHidden = true
            cropViewControler.rotateClockwiseButtonHidden = true
            
            
            cropViewControler.aspectRatioLockEnabled = true
            cropViewControler.resetAspectRatioEnabled = false
            
            cropViewControler.doneButtonTitle = "Save"
            cropViewControler.cancelButtonTitle = "Cancel"
            
            self.dismiss(animated: true, completion: {
                self.present(cropViewControler, animated: true, completion: nil)
            })
        } else {
            self.dismiss(animated: true, completion: nil)
        }
        
    }
    
    func imagePickerControllerDidCancel(_ picker: UIImagePickerController) {
        self.dismiss(animated: true, completion: nil)
    }
    
}

extension ViewController : CropViewControllerDelegate {
    
    func cropViewController(_ cropViewController: CropViewController, didCropToImage image: UIImage, withRect cropRect: CGRect, angle: Int) {
        
        self.dismiss(animated: true, completion: nil)
        
        var imgList = [UIImage]()
        imgList.append(image)
        uploadImage(imgList)
    }
    
}
