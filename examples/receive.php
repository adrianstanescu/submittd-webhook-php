<?php
require_once '../vendor/autoload.php';

use Submittd\Webhook\Webhook;

// Content-Type:application/json
// X-Hub-Signature:sha256=64990eb5ecd0126d7aaa3bf2c56899b12c56b34956243be05eb7d6bdccc8085f
//
// {"accountID":"test-account","applicationID":"test-application","formID":"test-form","events":[{"type":"submissionSuccess","timestamp":1530196096,"remoteIP":null,"origin":null,"referer":null,"fields":[{"name":"name","label":null,"value":"test"},{"name":"email","label":null,"value":"test@example.com"},{"name":"phone","label":"Phone number","value":"555-5555"}]}]}

$message = Webhook::handleRequest('01234567890abcdef');

$message->isValidSignature(); // true

$message->selectValidSignature([
    'secret-1' => 'xxxxxxxxxxxxxxxxx',
    'secret-2' => '01234567890abcdef'
]); // 'secret-2'
$events = $message->getEvents();
foreach ($events as $event) {
    $event->getFieldValue('email'); // 'test@example.com'
    $event->getFieldValue('missing', 'placeholder'); // 'placeholder'
    $event->getFieldsAssoc(); // ['name' => 'test', 'email' => 'test@example.com', 'phone' => '555-5555']
}