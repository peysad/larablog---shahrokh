class SearchHandler {
    constructor() {
        this.searchInput = document.querySelector('#search-input');
        this.searchForm = document.querySelector('#search-form');
        this.resultsContainer = document.querySelector('#search-results');
        
        if (this.searchInput) {
            this.init();
        }
    }

    init() {
        this.searchInput.addEventListener('input', this.debounce((e) => {
            this.handleSearch(e.target.value);
        }, 300));

        if (this.searchForm) {
            this.searchForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.handleSearch(this.searchInput.value);
            });
        }
    }

    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    async handleSearch(query) {
        if (query.length < 2) {
            if (this.resultsContainer) this.resultsContainer.innerHTML = '';
            return;
        }

        try {
            const response = await fetch(`/search?q=${encodeURIComponent(query)}&ajax=1`);
            const data = await response.json();
            
            if (this.resultsContainer) {
                this.displayResults(data);
            }
        } catch (error) {
            console.error('Search error:', error);
        }
    }

    displayResults(results) {
        // Implementation will depend on your search endpoint response
        let html = '<div class="dropdown-menu show" style="width: 100%;">';
        
        if (results.posts && results.posts.length > 0) {
            results.posts.forEach(post => {
                html += `
                    <a class="dropdown-item" href="/posts/${post.slug}">
                        ${post.title}
                        <small class="text-muted d-block">${post.excerpt.substring(0, 100)}...</small>
                    </a>
                `;
            });
        } else {
            html += '<div class="dropdown-item">No results found</div>';
        }
        
        html += '</div>';
        
        this.resultsContainer.innerHTML = html;
    }
}

// Initialize search when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new SearchHandler();
});