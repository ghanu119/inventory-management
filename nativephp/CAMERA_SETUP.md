# Webcam / barcode scanner in the desktop app (Windows)

Camera access for the barcode scanner is enabled in **`electron/src/main/index.js`** by both `setPermissionCheckHandler` and `setPermissionRequestHandler` for the `media` permission. Both are required so that permission checks and the actual getUserMedia request succeed.

If you run a **fresh NativePHP Electron install** (e.g. re-copy of the Electron project from vendor), that file may be overwritten and the camera permission code removed. Re-add it as follows.

At the end of **`nativephp/electron/src/main/index.js`**, ensure you have:

```js
import { app, session } from 'electron'   // add session to the import

// ... existing NativePHP.bootstrap() call ...

app.whenReady().then(() => {
  const ses = session.defaultSession

  // Allow permission checks for media (camera/mic) so getUserMedia can run
  ses.setPermissionCheckHandler((webContents, permission) => {
    if (permission === 'media') return true
    return false
  })

  // Allow permission requests when the page calls getUserMedia()
  ses.setPermissionRequestHandler((webContents, permission, callback, details) => {
    if (permission === 'media') {
      callback(true)
    } else {
      callback(false)
    }
  })
})
```

Then **rebuild the desktop app** (e.g. `php artisan nativephp:build` or your usual build command) so the changed `src/main/index.js` is compiled into `out/main/index.js`.

On **Windows**, users may also need to allow camera access in **Settings → Privacy & security → Camera** for the desktop app.

**Fallback when camera is unavailable:** The barcode scanner UI includes an **Upload image** button (and on camera error, **Choose image file to scan**). Users can select an image file from disk to decode barcode/QR without using the camera, so barcode functionality works on desktop even if camera access is denied or unavailable.
