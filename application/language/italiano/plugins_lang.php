<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Main Lang (Repairer) - English
* Author: Usman Sher
*         uskhan099@s@gmail.com
*         @usmansher
* Location: http://otsglobal.org/repair/
* Created:  03.14.2010
* Description:  English language file for Repair Management System
*/

$lang['upload_manager'] = array(
    "fileSingle"=> 'file',
    "filePlural"=> 'files',
    "browseLabel"=> 'Browse &hellip;',
    "removeLabel"=> 'Rimuovi',
    "removeTitle"=> 'Rimuovi i file selezionati',
    "cancelLabel"=> 'Cancella',
    "cancelTitle"=> 'Annulla caricamento in corso',
    "uploadLabel"=> 'Carica',
    "uploadTitle"=> 'Carica i file selezionati',
    "msgNo"=> 'No',
    "msgNoFilesSelected"=> 'Nessun file selezionato',
    "msgCancelled"=> 'Cancellati',
    "msgPlaceholder"=> 'Seleziona {file}...',
    "msgZoomModalHeading"=> 'Anteprima dettagliata',
    "msgFileRequired"=> 'Seleziona un file da caricare.',
    "msgSizeTooSmall"=> 'Il File "{name}" (<b>{size} KB</b>) e troppo piccolo deve essere piu grande di <b>{minSize} KB</b>.',
    "msgSizeTooLarge"=> 'Il File "{name}" (<b>{size} KB</b>) supera la grandezza massima di caricamento di <b>{maxSize} KB</b>.',
    "msgFilesTooLess"=> 'Devi selezionare almeno <b>{n}</b> {files} da caricare.',
    "msgFilesTooMany"=> 'Numuro di file selezionati da caricare <b>({n})</b> supera il limite massimo di <b>{m}</b>.',
    "msgFileNotFound"=> 'File "{name}" non trovato!',
    "msgFileSecured"=> 'Le restrizioni sulla sicurezza impediscono la lettura del file"{name}".',
    "msgFileNotReadable"=> 'File "{name}" non leggibile.',
    "msgFilePreviewAborted"=> 'Anteprima del file fallita"{name}".',
    "msgFilePreviewError"=> 'Errore durante la lettura del file "{name}".',
    "msgInvalidFileName"=> 'Caratteri invalidi o non supportati nel nome del file "{name}".',
    "msgInvalidFileType"=> 'Tipo invalido di file "{name}". Solo "{types}" file sono supportati.',
    "msgInvalidFileExtension"=> 'Estensione non valida per il file "{name}". Solo "{extensions}" sono supportate.',
     "msgFileTypes"=> array(
        'image'=> 'image',
        'html'=> 'HTML',
        'text'=> 'text',
        'video'=> 'video',
        'audio'=> 'audio',
        'flash'=> 'flash',
        'pdf'=> 'PDF',
        'object'=> 'object'
    ),
    "msgUploadAborted"=> 'Caricamento file interrotto',
    "msgUploadThreshold"=> 'in lavorazione...',
    "msgUploadBegin"=> 'Inizializzazione...',
    "msgUploadEnd"=> 'Fatto',
    "msgUploadEmpty"=> 'Nessun dato disponibile per il caricamento.',
    "msgUploadError"=> 'Errore',
    "msgValidationError"=> 'Errore di convalida',
    "msgLoading"=> 'Loading file {index} of {files} &hellip;',
    "msgProgress"=> 'Loading file {index} of {files} - {name} - {percent}% completato.',
    "msgSelected"=> '{n} {files} selezionati',
    "msgFoldersNotAllowed"=> 'Trascina e rilascia solo i file!Skipped {n} dropped folder(s).',
    "msgImageWidthSmall"=> 'Larghezza del file immagine "{name}" deve essere almeno {size} px.',
    "msgImageHeightSmall"=> 'Altezza dell\'immagine "{name}" deve essere almeno {size} px.',
    "msgImageWidthLarge"=> 'Larghezza del file immagine "{name}" non puo superare {size} px.',
    "msgImageHeightLarge"=> 'Altezza del file immagine "{name}" non puo superare {size} px.',
    "msgImageResizeError"=> 'Impossibile ottenere il ridimensionamento delle dimensioni dell\'immagine.',
    "msgImageResizeException"=> 'Error durante il ridimensionamento dell\'immagine.<pre>{errors}</pre>',
    "msgAjaxError"=> 'Qualcosa e andato storto con {operation} operazione. Per favore riprova piu tardi!',
    "msgAjaxProgressError"=> '{operation} fallito',
    "ajaxOperations"=> array(
        "deleteThumb"=> 'file cancellato',
        "uploadThumb"=> 'file caricato',
        "uploadBatch"=> 'caricamento di file batch',
        "uploadExtra"=> 'dati del modulo carica'
    ),
    "dropZoneTitle"=> 'Trascina e rilascia i file qui &hellip;',
    "dropZoneClickTitle"=> '<br>(o clicca per selezionare {files})',
    "fileActionSettings"=> array(
        "removeTitle"=> 'Rimuovi file',
        "uploadTitle"=> 'Carica file',
        "uploadRetryTitle"=> 'Riprova caricamento',
        "downloadTitle"=> 'Download file',
        "zoomTitle"=> 'Vedi Dettagli',
        "dragTitle"=> 'Muovi / Rearrange',
        "indicatorNewTitle"=> 'Non ancora caricato',
        "indicatorSuccessTitle"=> 'Caricato',
        "indicatorErrorTitle"=> 'Errore caricamento',
        "indicatorLoadingTitle"=> 'Caricamento ...'
    ),
    "previewZoomButtonTitles"=> array(
        "prev"=> 'Vedi file precedente',
        "next"=> 'Vedi prossimo file',
        "toggleheader"=> 'Attiva disattiva intestazione',
        "fullscreen"=> 'Attiva disattiva schermo intero',
        "borderless"=> 'Attiva disattiva modalità senza bordi',
        "close"=> 'Chiudi anteprima'
    ),
);



$lang['datatables_lang']        = array(
    'sEmptyTable'                   => "No data available in table",
    'sInfo'                         => "Showing _START_ to _END_ of _TOTAL_ entries",
    'sInfoEmpty'                    => "Showing 0 to 0 of 0 entries",
    'sInfoFiltered'                 => "(filtered from _MAX_ total entries)",
    'sInfoPostFix'                  => "",
    'sInfoThousands'                => ",",
    'sLengthMenu'                   => "Show _MENU_ ",
    'sLoadingRecords'               => "Loading...",
    'sProcessing'                   => "Processing...",
    'sSearch'                       => "Search",
    'sZeroRecords'                  => "No matching records found",
    'oAria'                         => array(
        'sSortAscending'                => ": activate to sort column ascending",
        'sSortDescending'               => ": activate to sort column descending"
    ),
    'oPaginate'                     => array(
        'sFirst'                        => "<< First",
        'sLast'                         => "Last >>",
        'sNext'                         => "Next >",
        'sPrevious'                     => "< Previous",
    )
);

