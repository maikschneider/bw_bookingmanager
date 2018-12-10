plugin.tx_bwbookingmanager_pi1 {
  view {
    templateRootPaths.0 = EXT:bw_bookingmanager/Resources/Private/Templates/
    templateRootPaths.1 = {$plugin.tx_bwbookingmanager_pi1.view.templateRootPath}
    partialRootPaths.0 = EXT:bw_bookingmanager/Resources/Private/Partials/
    partialRootPaths.1 = {$plugin.tx_bwbookingmanager_pi1.view.partialRootPath}
    layoutRootPaths.0 = EXT:bw_bookingmanager/Resources/Private/Layouts/
    layoutRootPaths.1 = {$plugin.tx_bwbookingmanager_pi1.view.layoutRootPath}
  }

  persistence {
    storagePid = {$plugin.tx_bwbookingmanager_pi1.persistence.storagePid}
    #recursive = 1
  }

  features {
    #skipDefaultArguments = 1
    # if set to 1, the enable fields are ignored in BE context
    ignoreAllEnableFieldsInBe = 0
    # Should be on by default, but can be disabled if all action in the plugin are uncached
    requireCHashArgumentForActionArguments = 1
  }

  mvc {
    #callDefaultActionIfActionCantBeResolved = 1
  }

  settings {
    showPid = {$plugin.tx_bwbookingmanager_pi1.settings.showPid}
    calendarPid = {$plugin.tx_bwbookingmanager_pi1.settings.calendarPid}
    entryPid = {$plugin.tx_bwbookingmanager_pi1.settings.entryPid}
    backPid = {$plugin.tx_bwbookingmanager_pi1.settings.backPid}
    javascript {
      jquery = {$plugin.tx_bwbookingmanager_pi1.settings.javascript.jquery}
      customValidators = {$plugin.tx_bwbookingmanager_pi1.settings.javascript.customValidators}
      bookingmanager = {$plugin.tx_bwbookingmanager_pi1.settings.javascript.bookingmanager}
    }

    ajax {
      enable = {$plugin.tx_bwbookingmanager_pi1.settings.ajax.enable}
      typeNum = {$plugin.tx_bwbookingmanager_pi1.settings.ajax.typeNum}
    }

    email {
      senderAddress = {$plugin.tx_bwbookingmanager_pi1.settings.email.senderAddress}
      senderName = {$plugin.tx_bwbookingmanager_pi1.settings.email.senderName}
      replytoAddress = {$plugin.tx_bwbookingmanager_pi1.settings.email.replytoAddress}
      subject = {$plugin.tx_bwbookingmanager_pi1.settings.email.subject}
      template = {$plugin.tx_bwbookingmanager_pi1.settings.email.template}
    }
  }
}

BOOKINGMANAGER = PAGE
BOOKINGMANAGER {
  typeNum < plugin.tx_bwbookingmanager_pi1.settings.ajax.typeNum
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
    pluginName = Pi1
    vendorName = Blueways
  }
}

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
