<?php

namespace spec\Knp\FriendlyContexts\Context;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Knp\FriendlyContexts\Utils\Asserter;
use Knp\FriendlyContexts\Utils\TextFormater;

class EntityContextSpec extends ObjectBehavior
{
    /**
     * @param Symfony\Component\DependencyInjection\ContainerInterface $container
     * @param Doctrine\Common\Persistence\ManagerRegistry $doctrine
     * @param Doctrine\Common\Persistence\ObjectManager $manager
     * @param Doctrine\ORM\EntityRepository $repository
     * @param Doctrine\ORM\QueryBuilder $queryBuilder
     * @param Doctrine\ORM\AbstractQuery $query
     * @param Knp\FriendlyContexts\Doctrine\EntityResolver $resolver
     * @param Knp\FriendlyContexts\Record\Collection\Bag $bag
     * @param Knp\FriendlyContexts\Record\Collection $collection
     * @param Knp\FriendlyContexts\Utils\Asserter $asserter
     * @param \ReflectionClass $reflectionClass
     **/
    function let($container, $doctrine, $manager, $repository, $queryBuilder, $query, $resolver, $bag, $collection, \ReflectionClass $reflectionClass, $asserter)
    {
        $doctrine->getManager()->willReturn($manager);
        $manager->getRepository(Argument::any())->willReturn($repository);
        $repository->createQueryBuilder(Argument::any())->willReturn($queryBuilder);
        $queryBuilder->getQuery()->willReturn($query);
        $query->getResult()->willReturn(['', '']);
        $resolver->resolve($manager, 'entities', [""])->willReturn([$reflectionClass]);
        $bag->getCollection(Argument::any())->willReturn($collection);

        $container->has(Argument::any())->willReturn(true);
        $container->get('doctrine')->willReturn($doctrine);
        $container->get('friendly.entity.resolver')->willReturn($resolver);
        $container->get('friendly.entity.resolver')->willReturn($resolver);
        $container->get('friendly.record.bag')->willReturn($bag);
        $container->get('friendly.asserter')->willReturn(new Asserter(new TextFormater));

        $this->initialize([], $container);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Knp\FriendlyContexts\Context\EntityContext');
    }

    function it_should_assert_a_deletion()
    {
        $this->entitiesShouldHaveBeenDeleted('no', 'entities')->shouldReturn(null);
        $this->shouldThrow(new \Exception('2 entities should have been deleted, 0 actually', 1))->duringEntitiesShouldHaveBeenDeleted('2', 'entities');
    }

    function it_should_assert_a_creation()
    {
        $this->entitiesShouldHaveBeenCreated('2', 'entities')->shouldReturn(null);
        $this->shouldThrow(new \Exception('0 entities should have been created, 2 actually', 1))->duringEntitiesShouldHaveBeenCreated('no', 'entities');
    }
}
