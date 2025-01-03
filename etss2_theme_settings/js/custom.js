// // custom.js
// (function ($) {
//     $(document).ready(function () {
//         // Listen for changes in the file input field
//         $('input[type="file"]').on('change', function () {
//             var fileInput = this;
//             if (fileInput.files.length > 0) {
//                 var file = fileInput.files[0];
//                 var newFileName = file.name.replace(/\.[^/.]+$/, '') + '_abc' + file.name.match(/\.[^/.]+$/)[0];

//                 // Update the label with the new file name (this is optional)
//                 var previewLabel = $('#file-name-preview');
//                 if (previewLabel.length) {
//                     previewLabel.text('New file name: ' + newFileName);
//                 }

//                 // The actual renaming happens on the server side when the file is uploaded
//                 // You cannot modify the file object in JavaScript before the upload
//             }
//         });
//     });
// })(jQuery);
