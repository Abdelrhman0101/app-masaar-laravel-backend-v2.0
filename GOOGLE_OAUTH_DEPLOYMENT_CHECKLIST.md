# Google OAuth Integration - Deployment Checklist

## Pre-Deployment Requirements

### 1. Google Cloud Console Setup
- [ ] Create a Google Cloud Project (if not exists)
- [ ] Enable Google+ API or Google Identity API
- [ ] Create OAuth 2.0 Client ID credentials
- [ ] Configure authorized redirect URIs:
  - Development: `http://localhost:8000/auth/google/callback`
  - Production: `https://yourdomain.com/auth/google/callback`
- [ ] Note down Client ID and Client Secret

### 2. Environment Configuration
- [ ] Add Google OAuth credentials to `.env` file:
  ```env
  GOOGLE_CLIENT_ID=your_google_client_id
  GOOGLE_CLIENT_SECRET=your_google_client_secret
  GOOGLE_REDIRECT_URI=https://yourdomain.com/auth/google/callback
  ```
- [ ] Ensure `.env` is not committed to version control
- [ ] Update production environment variables

### 3. Database Migration
- [ ] Run migration to add Google OAuth fields:
  ```bash
  php artisan migrate
  ```
- [ ] Verify new columns exist in users table:
  - `google_id` (string, nullable)
  - `avatar` (string, nullable)
  - `login_type` (string, nullable)

### 4. Dependencies
- [ ] Ensure Laravel Socialite is installed:
  ```bash
  composer require laravel/socialite
  ```
- [ ] Clear and rebuild cache:
  ```bash
  php artisan config:clear
  php artisan config:cache
  php artisan route:clear
  php artisan route:cache
  ```

## Code Verification

### 5. Files Created/Modified
- [ ] `config/services.php` - Google OAuth configuration
- [ ] `app/Http/Controllers/Auth/SocialLoginController.php` - OAuth controller
- [ ] `routes/web.php` - Google OAuth routes added
- [ ] `app/Models/User.php` - Updated fillable fields and casts
- [ ] `resources/views/login.blade.php` - Google login button added
- [ ] `database/migrations/*_add_google_oauth_fields_to_users_table.php` - Migration file
- [ ] `tests/Feature/GoogleOAuthTest.php` - Comprehensive tests

### 6. Route Verification
- [ ] Verify routes are registered:
  ```bash
  php artisan route:list --name=google
  ```
- [ ] Expected routes:
  - `GET auth/google/redirect` → `google.redirect`
  - `GET auth/google/callback` → `google.callback`

### 7. Testing
- [ ] Run Google OAuth tests:
  ```bash
  php artisan test --filter=GoogleOAuthTest
  ```
- [ ] All tests should pass (5 tests, 23 assertions)
- [ ] Manual testing:
  - [ ] Click "تسجيل الدخول بـ Google" button
  - [ ] Redirects to Google OAuth
  - [ ] Successful callback creates/updates user
  - [ ] User is logged in and redirected to dashboard

## Security Considerations

### 8. Security Checklist
- [ ] Google OAuth is restricted to `normal` user type only
- [ ] New users are automatically approved (`is_approved = true`)
- [ ] Email verification is bypassed for Google users
- [ ] Error handling prevents information disclosure
- [ ] HTTPS is enforced in production
- [ ] CSRF protection is maintained

### 9. User Experience
- [ ] Google login button is visually integrated
- [ ] Error messages are user-friendly (in Arabic)
- [ ] Loading states are handled appropriately
- [ ] Mobile responsiveness is maintained

## Production Deployment

### 10. Server Configuration
- [ ] Update production `.env` with correct Google credentials
- [ ] Ensure HTTPS is configured and working
- [ ] Update Google Cloud Console with production redirect URI
- [ ] Test OAuth flow in production environment

### 11. Monitoring
- [ ] Set up logging for OAuth failures
- [ ] Monitor user registration patterns
- [ ] Track OAuth conversion rates
- [ ] Set up alerts for authentication errors

### 12. Backup and Rollback
- [ ] Database backup before deployment
- [ ] Rollback plan documented
- [ ] Test rollback procedure in staging

## Post-Deployment Verification

### 13. Functional Testing
- [ ] Test Google OAuth flow end-to-end
- [ ] Verify existing login methods still work
- [ ] Test user creation and login scenarios
- [ ] Verify dashboard access after OAuth login
- [ ] Test error scenarios (OAuth denial, network issues)

### 14. Performance
- [ ] Monitor OAuth response times
- [ ] Check database query performance
- [ ] Verify no memory leaks or resource issues

## Troubleshooting Guide

### Common Issues
1. **"Invalid redirect URI"**
   - Check Google Cloud Console redirect URI configuration
   - Ensure production URL matches exactly

2. **"Client ID not found"**
   - Verify environment variables are set correctly
   - Clear and rebuild config cache

3. **"User not created"**
   - Check database migration status
   - Verify User model fillable fields

4. **"Route not found"**
   - Clear route cache: `php artisan route:clear`
   - Verify routes are properly registered

### Support Contacts
- Development Team: [team@example.com]
- Google Cloud Support: [Google Cloud Console]
- Laravel Documentation: [https://laravel.com/docs/socialite]

---

**Deployment Date:** ___________
**Deployed By:** ___________
**Verified By:** ___________
**Production URL:** ___________