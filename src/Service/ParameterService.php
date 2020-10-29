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
use WebEtDesign\ParameterBundle\Repository\ParameterRepository;

class ParameterService
{
    const DEFAULT_TYPE = [
        'text'     => 'Texte',
        'textarea' => 'Zone de texte',
        'number'   => 'Nombre',
        'list'     => [
            'array' => true,
            'type'  => 'Liste'
        ],
    ];

    protected ParameterRepository $repo;

    /**
     * Cached values of parameters
     *
     * @var array
     */
    protected array $values = [];

    protected array $parameters = [];

    protected array $types = [];

    /**
     * ParameterService constructor.
     *
     * @param ParameterRepository $repo
     * @param array $parametersType
     */
    public function __construct(ParameterRepository $repo, $parametersType = [])
    {
        $this->repo  = $repo;
        $this->types = $this->initTypes($parametersType);
    }

    private function initTypes($types)
    {
        $types = array_merge(self::DEFAULT_TYPE, $types);

        $tmp = [];
        foreach ($types as $k => $v) {
            if (!is_array($v)) {
                $type = $v;
            } else {
                $type = $v['type'];
            }

            $key       = is_int($k) ? $type : $k;
            $tmp[$key] = $type;
        }

        return $tmp;
    }

    /**
     * @return array
     */
    public function getTypes(): array
    {
        return $this->types;
    }

    /**
     * @param $code
     * @return Parameter
     */
    public function getParameter($code)
    {
        if (!isset($this->parameters[$code])) {
            $parameter = $this->repo->findCached($code);

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
        $parameter = $this->getParameter($code);

        return $parameter ? $parameter->getValue() : $default;
    }
}
