<?php

// This file is your starting point (= since it's the index)
// It will contain most of the logic, to prevent making a messy mix in the html

// This line makes PHP behave in a more strict way
declare(strict_types=1);

// We are going to use session variables so we need to enable sessions
session_start();

// Use this function when you need to need an overview of these variables
function whatIsHappening()
{
    echo '<pre><h2>$_GET</h2>';
    var_dump($_GET);
    echo '<h2>$_POST</h2>';
    var_dump($_POST);
    echo '<h2>$_COOKIE</h2>';
    var_dump($_COOKIE);
    echo '<h2>$_SESSION</h2>';
    var_dump($_SESSION);
    echo '</pre>';
}

//  provide some products (you may overwrite the example)
$products = [
    ['name' => 'favouriteDrink', 'price' => 2.5],
    ['name' => 'leastFavouriteDrink', 'price' => 25],
];


$totalValue = 0;

function validate()
{
    $invalidFields = [];
    // This function will send a list of invalid fields back
    foreach ($_POST as $key => $value) {
        $_COOKIE[$key] = $value;
        if ($value == '') {
            setcookie($key, '', time() + 3600, '/');
            $invalidFields[] = 'required field: ' . $key . '<br>';
            continue;
        } else if ($key = 'products') {
        } else {
            setcookie($key, test_input($value), time() + 3600, '/');
            if ($key == 'zipcode')
                preg_match("/^[1-9]{1}[0-9]{3}$/i", $value) ?: $invalidFields[] = 'invalid zipcode';
            else if ($key == 'email')
                filter_var($value, FILTER_VALIDATE_EMAIL) ?: $invalidFields[] = 'The email address is incorrect';
            else if ($key == 'street' && strlen($value) < 4)
                $invalidFields[] = 'address is too short!';
        }
    }
    return $invalidFields;
}
function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    $data = filter_var($data, FILTER_SANITIZE_STRING); //does nothing? depricated?
    return $data;
}
function handleForm()
{
    // form related tasks (step 1)

    whatIsHappening();
    // Validation (step 2)
    $invalidFields = validate();
    if (!empty($invalidFields)) {
        // handle errors
        foreach ($invalidFields as $problem) {
            echo '<p style="color:red"> ' . $problem . '</p>';
        }
    } else {
        // handle successful submission
        global $products;
        $total = 0;
        echo '<p style="background-color:green">Order submitted!<br> Ordered items:<br>';
        if (isset($_POST["products"])) {
            foreach ($_POST["products"] as $item => $uselessweirdphpthing) {
                echo $products[$item]['name'] . '   ' . $products[$item]['price'] . "$<br>";
                $total += $products[$item]['price'];
            }
            echo '<br>Total Value: ' . $total . '$<br>';
        }
        echo ' <br>' . ' Adress: ' . $_POST["street"] . $_POST["streetnumber"] . ', ' . $_POST['zipcode'] . ' ' . $_POST["city"] . ',<br>
        confirmation email will be sent to: ' . $_POST["email"] . '</p>';

    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    handleForm();
}

require 'form-view.php';