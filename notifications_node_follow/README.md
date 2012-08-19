# Node Follow notifications event

Adds a "follow-up" event that can be subscribed to with the Drupal [Notifications](http://drupal.org/project/notifications) framework. The idea of a follow-up article is common in news coverage, where a story is written and published as quickly as possible, then additonal stories with more information and new developments are published in subsequent days.

This module uses a CCK nodereference field to let authors or editors mark a node as being a follow-up to a previous node. Anyone subscribed to a previous node will receive a notifcation for the new node. The nodereferences are followed recursively to build a chain of related nodes, and notifications are sent for all previous nodes in the chain.

(c) 2012, Morris DigitalWorks

## Prerequisites

* Drupal 6
* Notifcations 6.x-4.0-beta7, with the notifications_content module enabled.
* CCK, with the nodereference module enabled.

## Installation

Add to the site's modules directory and enable the notfications_node_follow ("Node Follow-ups") module. This module modifies the admin page for Notifications Content to add checkboxes for which nodereferene fields to check. Make sure you have at least one content type with a nodereference field first, or there won't be much to configure.

## Caveat

This module has never seen the real world. The feature was not included in Follow News because it would require manual intervention from editors. I tested the module when I first wrote it but have not used it since then. It has never been used in a large deployment, so there could be performance problems, especially since it has to load multiple nodes to build a follow-up chain each time a watched node is saved.