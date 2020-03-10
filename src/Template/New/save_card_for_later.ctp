<div class="container">
    <input id="cardholder-name" type="text" class="form-control mt-4" name="cardholder_name" placeholder="Enter Holder name">
    <!-- placeholder for Elements -->
    <form id="setup-form" data-secret="<?= $intent->client_secret ?>">
        <div id="card-element" class="form-control mt-4"></div>
        <button id="card-button" >
            Save Card
        </button>
    </form>
</div>
<div class="container mt-5">
    <div class="row">
        <?php 
            if(!empty($paymentmethods)){
                foreach($paymentmethods['data'] as $paymentKey => $paymentCard){
                    ?>
                    <div class="col-4 mx-1 my-1 py-2" style="background:silver; border-radius:15px;">
                        <h1><?= $paymentCard['card']->brand ?></h1>
                        <h4><?= $paymentCard['billing_details']->name ?></h4>
                        <h2>xxxx xxxx xxxx <?= $paymentCard['card']->last4; ?></h2>   
                        <h5>Expiry : <?= $paymentCard['card']->exp_month."/".$paymentCard['card']->exp_year ; ?></h5>
                    </div>
                <?php
                }
            }
        ?>
    </div>
</div>

<script>
    $("#setup-form button").addClass("btn btn-primary btn-block mt-4");
    var stripe = Stripe('<?= STRIPE_PUBLISHABLE_KEY; ?>');

    var elements = stripe.elements();
    var cardElement = elements.create('card');
    cardElement.mount('#card-element');

    var cardholderName = document.getElementById('cardholder-name');
    var cardButton = document.getElementById('card-button');
    cardButton.addEventListener('click', function(ev) {
        ev.preventDefault();
        var clientSecret = $("form").data('secret');
        
        stripe.confirmCardSetup(
            clientSecret,
            {
                payment_method: {
                    card: cardElement,
                    billing_details: {
                        name: cardholderName.value,
                    },
                },
            }
        ).then(function(result) {
            console.log(result);
            if (result.error) {
            // Display error.message in your UI.
            } else {
                window.location.reload();
            }
        });
    });
</script>