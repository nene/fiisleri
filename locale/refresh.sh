find .. -iname '*.php' | grep -v 'PEAR' | xargs xgettext -o messages.pot
msgmerge --update --backup=off et_EE/LC_MESSAGES/kambja.po messages.pot
msgmerge --update --backup=off ru_RU/LC_MESSAGES/kambja.po messages.pot
