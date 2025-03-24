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
    const dropdownToggle = document.querySelector(".mobile-nav-menu-item-dropdwon");
    const dropdownMenu = document.querySelector(".mobile-dropdown-menu");
    const dropdownIcon = dropdownToggle.querySelector("i");

    // Toggle Dropdown Menu & Icon
    dropdownToggle.addEventListener("click", function (event) {
        event.preventDefault();

        // Toggle dropdown
        const isOpen = dropdownMenu.style.display === "block";
        dropdownMenu.style.display = isOpen ? "none" : "block";

        // Toggle icon
        dropdownIcon.classList.toggle("fa-chevron-down", isOpen);
        dropdownIcon.classList.toggle("fa-chevron-up", !isOpen);
    });

    // Tabs Functionality
    const tabButtons = document.querySelectorAll(".tab-btn");
    const tabContents = document.querySelectorAll(".tab-content");

    // Default: Hide all tabs
    tabContents.forEach((content) => {
        content.style.display = "none";
    });

    tabContents.forEach((content) => content.style.display = "none");

    tabButtons.forEach((btn) => {
        const icon = document.createElement("i"); // Add icon dynamically
        icon.classList.add("fa-solid", "fa-chevron-down"); // Default icon
        btn.appendChild(icon);

        btn.addEventListener("click", function () {
            // Remove active class & reset icons
            tabButtons.forEach((b) => {
                b.classList.remove("active");
                b.querySelector("i").classList.remove("fa-chevron-up");
                b.querySelector("i").classList.add("fa-chevron-down");
            });

            // Activate clicked tab
            this.classList.add("active");
            this.querySelector("i").classList.toggle("fa-chevron-down");
            this.querySelector("i").classList.toggle("fa-chevron-up");

            // Hide all content, show selected
            tabContents.forEach((content) => content.style.display = "none");
            document.getElementById(this.dataset.tab).style.display = "flex";
        });
    });
});

document.addEventListener('DOMContentLoaded', () => {
    const cards = document.querySelectorAll('.industrie-news-card');
    const loadMoreBtn = document.getElementById('loadMoreBtn');
    let visibleCards = 3; // Number of initially visible cards
    const incrementAmount = 3; // Cards to show on each click

    // Hide all cards except first 3
    cards.forEach((card, index) => {
        if (index >= visibleCards) card.classList.add('hidden');
    });

    loadMoreBtn.addEventListener('click', () => {
        // Show next set of cards
        visibleCards += incrementAmount;
        cards.forEach((card, index) => {
            if (index < visibleCards) card.classList.remove('hidden');
        });

        // Hide button when all cards are visible
        if (visibleCards >= cards.length) {
            loadMoreBtn.classList.add('hidden');
        }
    });

    // Initial button visibility check
    if (cards.length <= visibleCards) {
        loadMoreBtn.classList.add('hidden');
    }
});

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

            if (data.success && data.data && data.data.html) {
                marketWrapper.innerHTML = data.data.html;

                // Reassign marketWrapper after update
                marketWrapper = document.getElementById("marketDataWrapper");

                // Reattach scroll events
                attachScrollEvents();
            } else {
                console.error("Unexpected response structure:", data);
                marketWrapper.innerHTML = '<p class="error">No data available</p>';
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

    toggleDropdown.addEventListener('click', () => {
        document.querySelector('.future-section__latest-header-catg-list').classList.toggle('active');
    });
    
    categoryItems.forEach(item => {
        item.addEventListener("click", function () {
            const categoryId = this.getAttribute("data-cat");

            fetchLatestPosts(categoryId);
        });
    });

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