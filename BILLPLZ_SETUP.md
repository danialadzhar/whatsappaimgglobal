# 🚀 Billplz Integration - Quick Setup Guide

## ✅ What Has Been Implemented

### Backend (Laravel)
1. **BillplzService** (`app/Services/BillplzService.php`)
   - Handle Billplz API calls
   - Create payment bills
   - Verify webhook signatures
   - Parse webhook data

2. **CheckoutController** (`app/Http/Controllers/CheckoutController.php`)
   - Create direct payment
   - Get available payment gateways

3. **WebhookController** (`app/Http/Controllers/WebhookController.php`)
   - Handle Billplz webhook callbacks
   - Process payment success/failure
   - Auto stock management

4. **Database Migration**
   - Added payment fields to `orders` table
   - Already migrated ✅

5. **Routes**
   - `POST /api/checkout/billplz` - Create payment
   - `GET /api/payment/gateways` - Get payment methods
   - `POST /api/webhook/billplz` - Webhook callback (Billplz → Laravel)
   - `GET /billplz/redirect` - Redirect after payment

6. **Configuration**
   - Added Billplz config to `config/services.php`

---

## 🔧 Setup Steps (DO THIS NEXT)

### Step 1: Add Environment Variables

Tambah baris ini ke file `.env`:

```env
# Billplz Configuration
BILLPLZ_API_KEY=your_api_key_here
BILLPLZ_COLLECTION_ID=your_collection_id_here
BILLPLZ_X_SIGNATURE_KEY=your_x_signature_key_here
BILLPLZ_SANDBOX=true
```

**Cara dapatkan credentials:**

1. **Create Billplz Account:**
   - Sandbox: https://www.billplz-sandbox.com/
   - Production: https://www.billplz.com/

2. **Get API Key:**
   - Login → Settings → Developer Settings → API Keys
   - Copy "Secret Key"

3. **Create Collection:**
   - Billing → Collections → Create New Collection
   - Name: "E-commerce Orders" (atau nama lain)
   - Copy Collection ID

4. **Get X-Signature Key:**
   - Settings → Developer Settings → X Signature Key
   - Click "Generate" if not exists
   - Copy the key

### Step 2: Configure Billplz Webhook

Login ke Billplz Dashboard dan set:

**For Local Development (using ngrok):**
```bash
# Install ngrok
brew install ngrok

# Run ngrok
ngrok http 8000

# Copy the HTTPS URL (e.g., https://abc123.ngrok.io)
```

**Webhook URLs:**
- Callback URL: `https://your-domain.com/api/webhook/billplz`
- Redirect URL: `https://your-domain.com/billplz/redirect`

**Location in Billplz:**
- Settings → Developer Settings → Callback URL

### Step 3: Test Integration

```bash
# Start Laravel server
php artisan serve

# In another terminal, test API
curl -X POST http://localhost:8000/api/checkout/billplz \
  -H "Content-Type: application/json" \
  -d '{
    "customer_name": "Test User",
    "customer_email": "test@example.com",
    "customer_phone": "0123456789",
    "product_id": 1,
    "quantity": 1,
    "delivery_method": "cod",
    "payment_method": "full",
    "bank_code": "BP-FKR01"
  }'
```

### Step 4: Update Frontend

Rujuk file `BILLPLZ_FRONTEND_EXAMPLE.md` untuk contoh integration dengan:
- Vue.js / Inertia
- React
- Vanilla JavaScript

**Basic flow:**
```javascript
// 1. Call API
const response = await fetch('/api/checkout/billplz', {
  method: 'POST',
  body: JSON.stringify(orderData),
});

const result = await response.json();

// 2. Redirect to Billplz
if (result.success) {
  window.location.href = result.data.bill_url;
}
```

---

## 📋 Checklist

### Development Setup
- [ ] Add credentials to `.env`
- [ ] Test API endpoint dengan Postman/curl
- [ ] Setup ngrok untuk local webhook testing
- [ ] Configure webhook URL di Billplz dashboard
- [ ] Test payment flow end-to-end
- [ ] Check webhook logs di `storage/logs/laravel.log`

### Frontend Integration
- [ ] Update checkout form dengan bank selection
- [ ] Call `/api/checkout/billplz` endpoint
- [ ] Handle redirect to Billplz
- [ ] Test payment success flow
- [ ] Test payment failed flow

