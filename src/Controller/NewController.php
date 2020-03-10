<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Core\Configure;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\ORM\TableRegistry;

/**
 * Static content controller
 *
 * This controller will render views from Template/Pages/
 *
 * @link https://book.cakephp.org/3.0/en/controllers/pages-controller.html
 */
class NewController extends AppController
{

    /**
     * Displays a view
     *
     * @param array ...$path Path segments.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Http\Exception\ForbiddenException When a directory traversal attempt.
     * @throws \Cake\Http\Exception\NotFoundException When the view file could not
     *   be found or \Cake\View\Exception\MissingTemplateException in debug mode.
     */

    public function initialize()
    {
        parent::initialize();
        
    }

    public function index()
    {
        $this->layout = 'new_layout';

        if($this->request->is(['post'])){

            // Set your secret key. Remember to switch to your live secret key in production!
            // See your keys here: https://dashboard.stripe.com/account/apikeys
            \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

            // Token is created using Stripe Checkout or Elements!
            // Get the payment token ID submitted by the form:
            $token = $this->request->data['stripeToken'];
            // $charge = \Stripe\Charge::create([
            // 'amount' => 999,
            // 'currency' => 'usd',
            // 'description' => 'Example charge',
            // 'source' => $token,
            // ]);
            pr($token);die();
        }
    }

    public function newIntegration(){
        $this->layout = 'stripe_new_layout';

        // Set your secret key. Remember to switch to your live secret key in production!
        // See your keys here: https://dashboard.stripe.com/account/apikeys
        \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

        $intent = \Stripe\PaymentIntent::create([
            'amount' => 1099,
            'currency' => 'inr',
            // Verify your integration in this guide by including this parameter
            'metadata' => ['integration_check' => 'accept_a_payment'],
        ]);
            // pr($intent);die();
        $this->set("clientSecret", $intent->client_secret);
    }

    public function saveCardForLater(){
        $this->layout = 'stripe_new_layout';
        $ordersTbl = TableRegistry::get('orders');
        $user_id = 100;
        \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

        $orderData = $ordersTbl->find('all')->where(['user_id' => $user_id])->first();
        $customer_id = "";

        //Checking if the user_id has the customer_id or not.
        if(!empty($orderData)){
            $customer_id = $orderData['customer_id'];
        }else{
            $orderEntity = $ordersTbl->newEntity();
            $customer = \Stripe\Customer::create();
            $orderEntity->customer_id = $customer->id;
            $orderEntity->user_id = $user_id;

            $customer_id = $customer->id;

            if($orderSave = $ordersTbl->save($orderEntity)){
                echo "saved";
            }else{
                echo "not saved";
            }
        }
        $intent = \Stripe\SetupIntent::create([
            'customer' => $customer_id
        ]);

        $paymentmethods = \Stripe\PaymentMethod::all([
            'customer' => 'cus_GsZHGJ8NVEF1bg',
            'type' => 'card',
        ]);

        $this->set("intent", $intent);
        $this->set('paymentmethods', $paymentmethods);
    }

    // Param :: user_id
    public function listCustomerCard(){
        $this->autoRender = false;
        \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

        $paymentmethods = \Stripe\PaymentMethod::all([
            'customer' => 'cus_GsZHGJ8NVEF1bg',
            'type' => 'card',
        ]);

        pr($paymentmethods);die();
    }

    //Param :: Payment_method, user_id
    public function chargeSavedCard(){
        $this->autoRender = false;
        $ordersTbl = TableRegistry::get('orders');
        // Set your secret key. Remember to switch to your live secret key in production!
        // See your keys here: https://dashboard.stripe.com/account/apikeys
        \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);
        $user_id = 100;
        $ordersData = $ordersTbl->find('all')->where(['user_id' => $user_id])->first();

        $paymentmethods = \Stripe\PaymentMethod::all([
            'customer' => $ordersData['customer_id'],
            'type' => 'card',
        ]);

        try {
        \Stripe\PaymentIntent::create([
            'amount' => 1079,
            'currency' => 'inr',
            'customer' => $ordersData['customer_id'],
            'payment_method' => $paymentmethods->data['0']->id,
            'off_session' => true,
            'confirm' => true,
        ]);
        } catch (\Stripe\Exception\CardException $e) {
            // Error code will be authentication_required if authentication is needed
            echo 'Error code is:' . $e->getError()->code;
            $payment_intent_id = $e->getError()->payment_intent->id;
            $payment_intent = \Stripe\PaymentIntent::retrieve($payment_intent_id);
        }
    }

    //Param :: Card_id || PaymentMethodId
    public function retrivePerticularCard(){
        $this->autoRender = false;
        \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

        $card = \Stripe\PaymentMethod::retrieve(
        'pm_1GL1pyBPSoAou5pasQqEJeAm'
        );

        pr($card);die();
    }

    //Param :: Card_id || PaymentMethodId
    public function updatePerticularCard(){
        $this->autoRender = false;
        \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

        \Stripe\PaymentMethod::update(
        'pm_1GL1pyBPSoAou5pasQqEJeAm',
        ['metadata' => ['order_id' => '6735']]
        );
    }

    //Param :: Card_id || PaymentMethodId
    public function attachAPaymentMethodToCustomer(){
        $this->autoRender = false;
        \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

        $payment_method = \Stripe\PaymentMethod::retrieve(
        'pm_1GL1pyBPSoAou5pasQqEJeAm'
        );
        $payment_method->attach([
        'customer' => 'cus_GsZHGJ8NVEF1bg',
        ]);
    }

    //Param :: Card_id || PaymentMethodId
    public function DetachAPaymentMethodFromCustomer(){
        $this->autoRender = false;
        \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

        $payment_method = \Stripe\PaymentMethod::retrieve(
        'pm_1GL1EkBPSoAou5paHQytrgcY'
        );

        $payment_method->detach();
    }
}
