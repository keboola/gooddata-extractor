<?php

declare(strict_types=1);

namespace Keboola\GoodDataExtractor;

use Keboola\Component\Config\BaseConfigDefinition;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class ConfigDefinition extends BaseConfigDefinition
{
    protected function getParametersDefinition(): ArrayNodeDefinition
    {
        $parametersNode = parent::getParametersDefinition();
        // @formatter:off
        /** @noinspection NullPointerExceptionInspection */
        $parametersNode
            ->children()
            ->scalarNode('pid')->end()
            ->scalarNode('writer_id')->end()
            ->scalarNode('username')->end()
            ->scalarNode('#password')->end()
            ->scalarNode('host')->end()
            ->arrayNode('reports')->scalarPrototype()->end()
            ->end()
        ;
        // @formatter:on
        return $parametersNode;
    }
}
