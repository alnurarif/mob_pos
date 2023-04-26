<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


$lang['upload_manager'] = array(
    "fileSingle"=> 'Datei',
    "filePlural"=> 'Dateien',
    "browseLabel"=> 'Suchen',
    "removeLabel"=> 'Entfernen',
    "removeTitle"=> 'Ausgewählte Dateien löschen',
    "cancelLabel"=> 'Abbrechen',
    "cancelTitle"=> 'Laufenden Upload abbrechen',
    "uploadLabel"=> 'Hochladen',
    "uploadTitle"=> 'Ausgewählte Dateien hochladen',
    "msgNo"=> 'Nein',
    "msgNoFilesSelected"=> 'Keine Dateien ausgewählt',
    "msgCancelled"=> 'Storniert',
    "msgPlaceholder"=> 'Wählen Sie {files}...',
    "msgZoomModalHeading"=> 'Detaillierte Vorschau',
    "msgFileRequired"=> 'Sie müssen eine Datei zum Hochladen auswählen.',
    "msgSizeTooSmall"=> 'Datei "{name}" (<b>{size} KB</b>) ist zu klein und muss grösser als <b>{minSize} KB groß sein</b>.',
    "msgSizeTooLarge"=> 'Datei "{name}" (<b>{size} KB</b>) berschreitet die maximal zulässige Upload-Größe von <b>{maxSize} KB</b>.',
    "msgFilesTooLess"=> 'Sie müssen mindestenst <b>{n}</b> {files} zum Hochladen auswählen.',
    "msgFilesTooMany"=> 'Anzahl der zum Hochladen ausgewählten Dateien <b>({n})</b> überschreitet die maximal zulässige Grenze von <b>{m}</b>.',
    "msgFileNotFound"=> 'Datei "{name}" nicht gefunden!',
    "msgFileSecured"=> 'Sicherheitseinschränkungen verhindern das Lesen der Datei "{name}".',
    "msgFileNotReadable"=> 'Datei "{name}" ist nicht lesbar!',
    "msgFilePreviewAborted"=> 'Dateivorschau für "{name}" abgebrochen.',
    "msgFilePreviewError"=> 'Beim Lesen der Datei "{name}" ist ein Fehler aufgetreten.',
    "msgInvalidFileName"=> 'Ungültige oder nicht unterstützte Zeichen im Dateinamen "{name}".',
    "msgInvalidFileType"=> 'Invalid type for file "{name}". Only "{types}" files are supported.',
    "msgInvalidFileExtension"=> 'Ungültiger Typ für Datei "{name}". Nur "{extensions}" -Dateien werden unterstützt.',
    "msgFileTypes"=> array(
        'image'=> 'Bild',
        'html'=> 'HTML',
        'text'=> 'Text',
        'video'=> 'Video',
        'audio'=> 'Audio',
        'flash'=> 'Blitzlicht',
        'pdf'=> 'PDF',
        'object'=> 'Object'
    ),
    "msgUploadAborted"=> 'Der Datei-Upload wurde abgebrochen',
    "msgUploadThreshold"=> 'Verarbeitung...',
    "msgUploadBegin"=> 'Initialisierung...',
    "msgUploadEnd"=> 'Erledigt',
    "msgUploadEmpty"=> 'Keine gültigen Daten zum Hochladen verfügbar.',
    "msgUploadError"=> 'Fehler',
    "msgValidationError"=> 'Validierungsfehler',
    "msgLoading"=> 'Datei {index} von {files} &hellip laden;',
    "msgProgress"=> 'Datei laden {index} von {files} - {name} - {percent}% abgeschlossen.',
    "msgSelected"=> '{n} {files} ausgewählt',
    "msgFoldersNotAllowed"=> 'Nur Dateien per Drag & Drop ziehen! Überspringt {n} abgelegte(n) Ordner.',
    "msgImageWidthSmall"=> 'Breite der Bilddatei "{name}" muss mindestens {size} px. betragen',
    "msgImageHeightSmall"=> 'Höhe der Bilddatei "{name}" muss mindestens {size} px. betragen',
    "msgImageWidthLarge"=> 'Breite der Bilddatei "{name}" darf {size} px. nicht überschreiten',
    "msgImageHeightLarge"=> 'Höhe der Bilddatei "{name}" kann nicht größer als {size} px. sein',
    "msgImageResizeError"=> 'Could not get the image dimensions to resize.',
    "msgImageResizeException"=> 'Fehler bei der Größenänderung des Bildes.<pre>{errors}</pre>',
    "msgAjaxError"=> 'Etwas ist bei der Operation {operation} schief gelaufen. Bitte versuchen Sie es später noch einmal!',
    "msgAjaxProgressError"=> '{operation} ist fehlgeschlagen',
    "ajaxOperations"=> array(
        "deleteThumb"=> 'Datei löschen',
        "uploadThumb"=> 'Datei-Upload',
        "uploadBatch"=> 'Batch-Datei hochladen',
        "uploadExtra"=> 'Formulardaten hochladen'
    ),
    "dropZoneTitle"=> 'Dateien per Drag & Drop hierher ziehen &hellip;',
    "dropZoneClickTitle"=> '<br>(oder klicken Sie zur Auswahl von {files})',
    "fileActionSettings"=> array(
        "removeTitle"=> 'Datei entfernen',
        "uploadTitle"=> 'Datei hochladen',
        "uploadRetryTitle"=> 'Upload erneut versuchen',
        "downloadTitle"=> 'Datei herunterladen',
        "zoomTitle"=> 'Details anzeigen',
        "dragTitle"=> 'Verschieben / Umordnen',
        "indicatorNewTitle"=> 'Noch nicht hochgeladen',
        "indicatorSuccessTitle"=> 'Hochgeladen',
        "indicatorErrorTitle"=> 'Upload-Fehler',
        "indicatorLoadingTitle"=> 'Hochladen ...'
    ),
    "previewZoomButtonTitles"=> array(
        "prev"=> 'Vorherige Datei anzeigen',
        "next"=> 'Nächste Datei anzeigen',
        "toggleheader"=> 'Kopfzeile umschalten',
        "fullscreen"=> 'Vollbild umschalten',
        "borderless"=> 'Randloser Modus umschalten',
        "close"=> 'Detaillierte Vorschau schließen'
    ),
);



$lang['datatables_lang']        = array(
    'sEmptyTable'                   => "Keine Daten in der Tabelle verfügbar",
    'sInfo'                         => "Anzeigen von _START_ bis _END_ der _TOTAL_ Einträge",
    'sInfoEmpty'                    => "0 bis 0 von 0 Einträgen anzeigen",
    'sInfoFiltered'                 => "(gefiltert aus _MAX_ Gesamteinträgen)",
    'sInfoPostFix'                  => "",
    'sInfoThousands'                => ",",
    'sLengthMenu'                   => "_MENU_ anzeigen",
    'sLoadingRecords'               => "Laden...",
    'sProcessing'                   => "Verarbeitung...",
    'sSearch'                       => "Suchen",
    'sZeroRecords'                  => "Nichts gefunden",
    'oAria'                         => array(
        'sSortAscending'                => ": Aufsteigend sortieren",
        'sSortDescending'               => ": Absteigend sortieren"
    ),
    'oPaginate'                     => array(
        'sFirst'                        => "<< Anfang",
        'sLast'                         => "Ende >>",
        'sNext'                         => "Nächste >",
        'sPrevious'                     => "< Vorherige",
    )
);

