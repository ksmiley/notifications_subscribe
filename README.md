# Follow News for Drupal

Implements the "Follow" feature for Morris newspaper websites by adding a custom interface on the Drupal Messaging and Notifications frameworks.

More information about Follow News and screenshots of it in production are on my [portfolio page](http://keithsmiley.net/cv/projects/#project-follow).

(c) 2012, Morris DigitalWorks

## Prerequisites

* Drupal 6 (only tested with [Pressflow](http://pressflow.org/) but should work with stock Drupal)
* [OpenCalais](http://drupal.org/project/opencalais) and its prerequisites
* [Messaging](http://drupal.org/project/messaging) 6.x-4.0-beta8
* [Notifications](http://drupal.org/project/notifications) 6.x-4.0-beta7
* [Token](http://drupal.org/project/token) (required by Messaging and Notifications)
* [ctools](http://drupal.org/project/ctools) 1.7 (the newer 1.8 will work with minor changes)

## Installing

You probably shouldn't. This module implements only what we needed for the Follow News project and nothing else, so it probably doesn't quite fulfill your needs. It may even be dangerous, since it makes certain assumptions that were OK for our needs (such as skipping access checks on some notification types) but that might not fit your site. Instead, read through the source code and use this module as a guide for creating your own custom notifications system.

If you still really want to install this, see [CONFIGURE.md](CONFIGURE.md) for instructions on setting up Messaging and Notifications. The documentation assumes that updates will be sent with the [Contactology send method](https://github.com/ksmiley/messaging_contactology), but using a different email method should also work.

## Background

Follow News is a feature on the Morris newspaper sites that's meant to keep readers informed about a story as it develops on an unpredictable schedule. By emailing specific updates, the feature encourages repeat visitors and (hopefully) increases traffic.

Each story is potentially a teaser for future stories. The news is what we know now, with the promise to tell if we know more later. A crime near my work might catch my interest and make me wonder what will happen, but just browsing the website doesn't ensure I'll see updates about the arrest, arraignment, trial or verdict.

Unlike reading the print edition, visiting a newspaper website does not offer a fixed view of the day's news. Even a dedicated reader who visits the site multiple times each day is not guaranteed to see every story he or she is interested in. Depending on when the update is posted, the story could could already be pushed down or even off the front page.

Follow tries to keep the reader informed regardless of what time the next update is posted. Readers pick from a list of people, places, and topics related to the news story that they're interested in. Later, when an article is posted that also contains one of those elements, Follow sends an email to the reader with a summary and link.

The Follow project started with a few major requirements. Besides sending news updates, it needed to be:

* Completely automated, with no editor input required (though editors have some control if needed).
* Simple to use, by keeping the number of options for users (and therefore the number of decisions to make) to a minimum.
* Non-intrusive, with only opt-in notifications and obvious ways to stop getting notifications that aren't useful.

## Implementation

The topics that available to follow on an article are extracted from the body text using the [OpenCalais](http://www.opencalais.com/) service (and the associated Drupal module). The service can identify a substantial number of keywords across dozens of categories in even a short article, so we limit the categories that are displayed to the user. In testing we found that Calais is best at extracting proper nouns and that those were often most helpful for finding follow-up stories.

We used contributed Drupal modules as much as possible. The main challenge was reducing and simplifying the features offered by the Messaging and Notifications modules. They're designed to be versatile, but it's difficult to tailor them to a single, specific use case. The modules insisted on showing more information and options to users than we wanted to expose. Oftentimes the interface text was too generic and used abstract terms that were confusing in the context of Follow. For example, the verb "subscribe" is common in the default interface, but we wanted to use a more specific verb like "follow," both for branding and because subscribing already has a different meaning for a newspaper.

The `notifications_subscribe` module is the glue that assembles a collection of contributed code into Follow News. It implements a custom interface for following articles and managing followed topics, and it uses `hook_form_alter` and `hook_menu_alter` to disable parts of Messaging and Notifications that can't be changed by configuration.

The interface we built lets readers follow topics without leaving the story page. It also adds a tab on the account management screen that lets users quickly review and remove notifications that are no longer needed. The module uses Ctools AJAX methods to ensure these screens don't break if Javascript is not available.

The Messaging package has comes with an email send method, but we wanted to offload delivery to an external server. I wrote the [`messaging_contactology`](https://github.com/ksmiley/messaging_contactology)  module to instead send notifications through our email marketing provider. This approach gives us easy but coarse tracking of open and click-through rates.