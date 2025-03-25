const MenuIcon = document.querySelector('.menu-icon');
const MobileNav = document.querySelector('.mobile-nav');
if (MenuIcon){
    MenuIcon.addEventListener("click", () => {
        MobileNav.classList.toggle("mobile-nav-active");
    });
}
const dropdown = document.querySelector('.nav-menu-item-dropdwon');
const dropdownMenu = document.querySelector('.desktop-dropdown');
dropdown.addEventListener('mouseover', () => {
    dropdownMenu.style.display = 'block';
})
dropdown.addEventListener('mouseout', () => {
    dropdownMenu.style.display = 'none';
})
dropdownMenu.addEventListener('mouseover', () => {
    dropdownMenu.style.display = 'block';
})
dropdownMenu.addEventListener('mouseout', () => {
    dropdownMenu.style.display = 'none';
})

document.addEventListener("DOMContentLoaded", function () {
    let marketWrapper = document.getElementById("marketDataWrapper");
    const prevBtn = document.getElementById("marketSlidePrev");
    const nextBtn = document.getElementById("marketSlideNext");
    const categoryFilter = document.getElementById("categoryFilter");
    const categoryFilterMobile = document.getElementById("categoryFilterMobile");

    async function updateMarketData(category) {
        try {
            const response = await fetch(`/wp-admin/admin-ajax.php?action=market_ticker&category=${encodeURIComponent(category)}`);

            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);

            const data = await response.json();
            if (data){
                if (data.success && data.data && data.data.html) {
                    if(marketWrapper){
                        marketWrapper.innerHTML = data.data.html;

                        // Reassign marketWrapper after update
                        marketWrapper = document.getElementById("marketDataWrapper");
                    }
                    // Reattach scroll events
                    attachScrollEvents();
                } else {
                    console.error("Unexpected response structure:", data);
                    marketWrapper.innerHTML = '<p class="error">No data available</p>';
                }
            }
        } catch (error) {
            console.error("AJAX request failed:", error);
            marketWrapper.innerHTML = '<p class="error">Failed to load market data</p>';
        }
    }

    function attachScrollEvents() {
        if (!marketWrapper) return;

        prevBtn?.removeEventListener("click", scrollLeft);
        nextBtn?.removeEventListener("click", scrollRight);

        prevBtn?.addEventListener("click", scrollLeft);
        nextBtn?.addEventListener("click", scrollRight);
    }

    function scrollLeft() {
        if (marketWrapper) {
            console.log("Scrolling left");
            marketWrapper.scrollBy({ left: -300, behavior: "smooth" });
        }
    }

    function scrollRight() {
        if (marketWrapper) {
            console.log("Scrolling right");
            marketWrapper.scrollBy({ left: 300, behavior: "smooth" });
        }
    }

    function handleCategoryChange(event) {
        updateMarketData(event.target.value);
    }

    categoryFilter?.addEventListener("change", handleCategoryChange);
    categoryFilterMobile?.addEventListener("change", handleCategoryChange);

    // Load initial data and attach scroll events
    updateMarketData(categoryFilter?.value || "securities");
    attachScrollEvents();
});

document.addEventListener("DOMContentLoaded", function () {
    const toggleDropdown = document.getElementById('toggleDropdown');
    const categoryItems = document.querySelectorAll(".future-section__latest-header-catg-item");
    const latestNewsContainer = document.getElementById("latest-news-container");
    if(toggleDropdown){
        toggleDropdown.addEventListener('click', () => {
            document.querySelector('.future-section__latest-header-catg-list').classList.toggle('active');
        });
    }

    if(categoryItems){
        categoryItems.forEach(item => {
            item.addEventListener("click", function () {
                const categoryId = this.getAttribute("data-cat");

                fetchLatestPosts(categoryId);
            });
        });
    }
    function fetchLatestPosts(categoryId) {
        const data = new FormData();
        data.append("action", "fetch_latest_posts");
        data.append("category_id", categoryId);

        fetch(ajaxurl, {
            method: "POST",
            body: data
        })
        .then(response => response.text())
        .then(html => {
            latestNewsContainer.innerHTML = html;
        })
        .catch(error => console.error("Error fetching posts:", error));
    }
});

document.addEventListener("DOMContentLoaded", function () {
    const toggleDropdown = document.getElementById('toggleDropdownblog');
    const categoryItems = document.querySelectorAll(".latest-post__featured-section .future-section__latest-header-catg-item");
    const latestNewsContainer = document.getElementById("more-industrie-main");
    const paginationContainer = document.querySelector(".pagination"); // Select pagination

    toggleDropdown.addEventListener('click', () => {
        document.querySelector('.latest-post__featured-section .future-section__latest-header-catg-list').classList.toggle('active');
    });

    categoryItems.forEach(item => {
        item.addEventListener("click", function () {
            const categoryId = this.getAttribute("data-cat");
            fetchLatestPosts(categoryId);
        });
    });

    function fetchLatestPosts(categoryId) {
        const data = new FormData();
        data.append("action", "fetch_latest_posts_blog");
        data.append("category_id", categoryId);

        fetch(ajaxurl, {
            method: "POST",
            body: data
        })
        .then(response => response.json())
        .then(response => {
            latestNewsContainer.innerHTML = response.html;

            // Hide pagination if posts are fewer than 10
            if (response.total_posts <= 10) {
                paginationContainer.style.display = "none";
            } else {
                paginationContainer.style.display = "flex";
            }
        })
        .catch(error => console.error("Error fetching posts:", error));
    }
});

document.addEventListener("DOMContentLoaded", function () {
    const loadMoreBtn = document.getElementById("loadMoreBtn");
    const newsContainer = document.getElementById("industrie-news-container");
    let displayedPosts = [];
    // Select all the .industrie-news-card elements
    const newsCard = document.querySelectorAll('.industrie-news-card');
    const articleItem = document.querySelectorAll('.bb-article-item');
    // Iterate over each card
    articleItem.forEach(function( article) {
        // Get the post ID from the data-post-id attribute
        const postId =  article.getAttribute('data-post-id');
        
        // If the post ID exists, add it to the displayedPosts array
        if (postId) {
            displayedPosts.push(postId);
        }
    });
    // Iterate over each card
    newsCard.forEach(function(news) {
        // Get the post ID from the data-post-id attribute
        const postId = news.getAttribute('data-post-id');
        
        // If the post ID exists, add it to the displayedPosts array
        if (postId) {
            displayedPosts.push(postId);
        }
    });

    if (loadMoreBtn) {
        loadMoreBtn.addEventListener("click", function () {
            let offset = parseInt(loadMoreBtn.getAttribute("data-offset"));
            let categoryId = loadMoreBtn.getAttribute("data-category-id");

            loadMoreBtn.textContent = "Loading...";
            loadMoreBtn.disabled = true;

            fetch(ajaxurl, {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `action=load_more_parent_cat_news&offset=${offset}&category_id=${categoryId}&displayed_posts=${displayedPosts}`
            })
            .then(response => response.text())
            .then(data => {
                if (data.trim() === "no_more_posts") {
                    loadMoreBtn.style.display = "none";
                } else {
                    newsContainer.insertAdjacentHTML("beforeend", data);
                    loadMoreBtn.setAttribute("data-offset", offset + 6);
                    loadMoreBtn.textContent = "Load More";
                    loadMoreBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error("Error loading more posts:", error);
                loadMoreBtn.textContent = "Load More";
                loadMoreBtn.disabled = false;
            });
        });
    }
});
