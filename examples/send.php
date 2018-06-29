<?php
require_once '../vendor/autoload.php';

use Submittd\Webhook\Webhook;
use Submittd\Webhook\Event;
use Submittd\Webhook\Field;

define('SECRET', '01234567890abcdef');
define('URL', 'https://webhook.site/xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx');

$message = Webhook::createMessage(SECRET);
$message->setAccountID('test-account');
$message->setApplicationID('test-application');
$message->setFormID('test-form');

$event = Event::fromRequest();
$event->clearFields();

$event->setFields([
    'name' => 'test',
    'email' => 'test@example.com'
]);
// equivalent
$event->setFields([
    new Field('name', 'test'),
    new Field('email', 'test@example.com')
]);

$event->addField('phone', '555-5555', 'Phone number');
$message->addEvent($event);

$response = $message->send(URL);