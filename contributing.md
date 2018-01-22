# Contributing to the K-Box

Thanks for considering to contribute!

This document provides a set of best practices for [bug reports](submitting-bug-reports), [code submissions / pull requests](#contributing-changes), etc.

## Submitting bug reports

Bugs are tracked as [GitHub issues](https://github.com/k-box/k-box/issues).

Although we like investigating bugs, we kindly ask to fill the issue with as much details as possible:

- Use a clear and descriptive title for the issue to identify the problem.
- The K-Box version you are using
- What browser are you on? Chrome? Firefox? Edge? IE? Safari?...
- Describe the exact steps which reproduce the problem in as many details as possible.
- Describe the behavior you observed after following the steps and point out what exactly is the problem with that behavior.
- Explain which behavior you expected to see instead and why.

We are sure there is no need to say this, but please _search for your problem in the issue queue first_.

## Contributing changes

Pull requests are welcomed. To make everyone's life easier please consider to:

- Base your branch on the `master` branch
- Always [make a new branch for your work](#contribution-startup), no matter how small. This makes it easy for others to take just that one set of changes from your repository, in case you have multiple unrelated changes floating around.
 - A corollary: don’t submit unrelated changes in the same branch/pull request! The maintainer shouldn’t have to reject your awesome bugfix because the feature you put in with it needs more review.
- If there is an issue, make sure to cite it in the Pull Request
- Add unit tests

#### Contribution startup

1. Fork the project, creating e.g. `yourname/k-box`.
2. Clone the project on your local environment `git clone https://github.com/yourname/k-box.git`
3. Create the branch for the feature `git checkout -b [name_of_your_new_branch]`
4. Write tests expecting the correct/fixed functionality; make sure they fail.
5. Make your changes to the source code.
6. Run tests again, making sure they pass.
7. Commit your changes: `git commit -m "Closes #1 - Foo the bars"`. If you have created 2 or more commits please squash them in a single commit and always mention the reference issue.
8. Push your commit to get it back up to your fork: `git push`.
9. Create a Pull request and let it go.
