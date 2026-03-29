<?php
session_start();
$conn = new mysqli("localhost", "root", "", "stayhub");

// UPDATED: Using your provided Test Key
$paymongo_secret_key = 'sk_test_AhL2222giv2j4aDJdzFaKdym'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $bid = $_POST['booking_id'];
    $total = $_POST['total'];
    $room = $_POST['room_name'];

    // Update the pending record with the info from the Wizard Step 1
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $conn->query("UPDATE bookings SET full_name='$name', email='$email', phone='$phone' WHERE id='$bid'");

    // Generate PayMongo GCash Session
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => "https://api.paymongo.com/v1/checkout_sessions",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode([
            'data' => [
                'attributes' => [
                    'line_items' => [[
                        'currency' => 'PHP',
                        'amount' => (int)($total * 100), // Amount in centavos
                        'name' => "StayHub Luxury Suite: $room",
                        'quantity' => 1
                    ]],
                    'payment_method_types' => ['gcash'],
                    // Redirects to success.php with the specific Booking ID
                    'success_url' => "http://localhost/stayhub/customer/success.php?id=$bid",
                    'cancel_url' => "http://localhost/stayhub/customer/dashboard.php"
                ]
            ]
        ]),
        CURLOPT_HTTPHEADER => [
            "Accept: application/json",
            "Authorization: Basic " . base64_encode($paymongo_secret_key . ":"),
            "Content-Type: application/json"
        ],
    ]);

    $response = curl_exec($curl);
    $res = json_decode($response, true);
    curl_close($curl);

    if (isset($res['data']['attributes']['checkout_url'])) {
        // Redirect to PayMongo Payment Page
        header("Location: " . $res['data']['attributes']['checkout_url']);
        exit();
    } else {
        // Error handling for API issues
        echo "<h2>PayMongo API Error</h2>";
        echo "<pre>";
        print_r($res);
        echo "</pre>";
        die();
    }
}