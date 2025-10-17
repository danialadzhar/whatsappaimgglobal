# Billplz Environment Variables

Add these to your `whatsappai/.env` file:

```env
# ===========================================
# BILLPLZ API CREDENTIALS
# ===========================================
# Get these from: https://www.billplz-sandbox.com/enterprise (sandbox)
# or https://www.billplz.com/enterprise (production)

BILLPLZ_API_KEY=your_api_key_here
BILLPLZ_COLLECTION_ID=your_collection_id_here
BILLPLZ_X_SIGNATURE_KEY=your_x_signature_key_here

# Set to false for production
BILLPLZ_SANDBOX=true

# ===========================================
# APPLICATION URLS
# ===========================================
# Laravel Backend URL (current app)
APP_URL=http://localhost:8000

# Next.js Frontend URL (ecommerce-mgglobal)
FRONTEND_URL=http://localhost:3000
```

---

## Billplz Dashboard Configuration

Login to your Billplz dashboard and configure:

### 1. Callback URL (Webhook)
```
http://localhost:8000/api/webhook/billplz
```
**Production:**
```
https://yourdomain.com/api/webhook/billplz
```

### 2. Redirect URL
```
http://localhost:8000/billplz/redirect
```
**Production:**
```
https://yourdomain.com/billplz/redirect
```

### 3. X-Signature Key
1. Enable X-Signature in Billplz dashboard settings
2. Copy the generated key
3. Paste into `BILLPLZ_X_SIGNATURE_KEY` in your `.env` file

---

## Testing with ngrok (Local Development)

For local webhook testing, you need to expose your localhost to internet:

```bash
# Install ngrok
brew install ngrok

# Start ngrok tunnel
ngrok http 8000

# Use the ngrok URL in Billplz dashboard
# Example: https://abc123.ngrok.io/api/webhook/billplz
```

---

## Quick Setup Steps

1. Copy environment variables above to `whatsappai/.env`
2. Get Billplz credentials from dashboard
3. Configure webhook & redirect URLs in Billplz dashboard
4. Enable X-Signature and copy the key
5. Test with ngrok for local development
6. Update URLs for production deployment

For detailed setup guide, see: `BILLPLZ_WEBHOOK_SETUP.md`

