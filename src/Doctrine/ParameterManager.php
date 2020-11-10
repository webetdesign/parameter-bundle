<?php

namespace WebEtDesign\ParameterBundle\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use WebEtDesign\ParameterBundle\Entity\Parameter;
use WebEtDesign\ParameterBundle\Model\ParameterManagerInterface;

class ParameterManager implements ParameterManagerInterface
{
    private EntityManagerInterface $manager;

    private ParameterRepository $repository;

    public function __construct(EntityManagerInterface $manager, ParameterRepository $repository)
    {
        $this->manager    = $manager;
        $this->repository = $repository;
    }

    public function create($code = null)
    {
        return new Parameter($code);
    }

    public function add($parameter)
    {
        $this->manager->persist($parameter);
    }

    public function save()
    {
        $this->manager->flush();
    }

    public function find($code)
    {
        return $this->repository->findCached($code);
    }

    public function findIndexByCode()
    {
        return $this->repository->findAllIndexByCode();
    }
}