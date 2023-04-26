<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/** 
* Name: Main Lang (Repairer) - English 
* Author: Usman Sher 
* uskhan099@s@gmail.com 
* @usmansher 
* Location: http://otsglobal.org/repair/ 
* Created: 03.14.2010 
* Description: English language file for Repair Management System */

$lang['upload_manager'] = array(
    "fileSingle" => 'fájl',
    "filePlural" => 'fájlok',
    "browseLabel" => 'Keresés &hellip;',
    "removeLabel" => 'Visszavonás',
    "removeTitle" => 'A kiválasztott fájlok törlése',
    "cancelLabel" => 'Mégsem',
    "cancelTitle" => 'Feltöltés megszakítása',
    "uploadLabel" => 'Feltöltés',
    "uploadTitle" => 'Kiválasztott fájl feltöltése',
    "msgNo" => 'Nem',
    "msgNoFilesSelected" => 'Nincs kiválasztott fájl',
    "msgCancelled" => 'Megszakítva',
    "msgPlaceholder" => 'Kiválasztott {files}...',
    "msgZoomModalHeading" => 'Részlete előnézet',
    "msgFileRequired" => 'Egy fájlt ki kell választanod a feltöltéshez.',
    "msgSizeTooSmall" => 'Fájl "{name}" (<b>{size} KB</b>) túl kicsi, meg kell hogy haladja <b>{minSize} KB</b>.',
    "msgSizeTooLarge" => 'Fájl "{name}" (<b>{size} KB</b>) elérte a maximálisan feltölthető méretet <b>{maxSize} KB</b>.',
    "msgFilesTooLess" => 'Detailed Preview.',
    "msgFilesTooMany" => 'A feltöltésre kiválasztott fájlok száma <b>({n})</b> meghaladja a megengedett maximális korlátot <b>{m}</b>.',
    "msgFileNotFound" => 'A(z) "{name}" fájl nem található!',
    "msgFileSecured" => 'A biztonsági korlátozások megakadályozzák a(z) "{name}" fájl olvasását.',
    "msgFileNotReadable" => 'A(z) "{name}" fájl nem olvasható.',
    "msgFilePreviewAborted" => 'A(z) "{name}" fájl előnézete megszakítva.',
    "msgFilePreviewError" => 'Hiba történt a (z) "{name}" fájl olvasása közben.',
    "msgInvalidFileName" => 'Érvénytelen vagy nem támogatott karakterek a(z) "{name}" fájlnévben.',
    "msgInvalidFileType" => 'Érvénytelen a(z) "{name}" fájl típusa. Csak a(z) "{types}" fájlok támogatottak. ',
    "msgInvalidFileExtension" => 'Érvénytelen kiterjesztés a(z) "{name}" fájlra. Csak a(z) "{extensions} "fájlok támogatottak. ',
    "msgFileTypes" => array(
        'image' => 'image',
        'html' => 'HTML',
        'text' => 'text',
        'video' => 'video',
        'audio' => 'audio',
        'flash' => 'flash',
        'pdf' => 'PDF',
        'object' => 'object'
    ),
    "msgUploadAborted" => 'Fájlfeltöltés megszakítva',
    "msgUploadThreshold" => 'Folyamatban...',
    "msgUploadBegin" => 'Inicializálás...',
    "msgUploadEnd" => 'Kész',
    "msgUploadEmpty" => 'Nincs elérhető adat a feltöltéshez.',
    "msgUploadError" => 'Hiba',
    "msgValidationError" => 'Érvényesítési hiba',
    "msgLoading" => 'Fájl betöltése {index} a {files} &hellip;',
    "msgProgress" => 'Fájl betöltése {index} a {files} - {name} - {percent}% megtörtént.',
    "msgSelected" => '{n} {files} kiválasztva',
    "msgFoldersNotAllowed" => 'Csak drag & drop fájlok! Kihagyta a(z) {n} lemaradt mappát.',
    "msgImageWidthSmall" => 'A "{name}" képfájl szélességének legalább {size} px-nek kell lennie.',
    "msgImageHeightSmall" => 'A "{name}" képfájl magasságának legalább {size} px-nek kell lennie.',
    "msgImageWidthLarge" => 'A "{name}" képfájl szélessége nem haladhatja meg a (size) képpontot.',
    "msgImageHeightLarge" => 'A "{name}" képfájl magassága nem haladhatja meg a (size) képpontot.',
    "msgImageResizeError" => 'Nem sikerült átméretezni a kép méreteit.',
    "msgImageResizeException" => 'Hiba a kép átméretezésekor. <pre>{errors}</pre>',
    "msgAjaxError" => 'Valami hibás lett a {operation} művelettel. Kérlek, próbáld újra később!',
    "msgAjaxProgressError" => '{operation} sikertelen',
    "ajaxOperations" => array(
        "deleteThumb" => 'fájl törlés',
        "uploadThumb" => 'fájl feltöltés',
        "uploadBatch" => 'csoportos feltöltés',
        "uploadExtra" => 'adatból feltöltés'
    ),
    "dropZoneTitle" => 'Drag & drop fájlok ide &hellip;',
    "dropZoneClickTitle" => '<br>(vagy klikkelj a kiválsztott {files})',
    "fileActionSettings" => array(
        "removeTitle" => 'Fájl eltávolítása',
        "uploadTitle" => 'Fájl feltöltése',
        "uploadRetryTitle" => 'Újra feltölteni',
        "downloadTitle" => 'Fájl letöltése',
        "zoomTitle" => 'Részletek megtekintése',
        "dragTitle" => 'Áthelyezés / Átrendezés',
        "indicatorNewTitle" => 'Még nem töltötték fel',
        "IndicatorSuccessTitle" => 'Feltöltve',
        "indicatorErrorTitle" => 'Feltöltési hiba',
        "IndicatorLoadingTitle" => 'Feltöltés ...'
    ),
    "previewZoomButtonTitles" => array(
        "prev" => 'Előző fájl megtekintése',
        "next" => 'Következő fájl megtekintése',
        "toggleheader" => 'Fejléc váltása',
        "fullscreen" => 'Teljes képernyő átváltása',
        "borderless" => 'Váltás a szegély nélküli módra',
        "close" => 'Részletes előnézet bezárása'
    )
);

$lang['datatables_lang'] = array(
    'sEmptyTable' => "A táblázatban nem állnak rendelkezésre adatok",
    'sInfo' => "_TOTAL_ bejegyzés _START_ - _END_ megjelenítése",
    'sInfoEmpty' => "0-tól 0-ig 0 bejegyzés jelenik meg",
    'sInfoFiltered' => "(_MAX_ összes tételből szűrve)",
    'sInfoPostFix' => "",
    'sInfoThousands' => ",",
    'sLengthMenu' => "Mutatása _MENU_",
    'sLoadingRecords' => "Betöltés ...",
    'sProcessing' => "Feldolgozás ...",
    'sSearch' => "Keresés",
    'sZeroRecords' => "Nem található megfelelő rekord",
    'oAria' => array(
        'sSortAscending' => ": aktiválja az oszlop növekvő sorrendjét",
        'sSortDescending' => ": aktiválja az oszlop csökkenő sorrendjét"
    ),
    'oPaginate' => array(
        'sFirst' => "<< Első",
        'sLast' => "Utolsó >>",
        'sNext' => "Következő >",
        'sPrevious' => "< Előző"
    )
);