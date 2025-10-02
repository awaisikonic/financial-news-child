<?php

/**
 * The template for displaying all single posts
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;

get_header();
$container = get_theme_mod('understrap_container_type');

?>
<section class="articles-section">
  <?php
  while (have_posts()) {
    the_post();

    $post_id = get_the_ID();
    $user_id = get_current_user_id();
    $saved_articles = get_user_meta($user_id, 'saved_articles', true);
    $saved_articles = !empty($saved_articles) ? $saved_articles : array();

    $is_bookmarked = in_array($post_id, $saved_articles);
    $button_class = $is_bookmarked ? 'bookmark-btn bookmarked' : 'bookmark-btn';
    $button_text = $is_bookmarked ? 'Saved Article' : 'Bookmark This Article';
    $bookmark_icon = $is_bookmarked ? '<i class="fa-solid fa-bookmark"></i>' : '<i class="fa-regular fa-bookmark"></i>';

    $article_summary = get_field('article_summary');
    $why_it_matters = get_field('why_it_matters');
    $sentiment_analysis_indicator = get_field('sentiment_analysis_indicator');
    $tag_class = '';
    if ($sentiment_analysis_indicator === 'Positive') {
      $tag_class = 'tag-success';
    } elseif ($sentiment_analysis_indicator === 'Neutral') {
      $tag_class = 'tag-warning';
    } elseif ($sentiment_analysis_indicator === 'Negative') {
      $tag_class = 'tag-error';
    }
  ?>
    <div class="article-grid main-content-with-audio">
      <div class="article-grid-inner-grid article-heading">
        <?php
        $categories = get_the_category();
        $parent_category = '';
        $child_category = '';

        // Check if the post has categories
        if ($categories) {
          foreach ($categories as $category) {
            if ($category->parent == 0) {
              // Parent category
              $parent_category = $category;
            } else {
              // Child category (first found)
              $child_category = $category;
            }
          }
        }

        // Display parent and child category
        ?>
        <div class="article-heading-left">
          <?php if ($parent_category) : ?>
            <h6>
              <a href="<?php echo get_category_link($parent_category->term_id); ?>"><?php echo esc_html($parent_category->name); ?></a>
              <?php if ($child_category) : ?>
                > <span><a href="<?php echo get_category_link($child_category->term_id); ?>"><?php echo esc_html($child_category->name); ?></a></span>
              <?php endif; ?>
            </h6>
          <?php endif; ?>
          <?php if (!empty($article_summary)) { ?>
            <div class="tts-player-wrap">
              <h3>Article Summary</h3>
              <p><?php echo $article_summary; ?></p>
            </div>
          <?php } ?>
          <div class="tts-player-wrap">
            <h3>Want to listen to this article?</h3>
            <p>Click the play button below to have the article read aloud to you. You can pause, resume, or skip ahead anytime!</p>
            <div class="tts-player">
              <button id="tts-play-pause" title="Play"><span id="tts-icon">▶</span></button>
              <input type="range" id="tts-progress" value="0" disabled />
              <span id="tts-status">00%</span>
            </div>
          </div>
        </div>

        <div class="article-heading-right">
          <div class="bookmark-wrap">
            <?php if (!empty($sentiment_analysis_indicator)) { ?>
              <div class="sai-tag-wrap">
                <h3>Sentiment Analysis</h3>
                <span class="sai-tag <?php echo $tag_class; ?>"><?php echo $sentiment_analysis_indicator; ?></span>
              </div>
            <?php } ?>
            <?php if (is_user_logged_in()) { ?>
              <button class="<?php echo $button_class; ?>" data-post-id="<?php echo $post_id; ?>">
                <span class="bookmark-icon"><?php echo $bookmark_icon; ?></span>
                <span class="bookmark-text"><?php echo $button_text; ?></span>
              </button>
            <?php } ?>
          </div>
          <?php the_title('<h1 class="entry-title">', '</h1>'); ?>
          <?php if (has_post_thumbnail()) : ?>
            <div class="article-heading-img">
              <img src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'full'); ?>"
                alt="<?php the_title_attribute(); ?>">
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <div class="article-grid main-content-with-audio">
      <div class="article-grid-inner-grid main-content-with-audio">
        <div class="article-heading-left poll-section">
          <?php if (function_exists('vote_poll')): ?>
            <h3>Polls</h3>
            <?php get_poll(); ?>
          <?php endif; ?>
        </div>
        <div class="artical-main-content">
          <?php
          $post_temp_content = get_field('post_temp_content', get_the_ID());
          if ('' !== get_post()->post_content) {
            the_content();
          } else {
            echo $post_temp_content;
          }
          $source = get_field('source', get_the_ID());
          $source_link = get_field('source_link', get_the_ID());

          if ($source) {
          ?>
            <footer class="article-credit">
              <p>
                <strong>Source:</strong>
                <a href="<?php echo $source_link; ?>" target="_blank" rel="noopener noreferrer">
                  Original Article on <?php echo $source; ?>
                </a>
              </p>
            </footer>
          <?php
          }
          ?>
          <?php if (!empty($why_it_matters)) { ?>
            <h4>Why It matters</h4>
            <p><?php echo $why_it_matters; ?></p>
          <?php } ?>
        </div>
      </div>
    </div>

  <?php
    //understrap_post_nav();
  }
  ?>
</section>
<?php if (is_user_logged_in()) { ?>
  <section class="comments-section">
    <?php
    // Display comments template
    if (comments_open() || get_comments_number()) {
      comments_template();
    }
    ?>
  </section>
<?php } ?>
<section class="related-articles">

  <div class="top-reads covered-by">
    <h2>Also covered by</h2>
    <?php get_template_part('template-parts/covered-by'); ?>
  </div>

  <h2>More From InsightNews</h2>
  <?php get_template_part('template-parts/related'); ?>

  <div class="top-reads">
    <h2>Popular Reads</h2>
    <?php get_template_part('template-parts/popular'); ?>
  </div>
</section>
<script>
  const btn = document.getElementById('tts-play-pause');
  const icon = document.getElementById('tts-icon');
  const progressBar = document.getElementById('tts-progress');
  const status = document.getElementById('tts-status');
  const contentElement = document.querySelector('.artical-main-content');

  const synth = window.speechSynthesis;
  let words = [];
  let currentIndex = 0;
  let utterance;
  let isPlaying = false;
  let isSeeking = false;

  btn.onclick = () => {
    if (!isPlaying) {
      startSpeech(currentIndex);
    } else {
      pauseSpeech();
    }
  };

  function startSpeech(startFromIndex) {
    const text = contentElement.innerText.trim();
    if (!words.length) words = text.split(/\s+/);
    const remainingText = words.slice(startFromIndex).join(' ');

    if (synth.speaking) synth.cancel();
    utterance = new SpeechSynthesisUtterance(remainingText);

    utterance.onboundary = (event) => {
      if (event.name === 'word') {
        currentIndex++;
        updateProgress();
      }
    };

    utterance.onend = () => {
      isPlaying = false;
      icon.textContent = '▶';
      currentIndex = 0;
      updateProgress();
    };

    synth.speak(utterance);
    isPlaying = true;
    icon.textContent = '⏸';
    progressBar.disabled = false;
  }

  function pauseSpeech() {
    synth.cancel();
    isPlaying = false;
    icon.textContent = '▶';
  }

  function updateProgress() {
    const percent = Math.round((currentIndex / words.length) * 100);
    progressBar.value = percent;
    status.textContent = `${percent}%`;
  }

  // 🧠 Add Scrubbing Logic
  progressBar.addEventListener('input', (e) => {
    if (!words.length) return;
    isSeeking = true;
    const percent = parseInt(e.target.value, 10);
    const targetIndex = Math.floor((percent / 100) * words.length);
    status.textContent = `${percent}%`;
  });

  progressBar.addEventListener('change', (e) => {
    if (!words.length) return;
    const percent = parseInt(e.target.value, 10);
    const targetIndex = Math.floor((percent / 100) * words.length);
    currentIndex = targetIndex;
    isSeeking = false;
    if (isPlaying) {
      startSpeech(currentIndex);
    }
  });
</script>

<style>
  .tts-player {
    display: flex;
    align-items: center;
    background: #f3f3f3;
    border-radius: 8px;
    padding: 10px 15px;
    gap: 10px;
    max-width: 100%;
    margin: 20px 0;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
  }

  #tts-play-pause {
    background-color: #007BFF;
    color: white;
    border: none;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    font-size: 18px;
    cursor: pointer;
  }

  #tts-progress {
    flex-grow: 1;
  }

  #tts-status {
    font-size: 14px;
    color: #333;
    width: 40px;
    text-align: right;
  }

  .tts-player-wrap {
    margin: 50px 0 20px;
  }
</style>

<?php
get_footer();
