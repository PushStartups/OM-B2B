<?php

try {
    require_once('init.php');
    \Stripe\Stripe::setApiKey("sk_test_JPLIlkimcZXGmsgLGWdYj3O0"); //Replace with your Secret Key

    $token = $_POST['stripeToken'];
    echo $token;
    // Charge the user's card:
    $charge = \Stripe\Charge::create(array(
        "amount" => 1000,
        "currency" => "ILS",
        "description" => "Example charge",
        "source" => $token,
    ));



    //send the file, this line will be reached if no error was thrown above
    echo "<h1>Your payment has been completed. We will send you the Food in a 30 minute.</h1>";


//you can send the file to this email:
    echo $_POST['stripeEmail'];
}
//catch the errors in any way you like

catch(Stripe_CardError $e) {

}


catch (Stripe_InvalidRequestError $e) {

    // Invalid parameters were supplied to Stripe's API

}

catch (Stripe_AuthenticationError $e) {

    // Authentication with Stripe's API failed
    // (maybe you changed API keys recently)

}
catch (Stripe_ApiConnectionError $e) {

    // Network communication with Stripe failed
}
catch (Stripe_Error $e) {

   // Display a very generic error to the user, and maybe send
   // yourself an email
}
catch (Exception $e) {

   // Something else happened, completely unrelated to Stripe
}
?>