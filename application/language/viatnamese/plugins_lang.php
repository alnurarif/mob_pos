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
    "removeLabel"=> 'Remove',
    "removeTitle"=> 'Clear selected files',
    "cancelLabel"=> 'Cancel',
    "cancelTitle"=> 'Abort ongoing upload',
    "uploadLabel"=> 'Upload',
    "uploadTitle"=> 'Upload selected files',
    "msgNo"=> 'No',
    "msgNoFilesSelected"=> 'No files selected',
    "msgCancelled"=> 'Cancelled',
    "msgPlaceholder"=> 'Select {files}...',
    "msgZoomModalHeading"=> 'Detailed Preview',
    "msgFileRequired"=> 'You must select a file to upload.',
    "msgSizeTooSmall"=> 'File "{name}" (<b>{size} KB</b>) is too small and must be larger than <b>{minSize} KB</b>.',
    "msgSizeTooLarge"=> 'File "{name}" (<b>{size} KB</b>) exceeds maximum allowed upload size of <b>{maxSize} KB</b>.',
    "msgFilesTooLess"=> 'You must select at least <b>{n}</b> {files} to upload.',
    "msgFilesTooMany"=> 'Number of files selected for upload <b>({n})</b> exceeds maximum allowed limit of <b>{m}</b>.',
    "msgFileNotFound"=> 'File "{name}" not found!',
    "msgFileSecured"=> 'Security restrictions prevent reading the file "{name}".',
    "msgFileNotReadable"=> 'File "{name}" is not readable.',
    "msgFilePreviewAborted"=> 'File preview aborted for "{name}".',
    "msgFilePreviewError"=> 'An error occurred while reading the file "{name}".',
    "msgInvalidFileName"=> 'Invalid or unsupported characters in file name "{name}".',
    "msgInvalidFileType"=> 'Invalid type for file "{name}". Only "{types}" files are supported.',
    "msgInvalidFileExtension"=> 'Invalid extension for file "{name}". Only "{extensions}" files are supported.',
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
    "msgUploadAborted"=> 'The file upload was aborted',
    "msgUploadThreshold"=> 'Processing...',
    "msgUploadBegin"=> 'Initializing...',
    "msgUploadEnd"=> 'Done',
    "msgUploadEmpty"=> 'No valid data available for upload.',
    "msgUploadError"=> 'Error',
    "msgValidationError"=> 'Validation Error',
    "msgLoading"=> 'Loading file {index} of {files} &hellip;',
    "msgProgress"=> 'Loading file {index} of {files} - {name} - {percent}% completed.',
    "msgSelected"=> '{n} {files} selected',
    "msgFoldersNotAllowed"=> 'Drag & drop files only! Skipped {n} dropped folder(s).',
    "msgImageWidthSmall"=> 'Width of image file "{name}" must be at least {size} px.',
    "msgImageHeightSmall"=> 'Height of image file "{name}" must be at least {size} px.',
    "msgImageWidthLarge"=> 'Width of image file "{name}" cannot exceed {size} px.',
    "msgImageHeightLarge"=> 'Height of image file "{name}" cannot exceed {size} px.',
    "msgImageResizeError"=> 'Could not get the image dimensions to resize.',
    "msgImageResizeException"=> 'Error while resizing the image.<pre>{errors}</pre>',
    "msgAjaxError"=> 'Something went wrong with the {operation} operation. Please try again later!',
    "msgAjaxProgressError"=> '{operation} failed',
    "ajaxOperations"=> array(
        "deleteThumb"=> 'file delete',
        "uploadThumb"=> 'file upload',
        "uploadBatch"=> 'batch file upload',
        "uploadExtra"=> 'form data upload'
    ),
    "dropZoneTitle"=> 'Drag & drop files here &hellip;',
    "dropZoneClickTitle"=> '<br>(or click to select {files})',
    "fileActionSettings"=> array(
        "removeTitle"=> 'Remove file',
        "uploadTitle"=> 'Upload file',
        "uploadRetryTitle"=> 'Retry upload',
        "downloadTitle"=> 'Download file',
        "zoomTitle"=> 'View details',
        "dragTitle"=> 'Move / Rearrange',
        "indicatorNewTitle"=> 'Not uploaded yet',
        "indicatorSuccessTitle"=> 'Uploaded',
        "indicatorErrorTitle"=> 'Upload Error',
        "indicatorLoadingTitle"=> 'Uploading ...'
    ),
    "previewZoomButtonTitles"=> array(
        "prev"=> 'View previous file',
        "next"=> 'View next file',
        "toggleheader"=> 'Toggle header',
        "fullscreen"=> 'Toggle full screen',
        "borderless"=> 'Toggle borderless mode',
        "close"=> 'Close detailed preview'
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

