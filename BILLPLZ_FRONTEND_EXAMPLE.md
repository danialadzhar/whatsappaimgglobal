# Billplz Frontend Integration Examples

## Vue.js Example (Inertia)

### 1. Checkout Component with Bank Selection

```vue
<template>
  <div class="checkout-container">
    <h2>Payment Checkout</h2>
    
    <!-- Order Summary -->
    <div class="order-summary">
      <h3>Order Summary</h3>
      <p>Product: {{ product.name }}</p>
      <p>Quantity: {{ form.quantity }}</p>
      <p>Total: RM {{ totalAmount }}</p>
    </div>

    <!-- Customer Information -->
    <form @submit.prevent="handleCheckout">
      <div class="form-group">
        <label>Name</label>
        <input v-model="form.customer_name" required />
      </div>

      <div class="form-group">
        <label>Email</label>
        <input v-model="form.customer_email" type="email" required />
      </div>

      <div class="form-group">
        <label>Phone</label>
        <input v-model="form.customer_phone" required />
      </div>

      <!-- Payment Method Selection -->
      <div class="form-group">
        <label>Select Payment Method</label>
        <select v-model="form.bank_code" required>
          <optgroup label="FPX Banks">
            <option value="MB2U0227">Maybank2u</option>
            <option value="BCBB0235">CIMB Clicks</option>
            <option value="PBB0233">Public Bank</option>
            <option value="RHB0218">RHB Now</option>
            <option value="HLB0224">HLB Connect</option>
          </optgroup>
          
          <optgroup label="E-Wallets">
            <option value="BP-BST01">Boost</option>
            <option value="BP-TNG01">TouchNGo</option>
            <option value="BP-2C2PGRB">Grab</option>
            <option value="BP-2C2PSHPE">Shopee Pay</option>
          </optgroup>
          
          <optgroup label="Credit/Debit Cards">
            <option value="BP-BILLPLZ1">Visa / Mastercard</option>
            <option value="BP-PPL01">PayPal</option>
          </optgroup>
        </select>
      </div>

      <!-- Delivery & Payment Options -->
      <div class="form-group">
        <label>Delivery Method</label>
        <select v-model="form.delivery_method" required>
          <option value="cod">Cash on Delivery</option>
          <option value="postage">Postage (5% discount)</option>
          <option value="walkin">Walk-in (10% discount)</option>
        </select>
      </div>

      <div class="form-group">
        <label>Payment Type</label>
        <select v-model="form.payment_method" required>
          <option value="full">Full Payment (5% discount)</option>
          <option value="booking">Booking</option>
          <option value="walkin">Walk-in Payment (3% discount)</option>
        </select>
      </div>

      <!-- Submit Button -->
      <button type="submit" :disabled="processing">
        {{ processing ? 'Processing...' : 'Pay Now' }}
      </button>

      <div v-if="error" class="error-message">
        {{ error }}
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  product: Object,
});

const form = ref({
  customer_name: '',
  customer_email: '',
  customer_phone: '',
  product_id: props.product.id,
  quantity: 1,
  color: '',
  delivery_method: 'cod',
  payment_method: 'full',
  bank_code: 'MB2U0227', // Default
});

const processing = ref(false);
const error = ref('');

const totalAmount = computed(() => {
  // Calculate based on discounts
  let subtotal = props.product.price * form.value.quantity;
  // Add discount calculations here
  return subtotal.toFixed(2);
});

const handleCheckout = async () => {
  processing.value = true;
  error.value = '';

  try {
    // Call Laravel API
    const response = await fetch('/api/checkout/billplz', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify(form.value),
    });

    const result = await response.json();

    if (result.success) {
      // Redirect to Billplz payment page
      window.location.href = result.data.bill_url;
    } else {
      error.value = result.message || 'Payment creation failed';
      processing.value = false;
    }
  } catch (err) {
    error.value = 'Network error. Please try again.';
    processing.value = false;
    console.error('Checkout error:', err);
  }
};
</script>

<style scoped>
.checkout-container {
  max-width: 600px;
  margin: 0 auto;
  padding: 20px;
}

.form-group {
  margin-bottom: 15px;
}

.form-group label {
  display: block;
  margin-bottom: 5px;
  font-weight: 600;
}

.form-group input,
.form-group select {
  width: 100%;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 4px;
}

button {
  width: 100%;
  padding: 15px;
  background: #4CAF50;
  color: white;
  border: none;
  border-radius: 4px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
}

button:disabled {
  background: #ccc;
  cursor: not-allowed;
}

.error-message {
  margin-top: 10px;
  padding: 10px;
  background: #ffebee;
  color: #c62828;
  border-radius: 4px;
}

.order-summary {
  background: #f5f5f5;
  padding: 15px;
  border-radius: 4px;
  margin-bottom: 20px;
}
</style>
```

