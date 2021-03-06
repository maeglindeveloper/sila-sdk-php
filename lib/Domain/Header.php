<?php

/**
 * Header
 * PHP version 7.2
 */

namespace Silamoney\Client\Domain;

use JMS\Serializer\Annotation\Type;
use Respect\Validation\Validator as v;

/**
 * Header
 * Object used in the multiple msg.
 * @category Class
 * @package  Silamoney\Client
 * @author   José Morales <jmorales@digitalgeko.com>
 */
class Header extends HeaderBase
{
    /**
     * @var string
     * @Type("string")
     */
    private $reference;

    /**
     * @var string
     * @Type("string")
     */
    private $version;

    /**
     * @var string
     * @Type("string")
     */
    private $crypto;

    /**
     * Constructor for header object.
     *
     * @param string $userHandle
     * @param string $appHandle
     */
    public function __construct(string $userHandle, string $appHandle)
    {
        parent::__construct($userHandle, $appHandle);
        $this->crypto = CryptoCode::ETH;
        $this->reference = strval(rand(0, 1000000));
        $this->version = Version::ZERO_2;
    }

    public function isValid(): bool
    {
        $notEmptyString = v::stringType()->notEmpty();
        return $notEmptyString->validate($this->authHandle)
            && $notEmptyString->validate($this->userHandle)
            && v::intType()->positive()->validate($this->created)
            && $notEmptyString->validate($this->crypto)
            && $notEmptyString->validate($this->reference)
            && $notEmptyString->validate($this->version);
    }
}
