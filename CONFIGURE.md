# Configuring Follow

Deploying Follow News, the hard way. These are notes from preparing the original launch. Most of these steps were actually done in production by scripts.


## Modules to Deploy

### Contributed

* rdf
* opencalais
* autoload
* messaging
* notifications
* notifications_tools

### Custom

* messaging_contactology
* notifications_subscribe

### Order

1. enable RDF first. *Do not* enable Calais yet
2. enable Calais API, Calais, Calais Tag Modifier
3. enable Autoload (under Other)
4. enable Messaging, Simple Mail, Contactology Messaging, Text Templates
5. enable Notifications, Administration tools, Content Notifications,
   Node Revision event, SMS Follow News, Taxonomy Notifications


## Permissions

### authenticated user

* notifications: manage own subscriptions, maintain own subscriptions
* notifications_content: subscribe to content
* notifications_tags: subscribe to taxonomy terms

### Staff

* notifications_content: skip notifications

### Reporters and Editors

* calais: access calais

### Administrator

* messaging: administer messaging
* messaging_template: edit message templates
* calais: access calais, access calais rdf, administer calais
* calais_api: administer calais api
* notifications: administer notifications, manage all subscriptions


## Calais

API Settings:
- Paste your API key.

Node Settings:
- General: store locally NOT checked
- Global: check all categories
- Editorial node:
  * send request: Automatic
  * tag content: Always
  * minimum relevancy: 0.2 (can be tweaked)
  * tag categories: Global
- Semantic Proxy: turned off

Tag Modifications:
- blacklist:
  CDATA,XML,Other

Bulk Processing:
- "Add to queue" on the editorial nodes. Set the number to process per
  cron run to something reasonable. The queue can also be processed with
  "drush calais-pq", so cron can be a low number and drush can do most
  of the work overnight.


## Messaging

### Messaging tab

* disable Drupal Mail send method. set Contactology to default

### Send Methods tab

* change Contactology name to "E-mail"
* generate an API key from Contactology and put it in the field.
* set Contact Source to something descriptive like "Followed topic at <sitename>"
* create a Contactology campaign
* under filters, set Contactology format filter to "No filter" and final filter to "No filter"


## Messaging Templates

All custom templates go in the Contactology (or "E-mail") section.

All filters should be "none".

* Notifications event:
  * header and footer
* Notifications for node events:
  * leave subject blank
  * put in code for a single item
  * digest line is currently unused, but can probably just be set to the markup for a message title
* Notifications for node (revision, update, creation, etc.)
  * change the Default section
  * set subject to the title with "News Update: " before it
  * clear out other fields
* Notifications messages:
  * apparently only used for anonymous subscriptions, so can be ignored until needed.


## Notifications

* Main tab:
  * Notify poster of own posts OFF
  * Immediate sending OFF
  * Notifications sender: No one (All notifications will appear as coming from the web site)
  * Default interval: Immediately
* Events tab:
  * turn on Node revision, Node creation. others off. default templates.
* Intervals tab:
  * delete all except Immediately
* Subscriptions tab:
  * enable Tags and Thread. others off.
  * configure for each content type
      * under Administer Content Types, turn on Thread and Tags for Editorial node
  * Allowed vocabularies:
      * City
      * Company
      * Facility
      * Movie
      * Music Group
      * Natural Feature
      * Organization
      * Person
      * Published Medium
      * Radio Program
      * Radio Station
      * TV Station
* Queue Processing tab:
  * process on cron enabled


## Contactology

Modify default footer to not include "Forward to a Friend".

Create a new footer that includes "Forward to a Friend" that can be used with standard campaigns.


## Miscellaneous

* admin/settings/error-reporting: set 403 page to "login-required"
  That menu item is provided as part of different (unreleased) custom module. All it does is display a login form with permission-denied errors, which works for our setup because not being logged in is by far the most common reason for a 403.

* add the follow link to the appropriate place in the theme.

* setup a script to run "drush calais-pq" several times overnight to chew through backlog
  of untagged editorial nodes.