---

## 2. Dynamic Payment Gateway Selection

```vue
<template>
  <div class="payment-gateways">
    <h3>Choose Payment Method</h3>

    <!-- FPX Banks -->
    <div class="gateway-group">
      <h4>Online Banking (FPX)</h4>
      <div class="gateway-grid">
        <button
          v-for="(name, code) in paymentGateways.fpx"
          :key="code"
          @click="selectGateway(code)"
          :class="{ active: selectedGateway === code }"
          class="gateway-btn"
        >
          <img :src="`/images/banks/${code}.png`" :alt="name" />
          <span>{{ name }}</span>
        </button>
      </div>
    </div>

    <!-- E-Wallets -->
    <div class="gateway-group">
      <h4>E-Wallets</h4>
      <div class="gateway-grid">
        <button
          v-for="(name, code) in paymentGateways.ewallet"
          :key="code"
          @click="selectGateway(code)"
          :class="{ active: selectedGateway === code }"
          class="gateway-btn"
        >
          <img :src="`/images/ewallet/${code}.png`" :alt="name" />
          <span>{{ name }}</span>
        </button>
      </div>
    </div>

    <!-- Cards -->
    <div class="gateway-group">
      <h4>Credit/Debit Cards</h4>
      <div class="gateway-grid">
        <button
          v-for="(name, code) in paymentGateways.card"
          :key="code"
          @click="selectGateway(code)"
          :class="{ active: selectedGateway === code }"
          class="gateway-btn"
        >
          <img :src="`/images/cards/${code}.png`" :alt="name" />
          <span>{{ name }}</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const paymentGateways = ref({
  fpx: {},
  ewallet: {},
  card: {},
});

const selectedGateway = ref('MB2U0227');

const emit = defineEmits(['gateway-selected']);

onMounted(async () => {
  // Fetch available payment gateways from API
  try {
    const response = await fetch('/api/payment/gateways');
    const result = await response.json();
    
    if (result.success) {
      paymentGateways.value = result.data;
    }
  } catch (error) {
    console.error('Failed to load payment gateways:', error);
  }
});

const selectGateway = (code) => {
  selectedGateway.value = code;
  emit('gateway-selected', code);
};
</script>

<style scoped>
.gateway-group {
  margin-bottom: 30px;
}

.gateway-group h4 {
  margin-bottom: 15px;
  color: #333;
}

.gateway-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
  gap: 15px;
}

.gateway-btn {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 15px;
  background: white;
  border: 2px solid #e0e0e0;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s;
}

.gateway-btn:hover {
  border-color: #4CAF50;
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.gateway-btn.active {
  border-color: #4CAF50;
  background: #f1f8f4;
}

.gateway-btn img {
  width: 60px;
  height: 40px;
  object-fit: contain;
  margin-bottom: 10px;
}

.gateway-btn span {
  font-size: 13px;
  text-align: center;
}
</style>
```

---

## 3. Composable untuk Reusable Logic

