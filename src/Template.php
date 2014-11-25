<?php
namespace Gajus\Brick;

/**
 * Responsible for isolating template execution scope, extracting scope
 * variables and capturing the output buffer.
 * 
 * @link https://github.com/gajus/brick for the canonical source repository
 * @license https://github.com/gajus/brick/blob/master/LICENSE BSD 3-Clause
 */
class Template {
    /**
     * @param string $file
     * @param array $scope
     */
    public function __construct ($file, array $scope = []) {
        $this->file = $file;
        $this->scope = $scope;
    }

    /**
     * @return string
     */
    public function render () {
        return static::_render($this->file, $this->scope);
    }

    /**
     * Static render method is used to prevent exposing $this and other Template
     * properties to the template scope.
     *
     * @param string $file
     * @param array $scope
     * @return string
     */
    static private function _render () {
        extract(func_get_arg(1), \EXTR_REFS);

        ob_start();
        require func_get_arg(0);
        return ob_get_clean();
    }
}