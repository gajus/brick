<?php
namespace Gajus\Brick;

/**
 * Responsible for resource resolution and scope management.
 *
 * @link https://github.com/gajus/brick for the canonical source repository
 * @license https://github.com/gajus/brick/blob/master/LICENSE BSD 3-Clause
 */
class System {
    private
        /**
         * @var string Path to templates directory.
         */
        $directory,
        /**
         * @var array Variables shared across all templates.
         */
        $globals = [],
        /**
         * @var string Extension of the template files.
         */
        $template_extension = '.php';

    /**
     * @param string $directory Path to the template directory.
     * @param array $globals Variables shared across all templates.
     */
    public function __construct ($directory, array $globals = []) {
        $this->setDirectory($directory);
        $this->setGlobals($globals);
    }

    /**
     * Template resolution is restricted to the paths under the template directory.
     * 
     * @param string $directory Absolute path to the template directory.
     */
    public function setDirectory ($directory) {
        if (strpos($directory, '/') !== 0) {
            throw new Exception\InvalidArgumentException('Directory name must be an absolute path.');
        }

        if (!is_dir($directory)) {
            throw new Exception\LogicException('Template directory does not exist.');
        }

        $this->directory = realpath($directory);
    }

    /**
     * Get the template resolution base path.
     * 
     * @return string
     */
    public function getDirectory () {
        return $this->directory;
    }

    /**
     * @param array $globals
     */
    public function setGlobals (array $globals) {
        $this->globals = $globals;
    }

    /**
     * @return array
     */
    public function getGlobals () {
        return $this->globals;
    }

    /**
     * @param string $template_extension
     */
    public function setTemplateExtension ($template_extension) {
        $this->template_extension = $template_extension;
    }

    /**
     * @return string
     */
    public function getTemplateExtension () {
        return $this->template_extension;
    }

    /**
     * @param string $name Template file (excluding the file extension) relative to the templates directory.
     * @param array $scope Variables populated in the template scope.
     * @return string
     */
    public function view ($name, array $scope = []) {
        return $this->template($name, $scope)->render();
    }

    /**
     * @param string $name Template file (excluding the file extension) relative to the templates directory.
     * @param array $scope Variables populated in the template scope.
     * @return Gajus\Brick\Template
     */
    public function template ($name, array $scope = []) {
        $file = realpath($this->directory . '/' . $name . $this->getTemplateExtension());

        if (!$file) {
            throw new Exception\LogicException('Template ("' . $name . '") does not exist.');
        }

        if (mb_strpos($file, $this->directory) !== 0) {
            throw new Exception\InvalidArgumentException('Directory traversal attempt.');
        }

        if (array_key_exists('globals', $scope) && $scope['globals'] !== $this->globals) {
            throw new Exception\InvalidArgumentException('"globals" variable name is reserved.');
        }

        if (array_key_exists('system', $scope) && $scope['system'] !== $this) {
            throw new Exception\InvalidArgumentException('"system" variable name is reserved.');
        }

        $scope['globals'] = $this->getGlobals();
        $scope['system'] = $this;

        return new Template($file, $scope);
    }
}