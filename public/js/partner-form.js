$(document).ready(function() {
    // Initial state setup
    setupInitialFormState();

    function setVisibility(selector, visible) {
        $(selector).toggleClass('d-none', !visible);
        $(selector).attr('aria-hidden', visible ? 'false' : 'true');
    }
    
    // Born Again conditional fields
    $('select[name="born_again"]').change(function() {
        const isYes = $(this).val() === 'yes';
        setVisibility('.born-again-details', isYes);
        $('[name="salvation_date"], [name="salvation_place"]').prop('required', isYes);
    });

    // Water Baptism conditional fields
    $('select[name="water_baptized"]').change(function() {
        const isYes = $(this).val() === 'yes';
        setVisibility('.baptism-type-group', isYes);
        $('[name="baptism_type"]').prop('required', isYes);
    });

    // Holy Ghost Baptism conditional fields
    $('select[name="holy_ghost_baptism"]').change(function() {
        const isNo = $(this).val() === 'no';
        setVisibility('.holy-ghost-reason', isNo);
        $('[name="holy_ghost_baptism_reason"]').prop('required', isNo);
    });

    // Leadership Experience dynamic fields
    $('#addMoreLeadership').click(function() {
        const template = $('.leadership-entry:first').clone();
        template.find('input').val('');
        const removeBtn = $('<button type="button" class="btn btn-outline-danger mt-3 remove-leadership-entry">Remove Entry</button>');
        template.append(removeBtn);
        $(this).before(template);
    });

    $(document).on('click', '.remove-leadership-entry', function() {
        $(this).closest('.leadership-entry').remove();
    });

    // Leadership Experience conditional display
    $('select[name="leadership_experience"]').change(function() {
        const isYes = $(this).val() === 'yes';
        setVisibility('#leadershipDetails', isYes);
        $('#leadershipDetails input').prop('required', isYes);
    });

    function setupInitialFormState() {
        const formSelects = {
            'born_again': '.born-again-details',
            'water_baptized': '.baptism-type-group',
            'holy_ghost_baptism': '.holy-ghost-reason',
            'leadership_experience': '#leadershipDetails'
        };

        Object.entries(formSelects).forEach(([select, target]) => {
            const value = $(`select[name="${select}"]`).val();
            if (value) {
                setVisibility(target, value === 'yes');
            } else {
                setVisibility(target, false);
            }
        });
    }
});
