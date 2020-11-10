<?php

namespace WebEtDesign\ParameterBundle\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use WebEtDesign\ParameterBundle\Entity\Parameter;
use WebEtDesign\ParameterBundle\Model\AbstractParameterManager;

class ParameterManager extends AbstractParameterManager
{
    private EntityManagerInterface $manager;

    private ParameterRepository $repository;

    public function __construct(EntityManagerInterface $manager, ParameterRepository $repository, $parametersType = [])
    {
        parent::__construct($parametersType);
        $this->manager    = $manager;
        $this->repository = $repository;
    }

    public function add($parameter): void
    {
        $this->manager->persist($parameter);
    }

    public function save(): void
    {
        $this->manager->flush();
    }

    public function find($code): ?Parameter
    {
        return $this->repository->findCached($code);
    }

    public function findIndexByCode()
    {
        return $this->repository->findAllIndexByCode();
    }
}