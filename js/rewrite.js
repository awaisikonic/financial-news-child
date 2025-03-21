jQuery(document).ready(function ($) {
    $(document).on('click', '#gpt-rewrite-editor-btn', function () {
        let customFieldContent = $('#acf-field_67dc3d63e652c').val(); // Change to your custom field ID
        let postId = $('#post_ID').val();

        if (!customFieldContent) {
            alert("Please enter content in the custom field first.");
            return;
        }

        $(this).text('Rewriting...').prop('disabled', true);

        $.post(gpt_ajax.ajax_url, {
            action: 'gpt_rewrite_custom_field',
            post_id: postId,
            custom_field_content: customFieldContent
        })
        .done(function (response) {
            if (response.success) {
                // Update WordPress post editor content
                if (typeof tinyMCE !== "undefined" && tinyMCE.activeEditor) {
                    tinyMCE.activeEditor.setContent(response.data.rewritten_content);
                }
                $('#content').val(response.data.rewritten_content); // Backup for non-TinyMCE mode
            } else {
                alert("Error: " + response.data.message);
            }
        })
        .always(function () {
            $('#gpt-rewrite-editor-btn').text('Rewrite & Paste in Editor').prop('disabled', false);
        });
    });

    // Add button below the custom field
    if ($('#acf-field_67dc3d63e652c').length > 0) {
        $('#acf-field_67dc3d63e652c').after('<button type="button" id="gpt-rewrite-editor-btn" class="button">Rewrite & Paste in Editor</button>');
    }
});