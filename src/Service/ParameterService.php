<?php
/**
 * Class ParameterService
 * Date: 17/01/2019
 * Time: 09:25
 *
 * @package AppBundle\Service
 * @author Julien GRAEFFLY <julien@webetdesign.com>
 */

namespace WebEtDesign\ParameterBundle\Service;

use WebEtDesign\ParameterBundle\Entity\Parameter;
use WebEtDesign\ParameterBundle\Model\ParameterManagerInterface;

class ParameterService
{
    const DEFAULT_TYPE = [
        'text'     => 'Texte',
        'textarea' => 'Zone de texte',
        'number'   => 'Nombre',
        'list'     => 'Liste',
    ];

    /**
     * Cached values of parameters
     *
     * @var array
     */
    protected array $values = [];

    protected array $parameters = [];

    private ParameterManagerInterface $manager;

    /**
     * ParameterService constructor.
     *
     * @param ParameterManagerInterface $manager
     */
    public function __construct(ParameterManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param $code
     * @return Parameter
     */
    public function getParameter($code)
    {
        if (!isset($this->parameters[$code])) {
            $parameter = $this->manager->find($code);

            $this->parameters[$code] = $parameter ?: null;
        }

        return $this->parameters[$code];
    }

    /**
     * @param $code
     * @param string $default
     *
     * @return string
     */
    public function getValue($code, $default = '')
    {
        if (!isset($this->values[$code])) {
            $parameter = $this->manager->find($code);

            $this->values[$code] = $parameter ? $parameter->getValue() : $default;
        }

        return $this->values[$code];
    }
}