```javascript
// composables/useBillplzCheckout.js
import { ref } from 'vue';

export function useBillplzCheckout() {
  const processing = ref(false);
  const error = ref('');
  const paymentGateways = ref({});

  const createPayment = async (orderData) => {
    processing.value = true;
    error.value = '';

    try {
      const response = await fetch('/api/checkout/billplz', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: JSON.stringify(orderData),
      });

      const result = await response.json();

      if (result.success) {
        // Redirect to Billplz
        window.location.href = result.data.bill_url;
        return result.data;
      } else {
        error.value = result.message || 'Payment creation failed';
        processing.value = false;
        return null;
      }
    } catch (err) {
      error.value = 'Network error. Please try again.';
      processing.value = false;
      console.error('Checkout error:', err);
      return null;
    }
  };

  const fetchPaymentGateways = async () => {
    try {
      const response = await fetch('/api/payment/gateways');
      const result = await response.json();
      
      if (result.success) {
        paymentGateways.value = result.data;
      }
    } catch (err) {
      console.error('Failed to load payment gateways:', err);
    }
  };

  return {
    processing,
    error,
    paymentGateways,
    createPayment,
    fetchPaymentGateways,
  };
}
```

**Usage:**
```vue
<script setup>
import { useBillplzCheckout } from '@/composables/useBillplzCheckout';

const { processing, error, createPayment } = useBillplzCheckout();

const handleCheckout = async () => {
  await createPayment({
    customer_name: 'John Doe',
    customer_email: 'john@example.com',
    // ... other fields
  });
};
</script>
```

---

## 4. React Example

```jsx
// CheckoutPage.jsx
import { useState } from 'react';

export default function CheckoutPage({ product }) {
  const [form, setForm] = useState({
    customer_name: '',
    customer_email: '',
    customer_phone: '',
    product_id: product.id,
    quantity: 1,
    delivery_method: 'cod',
    payment_method: 'full',
    bank_code: 'MB2U0227',
  });

  const [processing, setProcessing] = useState(false);
  const [error, setError] = useState('');

  const handleSubmit = async (e) => {
    e.preventDefault();
    setProcessing(true);
    setError('');

    try {
      const response = await fetch('/api/checkout/billplz', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: JSON.stringify(form),
      });

      const result = await response.json();

      if (result.success) {
        // Redirect to Billplz
        window.location.href = result.data.bill_url;
      } else {
        setError(result.message || 'Payment creation failed');
        setProcessing(false);
      }
    } catch (err) {
      setError('Network error. Please try again.');
      setProcessing(false);
    }
  };

  const handleChange = (e) => {
    setForm({
      ...form,
      [e.target.name]: e.target.value,
    });
  };

  return (
    <div className="checkout-container">
      <h2>Payment Checkout</h2>

      <form onSubmit={handleSubmit}>
        <div className="form-group">
          <label>Name</label>
          <input
            name="customer_name"
            value={form.customer_name}
            onChange={handleChange}
            required
          />
        </div>

        <div className="form-group">
          <label>Email</label>
          <input
            name="customer_email"
            type="email"
            value={form.customer_email}
            onChange={handleChange}
            required
          />
        </div>

        <div className="form-group">
          <label>Phone</label>
          <input
            name="customer_phone"
            value={form.customer_phone}
            onChange={handleChange}
            required
          />
        </div>

        <div className="form-group">
          <label>Payment Method</label>
          <select
            name="bank_code"
            value={form.bank_code}
            onChange={handleChange}
            required
          >
            <optgroup label="FPX Banks">
              <option value="MB2U0227">Maybank2u</option>
              <option value="BCBB0235">CIMB Clicks</option>
              <option value="PBB0233">Public Bank</option>
            </optgroup>
            <optgroup label="E-Wallets">
              <option value="BP-BST01">Boost</option>
              <option value="BP-TNG01">TouchNGo</option>
            </optgroup>
          </select>
        </div>

        <button type="submit" disabled={processing}>
          {processing ? 'Processing...' : 'Pay Now'}
        </button>

        {error && <div className="error-message">{error}</div>}
      </form>
    </div>
  );
}
```

