# NativePHP desktop build

## Secure build **without** Bifrost

You can build a more secure desktop app without using Bifrost by doing the following.

### 1. What happens automatically

- **Prebuild** (in `config/nativephp.php`) runs before every build:
  - `php artisan route:clear` – no cached routes bundled
  - `php artisan config:clear` – no cached config bundled
  - `php artisan view:clear` – no cached views bundled
  - `php artisan event:clear` – no cached events bundled  
  So the built app does not ship with stale caches (avoids “Route not defined” and keeps the bundle clean).

- **Env cleanup**: When the app is bundled, keys listed in `config/nativephp.php` under `cleanup_env_keys` are removed from the built `.env` (e.g. AWS, Azure, Bifrost, secrets). Never put production secrets in `.env` if they are not in that list; add them to `cleanup_env_keys` if needed.

- **Excluded files**: `cleanup_exclude_files` in `config/nativephp.php` excludes logs, backup env files, tests, etc. from the build.

### 2. Code signing (recommended for distribution)

Signing makes the app trusted by Windows and reduces “unknown publisher” warnings.

- **Azure Trusted Signing** (no local certificate):  
  Set in `.env`: `AZURE_TENANT_ID`, `AZURE_CLIENT_ID`, `AZURE_CLIENT_SECRET`, `NATIVEPHP_AZURE_PUBLISHER_NAME`, `NATIVEPHP_AZURE_ENDPOINT`, `NATIVEPHP_AZURE_CERTIFICATE_PROFILE_NAME`, `NATIVEPHP_AZURE_CODE_SIGNING_ACCOUNT_NAME`.  
  These are stripped from the built app via `cleanup_env_keys`.

- **Traditional certificate**:  
  Use a code-signing certificate and follow [Electron code signing](https://www.electronforge.io/guides/code-signing/code-signing-windows).

### 3. Build command

```bash
php artisan native:build win all
```

You will still see the **“INSECURE BUILD”** message from NativePHP when no Bifrost bundle is present. That message refers only to the fact that the PHP runtime is not a signed Bifrost bundle and that app source is shipped as files. The steps above still make the build more secure (no stale caches, no secrets in built .env, optional code signing).

### 4. Optional: hide the “insecure build” message

If you want a clean build log without the warning, you can add a wrapper script (e.g. `build-secure.bat` on Windows) that runs `php artisan native:build win all` and filters the output, or ignore the message—it does not change how the app runs.

### 5. With Bifrost (signed PHP bundle)

For the **signed PHP runtime bundle** and no “insecure build” warning, you must use Bifrost: `bifrost:login`, `bifrost:init`, `bifrost:download-bundle`, then `native:build`. See [NativePHP building](https://nativephp.com/docs/desktop/2/publishing/building).
