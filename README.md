# Booking Manager v11

## Install

* via composer
```
composer require blueways/bw-bookingmanager
```

* include TypoScript setup and constants
* include route enhancer in site config:

```
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

Conditions for notifications can be modified by using [PSR-14 events](https://docs.typo3.org/m/typo3/reference-coreapi/main/en-us/ApiOverview/Events/EventDispatcher/Index.html).

To add a new condition in the backend, insert a new checkbox item via TCA (optional)
```php
$GLOBALS['TCA']['tx_bwbookingmanager_domain_model_notification']['columns']['conditions']['items'][] = ['New condition name', 'condition-value'];
```

Add an EventListener for the `DispatchEntryNotificationEvent` event and modify the `doDispatch` property:

```
class NewNotificationCondition
{
    public function __invoke(DispatchEntryNotificationEvent $event)
    {
        // ... some checks
        $event->setDoDispatch(false);
    }
```

## E-Mails

Emails are send through the [TYPO3 Mail API](https://docs.typo3.org/m/typo3/reference-coreapi/11.5/en-us/ApiOverview/Mail/Index.html). To use custom email templates, add your template directory to the TYPO3 configuration and make sure the configured template name exists.


## Changelog

* Confirmation mails not configured via TypoScript anymore - create a separate Notification
