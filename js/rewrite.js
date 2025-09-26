jQuery(document).ready(function ($) {
  let hasGeneratedContent = false;

  // Check if content field already has content on page load
  function checkContentStatus() {
    const content = $("#content").val();
    hasGeneratedContent = content && content.trim().length > 0;
    toggleSecondaryButtons(hasGeneratedContent);
  }

  // Toggle secondary buttons based on content availability
  function toggleSecondaryButtons(show) {
    const secondaryButtons = [
      "#gpt-article-summary-btn",
      "#gpt-why-it-matters-btn",
      "#gpt-sentiment-analysis-btn",
      "#gpt-news-coverage-btn",
    ];

    secondaryButtons.forEach((selector) => {
      const $btn = $(selector);
      if ($btn.length) {
        $btn.closest(".button-container").toggle(show);
        $btn.prop("disabled", !show);
      }
    });
  }

  // Generic function to handle GPT API calls
  function makeGptCall(action, content, successCallback) {
    let $btn = $(`#gpt-${action.replace(/_/g, "-")}-btn`);
    const originalText = $btn.text();

    $btn.text("Generating...").prop("disabled", true);

    $.post(gpt_ajax.ajax_url, {
      action: action,
      content: content,
      post_id: $("#post_ID").val(),
    })
      .done(function (response) {
        if (response.success) {
          successCallback(response.data);
        } else {
          alert("Error: " + response.data.message);
        }
      })
      .fail(function () {
        alert("AJAX request failed. Please try again.");
      })
      .always(function () {
        $btn.text(originalText).prop("disabled", false);
      });
  }

  // Main rewrite function
  $(document).on("click", "#gpt-rewrite-editor-btn", function () {
    let customFieldContent = $("#acf-field_67dc3d63e652c").val();
    let postId = $("#post_ID").val();

    if (!customFieldContent) {
      alert("Please enter content in the custom field first.");
      return;
    }

    let $btn = $(this);
    $btn.text("Rewriting...").prop("disabled", true);

    $.post(gpt_ajax.ajax_url, {
      action: "gpt_rewrite_custom_field",
      post_id: postId,
      custom_field_content: customFieldContent,
    })
      .done(function (response) {
        if (response.success) {
          const rewritten = response.data.rewritten_content;

          // Update the editor content
          if (typeof tinyMCE !== "undefined" && tinyMCE.activeEditor) {
            tinyMCE.activeEditor.execCommand(
              "mceRemoveEditor",
              false,
              "content"
            );
            tinyMCE.execCommand("mceAddEditor", false, "content");
            setTimeout(() => {
              tinyMCE.activeEditor.setContent(rewritten);
            }, 300);
          }

          $("#content").val(rewritten);
          hasGeneratedContent = true;
          toggleSecondaryButtons(true);

          alert(
            "Content rewritten successfully! You can now generate additional content using the buttons below."
          );
        } else {
          alert("Error: " + response.data.message);
        }
      })
      .fail(function () {
        alert("AJAX request failed. Please try again.");
      })
      .always(function () {
        $btn.text("Rewrite & Paste in Editor").prop("disabled", false);
      });
  });

  // Article Summary
  $(document).on("click", "#gpt-article-summary-btn", function () {
    const content = $("#content").val();
    if (!content) {
      alert("Please generate or enter content first.");
      return;
    }

    makeGptCall("gpt_generate_article_summary", content, function (data) {
      $("#acf-field_68d550a60c415").val(data.summary);
    });
  });

  // Why It Matters
  $(document).on("click", "#gpt-why-it-matters-btn", function () {
    const content = $("#content").val();
    if (!content) {
      alert("Please generate or enter content first.");
      return;
    }

    makeGptCall("gpt_generate_why_it_matters", content, function (data) {
      $("#acf-field_68d57d6b9d0b8").val(data.why_it_matters);
    });
  });

  // Sentiment Analysis
  $(document).on("click", "#gpt-sentiment-analysis-btn", function () {
    const content = $("#content").val();
    if (!content) {
      alert("Please generate or enter content first.");
      return;
    }

    makeGptCall("gpt_generate_sentiment_analysis", content, function (data) {
      $("#acf-field_68d6c23f9da92").val(data.sentiment_analysis);
    });
  });

  // Create buttons with containers for better control
  function createButtonContainers() {
    // Main rewrite button
    if ($("#acf-field_67dc3d63e652c").length > 0) {
      $("#acf-field_67dc3d63e652c").after(
        '<div class="button-container"><button type="button" id="gpt-rewrite-editor-btn" class="button">Rewrite & Paste in Editor</button></div>'
      );
    }

    // Secondary buttons (initially hidden)
    if ($("#acf-field_68d550a60c415").length > 0) {
      $("#acf-field_68d550a60c415").after(
        '<div class="button-container" style="display: none;"><button type="button" id="gpt-article-summary-btn" class="button">Generate AI-powered Article summary</button></div>'
      );
    }

    if ($("#acf-field_68d57d6b9d0b8").length > 0) {
      $("#acf-field_68d57d6b9d0b8").after(
        '<div class="button-container" style="display: none;"><button type="button" id="gpt-why-it-matters-btn" class="button">Generate Why It Matters Context</button></div>'
      );
    }

    if ($("#acf-field_68d6c23f9da92").length > 0) {
      $("#acf-field_68d6c23f9da92").after(
        '<div class="button-container" style="display: none;"><button type="button" id="gpt-sentiment-analysis-btn" class="button">Generate Sentiment Analysis Indicator</button></div>'
      );
    }
  }

  // Initialize
  createButtonContainers();
  checkContentStatus();

  // Also check when content changes
  $("#content").on("input", function () {
    checkContentStatus();
  });
});
