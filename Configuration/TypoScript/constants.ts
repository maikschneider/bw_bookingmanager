plugin.tx_bwbookingmanager {
  persistence {
    storagePid =
  }

  view {
    templateRootPath =
    partialRootPath =
    layoutRootPath =
  }

  settings {
    cancelTime = 1440
    mail {
      senderAddress = noreply@example.com
      senderName = Example sender name
      replytoAddress = noreply@example.com
      subject = Example subject
      template = Default
      showUid =
      doSendConfirmation = 0
    }

    directBooking {
      defaultStartTime =
      defaultEndTime =
      timeBetween =
      minLength =
    }
  }
}

plugin.tx_bwbookingmanager_pi1 {
  settings {
    showPid =
    calendarPid =
    entryPid =
    backPid =
  }
}

plugin.tx_bwemail {
  settings {
    senderAddress = {$plugin.tx_bwbookingmanager.settings.mail.senderAddress}
    senderName = {$plugin.tx_bwbookingmanager.settings.mail.senderName}
    replytoAddress = {$plugin.tx_bwbookingmanager.settings.mail.replytoAddress}
    subject = {$plugin.tx_bwbookingmanager.settings.mail.subject}
    template = {$plugin.tx_bwbookingmanager.settings.mail.template}
    showUid = {$plugin.tx_bwbookingmanager.settings.mail.showUid}
  }
}
