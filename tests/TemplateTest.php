<?php
class TemplateTest extends PHPUnit_Framework_TestCase {
    public function testHelloWord () {
        new \Gajus\Brick\Template(__DIR__ . '/template/hello');
    }
}