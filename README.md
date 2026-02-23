# Inventory Management

A desktop and web application for **inventory**, **stock**, and **billing**—built with Laravel and [NativePHP](https://nativephp.com). Run it in the browser or as a native Windows desktop app with products, customers, invoices, serial tracking, and an integrated barcode/QR scanner.

---

## Table of Contents

- [Features](#features)
- [How It Helps Small Business](#how-it-helps-small-business)
- [How to Use](#how-to-use)
- [Barcode Scanner & Text Extraction](#barcode-scanner--text-extraction)
- [Setup Scanner Using Mobile (DroidCam)](#setup-scanner-using-mobile-droidcam)
- [Desktop App Menus](#desktop-app-menus)
- [FAQ](#faq)
- [For Developers](#for-developers)

---

## Features

| Area | Description |
|------|-------------|
| **Products** | Add products with name, SKU, price, GST rate, HSN code. Optional **serial-number tracking** per unit. |
| **Stock** | **Stock In** (purchase, return, adjustment) and **Stock Out** (sales). Per-product stock history and **adjustments**. Serial numbers for in/out when the product uses serials. |
| **Customers** | Store customer name, phone, email, address, GST number. Used when creating invoices. |
| **Invoices** | Create and edit invoices with customer details, multiple line items (product, quantity, serial, price, GST, warranty). **PDF export** for printing. Payment modes: Cash, UPI, Card, Bank. |
| **Dashboard** | Totals for products, customers, invoices, and sales (₹). **High demand / low stock** alerts and **recent invoices**. |
| **Barcode / QR scanner** | Scan via **webcam** (live or capture), or **upload an image**. Supports **barcode** and **QR code** decoding. **Text extraction (OCR)** from a selected area (e.g. serial number under a barcode). |
| **Company & account** | Company settings (name, address, GST, etc.) and account (profile) management. |
| **First-run setup** | Installation wizard to create the admin user and configure the app. |

---

## How It Helps Small Business

- **Single place** for products, stock levels, and serials (when enabled).
- **Invoices** with customer details, GST, and **printable PDFs**.
- **Stock In / Out** with reasons (purchase, return, adjustment) and full **history**.
- **Low-stock and high-demand alerts** on the dashboard.
- **Barcode/QR scanning** speeds up stock-in and invoice line items (serial or product lookup).
- **Desktop app** runs offline on Windows; data stays on the machine (SQLite).

---

## How to Use

### Web (browser)

1. Install PHP 8.2+, Composer, and (optionally) Node.js.
2. Clone the repo, run `composer install`, copy `.env.example` to `.env`, set `APP_KEY` and `DB_DATABASE` (SQLite path).
3. Run `php artisan migrate` and open the app in the browser. Complete the install wizard if it’s the first run.
4. Log in and use **Dashboard**, **Products**, **Stock**, **Customers**, **Invoices**, and **Account** / **Settings**.

### Desktop (Windows)

1. Build the app: `php artisan native:build win all` (see [BUILD.md](BUILD.md) for options).
2. Install the built app from the output folder (e.g. `nativephp/electron/dist/win-unpacked`).
3. On first launch, complete the installation wizard, then log in. Use the app like the web version; **Navigate** and **Help** menus are in the menu bar (see below).

---

## Barcode Scanner & Text Extraction

The scanner is available wherever you see a **scan** (camera) button—e.g. **Stock In** (serial numbers) and **Create/Edit Invoice** (serial/product for a line item).

### How scanning works

1. **Open scanner** – Click the scan button next to the field.
2. **Choose input:**
   - **Live webcam** – Point at barcode/QR; the app can auto-detect and decode.
   - **Capture image** – Take a photo from the camera, then crop/rotate if needed.
   - **Upload image** – Select an image file (useful when no camera is available).
3. **Decode:**
   - **Extract from selection** – Draw a box around the barcode/QR, then decode.
   - **Extract from full image** – Decode from the whole image.
4. The decoded value (e.g. barcode or serial) is sent back into the form field.

### Text extraction (OCR)

- After capturing or uploading an image, draw a **selection** around the text (e.g. serial number below a barcode).
- Click **“Extract text from selection”**.
- The recognized text is inserted into the field. Use this for serials or other printed text when barcode decode isn’t enough.

---

## Setup Scanner Using Mobile (DroidCam)

You can use your **phone as a webcam** for the barcode scanner via [DroidCam](https://droidcam.app/).

1. **On phone** – Install [DroidCam for Android](https://play.google.com/store/apps/details?id=com.dev47apps.obsdroidcam) or [iOS](https://apps.apple.com/us/app/droidcam-webcam-sharing/id1472440573).
2. **On PC** – Install [DroidCam for Windows](https://droidcam.app/windows/).
3. **Connect** – Use **Wi‑Fi** (same network, enter IP:port in the PC client) or **USB** (Android: enable USB debugging, connect cable, select USB in DroidCam).
4. In the app, open the barcode scanner and choose the **DroidCam camera** if multiple devices are listed.

Detailed steps are in the app: **Help → Setup scanner using mobile (webcam)** (or the **?** icon in the scanner modal).

---

## Desktop App Menus

The native Windows app has a menu bar. **Navigate** and **Help** are at the end (after File, Edit, View, Window).

### Navigate menu

| Item | Action |
|------|--------|
| **Go to Dashboard** | Opens the dashboard. Use this if the UI is stuck or the spinner doesn’t go away. |
| **Force Logout** | Logs you out immediately. Use if the app is stuck and you need to get back to the login screen. |

### Help menu

| Item | Action |
|------|--------|
| **Setup scanner using mobile (webcam)** | Opens the DroidCam setup guide in a new window. |
| **Clear cache** | Runs Laravel’s `optimize:clear` (clears route, config, view, and event caches) and redirects to the dashboard with a “Cache removed” message. Use if the app behaves oddly after an update or to clear old cache data. |

---

## FAQ

**Spinner stuck or screen not loading?**  
→ Use **Navigate → Go to Dashboard** to jump back to the dashboard.

**Stuck and need to log out?**  
→ Use **Navigate → Force Logout** to log out and return to the login screen.

**Stuck or odd behavior after an update?**  
→ Use **Help → Clear cache** to clear all app caches, then continue from the dashboard.

**How do I use my phone as a scanner (webcam)?**  
→ Use **Help → Setup scanner using mobile (webcam)** for step-by-step DroidCam setup (Wi‑Fi or USB).

**Where do I scan barcodes in the app?**  
→ Use the **scan (camera)** button next to serial/product fields on **Stock In** and **Create/Edit Invoice**. You can use a built-in webcam, USB scanner, or DroidCam.

---

## For Developers

### Stack

- **PHP 8.2+**, **Laravel 11**
- **NativePHP Desktop** (Electron) for the Windows app
- **SQLite** (default), configurable in `.env`
- **Laravel mPDF** for invoice PDFs
- **Spatie Media Library** (if used for uploads)
- Front end: Blade, Tailwind CSS, Select2; barcode/QR via html5-qrcode and browser/OS APIs

### Requirements

- PHP 8.2+
- Composer
- Node.js & npm (for NativePHP desktop build)
- Windows (for building/running the desktop app)

### Local setup

```bash
composer install
cp .env.example .env
php artisan key:generate
# Set DB_DATABASE in .env (e.g. database/database.sqlite)
php artisan migrate
php artisan serve
```

### Desktop build

```bash
php artisan native:build win all
```

See [BUILD.md](BUILD.md) for secure build options, code signing, and Bifrost.

### Project structure (high level)

- `app/Http/Controllers/` – Auth, Dashboard, Products, Stock, Invoices, Customers, Company, Account, Install
- `app/Providers/NativeAppServiceProvider.php` – Desktop window and menu (Navigate, Help)
- `resources/views/components/barcode-scanner.blade.php` – Scanner UI and logic
- `resources/views/help/scanner-setup.blade.php` – DroidCam setup guide
- `routes/web.php` – Web and help routes (including `help.clear-cache`)

---

## License

MIT.
