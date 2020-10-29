<?php

namespace WebEtDesign\ParameterBundle\Twig;

use WebEtDesign\ParameterBundle\Service\ParameterService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ParameterTwigExtension extends AbstractExtension
{

    private ParameterService $parameterService;

    public function __construct(ParameterService $parameterService)
    {
        $this->parameterService = $parameterService;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('parameter', [$this, 'getParameter']),
        ];
    }

    /**
     * @param string $code
     * @param $default
     *
     * @return string|null
     */
    public function getParameter($code, $default = '')
    {
        return $this->parameterService->getValue($code, $default);
    }
}
