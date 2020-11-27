<?php

namespace PascalDeVink\ShortUuid;

use Brick\Math\BigInteger;
use Brick\Math\RoundingMode;
use Brick\Math\Exception\MathException;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * Can encode a given UUID to a shorter string and decode it back to the original UUID.
 */
final class ShortUuid
{
    /**
     * @var array
     */
    private $alphabet = [
        '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F', 'G',
        'H', 'J', 'K', 'L', 'M', 'N', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X',
        'Y', 'Z', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'm', 'n',
        'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
    ];

    /**
     * @var int
     */
    private $alphabetLength = 57;

    /**
     * @param array|null $alphabet
     */
    public function __construct(array $alphabet = null)
    {
        if (null !== $alphabet) {
            $this->setAlphabet($alphabet);
        }
    }

    /**
     * Generate a version 1 UUID from a host ID, sequence number, and the current time and shorten it.
     *
     * @param int|string $node A 48-bit number representing the hardware address
     *     This number may be represented as an integer or a hexadecimal string.
     * @param int $clockSeq A 14-bit number used to help avoid duplicates that
     *     could arise when the clock is set backwards in time or if the node ID
     *     changes.
     *
     * @return string
     */
    public static function uuid1($node = null, ?int $clockSeq = null) : string
    {
        $uuid = Uuid::uuid1($node, $clockSeq);
        $shortUuid = new self();
        return $shortUuid->encode($uuid);
    }

    /**
     * Generate a version 4 (random) UUID and shorten it.
     *
     * @return string
     */
    public static function uuid4() : string
    {
        $uuid = Uuid::uuid4();
        $shortUuid = new self();
        return $shortUuid->encode($uuid);
    }

    /**
     * Generate a version 5 UUID based on the SHA-1 hash of a namespace
     * identifier (which is a UUID) and a name (which is a string) and shorten it.
     *
     * @param string $ns The UUID namespace in which to create the named UUID
     * @param string $name The name to create a UUID for
     *
     * @return string
     */
    public static function uuid5(string $ns, string $name) : string
    {
        $uuid = Uuid::uuid5($ns, $name);
        $shortUuid = new self();
        return $shortUuid->encode($uuid);
    }

    /**
     * Encodes the given UUID to a shorter version.
     * For example:
     * - 4e52c919-513e-4562-9248-7dd612c6c1ca becomes fpfyRTmt6XeE9ehEKZ5LwF
     * - 59a3e9ab-6b99-4936-928a-d8b465dd41e0 becomes BnxtX5wGumMUWXmnbey6xH
     *
     * @param UuidInterface $uuid
     *
     * @return string
     *
     * @throws MathException
     */
    public function encode(UuidInterface $uuid) : string
    {
        $uuidInteger = BigInteger::of((string) $uuid->getInteger());
        return $this->numToString($uuidInteger);
    }

    /**
     * Decodes the given short UUID to the original version.
     * For example:
     * - fpfyRTmt6XeE9ehEKZ5LwF becomes 4e52c919-513e-4562-9248-7dd612c6c1ca
     * - BnxtX5wGumMUWXmnbey6xH becomes 59a3e9ab-6b99-4936-928a-d8b465dd41e0
     *
     * @param string $shortUuid
     *
     * @return UuidInterface
     */
    public function decode(string $shortUuid) : UuidInterface
    {
        return Uuid::fromInteger($this->stringToNum($shortUuid));
    }

    /**
     * Transforms a given (big) number to a string value, based on the set alphabet.
     *
     * @param BigInteger $number
     *
     * @return string
     *
     * @throws MathException
     */
    private function numToString(BigInteger $number) : string
    {
        $output = '';
        while ($number->isPositive()) {
            $previousNumber = clone $number;
            $number = $number->dividedBy($this->alphabetLength, RoundingMode::DOWN);
            $digit = $previousNumber->mod($this->alphabetLength);

            $output .= $this->alphabet[(int)$digit->toInt()];
        }

        return $output;
    }

    /**
     * Transforms a given string to a (big) number, based on the set alphabet.
     *
     * @param string $string
     *
     * @return BigInteger
     */
    private function stringToNum(string $string) : BigInteger
    {
        $number = BigInteger::of(0);
        foreach (str_split(strrev($string)) as $char) {
            $number = $number->multipliedBy($this->alphabetLength)->plus(array_search($char, $this->alphabet, false));
        }

        return $number;
    }

    /**
     * @param array $alphabet
     */
    private function setAlphabet(array $alphabet)
    {
        $this->alphabet = $alphabet;
        $this->alphabetLength = count($alphabet);
    }

    /**
     * Returns the currently used alphabet for encoding and decoding.
     *
     * @return array
     */
    public function getAlphabet() : array
    {
        return $this->alphabet;
    }
}
