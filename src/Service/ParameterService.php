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

use Doctrine\ORM\EntityManagerInterface;
use WebEtDesign\MediaBundle\CMS\transformer\MediaContentTransformer;
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
    private EntityManagerInterface $em;

    public function __construct(ParameterManagerInterface $manager, EntityManagerInterface $em)
    {
        $this->manager = $manager;
        $this->em = $em;
    }

    public function getParameter(string $code): ?Parameter
    {
        if (!isset($this->parameters[$code])) {
            $parameter = $this->manager->find($code);

            $this->parameters[$code] = $parameter ?: null;
        }

        return $this->parameters[$code];
    }

    public function getValue(string $code, string $default = '')
    {
        if (!isset($this->values[$code])) {
            /** @var Parameter|null $parameter */
            $parameter = $this->manager->find($code);

            if ($parameter && $parameter->getType() === 'media'){
                $transformer = new MediaContentTransformer($this->em);
                $this->values[$code] = $transformer->transform($parameter->getValue());
            }else{
                $this->values[$code] = $parameter ? $parameter->getValue() : $default;
            }
        }


        return $this->values[$code];
    }
}
