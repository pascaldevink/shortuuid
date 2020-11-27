<?php

namespace PascalDeVink\ShortUuid\Tests;

use PascalDeVink\ShortUuid\ShortUuid;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * @covers \PascalDeVink\ShortUuid\ShortUuid
 * @group FullCoverage
 */
class ShortUuidTest extends TestCase
{
    /**
     * @var ShortUuid
     */
    private $shortUuid;

    public function setUp() : void
    {
        $this->shortUuid = new ShortUuid();
    }

    /**
     * @test
     * @dataProvider uuid_provider
     */
    public function it_should_encode_a_given_uuid($uuid, $expectedShortUuid)
    {
        $shortUuid = $this->shortUuid->encode($uuid);
        $this->assertSame($expectedShortUuid, $shortUuid);
    }

    public function uuid_provider()
    {
        return [
            [Uuid::fromString('4e52c919-513e-4562-9248-7dd612c6c1ca'), 'fpfyRTmt6XeE9ehEKZ5LwF'],
            [Uuid::fromString('59a3e9ab-6b99-4936-928a-d8b465dd41e0'), 'BnxtX5wGumMUWXmnbey6xH'],
        ];
    }

    /**
     * @test
     * @dataProvider shortuuid_provider
     */
    public function it_should_decode_a_given_short_uuid($shortUuid, $expectedUuid)
    {
        $uuid = $this->shortUuid->decode($shortUuid);
        $this->assertTrue($expectedUuid->equals($uuid));
    }

    public function shortuuid_provider()
    {
        return [
            ['fpfyRTmt6XeE9ehEKZ5LwF', Uuid::fromString('4e52c919-513e-4562-9248-7dd612c6c1ca')],
            ['BnxtX5wGumMUWXmnbey6xH', Uuid::fromString('59a3e9ab-6b99-4936-928a-d8b465dd41e0')],
        ];
    }

    /**
     * @test
     */
    public function it_should_generate_a_short_uuid1()
    {
        $shortUuid = ShortUuid::uuid1();
        $this->assertLessThanOrEqual(22, strlen($shortUuid));
        $this->assertGreaterThanOrEqual(20, strlen($shortUuid));
    }

    /**
     * @test
     */
    public function it_should_generate_a_short_uuid4()
    {
        $shortUuid = ShortUuid::uuid4();
        $this->assertLessThanOrEqual(22, strlen($shortUuid));
        $this->assertGreaterThanOrEqual(20, strlen($shortUuid));
    }

    /**
     * @test
     */
    public function it_should_generate_a_short_uuid5()
    {
        $shortUuid = ShortUuid::uuid5(Uuid::NAMESPACE_DNS, 'ticketswap.com');
        $this->assertLessThanOrEqual(22, strlen($shortUuid));
        $this->assertGreaterThanOrEqual(20, strlen($shortUuid));
    }
}
