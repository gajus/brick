<?php
class TemplateTest extends PHPUnit_Framework_TestCase {
    public function testPlain () {
        $template = new \Gajus\Brick\Template(__DIR__ . '/templates/hello.php');

        $this->assertSame('Hi!', $template->render());
    }

    public function testScript () {
        $template = new \Gajus\Brick\Template(__DIR__ . '/templates/date.php');

        $this->assertSame(date('Y-m-d'), $template->render());
    }

    public function testVariables () {
        $template = new \Gajus\Brick\Template(__DIR__ . '/templates/get_defined_vars.php', ['a' => null]);

        $this->assertSame('a', $template->render());
    }
}