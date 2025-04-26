class SearchHandler {
  /**
   * Create a new search handler
   * @param {Object} config - Configuration object
   * @param {string} config.apiEndpoint - API endpoint for search requests
   * @param {HTMLElement} config.inputElement - Input element for search queries
   * @param {HTMLElement} config.resultsContainer - Container for search results
   * @param {Function} config.renderResultItem - Function to render a single result item
   * @param {Function} config.onSelect - Callback when a result is selected, can receive additional arguments
   * @param {Object} [config.selectionContext={}] - Additional context data to pass to onSelect callback
   * @param {Object} [config.extraParams={}] - Extra parameters to include in API requests
   * @param {number} [config.itemsPerPage=5] - Number of items per page
   */
  constructor(config) {
    this.apiEndpoint = config.apiEndpoint;
    this.inputElement = config.inputElement;
    this.resultsContainer = config.resultsContainer;
    this.renderResultItem = config.renderResultItem;
    this.onSelect = config.onSelect;
    this.extraParams = config.extraParams || {};
    this.selectionContext = config.selectionContext || {};
    this.itemsPerPage = config.itemsPerPage || 5;

    this.state = {
      query: "",
      results: [],
      page: 0,
      isResultsHovered: false,
    };

    this.init();
  }

  /**
   * Initialize event listeners
   */
  init() {
    // Set up input event listeners
    this.inputElement.addEventListener("input", (e) =>
      this.search(e.target.value)
    );
    this.inputElement.addEventListener("focusin", (e) =>
      this.search(e.target.value)
    );

    // Track when mouse enters/leaves the results container
    this.resultsContainer.addEventListener("mouseenter", () => {
      this.state.isResultsHovered = true;
    });

    this.resultsContainer.addEventListener("mouseleave", () => {
      this.state.isResultsHovered = false;
    });

    // Handle input blur - close results only if not interacting with results
    this.inputElement.addEventListener("focusout", (e) => {
      if (!this.state.isResultsHovered) {
        this.resultsContainer.innerHTML = "";
      }
    });

    // Handle scrolling for pagination
    this.resultsContainer.addEventListener("scrollend", () =>
      this.search(this.state.query, this.state.page + 1)
    );
  }

  /**
   * Perform a search with the given query
   * @param {string} query - Search query
   * @param {number} [page=1] - Page number
   */
  async search(query, page = 1) {
    // Build URL with query parameters
    const url = new URL(this.apiEndpoint, window.location.origin);
    url.searchParams.set("q", query);
    url.searchParams.set("p", page);
    url.searchParams.set("ipp", this.itemsPerPage);

    // Add any extra parameters
    Object.entries(this.extraParams).forEach(([key, value]) => {
      if (value !== undefined && value !== null) {
        url.searchParams.set(key, value);
      }
    });

    try {
      const response = await fetch(url);
      const data = await response.json();

      if (!data.success) {
        console.error(`Search failed: ${data.error}`);
        return;
      }

      // If no results, show empty message
      if (data.data.length === 0) {
        if (page === 1) {
          this.resultsContainer.innerHTML =
            '<div class="search-result">No results found</div>';
        }
        return;
      }

      // If input value changed during fetch, discard these results
      if (this.inputElement.value !== query) return;

      // Handle first page or pagination
      if (query !== this.state.query && page === 1) {
        // New search - replace results
        this.state.query = query;
        this.state.results = data.data;
        this.state.page = 1;
      } else if (query === this.state.query && page > this.state.page) {
        // Pagination - append results
        this.state.results = [...this.state.results, ...data.data];
        this.state.page = page;
      }

      this.renderResults();
    } catch (error) {
      console.error("Search request failed:", error);
    }
  }

  /**
   * Render search results
   */
  renderResults() {
    this.resultsContainer.innerHTML = "";

    if (this.state.results.length === 0) {
      this.resultsContainer.innerHTML =
        '<div class="search-result">No results found</div>';
      return;
    }

    this.state.results.forEach((item) => {
      const element = this.renderResultItem(item);
      element.addEventListener("click", (event) => {
        this.handleSelect(item, event);
      });
      this.resultsContainer.appendChild(element);
    });
  }

  /**
   * Handle selection of a result
   * @param {Object} item - Selected item
   * @param {Event} [event] - The original click event
   */
  handleSelect(item, event) {
    this.clear();
    if (this.onSelect) {
      this.onSelect(item, this.selectionContext, event);
    }
  }

  /**
   * Clear search state and results
   */
  clear() {
    this.state.query = "";
    this.state.results = [];
    this.state.page = 0;
    this.resultsContainer.innerHTML = "";
  }

  /**
   * Update extra parameters for the search
   * @param {Object} params - New parameters
   */
  updateParams(params) {
    this.extraParams = {
      ...this.extraParams,
      ...params,
    };
  }

  /**
   * Update the selection context for the onSelect callback
   * @param {Object} context - New context data
   */
  updateSelectionContext(context) {
    this.selectionContext = {
      ...this.selectionContext,
      ...context,
    };
  }
}
