<script>
    var stripe = Stripe('<?= STRIPE_PUBLISHABLE_KEY; ?>');
    var elements = stripe.elements();
</script>

<!-- ThankYou Modal -->
<div class="modal" tabindex="-1" role="dialog" id="thankyoumodal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-body text-center">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <img src="../webroot/img/success.png" alt="success_image" />
        <p>Your Transaction has been completed.</p>
      </div>
    </div>
  </div>
</div>

<div class="container">
    <form id="payment-form">
        <div id="card-element" class="form-control mt-4">
            <!-- Elements will create input elements here -->
        </div>

        <!-- We'll put the error messages in this element -->
        <div id="card-errors" role="alert"></div>

        <button id="submit">Pay</button>
    </form>
</div>
<script>
    // Set up Stripe.js and Elements to use in checkout form
    var style = {
        base: {
            color: "#32325d",
        }
    };

    //Custom Style
    $("#payment-form button").addClass("btn btn-primary btn-block mt-4");

    var card = elements.create("card", { style: style });
    card.mount("#card-element");

    card.addEventListener('change', ({error}) => {
        const displayError = document.getElementById('card-errors');
        if (error) {
            displayError.textContent = error.message;
        } else {
            displayError.textContent = '';
        }
    });

    var form = document.getElementById('payment-form');
    let clientSecret = '<?= $clientSecret; ?>';
    form.addEventListener('submit', function(ev) {
        ev.preventDefault();
        stripe.confirmCardPayment(clientSecret, {
            payment_method: {
                card: card,
                billing_details: {
                    name: 'Yashwant Singh'
                }
            }
        }).then(function(result) {
            if (result.error) {
                // Show error to your customer (e.g., insufficient funds)
                // console.log(result.error.message);
            } else {
                // The payment has been processed!
                if (result.paymentIntent.status === 'succeeded') {
                    console.log(result);
                    // window.location.reload();
                    $("#thankyoumodal").modal('show');
                }
            }
        });
    });
</script>