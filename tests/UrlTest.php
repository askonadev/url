<?php

namespace Askonadev\Tests;

use Askonadev\Url;
use PHPUnit\Framework\TestCase;

class UrlTest extends TestCase
{
    public function testToString(): void
    {
        $testingUrls = [
            "https://www.askona.ru/",
            "https://www.askona.ru/0/",
            "/",
            "",
            "/local/test/",
            "local/test/",
            "/?query=string",
            "/test/?query=string",
            "?query=string",
            "/#ancor",
            "/test/#ancor",
            "#ancor",
        ];

        foreach ($testingUrls as $testUrl) {
            $url = new Url($testUrl);
            $this->assertEquals($testUrl, $url->toString());
        }
    }

    public function testSetClosePathSlash(): void
    {
        $testUrl = "/local/test";

        $url = new Url($testUrl);

        $url->setClosePathSlash(true);
        $this->assertEquals($testUrl."/", $url->toString());

        $url->setClosePathSlash(false);
        $this->assertEquals($testUrl, $url->toString());
    }

    public function testUrlAssembly(): void
    {
        $url = new Url();
        $url->setPath("/local/test");
        $this->assertEquals("/local/test/", $url->toString());

        $url = new Url();
        $url->addPathItem("test");
        $this->assertEquals("test/", $url->toString());

        $url = new Url();
        $url->setPath("/local");
        $url->addPathItem("test");
        $this->assertEquals("/local/test/", $url->toString());

        $url = new Url();
        $url->setFragment("test");
        $this->assertEquals("#test", $url->toString());
    }

    public function testAddParam():void
    {
        $url = new Url();
        $url->addParam("key", "value");
        $this->assertEquals("?key=value", $url->toString());

        $url = new Url();
        $url->addParam("key", ["value1", "value2"]);
        $this->assertEquals("?key[0]=value1&key[1]=value2", $url->toString());

        $url = new Url();
        $url->addParam("key", ["value1", "value2"]);
        $url->addParam("key", "value");
        $this->assertEquals("?key=value", $url->toString());
    }

    public function testRemoveParam():void
    {
        $url = new Url();
        $url->addParam("key1", "value1");
        $url->addParam("key2", "value2");

        $url->removeParam("key1");

        $this->assertEquals("?key2=value2", $url->toString());
    }
}