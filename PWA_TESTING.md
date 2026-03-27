# PWA Installation Guide for Sanctuary

## Testing PWA Installation Locally

### Prerequisites
1. **HTTPS or localhost**: PWAs only work on HTTPS or localhost
2. **Modern browser**: Chrome, Edge, or Safari (latest versions)
3. **XAMPP/Local server**: Running on localhost

### Quick Test Steps

1. **Start your local server** (XAMPP, WAMP, etc.)
   - Access the app at `http://localhost/...your-path.../CLR/`

2. **Open Chrome DevTools** (F12 or Cmd+Option+I)
   - Go to **Application** tab
   - Check **Manifest** section for errors (should show no red errors)
   - Check **Service Workers** section to see if it's registered

3. **Test Installation**
   - On the **Home** page, the install banner should appear at the bottom-right
   - Or go to **Settings** page and click the "Install" button
   - The native browser install dialog should appear

4. **Debug if not working**
   - Open Console tab and look for:
     - "Service Worker registered successfully"
     - "beforeinstallprompt event fired"
   - If you see errors, they will guide you to the issue

### Common Issues and Solutions

#### Issue: "beforeinstallprompt event not firing"

**Possible causes:**
1. **Service Worker not registered** - Check Console for SW errors
2. **Manifest errors** - Check Application > Manifest tab for red errors
3. **Already installed** - Uninstall the PWA first, then try again
4. **Incognito mode** - Some browsers don't support PWA in incognito
5. **Path issues** - Service worker path doesn't match app path

**Solutions:**
- Clear browser cache and reload: Ctrl+Shift+R (Windows) or Cmd+Shift+R (Mac)
- Unregister old service workers: Application > Service Workers > Unregister
- Check Network tab to ensure service-worker.js loads with status 200
- Verify manifest.json loads correctly (not 404)

#### Issue: "Install prompt not available" alert

This happens when:
- The browser hasn't fired `beforeinstallprompt` yet
- The app doesn't meet PWA criteria

**Fix:**
1. Wait 3-5 seconds after page load
2. Reload the page (Ctrl+R or Cmd+R)
3. Check Chrome DevTools Console for any errors
4. Make sure you're on `http://localhost` not `http://127.0.0.1`

#### Issue: Service Worker fails to register

**Common causes:**
- Wrong MIME type (should be `application/javascript`)
- Service worker file not found (404 error)
- Service worker path doesn't match scope

**Fix:**
- Check Network tab for service-worker.js - should be status 200
- The `.htaccess` file sets correct MIME types
- Make sure `service-worker.js` is in the project root

### PWA Installability Criteria (Chrome)

Your app must meet ALL of these:

1. ✅ **Served over HTTPS** (or localhost for testing)
2. ✅ **Has a valid manifest.json** with:
   - name or short_name
   - icons (192px and 512px)
   - start_url
   - display: standalone, fullscreen, or minimal-ui
3. ✅ **Has a registered Service Worker** with a fetch event handler
4. ✅ **start_url returns HTTP 200** (not a redirect to login on first load)

### Testing on Mobile

1. **Deploy to HTTPS hosting** (InfinityFree with HTTPS enabled)
2. **Access from mobile browser**
3. **Chrome Android**: Banner appears automatically or check menu > "Install app"
4. **Safari iOS**: Share button > "Add to Home Screen"

### Debugging Tips

**Console messages to look for:**
```
✅ Service Worker registered successfully: /your-path/
✅ beforeinstallprompt event fired
✅ Showing install banner
```

**Or after clicking Install:**
```
✅ Manual install request pending, triggering prompt
✅ App installed successfully
```

**If you see errors:**
- Service Worker registration failed → Check path and MIME type
- Manifest: No matching icon → Check icon file paths
- Manifest: start_url does not respond with 200 → Check redirects

### Force Reinstall (for testing)

1. Chrome: `chrome://apps` → Right-click Sanctuary → Remove
2. Clear site data: DevTools > Application > Storage > Clear site data
3. Unregister service worker: DevTools > Application > Service Workers > Unregister
4. Hard reload: Ctrl+Shift+R (Windows) or Cmd+Shift+R (Mac)
5. Try installation again

## Production Deployment (InfinityFree)

1. **Enable HTTPS** in hosting control panel
2. **Upload all files** including service-worker.js and manifest.json
3. **Uncomment HTTPS redirect** in .htaccess if needed
4. **Test** from mobile device over HTTPS

The PWA should now work perfectly on both localhost and production!
