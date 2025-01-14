jQuery( window ).load(function() {

	console.log('testing');
	
        const maxVariants = 10;
        let hostname = jQuery('<a>').prop('href', url).prop('hostname');
        console.log(hostname);

        // Add a new variant input field
        jQuery('#abts-add-variant-btn').on('click', function () {
            const currentVariants = jQuery('.abts-variant-item').length;

            if (currentVariants < maxVariants) {
                let key = currentVariants + 1;
                const variantItem = `
                    <div class="abts-variant-item">
                        <span class="domain">${hostname}/</span>
                        <input type="text" name="variants[]" id="variant${key - 1}" placeholder="Page Url" onkeyup="loadResults('variant${key - 1}','key${key}')" />
                        <input type="hidden" name="variant_page_id[]" id="abts-page-id-key${key}" />
                        <div id="key${key}"></div>
                        <button type="button" class="abts-remove-variant-btn">Remove</button>
                    </div>
                `;
                jQuery('#abts-variants-container').append(variantItem);
            } else {
                alert('Maximum of 10 variants allowed.');
            }
        });

        // Remove a variant input field
        jQuery('#abts-variants-container').on('click', '.abts-remove-variant-btn', function () {
            jQuery(this).closest('.abts-variant-item').remove();
        });

        // // Handle form submission
        // jQuery('#abts-test-form').on('submit', function (event) {
        //     event.preventDefault();

        //     const formData = jQuery(this).serializeArray();
        //     console.log('Form submitted with data:');
        //     formData.forEach((field) => {
        //         console.log(`jQuery{field.name}: jQuery{field.value}`);
        //     });

        //     alert('Form submitted successfully!');
        // });
});
