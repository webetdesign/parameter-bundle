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

    public function getParameter(string $code, string $default = ''): string
    {
        return $this->parameterService->getValue($code, $default);
    }
}
