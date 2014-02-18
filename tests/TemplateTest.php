<?php
class TemplateTest extends PHPUnit_Framework_TestCase {
    private
        $template;

    public function setUp () {
        $this->template = new \Gajus\Brick\Template(__DIR__ . '/template');
    }

    public function testPlain () {
        $this->assertSame('Hello, world', $this->template->render('hello'));
    }

    public function testScript () {
        $this->assertSame(date('Y-m-d'), $this->template->render('date'));
    }

    public function testNotExposedTemplateInstance () {
        $this->assertSame('0', $this->template->render('parent'));
    }

    public function testNoDefinedVariables () {
        $this->assertSame('["template"]', $this->template->render('get_defined_vars'));
    }

    public function testInjectedVariables () {
        $this->assertSame('["a","template"]', $this->template->render('get_defined_vars', ['a' => 'b']));
    }

    public function testAppend () {
        $this->assertSame('Hello, world', $this->template->render('append_hello'));
    }

    public function testAppendDoNotInheritEnv () {
        $this->assertSame('["template"]', $this->template->render('append_get_defined_vars', ['a' => 'b']) );
    }

    public function testGetTemplateName () {
        $this->assertSame('["template_name"]', $this->template->render('template_name') );
    }

    public function testAppendGetTemplateName () {
        $this->assertSame('["append_template_name","template_name"]', $this->template->render('append_template_name') );
    }
}