# YTrip Test Plugin - Debug Access Issues

## 🧪 Test Plugin Created

I created a simple test plugin to check your access issues.

## 📁 Location
```
wp-content/plugins/ytrip-test/ytrip-test.php
```

## 🔧 How to Test

### Step 1: Activate Test Plugin
1. Go to: **Plugins** > **Installed Plugins**
2. Find: **YTrip Test Plugin**
3. Click: **Activate**

### Step 2: Visit Test Pages

**Page 1 - General Test (Everyone can see):**
```
/wp-admin/admin.php?page=ytrip-test
```
- Shows your user info
- Shows your roles
- Shows capabilities
- Shows if you're administrator

**Page 2 - Admin Test (Admin only):**
```
/wp-admin/admin.php?page=ytrip-admin-test
```
- Tests if you can access pages with `manage_options`
- If you see "SUCCESS", your account is fine
- If you see "ERROR", you don't have admin permissions

## 🔍 What to Check

### On Test Page 1:
1. **Is Administrator:**
   - ✓ YES = Good
   - ✗ NO = Login with admin account

2. **Can manage_options:**
   - ✓ YES = Should work
   - ✗ NO = Permission issue

3. **CSF Class Loaded:**
   - ✓ LOADED = Framework OK
   - ✗ NOT LOADED = Framework files missing

### On Test Page 2:
- **SUCCESS** = Your account has correct permissions
- **ERROR** = Your account is missing `manage_options`

## 📋 Share Results

Please visit both test pages and tell me:
1. What you see on Page 1
2. What you see on Page 2
3. Any error messages

This will help me fix the issue! 🚀
