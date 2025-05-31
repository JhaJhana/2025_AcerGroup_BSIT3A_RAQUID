class ProfileSplitTransition {
    constructor() {
        this.profileContainer = document.getElementById('profile-container');
        this.editContainer = document.getElementById('edit-profile-form');
        this.editButton = document.getElementById('edit-profile-btn');
        this.form = document.getElementById('profile-form');
        this.isSplit = false;

        this.init();
    }

    init() {
        // Add event listeners
        this.editButton.addEventListener('click', () => this.split());
        this.form.addEventListener('submit', (e) => this.merge(e));

        // Initialize form values
        this.initializeFormValues();
    }

    split() {
        if (this.isSplit) return;
        
        // Add split classes with animation
        this.profileContainer.style.transition = 'all 0.5s ease-in-out';
        this.editContainer.style.transition = 'all 0.5s ease-in-out';
        
        // Trigger split animation
        requestAnimationFrame(() => {
            this.profileContainer.style.transform = 'translateX(-50%)';
            this.editContainer.style.transform = 'translateX(0)';
            this.editContainer.style.opacity = '1';
        });

        this.isSplit = true;
    }

    merge(e) {
        e.preventDefault();
        if (!this.isSplit) return;

        // Update profile information
        this.updateProfileInfo();

        // Trigger merge animation
        requestAnimationFrame(() => {
            this.profileContainer.style.transform = 'translateX(0)';
            this.editContainer.style.transform = 'translateX(100%)';
            this.editContainer.style.opacity = '0';
        });

        this.isSplit = false;
    }

    updateProfileInfo() {
        // Update all profile information
        document.getElementById('fullname').textContent = document.getElementById('fullname-input').value;
        document.getElementById('email').textContent = document.getElementById('email-input').value;
        document.getElementById('mobile').textContent = document.getElementById('mobile-input').value;
        document.getElementById('birthdate').textContent = document.getElementById('birthdate-input').value;
        document.querySelector('.text-gray-600.text-sm.leading-relaxed').textContent = 
            document.getElementById('about-input').value;
    }

    initializeFormValues() {
        // Pre-fill form with current values
        document.getElementById('fullname-input').value = document.getElementById('fullname').textContent;
        document.getElementById('email-input').value = document.getElementById('email').textContent;
        document.getElementById('mobile-input').value = document.getElementById('mobile').textContent;
        document.getElementById('birthdate-input').value = document.getElementById('birthdate').textContent;
        document.getElementById('about-input').value = 
            document.querySelector('.text-gray-600.text-sm.leading-relaxed').textContent.trim();
    }
}

// Initialize the profile transition when the DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new ProfileSplitTransition();
}); 