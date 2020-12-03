############################
### COMMON CONFIGURATION ###
############################
plugin.tx_bwbookingmanager {

  persistence {
    storagePid = {$plugin.tx_bwbookingmanager_pi1.persistence.storagePid}
    #recursive = 1
  }

  mvc {
    callDefaultActionIfActionCantBeResolved = 1
  }

  view {
    templateRootPaths.0 = EXT:bw_bookingmanager/Resources/Private/Templates/
    templateRootPaths.1 = {$plugin.tx_bwbookingmanager.view.templateRootPath}
    partialRootPaths.0 = EXT:bw_bookingmanager/Resources/Private/Partials/
    partialRootPaths.1 = {$plugin.tx_bwbookingmanager.view.partialRootPath}
    layoutRootPaths.0 = EXT:bw_bookingmanager/Resources/Private/Layouts/
    layoutRootPaths.1 = {$plugin.tx_bwbookingmanager.view.layoutRootPath}
  }

  settings {
    cancelTime = {$plugin.tx_bwbookingmanager.settings.cancelTime}
    userStoragePid = {$plugin.tx_bwbookingmanager.settings.userStoragePid}
    mail {
      senderAddress = {$plugin.tx_bwbookingmanager.settings.mail.senderAddress}
      senderName = {$plugin.tx_bwbookingmanager.settings.mail.senderName}
      replytoAddress = {$plugin.tx_bwbookingmanager.settings.mail.replytoAddress}
      subject = {$plugin.tx_bwbookingmanager.settings.mail.subject}
      template = {$plugin.tx_bwbookingmanager.settings.mail.template}
      showUid = {$plugin.tx_bwbookingmanager.settings.mail.showUid}
      doSendConfirmation = {$plugin.tx_bwbookingmanager.settings.mail.doSendConfirmation}
    }

    directBooking {
      defaultStartTime = {$plugin.tx_bwbookingmanager.settings.directBooking.defaultStartTime}
      defaultEndTime = {$plugin.tx_bwbookingmanager.settings.directBooking.defaultEndTime}
      minOffset = {$plugin.tx_bwbookingmanager.settings.directBooking.minOffset}
      minLength = {$plugin.tx_bwbookingmanager.settings.directBooking.minLength}
    }
  }
}

######################
### CONTENT PLUGIN ###
######################
plugin.tx_bwbookingmanager_pi1 {

  features {
    skipDefaultArguments = 0
    # if set to 1, the enable fields are ignored in BE context
    ignoreAllEnableFieldsInBe = 0
    # Should be on by default, but can be disabled if all action in the plugin are uncached
    requireCHashArgumentForActionArguments = 1
  }

  settings {
    showPid = {$plugin.tx_bwbookingmanager_pi1.settings.showPid}
    calendarPid = {$plugin.tx_bwbookingmanager_pi1.settings.calendarPid}
    entryPid = {$plugin.tx_bwbookingmanager_pi1.settings.entryPid}
    backPid = {$plugin.tx_bwbookingmanager_pi1.settings.backPid}
  }
}

##################
### API PLUGIN ###
##################
plugin.tx_bwbookingmanager_api {
  features.requireCHashArgumentForActionArguments = 0
}

################
### API PAGE ###
################
BOOKINGMANAGER = PAGE
BOOKINGMANAGER {
  typeNum = 1556190329
  config {
    disableAllHeaderCode = 1
    xhtml_cleaning = 0
    admPanel = 0
    no_cache = 1
    contentObjectExceptionHandler = 0
    disableCharsetHeader = 0
  }

  10 = USER
  10 {
    userFunc = TYPO3\CMS\Extbase\Core\Bootstrap->run
    extensionName = BwBookingmanager
    pluginName = Api
    vendorName = Blueways
  }
}

##################
### ICS PLUGIN ###
##################
plugin.tx_bwbookingmanager_ics {
  features.requireCHashArgumentForActionArguments = 0
}

################
### ICS PAGE ###
################
BOOKINGMANAGERICS < BOOKINGMANAGER
BOOKINGMANAGERICS {
  typeNum = 1556190330
  10 {
    pluginName = Ics
  }
}

######################
### BACKEND MODULE ###
######################
module.tx_bwbookingmanager {
  settings {
    tableFields {
      1 = name
      2 = prename
      3 = email
      4 = phone
      5 = weight
    }

    showConfirmButton = 1
    showEditButton = 1
    showDeleteButton = 0
    showHideButton = 0
    showSecondaryButton = 0
    showHistoryButton = 0
    showViewBigButton = 0
  }
}

@import 'EXT:bw_bookingmanager/Configuration/TypoScript/Setup/config.typoscript'
@import 'EXT:bw_bookingmanager/Configuration/TypoScript/Setup/ext.bw_email.typoscript'
@import 'EXT:bw_bookingmanager/Configuration/TypoScript/Setup/ddev.typoscript'
