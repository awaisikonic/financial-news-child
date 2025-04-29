<?php
/**
 * The template for displaying all single posts
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header();
$container = get_theme_mod( 'understrap_container_type' );
?>
<section class="articles-section">
	<?php
	while ( have_posts() ) {
		the_post();
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
				if ($parent_category) : ?>
					<div class="article-heading-left">
						<h6>
							<a href="<?php echo get_category_link($parent_category->term_id); ?>"><?php echo esc_html($parent_category->name); ?></a>
							<?php if ($child_category) : ?>
								<br> <span><a href="<?php echo get_category_link($child_category->term_id); ?>"><?php echo esc_html($child_category->name); ?></a></span>
							<?php endif; ?>
						</h6>
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
				<?php endif; ?>
				<div class="article-heading-right">
					<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
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
				<div></div>
				<div class="artical-main-content">
					<?php
					$post_temp_content = get_field('post_temp_content', get_the_ID());
					if( '' !== get_post()->post_content ) {
						the_content();
					}
					else{
						echo $post_temp_content;
					}
					$source = get_field('source', get_the_ID());
					$source_link = get_field('source_link', get_the_ID());

					if ($source){
					?>
						<footer class="article-credit">
							<p>
							<strong>Source:</strong>
							<a href="<?php echo $source_link;?>" target="_blank" rel="noopener noreferrer">
								Original Article on <?php echo $source;?>
							</a>
							</p>
						</footer>
					<?php 
					}
					?>
				</div>
			</div>
		</div>

		<?php
		//understrap_post_nav();
	}
	?>
</section>
<section class="related-articles">
	<h2>More From InsightNews</h2>
	<?php get_template_part( 'template-parts/related' ); ?>

	<div class="top-reads">
		<h2>Popular Reads</h2>
		<?php get_template_part( 'template-parts/popular' ); ?>
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
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
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
	margin: 20px 0;
  }
</style>
<?php
get_footer();
