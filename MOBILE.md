# NativePHP Mobile — iOS & Android

This project can run as a **native mobile app** on **iOS** and **Android** using [NativePHP for Mobile](https://nativephp.com/docs). Use this guide to install the mobile package and run the app on iOS (or Android).

---

## 1. License

NativePHP for Mobile is **not open source**. You need a **paid license**:

- Purchase at: **https://nativephp.com/mobile**
- Your license key is used as the Composer password when installing the package.

---

## 2. Requirements

- **PHP 8.3+** (CLI) for Composer and Artisan
- **Laravel 11+** (you already have this)
- **iOS builds: macOS only** — You **cannot** build or run the iOS app on Windows or Linux. You need:
  - **macOS**
  - **Xcode 16.0+** (from Mac App Store)
  - **Xcode Command Line Tools**: `xcode-select --install`
  - **CocoaPods**: `brew install cocoapods`
- **Android builds**: Windows, macOS, or Linux with [Android Studio](https://developer.android.com/studio) and SDK.

If you don’t have a Mac, you can use [Bifrost](https://bifrost.nativephp.com/) (cloud build service) for iOS.

---

## 3. Install the mobile package

The Composer repository for NativePHP is already added in this project. From the project root:

```bash
composer require nativephp/mobile
```

When prompted:

- **Username**: email address used when you bought the license  
- **Password**: your **license key**

---

## 4. Environment variables

In your `.env`, set (or uncomment and edit the block in `.env.example`):

```env
NATIVEPHP_APP_ID=com.yourcompany.inventory_system
NATIVEPHP_APP_VERSION=DEBUG
NATIVEPHP_APP_VERSION_CODE=1
```

For **iOS** (optional but recommended), set your Apple Developer Team ID (from [developer.apple.com](https://developer.apple.com/account) → Membership details):

```env
NATIVEPHP_DEVELOPMENT_TEAM=YOUR_TEAM_ID
```

---

## 5. Run the NativePHP installer

This sets up the native project (e.g. `nativephp/` and config):

```bash
php artisan native:install
```

Choose whether you need **ICU-enabled PHP** (e.g. if you use `intl` or Filament). For this inventory app, the default (non-ICU) is usually fine.

---

## 6. Vite config for mobile

After installing `nativephp/mobile`, update `vite.config.js` so the mobile build and hot reload work.

**Replace** your current `vite.config.js` with:

```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import { nativephpMobile, nativephpHotFile } from './vendor/nativephp/mobile/resources/js/vite-plugin.js';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            hotFile: nativephpHotFile(),
        }),
        tailwindcss(),
        nativephpMobile(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
```

Before building for a specific platform, run:

```bash
npm run build -- --mode=ios
# or
npm run build -- --mode=android
```

---

## 7. Run on iOS (macOS only)

1. **Open the project on a Mac** (same repo, or copy it over).
2. **Install dependencies** (if needed):
   ```bash
   composer install
   npm install
   ```
3. **Build assets for iOS**:
   ```bash
   npm run build -- --mode=ios
   ```
4. **Run the app and choose iOS**:
   ```bash
   php artisan native:run
   ```
   When prompted, select **iOS** (simulator or device).

5. **Optional — use Xcode directly**:
   ```bash
   php artisan native:open
   ```
   Then open the iOS project in Xcode and run from there.

**Real iOS device:**  
- Enable [Developer Mode](https://developer.apple.com/documentation/xcode/enabling-developer-mode-on-a-device) on the device.  
- Add the device in [Apple Developer → Devices](https://developer.apple.com/account/resources/devices/list).

---

## 8. Run on Android

On any supported OS (Windows/macOS/Linux):

1. Build for Android:
   ```bash
   npm run build -- --mode=android
   php artisan native:run
   ```
2. Select **Android** when prompted.  
3. For a real device: enable [Developer options](https://developer.android.com/studio/debug/dev-options#enable) and **USB debugging (ADB)**.

---

## 9. Hot reload (faster development)

After a change, you can push updates without a full recompile:

```bash
php artisan native:watch ios
# or
php artisan native:watch android
```

Or build once and then watch:

```bash
php artisan native:run --watch
```

Keep `NATIVEPHP_APP_VERSION=DEBUG` in `.env` during development so the app always loads the latest Laravel build.

---

## 10. Summary: “Jump” to iOS

- **On Windows:** You cannot run or build the iOS app locally. Use a **Mac** or **Bifrost** (cloud) for iOS.
- **On macOS:**  
  1. `composer require nativephp/mobile` (with license).  
  2. Set `.env` (e.g. `NATIVEPHP_APP_ID`, `NATIVEPHP_APP_VERSION`, `NATIVEPHP_APP_VERSION_CODE`, optional `NATIVEPHP_DEVELOPMENT_TEAM`).  
  3. `php artisan native:install`.  
  4. Update `vite.config.js` as in section 6, then `npm run build -- --mode=ios`.  
  5. `php artisan native:run` and choose **iOS** to run in simulator or on device.

For more detail, see the official docs: [NativePHP Mobile](https://nativephp.com/docs).
