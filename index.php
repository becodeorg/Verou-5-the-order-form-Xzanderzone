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
$products = [];

$products_cocktail = [
    ['name' => 'batman', 'price' => 10],
    ['name' => 'team rocket', 'price' => 18],
    ['name' => 'baby yoda', 'price' => 10],
    ['name' => 'charmander', 'price' => 10],
    ['name' => 'tatooine sunset', 'price' => 10],
    ['name' => 'heimdal', 'price' => 10],
    ['name' => 'silver surfer', 'price' => 10],
    ['name' => 'bumblebee', 'price' => 10],
];
$products_soft = [
    ['name' => 'fanta', 'price' => 3],
    ['name' => 'cola', 'price' => 3],
    ['name' => 'sprite', 'price' => 3],
    ['name' => 'butterbeer', 'price' => 6],
    ['name' => 'mead', 'price' => 9],
    ['name' => 'coffee', 'price' => 2.5],
    ['name' => 'ginger tea', 'price' => 3],
    ['name' => 'water', 'price' => 1],
];
$products_food = [
    ['name' => 'pizza small', 'price' => 9],
    ['name' => 'pizza medium', 'price' => 15],
    ['name' => 'nachos', 'price' => 5],
    ['name' => 'jonasis least favorite food', 'price' => 10],
    ['name' => 'alexes least favorite food', 'price' => 10],
    ['name' => 'basiles favorite food', 'price' => 5],
    ['name' => 'anaises? favorite food', 'price' => 0],
];
if (isset($_GET['food'])) {
    if ($_GET["food"] == 3) //0 doesnt work 
        $products = $products_cocktail;
    else if ($_GET["food"] == 1)
        $products = $products_food;
    else if ($_GET["food"] == 2)
        $products = $products_soft;
}

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
        global $products;
        // handle successful submission
        $total = 0;
        echo '<p style="background-color:green">Order submitted!<br> Ordered items:<br>';
        if (isset($_POST["products"])) {
            foreach ($_POST["products"] as $item => $uselessweirdphpthing) {
                $price = $products[$item]['price'];
                $count = $_POST['product_count'][$item];
                echo $products[$item]['name'] . '   ' . $price . "$      x" . $count . "<br>";
                $total += $price * $count;
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