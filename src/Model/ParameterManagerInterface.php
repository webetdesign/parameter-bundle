<?php

namespace WebEtDesign\ParameterBundle\Model;

interface ParameterManagerInterface
{

    public function create($code = null);

    public function add($parameter);

    public function save();

    public function find($code);

    public function findIndexByCode();

    public function getTypes();

}