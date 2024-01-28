(() => {

    class MACF {
        constructor(container) {
            this._container = container;
            this._loadElements();
            this._loadListeners();
        }

        _loadElements() {
            this._form = this._container.querySelector('.macf-form');
            this._sourceMultisite = this._container.querySelector('#source_site');
            this._destinationMultisite = this._container.querySelector('#destination_site');

            this._sourcePage = this._container.querySelector('#source_page');
            this._destinationPage = this._container.querySelector('#destination_page');

            this._sourceCustomFields = this._container.querySelector('#source_acf');
            this._submitButton = this._container.querySelector('.macf-form__submit button');
        }

        _loadListeners() {
            this._sourceMultisite.addEventListener('change', (event) => this._changeMultisiteSelect(event,this._sourcePage));
            this._destinationMultisite.addEventListener('change', (event) => this._changeMultisiteSelect(event,this._destinationPage));
            
            this._sourcePage.addEventListener('change', (event) => this._changeSourcePage(event));

            this._form.addEventListener('submit', (event) => this._submitForm(event));
        }

        _changeMultisiteSelect(event, changedElement) {
            const selectedSiteId = event.target.value;

            // Fetch data from the API endpoint using POST
            fetch(`/wp-json/multisite-acf/v1/page-data/`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `site_id=${selectedSiteId}`,
            })
                .then(response => response.json())
                .then(data => this._populateSourcePage(data,changedElement))
                .catch(error => console.error('Error fetching data:', error));
        }

        _populateSourcePage(data,element) {
            // Clear existing options
            element.innerHTML = '';

            // Populate the _sourcePage select element with the fetched data
            data.forEach(item => {
                const option = document.createElement('option');
                option.value = item.value;
                option.textContent = item.label;
                element.appendChild(option);
            });

            // Show group
            element.closest('.macf-box__group').style.display = 'flex';
        }

        _changeSourcePage(event) {
            const selectedPageId = event.target.value;

            // Fetch data from the API endpoint using POST
            fetch(`/wp-json/multisite-acf/v1/acf-data/`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `post_id=${selectedPageId}`,
            })
                .then(response => response.json())
                .then(data => this._populateSourceACF(data))
                .catch(error => console.error('Error fetching data:', error));
        }

        _populateSourceACF(data) {

            // Clear existing options
            this._sourceCustomFields.innerHTML = '';

            // Populate the _sourcePage select element with the fetched data
            let i = 0;
            let acfCheck = false;

            data.forEach(item => {
                item.fields.forEach(fields => {
                    if(!fields.value) {
                        return;
                    }
                    i++;
                    acfCheck = true;

                    const wrapper = document.createElement('div');
                    wrapper.className = 'macf-box__checkbox';

                    const checkbox = document.createElement('input');
                    checkbox.type = "checkbox";
                    checkbox.name = "source_custom_fields[]";
                    checkbox.value = fields.name;
                    checkbox.id = "source_custom_fields" + i;

                    const label = document.createElement('label')
                    label.htmlFor = "source_custom_fields" + i;
                    label.appendChild(document.createTextNode(fields.label));

                    wrapper.appendChild(checkbox);
                    wrapper.appendChild(label);

                    this._sourceCustomFields.appendChild(wrapper);
                });
            });

            if(!acfCheck) {
                this._sourceCustomFields.innerHTML = 'We could not find any custom fields with values';
            }

            this._sourceCustomFields.style.display = 'flex';
        }

        _submitForm(event) {
            event.preventDefault();

            const formData = new FormData(event.target);

            const customFields = formData.get('source_custom_fields[]');

            if (!customFields) {
                alert('Please select custom fields.');
                return;
            }

            this._submitButton.textContent = 'Processing';

            // Send data to the API endpoint via POST request
            fetch('/wp-json/multisite-acf/v1/submit-form/', {
                method: 'POST',
                headers: {
                    // Add any headers if needed
                },
                body: formData,
            })
                .then(response => response.json())
                .then(data => {
                    // Change the text of the Submit button to "Done"
                    this._submitButton.textContent = 'Done, reloading page';

                    setTimeout(() => {
                        location.reload();
                    }, "1500");
                })
                .catch(error => {
                    console.error('Error sending data:', error);
                    // Handle the error if needed

                    // Change the text of the Submit button back to the original text
                    this._submitButton.textContent = 'Submit';

                });


        }
    }

    window.MACF = MACF;

})(window);