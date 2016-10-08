# Table of content
* [Purpose](#purpose)
* [Submitting issues](#submitting-issues)
 * [Labeling issues](#labelling-issues)
 * [Templates](#templates)
* [Contributing changes](#Contributing changes)
 * [Rules](#rules) 
 * [General flow](#general-flow)

# Purpose

This document provides a set of best practices for bug reports, features suggestions, code submissions / pull requests, etc.

# Submitting issues

Bugs, and features are tracked as [Gitlab issues](https://git.klink.asia/klinkdms/dms/issues).

To submit a bug report create an issue that explains the problem and include additional details to help maintainers reproduce the problem:
- Search the project’s issue tracker to make sure it’s not a known issue.
- Use a clear and descriptive title for the issue to identify the problem.
- Describe the exact steps which reproduce the problem in as many details as possible.
- Provide specific examples to demonstrate the steps. Include links to documents, collections or projects, screenshots or animated gifs.
- Describe the behavior you observed after following the steps and point out what exactly is the problem with that behavior.
- Explain which behavior you expected to see instead and why.

Please make sure to highlight:
- the DMS version you are on
- the browser you use, and its version
- your operating system? Windows? (Vista? 7? 32-bit? 64-bit?) Mac OS X? (10.7.4? 10.9.0?) Linux? (Which distro? Which version of that distro? 32 or 64 bits?).

To facilitate the submission of an issue [templates](#templates) are available.


## Labeling issues

We use also labels to facilitate the organization of the whole set of issues. **An issue that don't follow the label convention will not be addressed in any case.**

**Type** named labels define the type of issues that can be submitted

- `Type: Bug` The issue represents a bug, a wrong effect of an action
- `Type: Feature` New feature request 
- `Type: Question` A question that need an answer, a potential discussion 

**Priority** an issue could have a priority, `critical > high > medium > low`

- `Priority: Critical` the issue have a bad impact on the overall system and needs to be fixed for the current release
- `Priority: High` the issue needs to be addressed at least in the next release as it affects more than one user and might become critical
- `Priority: Medium` the issue has a potential impact on the overall experience, but users are able to survive without it
- `Priority: Low` this is just to signal that the issue is a "good to have" thing, but everyone can live without it

**Category** if the issue type is too generic you can add one of the categories defined to let the developer knows something more, at a glance, about your issue 

- `Category: Localization` the issue has an impact on the localization in multiple languages
- `Category: Compatibility` the issue regards browser compatibility 
- `Category: UI` the issue regards User Interface elements
- `Category: Build Process` Relates to the build of a release 

**Coming From**: adds an information about how the issue was brought to the developer attention

- `Coming From: Idea` the issue was originated after the review of an idea in the Support forum
- `Coming From: Support Ticket` The issue was originated by a user submitted support ticket
- `Coming From: Testing` The issue has been discovered during internal testing

### Labeling rules

The labels that can be applied to a single issue must follow this rules:

- only one `Type` label
- only one `Priority` Label
- only one `Coming From` label (optional)
- only one `Category` label (optional)

When an issue is created the `Status` label might be omitted

## Templates

Templates have been created to make everyone's life easier:

* Submitters know how to ask or propose
* Developers understand quickly where the information is

The available templates are:

- **Bug**: for bug reports
- **Feature**: for new feature requests

The selection between the templates is presented in the Issue creation UI. If you want you can contribute to the improvement of those templates by submitting a merge 
request on the files contained in the folder [`.gitlab/issue_templates`](./.gitlab/issue_templates). 


## Contributing changes

### Rules

1. Always make a new branch for your work, no matter how small. This makes it easy for others to take just that one set of changes from your repository, in case you have multiple unrelated changes floating around.
2. Base your branch on the `master` branch
3. A corollary: don’t submit unrelated changes in the same branch/pull request! The maintainer shouldn’t have to reject your awesome bugfix because the feature you put in with it needs more review.
4. Add unit tests
5. Add also the documentation in `docs` folder

### General flow

1. Fork the project, creating e.g. `yourname/dms`.
2. Clone the project on your local environment `git clone git@git.klink.asia:yourname/dms`
3. Create the branch for the feature.
4. Write tests expecting the correct/fixed functionality; make sure they fail.
5. Make your changes to the source code.
6. Run tests again, making sure they pass.
7. Commit your changes: `git commit -m "Closes #1 - Foo the bars"`. If you have created 2 or more commits please squash them in a single commit and always mention the reference issue.
8. Push your commit to get it back up to your fork: `git push origin HEAD`.
9. [Create a merge request](https://git.klink.asia/klinkdms/dms/merge_requests/new) and let it go.
