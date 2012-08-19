# Node Revision notifcations event

Adds a "new revision" event that can be subscribed to with the Drupal [Notifications](http://drupal.org/project/notifications) framework. This is similar to the builtin node-update event, but it only fires when the node is saved with the "Create new revison" checkbox enabled. The idea is to enable a workflow where only major updates to a node trigger the sending of notifications.

(c) 2012, Morris DigitalWorks

## Prerequisites

* Drupal 6
* Notifcations 6.x-4.0-beta7, with the notifications_content module enabled.

## Installation

Add to the site's modules directory and enable the notifcations_revision ("Node Revision event") module. The event has no special settings and is configured like other events through the Notifcations admin screen.