### Production Deployment
- [ ] Set `BILLPLZ_SANDBOX=false`
- [ ] Update to production API key & Collection ID
- [ ] Update webhook URL to production domain
- [ ] Test with real bank account (small amount)
- [ ] Monitor webhook logs for errors
- [ ] Setup email notifications (optional)

---

## 🧪 Testing Guide

### Test Payment Methods

**Sandbox Bank Codes:**
- `BP-FKR01` - Billplz Simulator (instant success/fail)
- `TEST0001` - Test Bank 1
- `TEST0002` - Test Bank 2

**Production Bank Codes:**
- `MB2U0227` - Maybank2u
- `BCBB0235` - CIMB Clicks
- `PBB0233` - Public Bank
- `BP-BST01` - Boost
- `BP-TNG01` - TouchNGo
- `BP-BILLPLZ1` - Visa/Mastercard

### Test Scenarios

1. **Happy Path:**
   - Create order → Pay successfully → Order status updated
   
2. **Failed Payment:**
   - Create order → Payment failed → Stock restored

3. **Webhook Timing:**
   - Test both webhook callback and redirect
   - Verify idempotency (no duplicate processing)

4. **Error Handling:**
   - Invalid product ID
   - Insufficient stock
   - Network errors

---

## 📁 File Structure

```
whatsappai/
├── app/
│   ├── Http/Controllers/
│   │   ├── CheckoutController.php       ✅ NEW
│   │   └── WebhookController.php        ✅ NEW
│   ├── Models/
│   │   └── Order.php                    📝 Updated
│   └── Services/
│       └── BillplzService.php           ✅ NEW
├── config/
│   └── services.php                     📝 Updated
├── database/migrations/
│   └── *_add_billplz_fields_to_orders_table.php  ✅ NEW
├── routes/
│   ├── api.php                          📝 Updated
│   └── web.php                          📝 Updated
└── documentation/
    ├── BILLPLZ_INTEGRATION.md           📚 Documentation
    ├── BILLPLZ_FRONTEND_EXAMPLE.md      📚 Frontend Examples
    └── BILLPLZ_SETUP.md                 📚 This file
```

---

## 🆘 Troubleshooting

### Issue: "Unauthorized" error
**Solution:** Check `BILLPLZ_API_KEY` in `.env` is correct

### Issue: Webhook not called
**Solution:** 
- Verify webhook URL is accessible from internet
- Check URL di Billplz dashboard
- Use ngrok for local testing

### Issue: Payment stuck at "pending"
**Solution:**
- Check webhook logs: `tail -f storage/logs/laravel.log`
- Manually trigger webhook from Billplz dashboard
- Check bill status at Billplz dashboard

### Issue: Stock not restored on failed payment
**Solution:**
- Check webhook is being called
- Verify `payment_status` field in database
- Check logs for errors in WebhookController

---

## 📚 Documentation Files

1. **BILLPLZ_INTEGRATION.md** - Complete integration guide
2. **BILLPLZ_FRONTEND_EXAMPLE.md** - Frontend code examples
3. **BILLPLZ_SETUP.md** - This quick setup guide

---

## 🔗 Useful Links

- **Billplz API Docs:** https://www.billplz.com/api
- **Billplz Sandbox:** https://www.billplz-sandbox.com/
- **Billplz Support:** https://help.billplz.com/
- **Ngrok Download:** https://ngrok.com/download

---

## 💡 Next Steps

1. **Setup .env credentials** ← START HERE
2. **Test API with Postman/curl**
3. **Setup ngrok for webhooks**
4. **Integrate frontend** (refer to BILLPLZ_FRONTEND_EXAMPLE.md)
5. **Test payment flow**
6. **Deploy to production**

---

## 📞 Need Help?

Kalau ada masalah atau soalan:
1. Check logs: `storage/logs/laravel.log`
2. Refer to documentation files
3. Check Billplz API documentation
4. Contact Billplz support

---

**Integration completed successfully! 🎉**

Now you can accept payments via:
- FPX (All major banks)
- E-Wallets (Boost, TouchNGo, Grab, Shopee Pay)
- Credit/Debit Cards (Visa, Mastercard)
- PayPal

