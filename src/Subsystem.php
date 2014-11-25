<?php
namespace Gajus\Brick;

/**
 * Responsible for template resolution and scope management.
 *
 * @link https://github.com/gajus/brick for the canonical source repository
 * @license https://github.com/gajus/brick/blob/master/LICENSE BSD 3-Clause
 */
class Subsystem {
    private
        /**
         * @var Gajus\Brick\System $system
         */
        $system;

    /**
     * @param Gajus\Brick\System $system
     */
    public function __construct (\Gajus\Brick\System $system) {
        $this->system = $system;
    }

    /**
     * @param string $name Template file (excluding the file extension) relative to the templates directory.
     * @param array $scope Variables populated in the template scope.
     * @return string
     */
    public function view ($name, array $scope = []) {
        return $this->system->view($name, $scope);
    }
}