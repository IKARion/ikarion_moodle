#!/bin/sh
# triggered by cron, runs acifread.php depending on controlfile

#cron user: moodle -> /usr/local/www/moodle/AC/$0

mail="connect@oncampus.de"

hier=$(dirname $0)

ct_base=$hier"/crontrigger_"

if [ -f $ct_base"on" ]
then
	$hier/acifread.php
	retv=$?
	# error encountered
	if [ $retv -eq 99 ]
	then
		rm $ct_base"on"
		touch $ct_base"error"
		echo "Subject: ERROR - beim Ausfuehren der Portal-zu-AdobeConnect-Datenversorgung; Prozess gestoppt!">/tmp/_em
        echo >>/tmp/_em
        echo "Bitte nach Fehlerbeseitigung die Datei ...moodle/AC/crontrigger_on benennen;">>/tmp/_em
        echo "folgend die letzten Zeilen aus dem Log (/var/log/moodle/ac*):">>/tmp/_em
        tail -12 /var/log/moodle/ac_error.log >>/tmp/_em 2>/dev/null
        cat /tmp/_em|/usr/sbin/sendmail -f$mail $mail
	else
		# warnings make it wait
		exit
	fi
fi

# there should be a file anyway
if [ ! -f $ct_base"off" ]&&[ ! -f $ct_base"error" ]
then
	rm -f ${ct_base}*
	touch $ct_base"off"
	exit
fi
