$(() => {

    $('form').prop('noValidate', true);
    // Autofocus
    $('form :input:not(button):first').focus();
    $('.err:first').prev().focus();
    $('.err:first').prev().find(':input:first').focus();
    
    // Confirmation message
    $('[data-confirm]').on('click', e => {
        const text = e.target.dataset.confirm || 'Are you sure?';
        if (!confirm(text)) {
            e.preventDefault();
            e.stopImmediatePropagation();
        }
    });

    // Initiate GET request
    $('[data-get]').on('click', e => {
        e.preventDefault();
        const url = e.target.dataset.get;
        location = url || location;
    });

    // Initiate POST request
    $('[data-post]').on('click', e => {
        e.preventDefault();
        const url = e.target.dataset.post;
        const f = $('<form>').appendTo(document.body)[0];
        f.method = 'POST';
        f.action = url || location;
        f.submit();
    });

    // Reset form
    $('[type=reset]').on('click', e => {
        e.preventDefault();
        location = location;
    });

    // Auto uppercase
    $('[data-upper]').on('input', e => {
        const a = e.target.selectionStart;
        const b = e.target.selectionEnd;
        e.target.value = e.target.value.toUpperCase();
        e.target.setSelectionRange(a, b);
    });

    // Photo preview
    $(document).ready(function () { // wait for the document to be ready
        $('#photo').on('change', function (e) { 
            const file = e.target.files[0]; 
            const previewImg = document.getElementById('preview'); // get the #preview element

            if (!file || !previewImg) return;

            if (file.type.startsWith('image/')) { // check if the file is an image
                const reader = new FileReader(); // create a new FileReader object
                reader.onload = function (e) { 
                    previewImg.src = e.target.result;  // set the src attribute of the #preview element
                };
                reader.readAsDataURL(file); // read the file as a data URL
            } else {
                alert("Please upload a valid image file.");
                $('#photo').val('');
            }
        });
    });

    // Image preview
    document.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById('image');
        const preview = document.getElementById('preview');
    
        input.addEventListener('change', function (event) {
            const file = event.target.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    });

});