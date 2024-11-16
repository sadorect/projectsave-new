$(document).ready(function() {
    let currentStep = 1;
    const totalSteps = 4;

    function initializeConditionals() {
        // Born Again conditionals
        $('select[name="born_again"]').on('change', function() {
            $('.born-again-details').toggle($(this).val() === 'yes');
            $('[name="salvation_date"], [name="salvation_place"]').prop('required', $(this).val() === 'yes');
        });

        // Water Baptism conditionals
        $('select[name="water_baptized"]').on('change', function() {
            $('.baptism-type-group').toggle($(this).val() === 'yes');
            $('[name="baptism_type"]').prop('required', $(this).val() === 'yes');
        });

        // Holy Ghost Baptism conditionals
        $('select[name="holy_ghost_baptism"]').on('change', function() {
            $('.holy-ghost-reason').toggle($(this).val() === 'no');
            $('[name="holy_ghost_baptism_reason"]').prop('required', $(this).val() === 'no');
        });

        // Leadership Experience conditionals
        $('select[name="leadership_experience"]').on('change', function() {
            $('#leadershipDetails').toggle($(this).val() === 'yes');
            $('#leadershipDetails input').prop('required', $(this).val() === 'yes');
        });

        // Leadership Add More functionality
        $('#addMoreLeadership').on('click', function() {
            const template = $('.leadership-entry:first').clone();
            template.find('input').val('');
            const removeBtn = $('<button type="button" class="btn btn-danger mt-2">Remove Entry</button>');
            template.append(removeBtn);
            $(this).before(template);
        });

        $(document).on('click', '.btn-danger', function() {
            $(this).closest('.leadership-entry').remove();
        });
    }
    
    const validationRules = {
        step1: {
            name: { required: true, minlength: 3 },
            email: { required: true, email: true },
            phone: { required: true, pattern: /^[0-9+\-\s]+$/ },
            dob: { required: true }
        },
        step2: {
            born_again: { required: true },
            water_baptized: { required: true },
            holy_ghost_baptism: { required: true }
        },
        step3: {
            leadership_experience: { required: true }
        },
        step4: {
            calling: { required: true },
            commitment_answer: { required: true }
        }
    };

    function validateStep(stepNumber) {
        const currentFields = $(`#step${stepNumber} :input`).serializeArray();
        let isValid = true;
        let firstError = null;

        currentFields.forEach(field => {
            const rules = validationRules[`step${stepNumber}`][field.name];
            if (rules) {
                const element = $(`[name="${field.name}"]`);
                const value = field.value;
                
                element.removeClass('is-invalid').next('.invalid-feedback').remove();

                if (!validateField(value, rules)) {
                    isValid = false;
                    element.addClass('is-invalid');
                    const errorMessage = getErrorMessage(field.name, rules);
                    element.after(`<div class="invalid-feedback">${errorMessage}</div>`);
                    
                    if (!firstError) firstError = element;
                }
            }
        });

        if (firstError) {
            firstError.focus();
        }

        return isValid;
    }

    function showStep(step) {
       // Hide all steps first
       $('.step-content').hide();
       // Show current step
       $(`#step${step}`).show();
       
       // Update navigation buttons
       $('#prevBtn').toggle(step > 1);
       $('#nextBtn').toggle(step < totalSteps);
       $('#submitBtn').toggle(step === totalSteps);
       
       updateProgressBar();
       initializeConditionals();
    }

    function updateProgressBar() {
        const progress = ((currentStep - 1) / (totalSteps - 1)) * 100;
        $('.progress-bar').css('width', `${progress}%`);
        
        $('.step-indicator').removeClass('active completed');
        $(`.step-indicator:nth-child(${currentStep})`).addClass('active');
        for(let i = 1; i < currentStep; i++) {
            $(`.step-indicator:nth-child(${i})`).addClass('completed');
        }
    }

    // Initialize first step
    showStep(1);
    
    // Event listeners for navigation buttons
    $('#nextBtn').on('click', function() {
        if(validateStep(currentStep)) {
            currentStep++;
            showStep(currentStep);
        }
    });

    $('#prevBtn').on('click', function() {
        currentStep--;
        showStep(currentStep);
    });

    $('#partnerForm').on('submit', function(e) {
        e.preventDefault();
        
        if(validateStep(currentStep)) {
            const formData = new FormData(this);
            
            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    window.location.href = response.redirect;
                },
                error: function(xhr) {
                    const errors = xhr.responseJSON.errors;
                    Object.keys(errors).forEach(field => {
                        $(`[name="${field}"]`).addClass('is-invalid')
                            .after(`<div class="invalid-feedback">${errors[field][0]}</div>`);
                    });
                }
            });
        }
    });

function validateField(value, rules) {
    if (rules.required && !value) {
        return false;
    }
    
    if (rules.minlength && value.length < rules.minlength) {
        return false;
    }
    
    if (rules.email && !validateEmail(value)) {
        return false;
    }
    
    if (rules.pattern && !rules.pattern.test(value)) {
        return false;
    }
    
    return true;
}

function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function getErrorMessage(fieldName, rules) {
    if (rules.required && !value) {
        return `${fieldName} is required`;
    }
    if (rules.minlength) {
        return `${fieldName} must be at least ${rules.minlength} characters`;
    }
    if (rules.email) {
        return 'Please enter a valid email address';
    }
    if (rules.pattern) {
        return `Please enter a valid ${fieldName}`;
    }
    return 'Invalid input';
}
    // Initialize form
    showStep(1);
    initializeConditionals();
});