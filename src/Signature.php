<?php
namespace Submittd\Webhook;

class Signature {
    const ALGORITHM_DEFAULT = Signature::ALGORITHM_SHA256;
    const ALGORITHM_SHA256 = 'sha256';

    /**
     * @var string
     */
    public $algorithm = Signature::ALGORITHM_DEFAULT;
    /**
     * @var string
     */
    public $secret;
    /**
     * @var string
     */
    public $raw;

    /**
     * @param string $encodedPayload
     * @param string $secret
     * @return string
     */
    public function signWith($encodedPayload, $secret) {
        return $this->algorithm . '=' . hash_hmac($this->algorithm, $encodedPayload, $secret);
    }

    /**
     * @param string $encodedPayload
     * @return string
     */
    public function sign($encodedPayload) {
        return $this->signWith($encodedPayload, $this->secret);
    }
}