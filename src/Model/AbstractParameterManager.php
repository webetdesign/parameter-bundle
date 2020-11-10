<?php

namespace WebEtDesign\ParameterBundle\Model;

use WebEtDesign\ParameterBundle\Entity\Parameter;

abstract class AbstractParameterManager implements ParameterManagerInterface
{
    const DEFAULT_TYPE = [
        'text'     => 'Texte',
        'textarea' => 'Zone de texte',
        'number'   => 'Nombre',
        'list'     => 'Liste',
    ];

    protected array $types = [];

    public function __construct($parametersType = [])
    {
        $this->types = $this->initTypes($parametersType);
    }

    protected function initTypes($types)
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

    public function create($code = null)
    {
        return new Parameter($code);
    }

    public function getTypes()
    {
        return $this->types;
    }
}