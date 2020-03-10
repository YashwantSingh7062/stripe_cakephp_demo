<script>
// Pass the failed PaymentIntent to your client from your server
stripe.confirmCardPayment(intent.client_secret, {
  payment_method: intent.last_payment_error.payment_method.id
}).then(function(result) {
  if (result.error) {
    // Show error to your customer
    console.log(result.error.message);
  } else {
    if (result.paymentIntent.status === 'succeeded') {
      // The payment is complete!
    }
  }
});
</script>