<?php
class TemplateTest extends PHPUnit_Framework_TestCase {
    private
        $template;

    public function setUp () {
        $this->template = new \Gajus\Brick\Template(__DIR__ . '/template/safe');
    }

    /**
     * @expectedException Gajus\Brick\Exception\LogicException
     * @expectedExceptionMessage Template directory does not exist.
     */
    public function testDirectoryNotFound () {
        new \Gajus\Brick\Template(__DIR__ . '/foobar');
    }

    /**
     * @expectedException Gajus\Brick\Exception\InvalidArgumentException
     * @expectedExceptionMessage Directory name must be an absolute path.
     */
    public function testRelativeDirectory () {
        new \Gajus\Brick\Template('./foobar');
    }

    /**
     * @expectedException Gajus\Brick\Exception\LogicException
     * @expectedExceptionMessage Template does not exist.
     */
    public function testTemplateNotFound () {
        $this->template->render('not_found');
    }

    /**
     * @dataProvider testTraversalAttemptProvider
     * @expectedException Gajus\Brick\Exception\InvalidArgumentException
     * @expectedExceptionMessage Directory traversal attempt.
     */
    public function testTraversalAttempt ($name) {
        $this->template->render($name);
    }

    public function testTraversalAttemptProvider () {
        return [
            ['../traversal'],
            ['traversal']
        ];
    }

    public function testGetDirectory () {
        $this->assertSame(__DIR__ . '/template/safe', $this->template->getDirectory());
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
        $this->assertSame('Hello, world', $this->template->render('append/hello'));
    }

    public function testAppendDoNotInheritEnv () {
        $this->assertSame('["template"]', $this->template->render('append/get_defined_vars', ['a' => 'b']) );
    }

    public function testGetNames () {
        $this->assertSame('["template_name"]', $this->template->render('template_name') );
    }

    public function testGetTranslatedNames () {
        $this->assertSame('["template_name_translated"]', $this->template->render('template_name_translated') );
    }

    public function testAppendGetNames () {
        $this->assertSame('["append\/template_name","template_name"]', $this->template->render('append/template_name') );
    }

    public function testAppendGetTranslatedNames () {
        $this->assertSame('["append__template_name_translated","template_name_translated"]', $this->template->render('append/template_name_translated') );
    }

    public function testInheritenceUsingOutputBuffer () {



        $this->assertXmlStringEqualsXmlString('<!DOCTYPE html><html><head><title>a</title></head><body><h1>a</h1><p>b</p></body></html>', $this->template->render('inheritence/post', ['post' => ['name' => 'a', 'body' => 'b']]) );
    }
}