jQuery(document).ready(function ($) {
    $(document).on('click', '#gpt-rewrite-editor-btn', function () {
        let customFieldContent = $('#acf-field_67dc3d63e652c').val();
        let postId = $('#post_ID').val();
    
        if (!customFieldContent) {
            alert("Please enter content in the custom field first.");
            return;
        }
    
        let $btn = $(this);
        $btn.text('Rewriting...').prop('disabled', true);
    
        $.post(gpt_ajax.ajax_url, {
            action: 'gpt_rewrite_custom_field',
            post_id: postId,
            custom_field_content: customFieldContent
        })
        .done(function (response) {
            if (response.success) {
                const rewritten = response.data.rewritten_content;
                const factCheck = response.data.fact_check_result || '';
    
                // Check for fact-check result
                const lowerCaseFact = factCheck.toLowerCase();
                const isAccurate = lowerCaseFact.includes('accurate') || lowerCaseFact.includes('correct') || lowerCaseFact.includes('factual');

                // Check for autosave and reload the editor content
                if (typeof tinyMCE !== "undefined" && tinyMCE.activeEditor) {
                    tinyMCE.activeEditor.execCommand('mceRemoveEditor', false, 'content');
                    tinyMCE.execCommand('mceAddEditor', false, 'content');
                    setTimeout(() => {
                        tinyMCE.activeEditor.setContent(rewritten);
                    }, 300); // slight delay to ensure editor is reinitialized
                }

                $('#content').val(rewritten);
    
                // Alert if fact check flags any issue
                if (!isAccurate) {
                    alert("⚠️ Fact-check warning:\n\n" + factCheck);
                }
            } else {
                alert("Error: " + response.data.message);
            }
        })
        .fail(function () {
            alert("AJAX request failed. Please try again.");
        })
        .always(function () {
            $btn.text('Rewrite & Paste in Editor').prop('disabled', false);
        });
    });

    // Add button below the custom field
    if ($('#acf-field_67dc3d63e652c').length > 0) {
        $('#acf-field_67dc3d63e652c').after('<button type="button" id="gpt-rewrite-editor-btn" class="button">Rewrite & Paste in Editor</button>');
    }
});