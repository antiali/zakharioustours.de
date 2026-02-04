# YTrip Plugin - Quick Access Guide

## 🔑 How to Access Admin Panel

### Step 1: Login
Go to: `/wp-admin/wp-login.php`
Login with Administrator account

### Step 2: Go to Settings
Navigate to: **YTrip** in left sidebar menu
OR
Direct URL: `/wp-admin/admin.php?page=ytrip-settings`

---

## ✅ What to Check

If you see error, check these:

1. **Is Codestar Framework loaded?**
   - Look for green checkmark "✓ Codestar Framework is loaded correctly"
   - If red, framework files are missing

2. **Can you manage options?**
   - Look for "Can manage_options: ✓ Yes"
   - If No, you're not an admin

3. **Is CSF class available?**
   - Look for "CSF Class: ✓ Loaded"
   - If Not Loaded, check file paths

---

## 🔧 If Still Error

### Option 1: Deactivate & Reactivate
1. Go to **Plugins** > **Installed Plugins**
2. Find **YTrip - Travel Booking Manager**
3. Click **Deactivate**
4. Wait 5 seconds
5. Click **Activate**

### Option 2: Check Files
These files must exist:
```
wp-content/plugins/ytrip/vendor/codestar-framework/codestar-framework.php
wp-content/plugins/ytrip/vendor/codestar-framework/classes/setup.class.php
wp-content/plugins/ytrip/admin/codestar-config.php
```

### Option 3: Clear Cache
If using caching plugin, clear all cache

---

## 📝 Quick Debug Info

**Current User:** [Shown on settings page]
**Capabilities:** [Shown on settings page]
**Framework Status:** [Shown on settings page]

---

## 🆘 Need Help?

Check the **Troubleshooting** section on settings page for detailed steps.

---

**Last Updated:** 2026-02-04 06:30 GMT
**Version:** 1.0.0
