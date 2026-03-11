# Stripe Payment Flow — Frontend Integration Guide

This document explains the exact steps the Vue.js frontend must follow to complete a ticket purchase.

---

## Complete Purchase Flow

```
User selects tickets
       │
       ▼
POST /api/orders                    ← Create a pending order
       │ returns { order_id }
       ▼
POST /api/payments/create-intent    ← Get Stripe client_secret
       │ returns { client_secret }
       ▼
Stripe.js confirmPayment()          ← Collect card on the frontend
       │ Stripe redirects / confirms
       ▼
POST /api/payments/confirm          ← Server-side verify & finalize
       │ returns { status: "paid" }
       ▼
Show success screen 🎉
```

---

## Step-by-Step Frontend Code

### 1. Install Stripe.js

```bash
npm install @stripe/stripe-js
```

### 2. Initialize Stripe

```javascript
import { loadStripe } from '@stripe/stripe-js'

const stripe = await loadStripe(import.meta.env.VITE_STRIPE_KEY)
```

### 3. Create the Order

```javascript
const orderRes = await fetch('/api/orders', {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
  body: JSON.stringify({ event_id: eventId, quantity: qty })
})
const { data: order } = await orderRes.json()
```

### 4. Create a PaymentIntent

```javascript
const intentRes = await fetch('/api/payments/create-intent', {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
  body: JSON.stringify({ order_id: order.id })
})
const { client_secret } = await intentRes.json()
```

### 5. Mount Stripe Elements & Confirm Payment

```vue
<template>
  <div id="payment-element"></div>
  <button @click="handlePayment">Pay {{ order.total_amount_display }}</button>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { loadStripe } from '@stripe/stripe-js'

const stripe = await loadStripe(import.meta.env.VITE_STRIPE_KEY)
const elements = stripe.elements({ clientSecret })
const paymentElement = elements.create('payment')

onMounted(() => {
  paymentElement.mount('#payment-element')
})

async function handlePayment() {
  const { error, paymentIntent } = await stripe.confirmPayment({
    elements,
    confirmParams: { return_url: `${window.location.origin}/payment/success` },
    redirect: 'if_required', // avoids page redirect for most card types
  })

  if (error) {
    // Show error to user
    return
  }

  // Verify server-side
  await fetch('/api/payments/confirm', {
    method: 'POST',
    headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json' },
    body: JSON.stringify({
      payment_intent_id: paymentIntent.id,
      order_id: order.id,
    })
  })

  // Navigate to success page
}
</script>
```

---

## Environment Variables (Vue / Vite)

```env
# .env
VITE_API_URL=http://localhost:8000/api
VITE_STRIPE_KEY=pk_test_xxxxxxxxxxxx    # Publishable key only!
```

---

## Test Cards (Stripe Test Mode)

| Card Number | Result |
|-------------|--------|
| `4242 4242 4242 4242` | Success |
| `4000 0000 0000 0002` | Declined |
| `4000 0025 0000 3155` | 3D Secure required |

Use any future expiry date, any 3-digit CVC, any ZIP.
