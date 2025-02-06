class Dropdown {
    constructor(element) {
        this.dropdown = element;
        this.trigger = element.querySelector('.dropdown-trigger');
        this.menu = element.querySelector('.dropdown-menu');
        this.init();
    }

    init() {
        // Toggle dropdown
        this.trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            this.toggle();
        });

        // Prevent menu clicks from closing dropdown
        this.menu?.addEventListener('click', (e) => e.stopPropagation());
    }

    toggle() {
        // Close other dropdowns
        document.querySelectorAll('.dropdown.active').forEach(d => {
            if (d !== this.dropdown) {
                d.classList.remove('active');
            }
        });

        this.dropdown.classList.toggle('active');
    }

    close() {
        this.dropdown.classList.remove('active');
    }

    static init() {
        // Initialize all dropdowns
        const dropdowns = document.querySelectorAll('.dropdown');
        dropdowns.forEach(d => new Dropdown(d));

        // Close all dropdowns when clicking outside
        document.addEventListener('click', () => {
            document.querySelectorAll('.dropdown.active').forEach(d => {
                d.classList.remove('active');
            });
        });
    }
}

// Initialize dropdowns when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    Dropdown.init();
});
