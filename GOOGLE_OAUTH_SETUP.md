# Google OAuth Setup Guide

## Overview
The "Continue with Google" functionality has been implemented using Laravel Socialite. Follow these steps to configure it:

## Step 1: Get Google OAuth Credentials

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project (or use an existing one)
3. Enable the "Google+ API"
4. Go to "Credentials" → Create OAuth 2.0 Client ID
5. Choose "Web application"
6. Add Authorized redirect URIs:
   - `http://localhost:8000/auth/google/callback` (development)
   - `http://127.0.0.1:8000/auth/google/callback` (if testing locally)
   - Your production URL: `https://yourdomain.com/auth/google/callback`

7. Copy the Client ID and Client Secret

## Step 2: Configure Environment Variables

Add these to your `.env` file:

```
GOOGLE_CLIENT_ID=your_client_id_here
GOOGLE_CLIENT_SECRET=your_client_secret_here
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```

For production, update `GOOGLE_REDIRECT_URI` to your production domain.

## Step 3: Database

The migration has already been run. The `users` table now has a `google_id` column to store Google user IDs.

## Step 4: Test

1. Navigate to the login page
2. Click "Continue with Google"
3. You'll be redirected to Google's login page
4. After authentication, you'll be logged in and redirected to the dashboard

## How It Works

1. User clicks "Continue with Google" button
2. User is redirected to Google OAuth consent screen
3. After consent, Google redirects back to `/auth/google/callback`
4. The app creates or updates the user record with:
   - Name
   - Email
   - Google ID
   - Email verified timestamp
5. User is automatically logged in and redirected to dashboard

## Features

- ✅ Automatic user creation on first login
- ✅ Email verification automatic (from Google)
- ✅ Remember me functionality
- ✅ Seamless login/registration flow
- ✅ Error handling with user-friendly messages

## Security Notes

- Google ID is stored uniquely to prevent duplicates
- Email is used as primary user identifier
- Password is not required for Google users
- CSRF protection is maintained

## Troubleshooting

### "Redirect URI mismatch" error
- Ensure the redirect URI in your Google Cloud Console exactly matches the one in your `.env` file
- Common issue: localhost vs 127.0.0.1

### User not being created
- Check that `GOOGLE_CLIENT_ID` and `GOOGLE_CLIENT_SECRET` are correct
- Verify the database migration ran successfully

### Session not persisting
- Make sure cookies are enabled in your browser
- Check that `APP_URL` in `.env` is correct
