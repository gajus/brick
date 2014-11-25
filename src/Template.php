<?php
namespace Gajus\Brick;

/**
 * Native PHP template system. Template class is used for resource location
 * and execution of the template scripts using custom variable scope.
 *
 * @link https://github.com/gajus/brick for the canonical source repository
 * @license https://github.com/gajus/brick/blob/master/LICENSE BSD 3-Clause
 */
class Template implements \Psr\Log\LoggerAwareInterface {
    private
        /**
         * @var Psr\Log\LoggerInterface
         */
        $logger,
        /**
         * @var string Path to templates directory.
         */
        $directory,
        /**
         * @var string Templates that were rendered.
         */
        $templates = [],
        /**
         * @var array Shared environment variables accessible under the $template variable.
         */
        $env = [],
        /**
         * @todo Naming convention.
         * @var array Shared environment variables imported to all templates (can be overwritten at the time of render).
         */
        $globals = [];

    /**
     * Setting template directory will isolate all template resources
     * to the chosen base path. This is implemented to prevent malicious
     * fs traversal.
     * 
     * @param string $directory Absolute pate to the template directory.
     * @param array $globals Shared environment variables imported to all templates.
     */
    public function __construct ($directory, array $globals = []) {
        if (strpos($directory, '/') !== 0) {
            throw new Exception\InvalidArgumentException('Directory name must be an absolute path.');
        }

        if (!is_dir($directory)) {
            throw new Exception\LogicException('Template directory does not exist.');
        }

        $this->directory = realpath($directory);
        $this->globals = $globals;
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
     * Get name of the current template.
     *
     * @param boolean $translate
     * @return array
     */
    public function getName ($translate = true) {
        $name = $this->templates[count($this->templates) - 1];

        return $translate ? static::translateName($name) : $name;
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
        if ($this->logger) {
            $this->logger->debug('Rendering template.', ['method' => __METHOD__, 'template' => $name]);
        }

        $file = realpath($this->directory . '/' . $name . '.php');

        if (!$file) {
            throw new Exception\LogicException('Template ("' . $name . '") does not exist.');
        }

        if (mb_strpos($file, $this->directory) !== 0) {
            throw new Exception\InvalidArgumentException('Directory traversal attempt.');
        }

        $this->templates[] = $name;

        if (isset($env['template']) && $env['template'] !== $this) {
            throw new Exception\InvalidArgumentException('$template variable name is reserved.');
        }

        $env['template'] = $this;

        $env = $this->globals + $env;

        ksort($env);

        $output = static::renderView($file, $env);

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
     * @return null
     */
    public function append ($name, array $env = []) {
        echo $this->render($name, $env);
    }

    /**
     * Return template name that is derived from the relative template file name, e.g.
     * "booking/form" will result in "booking__form".
     *
     * @param string $name
     * @return string
     */
    static private function translateName ($name) {
        $name = str_replace('/', '__', $name);

        return $name;
    }

    /**
     * Sets a logger instance on the object
     *
     * @param LoggerInterface $logger
     * @return null
     */
    public function setLogger (\Psr\Log\LoggerInterface $logger) {
        $this->logger = $logger;
    }
}