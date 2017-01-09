<?php
namespace SpareParts\Overseer\Tests\Integration;


use SpareParts\Overseer\Assembly\VotingAbilityAwareAssembly;
use SpareParts\Overseer\Assembly\VotingAssembly;
use SpareParts\Overseer\Context\IIdentityContext;
use SpareParts\Overseer\GenericVotingManager;
use SpareParts\Overseer\Context\IdentityContext;
use SpareParts\Overseer\Context\IVotingContext;
use SpareParts\Overseer\StrategyEnum;
use SpareParts\Overseer\Voter\ClosureVoter;
use SpareParts\Overseer\Voter\RoleVoter;
use SpareParts\Overseer\Voter\SingleVoterResult;
use SpareParts\Overseer\VotingDecisionEnum;

class IntegrationTest extends \PHPUnit_Framework_TestCase
{


    /**
     * @param DummyArticle $article
     * @param IVotingContext $context
     * @param VotingDecisionEnum $decision
     * @param string $reason
     * @test
     * @dataProvider fvdDataProvider
     */
    public function strategyFirstVoteDecidesWorksCorrectly(DummyArticle $article, IVotingContext $context, VotingDecisionEnum $decision, $reason)
    {
        $assembly1 = new VotingAbilityAwareAssembly(
            StrategyEnum::FIRST_VOTE_DECIDES(),
            [
                new RoleVoter(VotingDecisionEnum::ALLOWED(), 'admin', 'is_admin'),
                new ClosureVoter(function (DummyArticle $article) {
                    if ($article->isBanned()) {
                        return new SingleVoterResult(VotingDecisionEnum::DENIED(), 'banned');
                    }
                }),
                new RoleVoter(VotingDecisionEnum::ALLOWED(), 'user', 'is_user'),
            ],
            'read',
            DummyArticle::class,
            IIdentityContext::class
        );

        $manager = new GenericVotingManager([
            $assembly1
        ]);

        $result = $manager->vote('read', $article, $context);
        $this->assertSame($decision, $result->getDecision());
        $this->assertSame($reason, $result->getPartialResults()[0]->getReason());
    }


    /**
     * @return array
     */
    public function fvdDataProvider()
    {
        return [
            'user see article' => [
                'article' => new DummyArticle(false),
                'context' => new IdentityContext(1, ['user']),
                VotingDecisionEnum::ALLOWED(),
                'is_user'
            ],
           'user cant see banned article' => [
                'article' => new DummyArticle(true),
                'context' => new IdentityContext(1, ['user']),
                VotingDecisionEnum::DENIED(),
                'banned'
            ],
           'admin can see banned article' => [
                'article' => new DummyArticle(true),
                'context' => new IdentityContext(1, ['admin']),
                VotingDecisionEnum::ALLOWED(),
                'is_admin'
            ],
        ];
    }

}
