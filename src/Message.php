<?php
namespace Submittd\Webhook;

class Message {
    /**
     * @var Signature
     */
    public $signature;
    /**
     * @var Payload
     */
    public $payload;

    public function __construct($encodedContent = null, $signature = null) {
        $this->signature = new Signature();
        $this->payload = new Payload();
        if ($signature !== null) {
            $this->signature->raw = $signature;
        }
        if ($encodedContent !== null) {
            $this->payload->setEncoded($encodedContent);
        }
    }

    /**
     * @param string $accountID
     */
    public function setAccountID($accountID) {
        $this->payload->accountID = $accountID;
    }
    /**
     * @param string $applicationID
     */
    public function setApplicationID($applicationID) {
        $this->payload->applicationID = $applicationID;
    }
    /**
     * @param string $formID
     */
    public function setFormID($formID) {
        $this->payload->formID = $formID;
    }

    /**
     * @param Event $event
     */
    public function addEvent($event) {
        $this->payload->events[] = $event;
    }

    /**
     * @return string
     */
    public function getSignature() {
        $signature = $this->signature->sign($this->getContent());
        return $signature;
    }

    /**
     * @return string
     */
    public function getContent() {
        return $this->payload->getEncoded();
    }

    /**
     * @param string $url
     * @param int $timeout In seconds
     *
     * @return string|bool Returns the raw response data or false on failure.
     */
    public function send($url, $timeout = 30) {
        $signature = $this->getSignature();
        $content = $this->getContent();
        $context = stream_context_create(array(
            'http' => array(
                'header' => "Content-type: application/json\r\nAccept: application/json\r\nX-Hub-Signature: $signature\r\n",
                'method' => 'POST',
                'content' => $content,
                'ignore_errors' => true,
                'timeout' => $timeout
            )
        ));
        $response = file_get_contents($url, false, $context);
        return $response;
    }

    /**
     * @param array $secrets
     * @return null | string
     */
    public function selectValidSignature($secrets) {
        if ($this->signature->raw === null) {
            throw new \RuntimeException('Message has no signature');
        }
        $encodedContent = $this->payload->getEncoded();
        foreach ($secrets as $key => $secret) {
            $signature = $this->signature->signWith($encodedContent, $secret);
            if ($signature === $this->signature->raw) {
                return $key;
            }
        }
        return null;
    }

    /**
     * @param null | string $secret
     * @return bool
     */
    public function isValidSignature($secret = null) {
        if ($this->signature->raw === null) {
            throw new \RuntimeException('Message has no signature');
        }
        $encodedContent = $this->payload->getEncoded();
        if ($secret === null) {
            $secret = $this->signature->secret;
        }
        return $this->signature->signWith($encodedContent, $secret) === $this->signature->raw;
    }

    /**
     * @return Event[]
     */
    public function getEvents() {
        return $this->payload->events;
    }
}