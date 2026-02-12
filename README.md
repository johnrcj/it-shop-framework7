# Framework7 / It-Coupon 

<img src="mobile/assets/images/ic_splash_logo.png" alt="Splash Logo" width="240">

A multi-platform voucher management system — admin backend + mobile web app + native Android/iOS clients.

- 🚀 Web backend: CodeIgniter (PHP)
- 📱 Mobile web & hybrid UI: Framework7 (Framework7 + jQuery + Framework7 assets)
- 🤖 Android: Gradle project
- 🍎 iOS: Xcode with CocoaPods (Firebase, Alamofire, etc.)
- 🗄️ Database: MySQL (schema in database/shop.sql)

---

## 🎯 About

Coupon (it-shop-framework7) is a voucher / coupon manager platform. The repository contains:

- Admin dashboard (admin/) — CodeIgniter based.
- Mobile web API & UI (mobile/) — CodeIgniter endpoints and Framework7-powered UI.
- Native mobile clients:
  - Android app (app/android/shop) — Gradle project using a small library included.
  - iOS app (app/ios/shop) — Swift project using CocoaPods for Firebase, Alamofire, CropViewController, etc.
- Database schema: database/shop.sql

Framework7 is used as the mobile UI framework (see mobile/assets and mobile/views). The Framework7 files and styles power the mobile experience, making it feel native on Android/iOS devices when wrapped in a WebView or used as a PWA shell.

---

## 📸 Screenshot

A mobile splash logo (relative path) is included:

<img src="mobile/assets/images/ic_splash_logo.png" alt="App splash" width="320">

(If you open this README on GitHub, the image will render from the repository path.)

---

## 🗂️ Project Structure (deep level)

High-level tree (selected important files & folders). Use this to quickly find where to edit features.

