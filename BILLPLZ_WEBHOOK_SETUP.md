# Billplz Webhook Setup Guide

## Overview
Webhook Billplz telah diimplementasikan untuk handle automatic payment status updates. Flow ini membolehkan sistem auto-update order status lepas customer buat payment.

---

## 🔄 Complete Payment Flow

```
1. Customer submit checkout form di Next.js frontend
   ↓
2. Frontend POST ke /api/checkout/billplz
   ↓
3. Backend Laravel:
   - Create order (status: pending, payment_status: pending)
   - Create Billplz bill dengan direct payment gateway
   - Deduct stock temporarily
   - Return bill_url
   ↓
4. Customer redirect ke Billplz payment page
   ↓
5. Customer buat payment di Billplz
   ↓
6. [WEBHOOK - Background Process]
   Billplz POST ke /api/webhook/billplz
   - Verify X-Signature (security)
   - Update order status: pending → processing
   - Update payment_status: pending → paid
   - OR restore stock if payment failed
   ↓
7. [REDIRECT - User Action]
   Billplz redirect customer to /billplz/redirect
   - Laravel verify payment
   - Update order if webhook belum sampai
   - Redirect to Next.js success page with order_number
   ↓
8. Next.js Success Page
   - Fetch order details from API
   - Display payment status badge
   - Show order confirmation
```

---

## ⚙️ Configuration Required

### 1. Laravel `.env` Configuration

Add these to your `whatsappai/.env`:

```env
# Billplz API Credentials
BILLPLZ_API_KEY=your_billplz_api_key_here
BILLPLZ_COLLECTION_ID=your_collection_id_here
BILLPLZ_X_SIGNATURE_KEY=your_x_signature_key_here
BILLPLZ_SANDBOX=true

# Application URLs
APP_URL=http://localhost:8000
FRONTEND_URL=http://localhost:3000
```

### 2. Billplz Dashboard Configuration

Login to Billplz Dashboard dan configure:

#### Callback URL (Webhook):
```
http://localhost:8000/api/webhook/billplz
```
**IMPORTANT:** Untuk production, ganti dengan your actual domain!

#### Redirect URL:
```
http://localhost:8000/billplz/redirect
```

#### X-Signature Key:
- Enable X-Signature di Billplz dashboard
- Copy key dan set dalam `BILLPLZ_X_SIGNATURE_KEY`

---

## 📋 API Endpoints

### 1. Create Payment Bill
```
POST /api/checkout/billplz
```

**Request Body:**
```json
{
  "customer_name": "Ahmad Ali",
  "customer_phone": "0123456789",
  "customer_email": "ahmad@example.com",
  "delivery_method": "cod|postage|walkin",
  "payment_method": "full|booking|walkin",
  "product_id": 1,
  "quantity": 2,
  "color": "Black",
  "bank_code": "MB2U0227"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "order_id": 1,
    "order_number": "ORD-20250117-12345",
    "bill_url": "https://www.billplz-sandbox.com/bills/xxx",
    "bill_id": "xxx",
    "total_amount": 299.00
  },
  "message": "Payment bill created successfully"
}
```

### 2. Get Payment Gateways
```
GET /api/payment/gateways
```

**Response:**
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
      "BP-TNG01": "TouchNGo",
      ...
    },
    "card": {
      "BP-BILLPLZ1": "Visa/Mastercard (Billplz)",
      ...
    }
  }
}
```

### 3. Webhook Callback (Billplz calls this)
```
POST /api/webhook/billplz
```

**Billplz sends:**
```json
{
  "id": "bill_id",
  "collection_id": "xxx",
  "paid": "true",
  "state": "paid",
  "amount": "29900",
  "paid_amount": "29900",
  "paid_at": "2025-01-17 12:00:00",
  "x_signature": "..."
}
```

### 4. Get Order by Order Number
```
GET /api/orders/{order_number}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "order": {
      "id": 1,
      "order_number": "ORD-20250117-12345",
      "customer_name": "Ahmad Ali",
      "total_amount": 299.00,
      "status": "processing",
      "payment_status": "paid",
      "order_items": [...]
    }
  }
}
```

---

## 🔒 Security Features

### 1. X-Signature Verification
Webhook verify authenticity menggunakan HMAC SHA256:
- Prevent fake webhook calls
- Ensure data integrity

### 2. Idempotency Check
- Prevent duplicate payment processing
- Check kalau order dah paid sebelum update

### 3. Stock Management
- Temporary deduction on order creation
- Auto-restore if payment fails
- Prevent overselling

---

## 🧪 Testing Webhook Locally

### Option 1: Using ngrok (Recommended)
```bash
# Install ngrok
brew install ngrok

# Start ngrok tunnel
ngrok http 8000

# Use ngrok URL in Billplz dashboard
# Example: https://abc123.ngrok.io/api/webhook/billplz
```

### Option 2: Manual Testing
```bash
# Simulate webhook POST
curl -X POST http://localhost:8000/api/webhook/billplz \
  -H "Content-Type: application/json" \
  -d '{
    "id": "test_bill_id",
    "collection_id": "test_collection",
    "paid": "true",
    "state": "paid",
    "amount": "29900",
    "paid_amount": "29900",
    "reference_2": "ORD-20250117-12345"
  }'
```

---

## 📊 Order Status Flow

### Payment Status:
- `pending` → Order created, waiting payment
- `paid` → Payment successful (updated by webhook)
- `failed` → Payment failed

### Order Status:
- `pending` → Order created
- `processing` → Payment successful, order being prepared
- `completed` → Order completed/delivered
- `cancelled` → Order cancelled or payment failed

---

## 🐛 Troubleshooting

### Webhook Not Receiving Callbacks

**Check:**
1. Billplz dashboard callback URL configured correctly
2. Server accessible dari internet (use ngrok for local)
3. Check Laravel logs: `storage/logs/laravel.log`

**Debug Commands:**
```bash
# Check webhook logs
tail -f storage/logs/laravel.log | grep Billplz

# Test webhook endpoint
curl http://localhost:8000/api/webhook/billplz
```

### Order Status Not Updating

**Check:**
1. Webhook received? Check logs
2. X-Signature valid? Check `BILLPLZ_X_SIGNATURE_KEY`
3. Order exists? Check `reference_2` in webhook data

### Payment Redirect Issues

**Check:**
1. `FRONTEND_URL` configured correctly in `.env`
2. Redirect URL in Billplz dashboard: `http://localhost:8000/billplz/redirect`
3. Check browser console for errors

---

## 📝 Important Notes

1. **Webhook adalah CRITICAL** - Jangan disable atau skip webhook setup
2. **X-Signature** - MUST enable untuk security
3. **Production URLs** - Remember to update all URLs untuk production
4. **Stock Management** - Webhook handle stock restoration automatically
5. **Idempotency** - Webhook boleh dipanggil multiple times, system handle duplicates

---

## 🚀 Production Deployment Checklist

- [ ] Update `APP_URL` to production domain
- [ ] Update `FRONTEND_URL` to production domain  
- [ ] Set `BILLPLZ_SANDBOX=false`
- [ ] Get production API keys dari Billplz
- [ ] Update Billplz dashboard callback URL
- [ ] Update Billplz dashboard redirect URL
- [ ] Test webhook dengan ngrok first
- [ ] Monitor logs for webhook calls
- [ ] Test payment flow end-to-end

---

## 📞 Support

Kalau ada issues, check:
1. Laravel logs: `storage/logs/laravel.log`
2. Browser console errors
3. Network tab untuk API calls
4. Billplz dashboard transaction logs

