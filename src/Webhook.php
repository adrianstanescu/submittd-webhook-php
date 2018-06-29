<?php
namespace Submittd\Webhook;

class Webhook {
    /**
     * @param string $secret
     * @return Message
     */
    public static function createMessage($secret) {
        $message = new Message();
        $message->signature->secret = $secret;
        return $message;
    }

    /**
     * @param string | null $secret
     * @return Message
     */
    public static function handleRequest($secret = null) {
        $message = new Message();
        $content = file_get_contents('php://input');
        if ($content === '') {
            throw new \RuntimeException('Empty request body');
        }
        $signature = isset($_SERVER['HTTP_X_HUB_SIGNATURE']) ? $_SERVER['HTTP_X_HUB_SIGNATURE'] : null;
        $message->signature->raw = $signature;
        if ($secret !== null) {
            $message->signature->secret = $secret;
        }
        $message->payload->setEncoded($content);
        return $message;
    }
}