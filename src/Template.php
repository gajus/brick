<?php
namespace Gajus\Brick;

/**
 * Native PHP template system. Template class is used for resource location
 * and execution of the template scripts using custom variable scope.
 *
 * @link https://github.com/gajus/brick for the canonical source repository
 * @license https://github.com/gajus/brick/blob/master/LICENSE BSD 3-Clause
 */
class Template {
    private
        /**
         * @var string Path to templates directory.
         */
        $directory,
        /**
         * @var string Templates that were rendered.
         */
        $templates = [];

    
    public function __construct ($directory) {
        $this->setDirectory($directory);

        #$this->env['template'] = $this;
    }

    /**
     * Setting template directory will isolate all template resources
     * to the chosen base path. This is implemented to prevent malicious
     * fs traversal.
     * 
     * @param string $directory Absolute pate to the template directory.
     * @return void
     */
    private function setDirectory ($directory) {
        if (!is_dir($directory)) {
            throw new Exception\LogicException('Template directory does not exist.');
        }

        $this->directory = realpath($directory);
    }

    public function getDirectory () {
        return $this->directory;
    }

    /**
     * Get names of all templates that were rendedred using
     * this instance of Template.
     *
     * @return array
     */
    public function getTemplates () {
        return $this->templates;
    }

    /**
     * Defines new scope for template execution using $evn variables and
     * executes the template script in an output buffer.
     *
     * @param string $file File name (excluding file extension) relavite to the template directory.
     * @param array $env Variables populated in the template scope.
     * @return string Template output.
     */
    public function render ($file, array $env = []) {
    	$file = realpath($this->directory . '/' . $file . '.tpl.php');

        if (!$file) {
            throw new Exception\LogicException('Template does not exist.');
        }

        if (mb_strpos(realpath($file), $this->directory) !== 0) {
            throw new Exception\InvalidArgumentException('Directory traversal attempt.');
        }

        $view = new View ($file, $env);
	}

    /**
     * The additional static render method is used to prevent
     * exposing $this and other Template properties to the template scope.
     * Two variables (file and env) are captured using func_get_arg for
     * the same reason of not exposing them to the template scope.
     *
     * @return string Template output.
     */
    static private function render () {
        extract(func_get_arg(1), \EXTR_REFS);

        ob_start();
        require func_get_arg(0);
        return ob_get_clean();
    }

    /**
     * Return template name that is derived from the relative template file name, e.g.
     * "booking/form" will result in "booking__form".
     *
     * @param string $file
     * @return string
     */
    private function parseName ($file) {
        $file = trim(mb_substr($file, mb_strlen($this->directory)), '/');
        $file = strstr($file, '.', true);
        $file = str_replace('/', '__', $file);

        return $file;
    }
}