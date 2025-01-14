jQuery( window ).load(function() {

    console.log('testing');
    console.log(options);
	
    var maxVariants = 10;
    var hostname = window.location.origin
    console.log(hostname);
	
	if(document.getElementById('max_test_variant')){
		maxVariants = document.getElementById('max_test_variant').value;
	}
	console.log("mv is: " + maxVariants);
	

    // Add a new variant input field
    jQuery('#abts-add-variant-btn').on('click', function () {
        console.log(options);
        const currentVariants = jQuery('.abts-variant-item').length;

        if (currentVariants <= maxVariants) {
            let key = currentVariants + 1;
            const variantItem = `
                <div class="abts-variant-item">
                    <select id="variant${key - 1}" name="variants[]">${options}</select>
                    <button type="button" class="abts-remove-variant-btn">Remove</button>
                </div>
            `;
            // const variantItem = `
            //     <div class="abts-variant-item">
            //         <span class="domain">${hostname}/</span>
            //         <input type="text" name="variants[]" id="variant${key - 1}"  onkeyup="loadResults('variant${key - 1}','key${key}')" />
            //         <input type="hidden" name="variant_page_id[]" id="abts-page-id-key${key}" />
            //         <div id="key${key}" class="abtspageslist"></div>
            //         <button type="button" class="abts-remove-variant-btn">Remove</button>
            //     </div>
            // `;
            jQuery('#abts-variants-container').append(variantItem);
        } else {
            alert('Maximum of ' + maxVariants +' variants allowed.');
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
