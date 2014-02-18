<?php
namespace Gajus\Brick;

/**
 * Native PHP template system. Template class is used for resource location
 * and execution of the template scripts using custom variable scope.
 *
 * @link https://github.com/gajus/brick for the canonical source repository
 * @license https://github.com/gajus/brick/blob/master/LICENSE BSD 3-Clause
 */
class Template implements \ArrayAccess {
    private
        /**
         * @var string Path to templates directory.
         */
        $directory,
        /**
         * @var string Templates that were rendered.
         */
        $templates = [],
        /**
         * @var array Shared environment variables accessible under $template variable.
         */
        $env = [],
        /**
         * @var array $extending Populated when template requests to be wrapped in another template.
         */
        $extend = [];

    /**
     * Setting template directory will isolate all template resources
     * to the chosen base path. This is implemented to prevent malicious
     * fs traversal.
     * 
     * @param string $directory Absolute pate to the template directory.
     */
    public function __construct ($directory) {
        if (strpos($directory, '/') !== 0) {
            throw new Exception\InvalidArgumentException('Directory name must be an absolute path.');
        }

        if (!is_dir($directory)) {
            throw new Exception\LogicException('Template directory does not exist.');
        }

        $this->directory = realpath($directory);
    }

    /**
     * @return string
     */
    public function getDirectory () {
        return $this->directory;
    }

    /**
     * Get names of all templates that were rendedred using
     * this instance of Template.
     *
     * @param boolean $translate
     * @return array
     */
    public function getNames ($translate = true) {
        return $translate ? array_map(['static', 'translateName'], $this->templates) : $this->templates;
    }

    /**
     * Defines new scope for template execution using $evn variables and
     * executes the template script in an output buffer.
     *
     * @param string $name File name (excluding file extension) relavite to the template directory.
     * @param array $env Variables populated in the template scope.
     * @return string Template output.
     */
    public function render ($name, array $env = []) {
    	$file = realpath($this->directory . '/' . $name . '.tpl.php');

        if (!$file) {
            throw new Exception\LogicException('Template does not exist.');
        }

        if (mb_strpos(realpath($file), $this->directory) !== 0) {
            throw new Exception\InvalidArgumentException('Directory traversal attempt.');
        }

        $this->templates[] = $name;

        if (isset($env['template']) && $env['template'] !== $this) {
            throw new Exception\InvalidArgumentException('$template variable name is reserved.');
        }

        $env['template'] = $this;

        ksort($env);

        $output = static::renderView($file, $env);

        if ($this->extend) {
            $view_output = $output;
            $output = '';

            $extend = $this->extend;

            $this->extend = [];

            foreach ($extend as $render) {
                $output .= $this->render($render[0], ['output' => $view_output] + $render[1]);
            }
        }

        return $output;
	}

    /**
     * The additional static render method is used to prevent exposing $this and other Template
     * properties to the template scope. Two variables (file and env) are captured using func_get_arg
     * for the same reason of not exposing them to the template scope.
     *
     * @return string Template output.
     */
    static private function renderView () {
        extract(func_get_arg(1), \EXTR_REFS);

        ob_start();
        require func_get_arg(0);
        return ob_get_clean();
    }

    /**
     * Shorthand method for rendering the template and printing the output.
     * 
     * @param string $name File name (excluding file extension) relavite to the template directory.
     * @param array $env Variables populated in the template scope.
     * @return void
     */
    public function append ($name, array $env = []) {
        echo $this->render($name, $env);
    }

    /**
     * This method can be called only from template context. It will wait for the template to finish
     * and then pass the output via "output" $env parameter to the template.
     *
     * @param string $name File name (excluding file extension) relavite to the template directory.
     * @param array $env Variables populated in the template scope.
     * @return void
     */
    private function extend ($name, array $env = []) {
        if (isset($env['output'])) {
            throw new Exception\InvalidArgumentException('$output variable name is reserved.');
        }

        $this->extend[] = [$name, $env];
    }

    /**
     * Return template name that is derived from the relative template file name, e.g.
     * "booking/form" will result in "booking__form".
     *
     * @param string $name
     * @return string
     */
    static private function translateName ($name) {
        #$file = trim(mb_substr($file, mb_strlen($this->directory)), '/');
        #$file = strstr($file, '.', true);
        $name = str_replace('/', '__', $name);

        return $name;
    }

    /**
     * @param string $offset
     * @param mixed $value
     * @return void
     */
    public function offsetSet ($offset, $value) {
        if (!is_string($offset)) {
            throw new Exception\InvalidArgumentException('Variable name is not a string.');
        }

        $this->env[$offset] = $value;
    }

    /**
     * @param mixed $offset
     * @return boolean
     */
    public function offsetExists ($offset) {
        return isset($this->env[$offset]);
    }

    /**
     * @param mixed $offset
     * @return void
     */
    public function offsetUnset ($offset) {
        unset($this->env[$offset]);
    }

    /**
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet ($offset) {
        return $this->offsetExists($offset) ? $this->env[$offset] : null;
    }
}