<?php

/**
 * Link Account Message
 * PHP version 7.2
 */

namespace Silamoney\Client\Domain;

use JMS\Serializer\Annotation\Type;
use Respect\Validation\Validator as v;

/**
 * Link Account Message
 * Object sent in the link account method.
 * @category Class
 * @package  Silamoney\Client
 * @author   José Morales <jmorales@digitalgeko.com>
 */
class LinkAccountMessage implements ValidInterface
{
    /**
     * @var string
     * @Type("string")
     */
    private $publicToken;

    /**
     * @var string
     * @Type("string")
     */
    private $accountName;

    /**
     * @var Silamoney\Client\Domain\Header
     * @Type("Silamoney\Client\Domain\Header")
     */
    private $header;

    /**
     * @var string
     * @Type("string")
     */
    private $selectedAccountId;

    /**
     * @var string
     * @Type("string")
     */
    private $message;

    /**
     * Constructor for LinkAccountMessage object.
     *
     * @param string $userHandle
     * @param string $accountName
     * @param string $publicToken
     * @param string $appHandle
     */
    public function __construct(string $userHandle, string $accountName, string $publicToken, string $appHandle)
    {
        $this->publicToken = $publicToken;
        $this->accountName = $accountName;
        $this->header = new Header($userHandle, $appHandle);
        $this->message = Message::LINK_ACCOUNT;
    }

    public function isValid(): bool
    {
        return v::notOptional()->validate($this->header)
            && $this->header->isValid()
            && v::stringType()->notEmpty()->validate($this->message)
            && v::stringType()->notEmpty()->validate($this->publicToken)
            && ($this->accountName === null || v::stringType()->validate($this->accountName))
            && ($this->selectedAccountId === null || v::stringType()->validate($this->selectedAccountId));
    }
}
