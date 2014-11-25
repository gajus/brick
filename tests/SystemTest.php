<?php
class SystemTest extends PHPUnit_Framework_TestCase {
    private
        $system;

    public function setUp () {
        $this->system = new \Gajus\Brick\System(__DIR__ . '/templates');
    }

    public function testGetDirectory () {
        $this->assertSame($this->system->getDirectory(), __DIR__ . '/templates');
    }

    /**
     * @expectedException Gajus\Brick\Exception\LogicException
     * @expectedExceptionMessage Template directory does not exist.
     */
    public function testSetDirectoryNotFound () {
        $this->system->setDirectory(__DIR__ . '/does-not-exist/');
    }

    /**
     * @expectedException Gajus\Brick\Exception\InvalidArgumentException
     * @expectedExceptionMessage Directory name must be an absolute path.
     */
    public function testSetDirectoryRelative () {
        $this->system->setDirectory('./foobar');
    }

    public function testView () {
        $this->assertSame('Hi!', $this->system->view('hello'));
    }

    /**
     * @expectedException Gajus\Brick\Exception\LogicException
     * @expectedExceptionMessage Template ("not_found") does not exist.
     */
    public function testViewNotFound () {
        $this->system->view('not_found');
    }

    public function testViewVariablesScope () {
        $this->assertSame('bar', $this->system->view('variable_scope', ['foo' => 'bar']));
    }

    public function testViewVariablesGlobals () {
        $this->system->setGlobals(['foo' => 'bar']);

        $this->assertSame('bar', $this->system->view('variable_globals'));
    }

    public function testViewVariablesDefaultDefined () {
        $this->assertSame('globals,system', $this->system->view('get_defined_vars'));
    }

    /**
     * @dataProvider viewVariablesUseReservedProvider
     */
    public function testViewVariablesUseReserved ($name, $scope) {
        $this->setExpectedException('Gajus\Brick\Exception\InvalidArgumentException', '"' . $name . '" variable name is reserved.');

        $this->system->view('hello', $scope);
    }

    public function viewVariablesUseReservedProvider () {
        return [
            [
                'system',
                ['system' => null]
            ],
            [
                'globals',
                ['globals' => null]
            ]
        ];
    }
}