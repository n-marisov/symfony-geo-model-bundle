<?php

namespace Maris\Symfony\Geo\Model\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('geo');
        $treeBuilder->getRootNode()
            ->children()

                /*->arrayNode("earth")
                    ->children()

                    # Модель земного шара по умолчанию.
                    ->enumNode("model")
                        ->values(["ellipsoidal","spherical"])
                        ->defaultValue("spherical")
                    ->end()

                    # Стандарт эллипсоида.
                    ->enumNode('standard')
                        ->values(array_map(fn ( Ellipsoid $ellipsoid ) => $ellipsoid->name, Ellipsoid::cases()))
                        ->defaultValue(Ellipsoid::WGS_84->name)
                    ->end()

                    ->end()
                ->end()*/




                # Настройки точности по умолчанию.
                ->arrayNode("accuracy")
                    ->children()
                        # Допустимая погрешность в метрах при сравнениях объектов.
                        ->floatNode("allowed")->min(0.01 )->defaultValue(1.5)->end()

                        # Количество знаков после запятой для PolylineEncoder
                        ->integerNode("precision")->min(0)->max(PHP_FLOAT_DIG )->defaultValue(6)->end()
                    ->end()
                ->end()

            ->end()
        ->end();

        return $treeBuilder;
    }
}
