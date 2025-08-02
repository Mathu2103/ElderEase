// Login JavaScript for Admin and Caregiver
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const errorMessage = document.getElementById('errorMessage');
    const submitBtn = document.querySelector('.submit-btn');
    const roleRadios = document.querySelectorAll('input[name="role"]');
    const nicLabel = document.getElementById('nicLabel');

    // Update label based on selected role
    function updateRoleLabel() {
        const selectedRole = document.querySelector('input[name="role"]:checked').value;
        if (selectedRole === 'admin') {
            nicLabel.textContent = 'NIC (National Identity Number) - Admin';
        } else {
            nicLabel.textContent = 'NIC (National Identity Number) - Caregiver';
        }
    }

    // Add event listeners for role selection
    roleRadios.forEach(radio => {
        radio.addEventListener('change', updateRoleLabel);
    });

    // Initialize label
    updateRoleLabel();

    // Handle form submission
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Get form data
        const formData = new FormData(loginForm);
        const role = document.querySelector('input[name="role"]:checked').value;
        const nic = formData.get('nic');
        const password = formData.get('password');

        // Hide previous error messages
        hideError();

        // Basic validation
        if (!nic || !password) {
            showError('Please fill in all fields');
            return;
        }

        // Validate NIC format (basic validation)
        if (!isValidNIC(nic)) {
            showError('Please enter a valid NIC number');
            return;
        }

        // Show loading state
        setLoadingState(true);

        // Send login request
        fetch('caregiverlogin.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `role=${role}&nic=${encodeURIComponent(nic)}&password=${encodeURIComponent(password)}`
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            setLoadingState(false);
            
            if (data.success) {
                // Show success message briefly before redirect
                showSuccess('Login successful! Redirecting...');
                
                // Redirect based on role
                setTimeout(() => {
                    if (role === 'admin') {
                        window.location.href = 'admin/dashboard.php';
                    } else {
                        window.location.href = 'caregiver/dashboard.php';
                    }
                }, 1000);
            } else {
                showError(data.message || 'Login failed. Please check your credentials.');
            }
        })
        .catch(error => {
            setLoadingState(false);
            showError('An error occurred. Please try again.');
            console.error('Error:', error);
        });
    });

    // NIC validation function
    function isValidNIC(nic) {
        // Basic NIC validation - can be customized based on your country's format
        // This is a simple example for Sri Lankan NIC format
        const nicRegex = /^[0-9]{9}[vVxX]$|^[0-9]{12}$/;
        return nicRegex.test(nic);
    }

    // Show error message
    function showError(message) {
        errorMessage.textContent = message;
        errorMessage.classList.remove('hidden');
        errorMessage.classList.add('error-message');
    }

    // Show success message
    function showSuccess(message) {
        errorMessage.textContent = message;
        errorMessage.classList.remove('hidden', 'error-message');
        errorMessage.classList.add('success-message');
        errorMessage.style.backgroundColor = '#d1fae5';
        errorMessage.style.borderColor = '#a7f3d0';
        errorMessage.style.color = '#065f46';
    }

    // Hide error message
    function hideError() {
        errorMessage.classList.add('hidden');
    }

    // Set loading state
    function setLoadingState(loading) {
        if (loading) {
            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Logging in...';
        } else {
            submitBtn.classList.remove('loading');
            submitBtn.disabled = false;
            submitBtn.textContent = 'Login';
        }
    }

    // Clear form on page load
    loginForm.reset();

    // Focus on NIC field when page loads
    document.getElementById('nic').focus();

    // Add input event listeners for real-time validation
    const nicInput = document.getElementById('nic');
    const passwordInput = document.getElementById('password');

    nicInput.addEventListener('input', function() {
        if (this.value && !isValidNIC(this.value)) {
            this.style.borderColor = '#ef4444';
        } else {
            this.style.borderColor = '#d1d5db';
        }
    });

    passwordInput.addEventListener('input', function() {
        if (this.value.length < 6) {
            this.style.borderColor = '#ef4444';
        } else {
            this.style.borderColor = '#d1d5db';
        }
    });
}); 