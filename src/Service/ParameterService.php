<?php
/**
 * Class ParameterService
 * Date: 17/01/2019
 * Time: 09:25
 *
 * @package AppBundle\Service
 * @author Julien GRAEFFLY <julien@webetdesign.com>
 */

declare(strict_types=1);

namespace WebEtDesign\ParameterBundle\Service;

use WebEtDesign\ParameterBundle\Entity\Parameter;
use WebEtDesign\ParameterBundle\Model\ParameterManagerInterface;

class ParameterService
{
    /**
     * Cached values of parameters
     */
    protected array $values = [];

    protected array $parameters = [];

    private ParameterManagerInterface $manager;

    public function __construct(ParameterManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function getParameter(string $code): ?Parameter
    {
        if (!isset($this->parameters[$code])) {
            $parameter = $this->manager->find($code);

            $this->parameters[$code] = $parameter ?: null;
        }

        return $this->parameters[$code];
    }

    public function getValue(string $code, string $default = ''): string
    {
        if (!isset($this->values[$code])) {
            $parameter = $this->manager->find($code);

            $this->values[$code] = $parameter ? $parameter->getValue() : $default;
        }

        return $this->values[$code];
    }
}
