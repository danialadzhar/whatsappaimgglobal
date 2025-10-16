# 🧪 Quick Testing Guide - Billplz Integration

## Setup Untuk Testing (5 Minit)

### 1. Setup Environment (.env)

Tambah baris ini ke file `.env`:

```env
# Billplz Sandbox Configuration
BILLPLZ_API_KEY=73eb57f0-7d4e-42b9-a544-aeac6e4b0f81
BILLPLZ_COLLECTION_ID=inbmmepb
BILLPLZ_X_SIGNATURE_KEY=S-HDXHxRJb5Vjrr4hk6K9w
BILLPLZ_SANDBOX=true
```

**Note:** Credentials di atas adalah contoh. Guna credentials dari Billplz Sandbox account anda.

**Cara dapatkan credentials:**
1. Register/Login: https://www.billplz-sandbox.com/
2. API Key: Settings → Developer Settings → API Keys
3. Collection: Billing → Collections → Create New (nama: "Test Orders")
4. X-Signature: Settings → Developer Settings → Generate X-Signature Key

### 2. Build Frontend Assets

```bash
npm run build
# atau untuk dev mode
npm run dev
```

### 3. Clear Laravel Cache (Optional tapi recommended)

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

---

## Testing Flow

### Step 1: Access Checkout Page

1. Buka browser: `http://localhost:8000/ecommerce`
2. Pilih mana-mana product
3. Click "Buy Now" atau masuk ke product detail
4. Click "Checkout"

### Step 2: Fill Form

**Customer Information:**
- Name: `Test User`
- Phone: `0123456789`
- Email: `test@example.com`

**Delivery Method:**
- Pilih salah satu (contoh: COD)

**Payment Method:**
- Pilih salah satu (contoh: Full Payment - 5% discount)

**Payment Gateway:**
Untuk sandbox testing, pilih:
- **BP-FKR01** - Billplz Simulator (recommended untuk test)
- **TEST0001** - Test Bank 1
- **MB2U0227** - Maybank2u (sandbox)

### Step 3: Submit & Test Payment

1. Click **"Proceed to Payment"**
2. Anda akan di-redirect ke Billplz payment page
3. Di Billplz Sandbox:
   - Untuk test **SUCCESS**: Click "Pay Now" → Proceed
   - Untuk test **FAILED**: Klik cancel atau back button

### Step 4: Verify Results

#### Check Database:
```bash
php artisan tinker
```

```php
// Check latest order
$order = \App\Models\Order::latest()->first();
$order->payment_status; // Should be 'paid' or 'failed'
$order->billplz_bill_id; // Should have value
$order->paid_at; // Should have timestamp if paid

// Check payment metadata
$order->payment_metadata;
```

#### Check Logs:
```bash
tail -f storage/logs/laravel.log
```

Cari log messages:
- `Billplz Webhook Received`
- `Billplz Webhook: Payment successful`
- `Billplz Webhook: Payment failed`

---

## Testing Scenarios

### ✅ Scenario 1: Successful Payment

1. Fill checkout form
2. Select **BP-FKR01** (Billplz Simulator)
3. Click "Proceed to Payment"
4. At Billplz page, click "Pay Now"
5. Complete payment (sandbox akan auto-success)

**Expected Results:**
- Order `payment_status` = `paid`
- Order `status` = `processing`
- `paid_at` timestamp recorded
- Stock deducted

### ❌ Scenario 2: Failed Payment

1. Fill checkout form
2. Select **BP-FKR01**
3. Click "Proceed to Payment"
4. At Billplz page, click "Back" or "Cancel"

**Expected Results:**
- Order `payment_status` = `failed`
- Order `status` = `cancelled`
- Stock restored to original

### 🔄 Scenario 3: Test Different Payment Methods

Test dengan different bank codes:
- `MB2U0227` - Maybank
- `BCBB0235` - CIMB
- `BP-BST01` - Boost (E-Wallet)
- `BP-TNG01` - TouchNGo

---

## Quick API Testing (Postman/cURL)

