/* jshint devel:true */
/* global wp */
/* global wpErp */
/* global RbsErpPayrollRun */

window.wperp = window.wperp || {};

(function($) {
    'use strict';

    var RBS_ERP_PAYROLL_RUN = {

        /**
         * Initialize the events
         *
         * @return {void}
         */
        initialize: function() {
            $( '.selected_employee_id').on( 'change', this.onChangeloadEmployeeInfo );

            var addition_item_array = [];

            $(".add-allowance-item").click(function(){
                var prDetails_id = $(".prDetails_id").val();
                var total_number_of_day = $('.total_number_of_day_in_month').val();
                if(prDetails_id==''){
                    alert('please select employee');
                    $(".selected_employee_id").focus();
                    return false;
                }
                $('.delete_allowance_item').each(function(){
                    addition_item_array.push(this.value);
                });
                var basic_salary = $(".basic_payment_hidden").val();

                var total_payment = $(".total_payment").html();

                var addition_value = $(".addition_name").val();
                var addition_name = $(".addition_name option:selected").text();
                var addition_amount = $(".addition_amount").val();
                var amount_type = $(".amount_type").val();
                if(addition_value=='-1'){
                   alert('Please select allowance item');
                   return false;
                }
                if(addition_amount==''){
                    alert('Please enter allowance amount');
                    return false;
                }
                if( $.inArray(addition_value, addition_item_array) !== -1 ) {

                    alert("This allowance already added.");
                    return false;
                }
                if(amount_type=='percentage'){
                    if(basic_salary=='' || basic_salary=='0'){
                        alert('Please enter basic salary.');
                        // $("#basic_salary").focus();
                        return false;
                    }
                    addition_amount = number_format((basic_salary*addition_amount)/100,'2','.','');
                }
                if(amount_type =='hour'){
                    if(basic_salary=='' || basic_salary=='0'){
                        alert('Please enter basic salary.');
                        // $("#basic_salary").focus();
                        return false;
                    }
                    addition_amount =  number_format( (((basic_salary/total_number_of_day)/8)*2)*addition_amount, '2','.','');
                }
                if(amount_type =='day'){
                    if(total_payment=='' || total_payment=='0'){
                        alert('Please enter basic salary.');
                        // $("#basic_salary").focus();
                        return false;
                    }
                    addition_amount =  number_format( (total_payment/total_number_of_day)*addition_amount, '2','.','');
                }
                addition_item_array.push(addition_value);

                var markup = "<tr><td class='col-md-1'><button type='button' data-amount='"+addition_amount+"' data-id='"+prDetails_id+"' title='Remove' value='"+addition_value+"' class='btn btn-danger btn-xs delete_allowance_item'>X</button></td>" +
                    "<td><input type='hidden' class='selected_addition_id' name='selected_addition_id[]' value='"+addition_value+"'>" +
                    "<input type='hidden' name='selected_addition_name[]' value='"+addition_name+"'>" + addition_name +
                    "</td><td><input type='hidden' class='selected_addition_amount' name='selected_addition_amount[]' value='"+addition_amount+"'>" +
                    "<input type='hidden' name='selected_amount_type[]' value='"+amount_type+"'>" + addition_amount + "</td></tr>";


                if(prDetails_id){
                    var action = 'add_addition_item_into_json';
                    $.ajax({
                        type: 'POST',
                        url: RbsErpPayrollRun.ajaxurlpayrun,
                        data: {
                            'action': action,
                            'prDetailId': prDetails_id,
                            'addItemId': addition_value,
                            'addItemAmount': addition_amount
                        },success: function (response) {
                            $("table.allowance_items tbody").append(markup);
                            // $(".total_payment").html(parseFloat(total_payment)+parseFloat(addition_amount));

                            var total_pay_amount = parseFloat(total_payment)+parseFloat(addition_amount);
                            var total_deduction = $(".total_deduction").html();
                            $(".total_payment").html( number_format(total_pay_amount,'2','.',''));
                            $('.net_total').html(total_pay_amount-parseFloat(total_deduction));

                            addition_item_array = [''];
                        }
                    });
                }

            });

            // Find and remove selected table rows
            $("table.allowance_items").on('click','.delete_allowance_item', function(){
                var element = $(this);
                var removeItem = $(this).val();
                var itemAmount = $(this).data('amount');
                var prDetailId = $(this).data('id');
                var total_payment = $(".total_payment").html();

               if(confirm('Are you sure remove this item?')) {

                   addition_item_array = jQuery.grep(addition_item_array, function(value) {
                       return value != removeItem;
                   });
                   if(prDetailId){
                       var action = 'update_addition_json_item';
                       $.ajax({
                           type: 'POST',
                           url: RbsErpPayrollRun.ajaxurlpayrun,
                           data: {
                               'action': action,
                               'prDetailId': prDetailId,
                               'removeItem': removeItem
                           },success: function (response) {
                               $(element).parents("tr").remove();
                               var total_pay_amount = parseFloat(total_payment)-parseFloat(itemAmount);
                               var total_deduction = $(".total_deduction").html();
                               $(".total_payment").html(total_pay_amount);
                               $('.net_total').html(total_pay_amount-parseFloat(total_deduction));
                           }
                       });
                   }else {
                       $(element).parents("tr").remove();
                   }



               }
            });

            $(".add_allowance_description").click(function(){
                var prDetails_id = $(".prDetails_id").val();
                if(prDetails_id==''){
                    alert('please select employee');
                    $(".selected_employee_id").focus();
                    return false;
                }

                var allowance_description = $(".allowance_description").val();
                if(allowance_description==''){
                    alert('Please enter allowance description');
                    return false;
                }

                if(prDetails_id){
                    var action = 'add_addition_description';
                    $.ajax({
                        type: 'POST',
                        url: RbsErpPayrollRun.ajaxurlpayrun,
                        data: {
                            'action': action,
                            'prDetailId': prDetails_id,
                            'allowance_description': allowance_description,
                        },success: function (response) {
                            $('.show_allowance_description').html(allowance_description);
                            $(".allowance_description").val('');
                        }
                    });
                }

            });

            $(document).on('click','.row_hide',function () {
              var amount =  $(this).data('amount');
              var grand_total =  $('.grand_total').val();
              var calculate_grand_amount = grand_total-amount;
                $('.grand_total').val(calculate_grand_amount);
                $('.grand_total_amount').text( number_format(calculate_grand_amount, '2','.',','));

              $(this).closest('tr').remove();

            });
            $(document).on('click','.table_hide',function () {
              var amount =  $(this).data('amount');

              $(this).closest('table').remove();

            });
            $(document).on('change','#payment_type',function () {
              var elementValue =  $(this).val();
                $('.bonus_name_section').hide();
                $(".bonus_name").prop("checked", false);
              if(elementValue==='BONUS_PAYMENT'){
                $('.bonus_name_section').show();
              }

            });

        },

        onChangeloadEmployeeInfo: function() {
            var addition_item_array = [];
            var deduction_item_array = [];
            var self = $(this),
                employee_id = self.val(),
                pay_calendar_run_id = $('#current_pay_calendar_run_id' ).val();

            if(employee_id=='' || employee_id=='-1') {
                return false;
            }
            var action = 'on_change_load_employee_payment_info';
            $.ajax({
                type: 'POST',
                url: RbsErpPayrollRun.ajaxurlpayrun,
                data: {
                    'action': action,
                    'employee_id': employee_id,
                    'pay_calendar_run_id': pay_calendar_run_id
                },success: function (response) {
                    $('.prDetails_id').val(response.prDetails_id);
                    $('.basic_payment').html(response.basic_salary);
                    $('.basic_payment_hidden').val(response.payroll_info_basic_salary);
                    $('.allowance_items').html(response.allowance);
                    $('.total_payment').html(response.total_payment);
                    $('.deduction_items').html(response.deduction);
                    $('.total_deduction').html(response.total_deduction);
                    $('.net_total').html(response.net_total);
                    $('.show_allowance_description').html(response.allowance_description);
                    $('.show_deduction_description').html(response.deduction_description);
                    if(response.absence_day>1){
                        var absence_day = response.absence_day + ' days'
                    }else {
                            absence_day = response.absence_day + ' day'
                    }
                    $('.absence_day').html(absence_day);
                    $('.absence_day_hidden').val(response.absence_day);

                    $('.delete_allowance_item').each(function(){
                        addition_item_array.push(this.value);
                    });

                    $('.delete_deduction_item').each(function(){
                        deduction_item_array.push(this.value);
                    });
                }
            });



        },

        initializededuction: function() {

            // $( '.deduction_name').on( 'change', this.onChangeLoadDeductionInfo );



            var deduction_item_array = [];

            $(".add-deduction-item").click(function(){

                var deduction_item_array = [];

                $('.delete_deduction_item').each(function(){
                    deduction_item_array.push(this.value);
                });
                var basic_salary = $(".basic_payment_hidden").val();
                var prDetails_id = $(".prDetails_id").val();
                var deduction_value = $(".deduction_name").val();
                var deduction_name = $(".deduction_name option:selected").text();
                var deduction_amount = $(".deduction_amount").val();
                var amount_type = $(".deduction_amount_type").val();
                if(deduction_value=='-1'){
                   alert('Please select deduction item');
                   return false;
                }
                if(deduction_amount==''){
                    alert('Please enter deduction amount');
                    return false;
                }

                if( $.inArray(deduction_value, deduction_item_array) !== -1 ) {

                    alert("This deduction item already added.");
                    return false;
                }
                if(amount_type=='percentage'){
                    if(basic_salary=='' || basic_salary=='0'){
                        alert('Please enter basic salary.');
                        return false;
                    }
                    deduction_amount = number_format((basic_salary*deduction_amount)/100, '2','.','');
                }

                deduction_item_array.push(deduction_value);


                var markup = "<tr><td class='col-md-1'><button type='button' data-amount='"+deduction_amount+"' data-id='"+prDetails_id+"'  title='Remove' value='"+deduction_value+"' class='btn btn-danger btn-xs delete_deduction_item'>X</button></td>" +
                    "<td>" + deduction_name +"</td>" +
                    "<td>" + deduction_amount +"</td></tr>";


                if(prDetails_id){
                    var action = 'add_deduction_item_into_json';
                    $.ajax({
                        type: 'POST',
                        url: RbsErpPayrollRun.ajaxurlpayrun,
                        data: {
                            'action': action,
                            'prDetailId': prDetails_id,
                            'addItemId': deduction_value,
                            'addItemAmount': deduction_amount
                        },success: function (response) {

                            $("table.deduction_items tbody").append(markup);
                            var total_deduction=0;
                            $('.delete_deduction_item').each(function(){
                                total_deduction+=parseFloat($(this).data('amount'));
                            });
                            $(".total_deduction").html(total_deduction);

                            var total_pay_amount = $(".total_payment").html();
                            $('.net_total').html(parseFloat(total_pay_amount)-parseFloat(total_deduction));
                        }
                    });
                }
            });

            // Find and remove selected table rows
            $("table.deduction_items").on('click',".delete_deduction_item", function(){

                var element = $(this);
                var removeItem = $(this).val();
                var itemAmount = $(this).data('amount');
                var prDetailId = $(this).data('id');
                var total_deduction = $(".total_deduction").html();

                if(confirm('Are you sure remove this item?')) {

                    deduction_item_array = jQuery.grep(deduction_item_array, function(value) {
                        return value != removeItem;
                    });
                    if(prDetailId){
                        var action = 'delete_deduction_json_item';
                        $.ajax({
                            type: 'POST',
                            url: RbsErpPayrollRun.ajaxurlpayrun,
                            data: {
                                'action': action,
                                'prDetailId': prDetailId,
                                'removeItem': removeItem
                            },success: function (response) {
                                $(element).parents("tr").remove();
                                var total_deduction_amount = parseFloat(total_deduction)-parseFloat(itemAmount);
                                $(".total_deduction").html(total_deduction_amount);

                                var total_pay_amount = $(".total_payment").html();
                                $('.net_total').html(parseFloat(total_pay_amount)-parseFloat(total_deduction_amount));
                            }
                        });
                    }else {
                        $(element).parents("tr").remove();
                    }



                }
            });


            $(".add_deduction_description").click(function(){
                var prDetails_id = $(".prDetails_id").val();
                if(prDetails_id==''){
                    alert('please select employee');
                    $(".selected_employee_id").focus();
                    return false;
                }

                var deduction_description = $(".deduction_description").val();
                if(deduction_description==''){
                    alert('Please enter deduction description');
                    return false;
                }

                if(prDetails_id){
                    var action = 'add_deduction_description';
                    $.ajax({
                        type: 'POST',
                        url: RbsErpPayrollRun.ajaxurlpayrun,
                        data: {
                            'action': action,
                            'prDetailId': prDetails_id,
                            'deduction_description': deduction_description,
                        },success: function (response) {
                            $('.show_deduction_description').html(deduction_description);
                            $(".deduction_description").val('');
                        }
                    });
                }

            });

        },

        initializeabsenceday: function() {
            $(".add_number_of_absence_day").click(function(){
                var prDetails_id = $(".prDetails_id").val();
                if(prDetails_id==''){
                    alert('Please select employee');
                    $(".selected_employee_id").focus();
                    return false;
                }
                var total_number_of_day = $('.total_number_of_day_in_month').val();
                var number_of_absence_day = $('.number_of_absence_day').val();

                if(prDetails_id){
                    var action = 'update_number_of_absence_day';
                    $.ajax({
                        type: 'POST',
                        url: RbsErpPayrollRun.ajaxurlpayrun,
                        data: {
                            'action': action,
                            'prDetailId': prDetails_id,
                            'total_number_of_day': total_number_of_day,
                            'numberOfAbsence': number_of_absence_day
                        },success: function (response) {

                            if(response.absence_day>1){
                                var absence_day = response.absence_day + ' days'
                            }else {
                                absence_day = response.absence_day + ' day'
                            }
                            $('.absence_day').html(absence_day);
                            $('.absence_day_hidden').val(response.absence_day);
                            alert('Your request has been applied.')

                        }
                    });
                }else {
                    $(element).parents("tr").remove();
                }

            });

        },

        initializeAdjustedAmount: function() {
            $(".add_adjusted_amount_per_transaction").click(function(){
                var prDetails_id = $(".prDetails_id").val();
                var employee_id = $('.selected_employee_id'). val();

                if(employee_id=='' || employee_id=='-1') {
                    alert('Please select employee');
                    $(".selected_employee_id").focus();
                    return false;
                }
                if(prDetails_id==''){
                    alert('Please select employee');
                    $(".selected_employee_id").focus();
                    return false;
                }
                var adjusted_amount_per_transaction = $('.adjusted_amount_per_transaction').val();

                if(!isNumber(adjusted_amount_per_transaction) || adjusted_amount_per_transaction < 0 || adjusted_amount_per_transaction ==''){
                    alert('Please enter a number');
                    $(".adjusted_amount_per_transaction").focus();
                    return false;
                }

                if(prDetails_id){
                    var action = 'update_adjusted_amount_per_transaction';
                    $.ajax({
                        type: 'POST',
                        url: RbsErpPayrollRun.ajaxurlpayrun,
                        data: {
                            'action': action,
                            'prDetailId': prDetails_id,
                            'adjusted_amount_per_transaction': adjusted_amount_per_transaction,
                            'employee_id': employee_id,
                        },success: function (response) {
                                alert('Your request has been accepted');

                        }
                    });
                }

            });

        }






    };
    function isNumber(n) {
        return !isNaN(parseFloat(n)) && isFinite(n);
    }
    function number_format (number, decimals, dec_point, thousands_sep) {
        var n = number, prec = decimals;

        var toFixedFix = function (n,prec) {
            var k = Math.pow(10,prec);
            return (Math.round(n*k)/k).toString();
        };

        n = !isFinite(+n) ? 0 : +n;
        prec = !isFinite(+prec) ? 0 : Math.abs(prec);
        var sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep;
        var dec = (typeof dec_point === 'undefined') ? '.' : dec_point;

        var s = (prec > 0) ? toFixedFix(n, prec) : toFixedFix(Math.round(n), prec);
        //fix for IE parseFloat(0.55).toFixed(0) = 0;

        var abs = toFixedFix(Math.abs(n), prec);
        var _, i;

        if (abs >= 1000) {
            _ = abs.split(/\D/);
            i = _[0].length % 3 || 3;

            _[0] = s.slice(0,i + (n < 0)) +
                _[0].slice(i).replace(/(\d{3})/g, sep+'$1');
            s = _.join(dec);
        } else {
            s = s.replace('.', dec);
        }

        var decPos = s.indexOf(dec);
        if (prec >= 1 && decPos !== -1 && (s.length-decPos-1) < prec) {
            s += new Array(prec-(s.length-decPos-1)).join(0)+'0';
        }
        else if (prec >= 1 && decPos === -1) {
            s += dec+new Array(prec).join(0)+'0';
        }
        return s;
    }

    $(function() {
        RBS_ERP_PAYROLL_RUN.initialize();
        RBS_ERP_PAYROLL_RUN.initializededuction();
        RBS_ERP_PAYROLL_RUN.initializeabsenceday();
        RBS_ERP_PAYROLL_RUN.initializeAdjustedAmount();
    });

})(jQuery, this);