---

## 5. Vanilla JavaScript Example

```html
<!DOCTYPE html>
<html>
<head>
  <title>Billplz Checkout</title>
</head>
<body>
  <form id="checkoutForm">
    <input type="text" name="customer_name" placeholder="Name" required />
    <input type="email" name="customer_email" placeholder="Email" required />
    <input type="tel" name="customer_phone" placeholder="Phone" required />
    
    <select name="bank_code" required>
      <option value="MB2U0227">Maybank2u</option>
      <option value="BCBB0235">CIMB Clicks</option>
      <option value="BP-BST01">Boost</option>
      <option value="BP-TNG01">TouchNGo</option>
    </select>

    <button type="submit">Pay Now</button>
  </form>

  <script>
    document.getElementById('checkoutForm').addEventListener('submit', async (e) => {
      e.preventDefault();

      const formData = new FormData(e.target);
      const data = Object.fromEntries(formData);

      // Add additional data
      data.product_id = 1;
      data.quantity = 1;
      data.delivery_method = 'cod';
      data.payment_method = 'full';

      try {
        const response = await fetch('/api/checkout/billplz', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(data),
        });

        const result = await response.json();

        if (result.success) {
          // Redirect to Billplz
          window.location.href = result.data.bill_url;
        } else {
          alert('Error: ' + result.message);
        }
      } catch (error) {
        alert('Network error. Please try again.');
      }
    });
  </script>
</body>
</html>
```

---

## Best Practices

### 1. Error Handling
```javascript
const handleCheckout = async () => {
  try {
    const response = await fetch('/api/checkout/billplz', {
      method: 'POST',
      body: JSON.stringify(form),
    });

    if (!response.ok) {
      const error = await response.json();
      throw new Error(error.message || 'Payment failed');
    }

    const result = await response.json();
    window.location.href = result.data.bill_url;
  } catch (error) {
    // Show user-friendly error
    console.error('Checkout error:', error);
    alert(error.message);
  }
};
```

### 2. Loading States
```vue
<button :disabled="processing">
  <span v-if="processing">
    <LoadingSpinner /> Processing...
  </span>
  <span v-else>Pay Now</span>
</button>
```

### 3. Form Validation
```javascript
const validateForm = () => {
  if (!form.customer_email.includes('@')) {
    setError('Invalid email address');
    return false;
  }
  
  if (form.customer_phone.length < 10) {
    setError('Invalid phone number');
    return false;
  }
  
  return true;
};
```

### 4. Prevent Double Submission
```javascript
let isSubmitting = false;

const handleCheckout = async () => {
  if (isSubmitting) return;
  
  isSubmitting = true;
  try {
    // Process payment
  } finally {
    isSubmitting = false;
  }
};
```

---

## Testing Tips

1. **Test all payment methods** - FPX, E-Wallet, Cards
2. **Test error scenarios** - Invalid data, network errors
3. **Test mobile responsiveness** - Payment gateways redirect to mobile apps
4. **Use Billplz Sandbox** - Test with sandbox credentials first
5. **Monitor webhook logs** - Check Laravel logs for webhook callbacks

---

## Common Issues & Solutions

### Issue: Payment redirect not working
**Solution:** Check if `window.location.href` is being blocked by popup blocker

### Issue: Bank selection not showing
**Solution:** Verify API endpoint `/api/payment/gateways` is accessible

### Issue: Form data not sending correctly
**Solution:** Check Content-Type header and JSON.stringify() usage

---

## Additional Resources

- Vue 3 Documentation: https://vuejs.org/
- React Documentation: https://react.dev/
- Inertia.js: https://inertiajs.com/
- Billplz API: https://www.billplz.com/api

