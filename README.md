# Booking Manager v11

## Install

* via composer
```bash
composer require blueways/bw-bookingmanager
```

* include TypoScript setup and constants
* include route enhancer in site config:

```yaml
imports:
  - resource: 'EXT:bw_bookingmanager/Configuration/Routing/Api.yaml'
  - resource: 'EXT:bw_bookingmanager/Configuration/Routing/Ics.yaml'
```

## Usage

To create a new calendar

* Create a new SysFolder and use it as container for "Booking Manager"
* Add a new Calendar to the folder
* Create timeslots for the calendar or enable direct booking in calendar settings

## Notifications

To send automated emails, add a new Notification record inside a SysFolder and select the event and calendars you want to be notified about.

### E-Mail Templates

The template can be selected in the Notification settings. To modify the available templates, use PageTS:

```typo3_typoscript
TCEFORM.tx_bwbookingmanager_domain_model_notification {
  template.addItems {
    welcome = Welcome Template
  }
}
```

Emails are send through the [TYPO3 Mail API](https://docs.typo3.org/m/typo3/reference-coreapi/11.5/en-us/ApiOverview/Mail/Index.html). To use custom email templates, add your template directory to the TYPO3 configuration and make sure the configured template name exists:

```php
$GLOBALS['TYPO3_CONF_VARS']['MAIL']['templateRootPaths'][107] = 'EXT:extension/Resources/Private/Templates/Email';
```

### Conditional notifications

To add a new condition in the backend, register a new checkbox item via TCA:

```php
$GLOBALS['TCA']['tx_bwbookingmanager_domain_model_notification']['columns']['conditions']['items'][] = [
    'New condition name', \Vendor\Extension\NotificationCondition\TheNewCondition::class
];
```

The value of the item should be a class name that implements the ```NotificationConditionInterface```.

```php
class TheNewCondition implements NotificationConditionInterface
{

    public function doSend(Entry $entry): bool
    {
        // ...logic

        // prevent sending of email
        return false;
    }
}
```

## API

There a various ways to extend or modify the behavior of the booking manager.

### Events

There are [PSR-14 events](https://docs.typo3.org/m/typo3/reference-coreapi/main/en-us/ApiOverview/Events/EventDispatcher/Index.html) dispatched which offer a way to execute custom functionality

* AfterEntryCreationEvent

## Changelog

* Confirmation mails not configured via TypoScript anymore - create a separate Notification