### Test Payment Gateway List

```bash
curl http://localhost:8000/api/payment/gateways
```

**Expected Response:**
```json
{
  "success": true,
  "data": {
    "fpx": {
      "MB2U0227": "Maybank2u",
      "BCBB0235": "CIMB Clicks",
      ...
    },
    "ewallet": {
      "BP-BST01": "Boost",
      ...
    },
    "card": {
      "BP-BILLPLZ1": "Visa/Mastercard"
    }
  }
}
```

### Test Create Payment

```bash
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

**Expected Response:**
```json
{
  "success": true,
  "data": {
    "order_id": 123,
    "order_number": "ORD-20251016-001",
    "bill_url": "https://billplz-sandbox.com/bills/xyz?auto_submit=true",
    "bill_id": "xyz123",
    "total_amount": 199.50
  }
}
```

---

## Webhook Testing (Local Development)

Untuk test webhook di localhost, **kena guna ngrok**:

### Setup Ngrok

```bash
# Install ngrok
brew install ngrok

# Run ngrok
ngrok http 8000
```

Ngrok akan bagi URL seperti: `https://abc123.ngrok.io`

### Update Billplz Webhook URL

1. Login Billplz Sandbox
2. Settings → Developer Settings
3. Set Callback URL: `https://abc123.ngrok.io/api/webhook/billplz`
4. Set Redirect URL: `https://abc123.ngrok.io/billplz/redirect`

### Test Webhook

1. Create payment dari checkout
2. Complete payment di Billplz
3. Check ngrok dashboard: `http://localhost:4040`
4. Verify webhook POST received
5. Check Laravel logs untuk webhook processing

---

## Common Issues & Solutions

### ❌ Issue: "Unauthorized" Error

**Solution:**
```bash
# Check .env
grep BILLPLZ_API_KEY .env

# Clear config cache
php artisan config:clear
```

### ❌ Issue: Payment Gateway tidak muncul

**Solution:**
```bash
# Test API endpoint
curl http://localhost:8000/api/payment/gateways

# Check if BillplzService berfungsi
php artisan tinker
>>> app(\App\Services\BillplzService::class)->getPaymentGateways()
```

### ❌ Issue: Redirect to 404 after payment

**Solution:**
- Check route `billplz.redirect` exists
- Check redirect_url di `config/services.php`

### ❌ Issue: Stock tidak restore bila payment failed

**Solution:**
- Check webhook logs
- Verify X-Signature key betul
- Check if webhook URL accessible

---

## Checklist Before Testing

- [ ] `.env` configured dengan Billplz credentials
- [ ] Database migrated (`php artisan migrate`)
- [ ] Frontend assets built (`npm run build` atau `npm run dev`)
- [ ] Laravel server running (`php artisan serve`)
- [ ] At least 1 product exists in database
- [ ] Product has stock > 0

---

## Testing Commands

```bash
# Start Laravel server
php artisan serve

# Watch frontend (separate terminal)
npm run dev

# Watch logs (separate terminal)
tail -f storage/logs/laravel.log

# Check database
php artisan tinker
>>> \App\Models\Order::latest()->get()
>>> \App\Models\Product::first()
```

---

## Success Indicators

✅ **Checkout page loads** dengan bank selection  
✅ **Payment gateways** shown (FPX, E-Wallet, Cards)  
✅ **Form submission** redirects to Billplz  
✅ **Webhook callback** processed (check logs)  
✅ **Order status** updated correctly  
✅ **Stock management** working (deduct/restore)  

---

## Next Steps After Testing

1. ✅ Test all payment methods (FPX, E-Wallet, Cards)
2. ✅ Test successful & failed payments
3. ✅ Verify webhook callbacks
4. ✅ Check order tracking works
5. 🚀 Ready for production!

---

**Happy Testing! 🎉**

Kalau ada issue, check:
1. Laravel logs: `storage/logs/laravel.log`
2. Browser console (F12)
3. Network tab untuk API calls
4. Database untuk verify data

