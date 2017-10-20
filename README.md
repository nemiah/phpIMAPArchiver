# phpIMAPArchiver
One of my customers had the problem that her iPhone couldn't handle the huge amount of emails in her INBOX.

So I wrote this script to regularly archive her INBOX to another folder.

## Getting Started
Just clone this repository and edit the file archive.php:

    $server = "localhost:143";
    $user = "test@test.de";
    $password = "123456";

    $from = "INBOX";
    $to = "INBOX.Archiv.{Y}";

    $olderThan = "-6 months"; //strtotime format

Then run the script:

    php archive.php

## Cronjob

To regularly archive the messages, add a cronjob:

    0 3 * * 0 /usr/bin/php /home/nemiah/archive.php