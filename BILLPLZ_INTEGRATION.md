# Billplz Direct Payment Gateway Integration

## Overview
Integration Billplz Direct Payment Gateway untuk sistem e-commerce Laravel. Sistem ini menggunakan architecture yang betul dimana **Laravel sebagai backend** (source of truth) dan frontend hanya call API endpoints.

## Features Implemented
✅ Direct Payment Gateway (bypass Billplz selection page)  
✅ Support multiple payment methods (FPX, E-Wallets, Cards)  
✅ Secure webhook callback dengan X-Signature verification  
✅ Idempotency untuk prevent duplicate payments  
✅ Auto stock management (deduct on order, restore on failed payment)  
✅ Comprehensive logging dan error handling  

---

## Setup Instructions

### 1. Environment Configuration

Tambah configuration berikut ke file `.env`:

```env
# Billplz Configuration
BILLPLZ_API_KEY=your_api_key_here
BILLPLZ_COLLECTION_ID=your_collection_id_here
BILLPLZ_X_SIGNATURE_KEY=your_x_signature_key_here
BILLPLZ_SANDBOX=true

# Application URL (untuk webhook callback)
APP_URL=http://localhost:8000
```

**Cara dapatkan credentials:**
1. Login ke [Billplz Dashboard](https://www.billplz.com/)
2. API Key: Settings → API Keys
3. Collection ID: Billing → Collections (create new atau guna existing)
4. X-Signature Key: Settings → Developer Settings

### 2. Run Migration

Migration sudah dijalankan. Database fields berikut ditambah ke `orders` table:
- `billplz_bill_id` - Bill ID dari Billplz
- `billplz_collection_id` - Collection ID
- `payment_status` - Status payment (pending, paid, failed, refunded)
- `paid_at` - Timestamp bila payment successful
- `idempotency_key` - Unique key untuk prevent duplicate
- `payment_metadata` - JSON metadata dari Billplz

### 3. Configure Billplz Webhook

Login ke Billplz Dashboard dan set webhook URL:

**Callback URL (Compulsory):**
```
https://your-domain.com/api/webhook/billplz
```

**Redirect URL (Optional tapi recommended):**
```
https://your-domain.com/billplz/redirect
```

**Note:** Untuk local testing, guna ngrok atau expose.sh untuk expose local server.

---

## API Endpoints

### 1. Create Payment (Checkout)

**Endpoint:** `POST /api/checkout/billplz`

**Request Body:**
```json
{
  "customer_name": "Ahmad bin Ali",
  "customer_phone": "0123456789",
  "customer_email": "ahmad@example.com",
  "delivery_method": "cod",
  "payment_method": "full",
  "product_id": 1,
  "quantity": 2,
  "color": "Black",
  "bank_code": "MB2U0227"
}
```

**Bank Codes Available:**
- `MB2U0227` - Maybank2u
- `BCBB0235` - CIMB Clicks
- `PBB0233` - Public Bank
- `BP-BST01` - Boost
- `BP-TNG01` - TouchNGo
- `BP-BILLPLZ1` - Visa/Mastercard (default)

**Response:**
```json
{
  "success": true,
  "data": {
    "order_id": 123,
    "order_number": "ORD-20251016-001",
    "bill_url": "https://www.billplz.com/bills/xyz123?auto_submit=true",
    "bill_id": "xyz123",
    "total_amount": 199.50
  },
  "message": "Payment bill created successfully"
}
```

**Frontend Flow:**
```javascript
// Call API
const response = await fetch('/api/checkout/billplz', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify(orderData)
});

const result = await response.json();

// Redirect to Billplz
if (result.success) {
  window.location.href = result.data.bill_url;
}
```

### 2. Get Payment Gateways

**Endpoint:** `GET /api/payment/gateways`

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
      "BP-BILLPLZ1": "Visa/Mastercard",
      ...
    }
  }
}
```

### 3. Webhook Callback (Billplz → Laravel)

**Endpoint:** `POST /api/webhook/billplz`

Endpoint ini akan dipanggil automatically oleh Billplz server bila payment status berubah. **Jangan panggil endpoint ini manually dari frontend.**

**System akan:**
- Verify X-Signature untuk security
- Update order status (pending → processing/cancelled)
- Update payment_status (pending → paid/failed)
- Restore stock kalau payment failed
- Log transaction details

---

## Payment Flow

### Standard Flow
```
1. Customer → Checkout Form (Frontend)
2. Frontend → POST /api/checkout/billplz (Laravel)
3. Laravel → Create Order (status: pending, payment_status: pending)
4. Laravel → Create Bill via Billplz API
5. Laravel → Return bill_url to Frontend
6. Frontend → Redirect customer to bill_url
7. Customer → Pay di Billplz
8. Billplz → POST /api/webhook/billplz (Webhook Callback)
9. Laravel → Update order status & payment_status
10. Billplz → Redirect customer to redirect_url
11. Customer → Lihat success/failed page
```

### Direct Payment Gateway Flow
Dengan `auto_submit=true`, customer akan **skip Billplz selection page** dan terus redirect ke bank/gateway yang dipilih.

---

## Code Structure

### Files Created/Modified

**New Files:**
- `app/Services/BillplzService.php` - Billplz API integration
- `app/Http/Controllers/CheckoutController.php` - Payment checkout logic
- `app/Http/Controllers/WebhookController.php` - Webhook callback handler
- `database/migrations/*_add_billplz_fields_to_orders_table.php` - Database migration

**Modified Files:**
- `app/Models/Order.php` - Added payment fields
- `config/services.php` - Added Billplz configuration
- `routes/api.php` - Added payment routes
- `routes/web.php` - Added redirect route

### Key Classes

#### BillplzService
```php
// Create payment
$billplz = app(BillplzService::class);
$bill = $billplz->createDirectPaymentBill($order, 'MB2U0227');

// Verify webhook
$isValid = $billplz->verifyWebhookSignature($data, $signature);

// Get payment gateways
$gateways = $billplz->getPaymentGateways();
```

#### CheckoutController
- `createDirectPayment()` - Create order & Billplz bill
- `getPaymentGateways()` - Get available payment methods

#### WebhookController
- `handleBillplzCallback()` - Process webhook dari Billplz
- `handleBillplzRedirect()` - Handle customer redirect after payment

---

## Security Best Practices

### ✅ Implemented
1. **X-Signature Verification** - Verify webhook authenticity
2. **Idempotency** - Prevent duplicate payment processing
3. **HTTPS Only** - All Billplz API calls via HTTPS
4. **API Key Security** - Stored in .env, never exposed to frontend
5. **Order Validation** - Validate stock before creating payment
6. **Error Logging** - Comprehensive logging untuk debugging

### ⚠️ Important Notes
- **Jangan expose API key** ke frontend
- **Webhook URL must be public** (accessible from internet)
- **Always verify X-Signature** di webhook handler
- **Test dengan sandbox** sebelum production

---

## Testing

### Sandbox Testing
1. Set `BILLPLZ_SANDBOX=true` di `.env`
2. Guna sandbox credentials dari Billplz Sandbox dashboard
3. Test bank codes: `TEST0001`, `TEST0002`, `TEST0003`, `BP-FKR01`

### Test Payment Flow
```bash
# 1. Create test order
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

# 2. Open bill_url returned in response
# 3. Complete payment di Billplz
# 4. Check webhook logs di storage/logs/laravel.log
```

### Local Webhook Testing
Untuk test webhook di local environment, guna **ngrok**:

```bash
# Install ngrok
brew install ngrok

# Expose local server
ngrok http 8000

# Update Billplz webhook URL dengan ngrok URL
# Example: https://abc123.ngrok.io/api/webhook/billplz
```

---

## Troubleshooting

### Common Issues

**1. Webhook tidak dipanggil**
- Check webhook URL di Billplz dashboard betul
- Pastikan URL accessible from internet (guna ngrok untuk local)
- Check firewall/security settings

**2. X-Signature verification failed**
- Pastikan `BILLPLZ_X_SIGNATURE_KEY` betul di `.env`
- Check webhook data format

**3. Payment stuck di pending**
- Check webhook logs: `storage/logs/laravel.log`
- Manually check bill status via Billplz dashboard
- Check database `payment_status` field

**4. Stock tidak restore bila payment failed**
- Check webhook callback running successfully
- Check logs untuk error messages

### Debug Mode
```php
// Add to WebhookController untuk debug
Log::info('Billplz Webhook Debug', [
    'headers' => $request->headers->all(),
    'body' => $request->all(),
    'signature' => $request->header('X-Signature'),
]);
```

---

## Production Checklist

Before going live:

- [ ] Set `BILLPLZ_SANDBOX=false`
- [ ] Update ke production API key & Collection ID
- [ ] Update webhook URL ke production domain
- [ ] Enable X-Signature verification
- [ ] Test all payment methods (FPX, E-Wallet, Card)
- [ ] Setup monitoring/alerts untuk failed webhooks
- [ ] Review error logs regularly
- [ ] Implement reconciliation job (optional)

---

## Future Enhancements

Suggested improvements:
1. **Email notifications** - Send email pada order paid/failed
2. **SMS notifications** - Send SMS confirmation
3. **Refund API** - Implement refund functionality
4. **Recurring billing** - For subscription payments
5. **Reconciliation job** - Auto-sync payment status from Billplz
6. **Admin dashboard** - View payment statistics

---

## Support & Documentation

- **Billplz API Docs:** https://www.billplz.com/api
- **Billplz Support:** https://help.billplz.com/
- **Developer Community:** https://developers.billplz.com/

---

## Changelog

**Version 1.0.0** (16 Oct 2025)
- Initial implementation
- Direct Payment Gateway integration
- Webhook callback handler
- X-Signature verification
- Idempotency support
- Auto stock management