- 📁 root
  - 📁 admin
    - 📄 index.php (CodeIgniter admin front controller)
    - 📁 application
      - 📁 config
        - 📄 config.php (base_url, encryption key, etc.)
        - 📄 database.php (admin DB config)
        - 📄 routes.php
      - 📁 controllers
        - 📄 Home.php (example admin controller)
        - 📄 Login.php, Notice.php, Qna.php, Terms.php, User.php, Voucher.php, Warning.php
      - 📁 core
        - 📄 MY_Controller.php, MY_Model.php, Common.php
      - 📁 libraries
        - 📁 Classes/PHPExcel (spreadsheet utilities)
      - 📁 views
        - 📁 layout (header.php, footer.php)
        - 📁 login, notice, qna, terms, user, voucher, warning (list/detail views)
    - 📁 assets
      - 📁 global (plugins: amcharts, bootstrap, datatables, etc.)
      - 📁 layouts, pages (JS/CSS)
  - 📁 mobile
    - 📄 index.php (CodeIgniter mobile front controller)
    - 📁 application
      - 📁 config
        - 📄 application_config.php (assets_url, timezone)
        - 📄 database.php (mobile DB config)
        - 📄 routes.php (mobile routes)
      - 📁 controllers
        - 📄 Intro.php, Main.php, Mypage.php, Refund.php, Term.php
        - Main.php contains many API endpoints (recognize voucher, get_home_info, add_voucher, etc.)
      - 📁 views
        - 📁 login, main, mypage, refund (HTML templates used by Framework7)
    - 📁 assets
      - 📁 css
        - framework7.min.css, app.css
      - 📁 js
        - framework7.js, app.js, utils, routes.js
      - 📁 images
        - splash & UI assets (ic_splash_logo.png, icons...)
      - 📁 fonts
        - Framework7Icons, MaterialIcons, etc.
  - 📁 app
    - 📁 android
      - 📁 shop
        - 📁 app (Android app module)
          - build.gradle (app module)
          - src/main/java/co/shop/*.java (Native Android code)
        - 📁 library (SlideToggle view library used by Android project)
    - 📁 ios
      - 📁 shop
        - Podfile (Firebase, Alamofire, CropViewController, SwiftyJSON)
        - shop.xcodeproj / shop.xcworkspace
        - AppDelegate.swift, ViewController.swift
  - 📁 database
    - 📄 shop.sql (MySQL database schema)
  - 📄 LICENSE (MIT)
  - ... (many vendor assets and 3rd-party libs)

---

## 🧩 Tech Stack

- Backend
  - PHP (CodeIgniter 3.x) — admin and mobile APIs
  - MySQL — schema in database/shop.sql
  - PHPExcel — spreadsheet handling (admin/application/libraries/Classes/PHPExcel)
- Mobile web UI
  - Framework7 — primary UI framework for mobile web (mobile/assets/**)
  - jQuery, moment.js, Framework7 routes
- Native clients
  - Android — Java + Gradle (app/android/shop)
  - iOS — Swift + CocoaPods (Pods include Firebase, Alamofire, CropViewController, SwiftyJSON)
- Integrations
  - Firebase Messaging / InstanceID / Analytics (iOS pods, Android FCM code in app)
- Dev & Tools
  - Gradle for Android
  - CocoaPods for iOS
  - Many third-party JS/CSS plugins in admin/assets (DataTables, amcharts, fullcalendar, CKEditor, etc.)

---

## ⚙️ Installation & Quickstart

These steps will get a development environment running locally.

Prerequisites
- PHP 7.x (compatible with CodeIgniter)
- Apache or Nginx (or PHP built-in server for quick tests)
- MySQL (or MariaDB)
- Node / npm (optional for front-end workflows)
- Android Studio (for Android)
- Xcode + CocoaPods (for iOS)

1. Clone the repository
```bash
git clone https://github.com/johnrcj/it-shop-framework7.git
cd it-shop-framework7
```

2. Database — import schema
```bash
# create database
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS shop CHARACTER SET utf8 COLLATE utf8_general_ci;"
# import schema
mysql -u root -p shop < database/shop.sql
```

3. Configure backend (admin & mobile)
- Edit admin/application/config/config.php
  - Set $config['base_url'] to your admin URL, e.g. `http://localhost/ShopAdmin/` or use a virtual host.
  - Set $config['encryption_key'] (non-empty random string).
- Edit admin/application/config/database.php and mobile/application/config/database.php
  - Update `hostname`, `username`, `password`, `database` according to your DB.
- Edit mobile/application/config/application_config.php (assets_url/admin_url) if you serve mobile assets from different path.

4. Web server
- Option A — Virtual hosts (recommended): create vhosts for:
  - admin -> document root to repo/admin
  - mobile -> document root to repo/mobile
- Option B — PHP built-in (quick dev)
```bash
# Serve mobile on port 8080
cd mobile
php -S localhost:8080
# Serve admin on port 8081
cd ../admin
php -S localhost:8081
```
Note: CodeIgniter is usually served through Apache/Nginx because of URL rewrites. If using PHP built-in, ensure index.php handles requests (or use a router script).

5. Native Android
```bash
cd app/android/shop
# from Android Studio: Open project and run on device/emulator
# or command line (gradlew)
./gradlew assembleDebug
```

6. Native iOS
```bash
cd app/ios/shop
pod install
# open shop.xcworkspace in Xcode and build/run
open shop.xcworkspace
```

---

## 🔐 Configuration Tips

- Admin base_url: admin/application/config/config.php -> $config['base_url']
- Mobile assets URL: mobile/application/config/application_config.php -> $config['assets_url']
- Database credentials: admin/application/config/database.php and mobile/application/config/database.php
- Encryption key: admin/application/config/config.php -> $config['encryption_key'] (set to a secure random string)
- Session/cookie settings: config.php (check cookie_domain, cookie_secure if using HTTPS)

---

## 🛠️ Development Notes & API pointers

- Routes
  - Admin routes: admin/application/config/routes.php (default controller: Login)
  - Mobile routes: mobile/application/config/routes.php (default controller: intro)
- Key controllers:
  - Admin: admin/application/controllers/* (Login, User, Notice, Voucher, etc.)
  - Mobile API: mobile/application/controllers/Main.php — many API endpoints you can call from mobile app.
    - Examples:
      - POST /main/get_home_info — requires user_id, returns available/use_end/expired vouchers.
      - POST /main/recognize_voucher — sends image path and returns recognized voucher info (test stubbed).
- Views & UI:
  - Mobile UI built with Framework7 templates found under mobile/application/views and mobile/assets.
  - Framework7 routes and page templates defined in mobile/assets/js/routes.js and mobile/assets/js/app.js

Example: Getting home info (mobile)
- Endpoint located in mobile/application/controllers/Main.php -> get_home_info()
- Parameters: type, search_key, user_id
- Response: JSON containing lists and counts for available/use_end/expired vouchers.

---

## 📦 Packaging & Third-party Licenses

- Many third-party libraries are included under admin/assets and app/ios/shop/Pods.
- Please check the respective LICENSE files (e.g., admin/assets/global/plugins/* LICENSE files, Pods LICENSEs) when redistributing.

---

## 🤝 Contributing

- Issues and PRs welcome.
- Suggested flow:
  - Fork → feature branch → PR with description and screenshots (if UI)
  - Keep commits small and focused; write clear commit messages.
- Coding standards:
  - PHP (CodeIgniter best practices), HTML/CSS/JS for front-end, Java for Android, Swift for iOS.

---

## 🧭 Future Roadmap

| Feature | Area | Priority | Status |
|---|---:|:---:|:---|
| Improve voucher recognition engine (OCR/barcode) | Mobile / API | 🔥 High | Planned |
| Add automated tests for backend controllers | Backend | ⚙️ Medium | Planned |
| PWA support (offline vouchers) | Mobile (Framework7) | ⚙️ Medium | Planned |
| Push-notifications improvements / topics | Mobile / Native | 🔥 High | In progress |
| Admin UX polish (datatable filters) | Admin UI | ⚙️ Low | Planned |
| CI / Dockerization for dev env | DevOps | ⚙️ Medium | Planned |

---

## 📚 References & Resources

- CodeIgniter: https://codeigniter.com
- Framework7: https://framework7.io (Mobile UI used in mobile/assets)
- Android Gradle: see app/android/shop/build.gradle
- iOS CocoaPods: see app/ios/shop/Podfile (Firebase/Messaging/Auth, CropViewController, Alamofire, SwiftyJSON)

---

## 📞 Contact & Maintainers

- Repository: johnrcj/it-shop-framework7
- License: MIT (see LICENSE)

---

## 📝 License

MIT License — see LICENSE file.

---

Thank you for sharing your project! If you want, I can:
- Generate a shorter developer quickstart checklist (1-page).
- Extract a minimal demo environment (Docker Compose) to start the app and DB locally.
- Add an API reference section documenting endpoints in mobile/controllers/Main.php.