$(document).ready(function() {
    // Initial state setup
    setupInitialFormState();
    
    // Born Again conditional fields
    $('select[name="born_again"]').change(function() {
        const isYes = $(this).val() === 'yes';
        $('.born-again-details').toggle(isYes);
        $('[name="salvation_date"], [name="salvation_place"]').prop('required', isYes);
    });

    // Water Baptism conditional fields
    $('select[name="water_baptized"]').change(function() {
        const isYes = $(this).val() === 'yes';
        $('.baptism-type-group').toggle(isYes);
        $('[name="baptism_type"]').prop('required', isYes);
    });

    // Holy Ghost Baptism conditional fields
    $('select[name="holy_ghost_baptism"]').change(function() {
        const isNo = $(this).val() === 'no';
        $('.holy-ghost-reason').toggle(isNo);
        $('[name="holy_ghost_baptism_reason"]').prop('required', isNo);
    });

    // Leadership Experience dynamic fields
    $('#addMoreLeadership').click(function() {
        const template = $('.leadership-entry:first').clone();
        template.find('input').val('');
        const removeBtn = $('<button type="button" class="btn btn-danger mt-2">Remove Entry</button>');
        template.append(removeBtn);
        $(this).before(template);
    });

    $(document).on('click', '.btn-danger', function() {
        $(this).closest('.leadership-entry').remove();
    });

    // Leadership Experience conditional display
    $('select[name="leadership_experience"]').change(function() {
        const isYes = $(this).val() === 'yes';
        $('#leadershipDetails').toggle(isYes);
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
                $(target).toggle(value === 'yes');
            }
        });
    }
});
