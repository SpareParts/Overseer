# Overseer
Action-based authorization manager

Quick disclaimer: This is pretty much a work in progress. At this point this is more of a "proof-of-concept" than working code. Though the logic is sound and I fully intend to finish this into an awesome 1.0 release.

[![Build Status](https://travis-ci.org/SpareParts/Overseer.svg?branch=master)](https://travis-ci.org/SpareParts/Overseer)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/SpareParts/Overseer/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/SpareParts/Overseer/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/SpareParts/Overseer/badges/build.png?b=master)](https://scrutinizer-ci.com/g/SpareParts/Overseer/build-status/master)
[![Code Coverage](https://scrutinizer-ci.com/g/SpareParts/Overseer/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/SpareParts/Overseer/?branch=master)

## What is this and why should I care?

Overseer is an "action-based" auth manager, meaning it is based on authorizing possible "actions" (such as read, edit, delete, etc.) with given "subject" (such as Article, Product, Category etc.).

Overseer focuses on decoupling auth logic from the rest of the application. When solving problems as "user that is the owner of this product can edit it" other auth managers tend to wire the logic directly into the said product class or pile all possible actions (read, write, delete, ...) into one big method. Either way it breaks S of the SOLID principles (single responsibility principle) and that's where Overseer jumps in.

Basic building stones of Overseer are "voting assemblies", consisting of "voters". Each combination of action and subject can have (doesn't have to, though) its own voting assembly, thus separating concerns and responsibilities involved.

## Installation

### Composer

This is how we do it, boys.

```
composer require spareparts/overseer
```

## Basic usage

Let's imagine we have an article site, and we want to make sure the admin can edit article always, it's creator only unless it's banned.

This is how we create the voting assembly for this specific subject and action. It contains three voters, 
````
$assembly = new VotingAssembly(
	$subjectName = Article::class,
	$actionName = 'edit',
	$strategy = Strategy::FIRST_VOTE_DECIDES,
	$voters = [
		new RoleVoter(IVoteResult::ALLOW, 'admin'),
		new ClosureVoter(function(Article $subject, IdentityContext $context) {
			// allow the owner to edit
			if ($subject->ownerId === $context->getId()) {
				return IVotingResult::ALLOW;
			}
			return null;
		}),
		new RoleVoter(IVoteResult::ALLOW, 'user'),
	]
);

$authorizationManager = new GenericVotingManager([
	// our article edit assembly
	$assembly,
	// other assemblies...
	// ...
]);

````

Now let's use it
```
$context = new IdentityContext($userId, $userRoles);
$authorized = $authorizationManager->vote('edit', $article, $context);
if ($authorized->isAllowed()) {
	// we can edit!
}
```