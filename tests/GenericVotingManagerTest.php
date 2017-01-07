<?php
namespace SpareParts\Overseer\Tests;

use SpareParts\Overseer\Assembly\IVotingAbilityAwareAssembly;
use SpareParts\Overseer\Assembly\IVotingAssembly;
use SpareParts\Overseer\GenericVotingManager;
use SpareParts\Overseer\Context\IVotingContext;
use SpareParts\Overseer\IVotingResult;

class GenericVotingManagerTest extends \PHPUnit_Framework_TestCase
{


    /**
     * @test
     */
    public function decidesCorrectAssembly()
    {
        $subject = $this->prepareSubject('Category');
        $action = 'edit';
        $context = $this->prepareContext(12, ['editor', 'user']);
        $result = \Mockery::mock(IVotingResult::class);
        $assemblies = [
            $a1 = \Mockery::mock(IVotingAbilityAwareAssembly::class)
                ->shouldReceive('canVoteOn')->with($action, $subject, $context)->andReturn(false)->getMock()
                ->shouldNotReceive('commenceVote')->getMock(),
            $a2 = \Mockery::mock(IVotingAbilityAwareAssembly::class)
                ->shouldReceive('canVoteOn')->with($action, $subject, $context)->andReturn(true)->getMock()
                ->shouldReceive('commenceVote')->with($subject, $context)->andReturn($result)->getMock(),
            $a3 = \Mockery::mock(IVotingAbilityAwareAssembly::class)
                ->shouldNotReceive('canVoteOn')->getMock()
                ->shouldNotReceive('commenceVote')->getMock(),
        ];

        $manager = new GenericVotingManager($assemblies);
        $r = $manager->vote($action, $subject, $context);
        $this->assertSame($result, $r);
    }

    /**
     * @test
     * @expectedException \SpareParts\Overseer\InvalidVotingResultException
     */
    public function noValidAssemblyMeansException()
    {
        $subject = $this->prepareSubject('Category');
        $action = 'edit';
        $context = $this->prepareContext(12, ['editor', 'user']);
        $assemblies = [
            $a1 = \Mockery::mock(IVotingAbilityAwareAssembly::class)
                ->shouldReceive('canVoteOn')->with($action, $subject, $context)->andReturn(false)->getMock()
                ->shouldNotReceive('commenceVote')->getMock(),
            $a2 = \Mockery::mock(IVotingAbilityAwareAssembly::class)
                ->shouldReceive('canVoteOn')->with($action, $subject, $context)->andReturn(false)->getMock()
                ->shouldNotReceive('commenceVote')->getMock(),
        ];

        $manager = new GenericVotingManager($assemblies);
        $manager->vote($action, $subject, $context);
    }


    /**
     * @test
     */
    public function decidesCorrectAssemblyWhenMultipleCanVote()
    {
        $subject = $this->prepareSubject('Category');
        $action = 'edit';
        $context = $this->prepareContext(12, ['editor', 'user']);
        $result = \Mockery::mock(IVotingResult::class);
        $assemblies = [
            $a1 = \Mockery::mock(IVotingAbilityAwareAssembly::class)
                ->shouldReceive('canVoteOn')->with($action, $subject, $context)->andReturn(false)->getMock()
                ->shouldNotReceive('commenceVote')->getMock(),
            $a2 = \Mockery::mock(IVotingAbilityAwareAssembly::class)
                ->shouldReceive('canVoteOn')->with($action, $subject, $context)->andReturn(true)->getMock()
                ->shouldReceive('commenceVote')->with($subject, $context)->andReturn($result)->getMock(),
            $a3 = \Mockery::mock(IVotingAbilityAwareAssembly::class)
                ->shouldReceive('canVoteOn')->with($action, $subject, $context)->andReturn(true)->getMock()
                ->shouldNotReceive('commenceVote')->getMock(),
        ];

        $manager = new GenericVotingManager($assemblies);
        $r = $manager->vote($action, $subject, $context);
        $this->assertSame($result, $r);
    }


    /**
     * @test
     * @expectedException \SpareParts\Overseer\InvalidArgumentException
     * @expectedExceptionMessage must implement IVotingAbilityAwareAssembly
     */
    public function basicVotingContextThrowsException()
    {
        $subject = $this->prepareSubject('Category');
        $action = 'edit';
        $context = $this->prepareContext(12, ['editor', 'user']);
        $assemblies = [
            $a1 = \Mockery::mock(IVotingAssembly::class)
                ->shouldReceive('canVoteOn')->with($action, $subject, $context)->andReturn(false)->getMock()
                ->shouldNotReceive('commenceVote')->getMock(),
            $a2 = \Mockery::mock(IVotingAbilityAwareAssembly::class)
                ->shouldReceive('canVoteOn')->with($action, $subject, $context)->andReturn(false)->getMock()
                ->shouldNotReceive('commenceVote')->getMock(),
        ];

        $manager = new GenericVotingManager($assemblies);
        $manager->vote($action, $subject, $context);
    }


    /**
     * @param string $classname
     *
     * @return \Mockery\MockInterface
     */
    private function prepareSubject($classname)
    {
        $subject = \Mockery::mock($classname);
        return $subject;
    }

    /**
     * @param mixed $id
     * @param string[] $roles
     *
     * @return \Mockery\MockInterface|IVotingContext
     */
    private function prepareContext($id, $roles)
    {
        $context = \Mockery::mock(IVotingContext::class)
            ->shouldReceive('getId')->andReturn($id)->getMock()
            ->shouldReceive('getRoles')->andReturn($roles)->getMock();
        return $context;
    }

    public function tearDown()
    {
        \Mockery::close();
    }
}
