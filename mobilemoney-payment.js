(function($) {

    $(document).ready(function () {
        $(document.body).on('updated_checkout', function() {
            initMobileMoneyPayment();
        });
        
        initMobileMoneyPayment();
    });

    function initMobileMoneyPayment() {
        var $select = $('#mm_operator_field select');
        
        if ($select.length) {
            var defaultValue = $select.val();
            checkValue(defaultValue);
    
            $select.off('change').on('change', function() {
                checkValue(this.value);
            });
        }
    }

    function checkValue(value) {
        if (!mmpayment_data || !mmpayment_data.operators) {
            return;
        }

        const instruction = mmpayment_data.operators[value];
        const $instructionDiv = $("#mm_instruction");
        
        if (instruction && instruction !== "") {
            let message;
            
            if (instruction.match(/^[*#].*#$/)) {
                message = 'Composez <strong>' + instruction + '</strong> pour effectuer le paiement';
            } 
            else if (instruction.length < 10) {
                message = 'Code: <strong>' + instruction + '</strong>';
            } 
            else {
                message = instruction;
            }
            
            $instructionDiv.html(message).fadeIn(300);
        } else {
            $instructionDiv.fadeOut(200, function() {
                $(this).html("");
            });
        }
    }

    $(document).on('blur', '.mm-input', function() {
        var $input = $(this);
        if ($input.val().trim() !== '') {
            $input.css('border-color', '#4caf50');
            setTimeout(function() {
                $input.css('border-color', '');
            }, 800);
        }
    });

})( jQuery );