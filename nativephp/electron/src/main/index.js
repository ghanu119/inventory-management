import { app, session } from 'electron'
import NativePHP from '#plugin'
import path from 'path'

// Inherit User's PATH in Process & ChildProcess
import fixPath from 'fix-path';
fixPath();

const buildPath = path.resolve(import.meta.dirname, import.meta.env.MAIN_VITE_NATIVEPHP_BUILD_PATH);
const defaultIcon = path.join(buildPath, 'icon.png')
const certificate = path.join(buildPath, 'cacert.pem')

const executable = process.platform === 'win32' ? 'php.exe' : 'php';
const phpBinary = path.join(buildPath,'php', executable);
const appPath = path.join(buildPath, 'app')

/**
 * Turn on the lights for the NativePHP app.
 */
NativePHP.bootstrap(
    app,
    defaultIcon,
    phpBinary,
    certificate,
    appPath
);

/**
 * Camera (and microphone) access for webcam barcode scanner in desktop app.
 * Required so getUserMedia() in the renderer works. Both check and request
 * handlers must allow 'media'. On Windows, also allow the app in
 * Settings → Privacy & security → Camera.
 */
function setupCameraPermissions() {
  const ses = session.defaultSession
  if (!ses) return

  ses.setPermissionCheckHandler((webContents, permission) => {
    if (permission === 'media') return true
    return false
  })

  ses.setPermissionRequestHandler((webContents, permission, callback, details) => {
    if (permission === 'media') {
      callback(true)
    } else {
      callback(false)
    }
  })
}

app.whenReady().then(() => {
  setupCameraPermissions()
})
