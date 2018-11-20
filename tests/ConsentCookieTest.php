<?php

use PHPUnit\Framework\TestCase;

class ConsentCookieTest extends TestCase
{
    public function testCanBeCreatedFromEmptyConsentCookie()
    {
        $this->assertInstanceOf(
            Mifefr\ConsentString\ConsentCookie::class,
            new Mifefr\ConsentString\ConsentCookie
        );
    }
}