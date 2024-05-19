
    function submitForm(event) {
        event.preventDefault(); // Prevent form submission

        // Serialize form data
        var formData = new FormData(document.getElementById('contact-form'));

        // Send form data to PHP script using fetch API
        fetch('contact.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (response.ok) {
                return response.text();
            }
            throw new Error('Network response was not ok.');
        })
        .then(data => {
            // Check the response from PHP
            if (data === 'success') {
                // Hide contact form section
                document.getElementById('contact-form-section').classList.remove('d-none');
                // Display thanks message section
                document.getElementById('thanks-message-section').classList.add('d-none');
            } else {
                // Display error message
                document.getElementById('error-message-section').classList.add('d-none');
            }
        })
        .catch(error => {
            // Display error message
            document.getElementById('error-message-section').classList.remove('d-none');
            console.error('There was a problem with the fetch operation:', error.message);
        });
    }

