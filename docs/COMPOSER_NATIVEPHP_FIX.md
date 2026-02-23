# Fix: Composer cURL 23 When Installing nativephp/mobile

Error:
```
curl error 23 while downloading https://nativephp.composer.sh/packages.json: Failed writing received data to disk/application
```

Try these in order.

---

## 1. Clear Composer cache and retry

```bash
composer clear-cache
composer require nativephp/mobile
```

---

## 2. Exclude folders from Windows Defender (recommended on Windows)

Windows Defender can lock files during download and cause this error.

1. Open **Windows Security** → **Virus & threat protection** → **Manage settings** under "Virus & threat protection settings".
2. Scroll to **Exclusions** → **Add or remove exclusions** → **Add an exclusion** → **Folder**.
3. Add these folders:
   - Your project folder, e.g. `D:\gashra\inventory_system`
   - Composer cache: `%LOCALAPPDATA%\Composer` (or `C:\Users\<YourUser>\AppData\Local\Composer`)
   - Temp: `C:\temp` (create it if needed)

4. Retry:
   ```bash
   composer clear-cache
   composer require nativephp/mobile
   ```

---

## 3. Run Composer from a short path

If the project path is very long, try from a shorter one (e.g. `C:\proj\inv`) to avoid path length issues:

```bash
cd C:\proj\inv
# copy or clone your project here, then:
composer clear-cache
composer require nativephp/mobile
```

---

## 4. Use Composer with increased timeout and no cache

```bash
composer clear-cache
composer require nativephp/mobile --no-cache --prefer-dist
```

Or with a longer timeout:

```bash
set COMPOSER_PROCESS_TIMEOUT=300
composer require nativephp/mobile
```

---

## 5. Check disk space and PHP/Composer

- Ensure the drive has enough free space (e.g. > 500 MB).
- Use PHP 8.3+ for Composer (NativePHP mobile requirement):
  ```bash
  php -v
  ```
- Update Composer:
  ```bash
  composer self-update
  ```

---

## 6. Temporarily disable real-time antivirus (last resort)

Only if the above fail and you are sure the source is safe:

- Temporarily turn off real-time protection, run `composer require nativephp/mobile`, then turn it back on.

After a successful install, you can remove the exclusions if you prefer, but keeping the project and Composer cache excluded often avoids similar issues later.
