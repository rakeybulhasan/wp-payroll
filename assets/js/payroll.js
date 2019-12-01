/* jshint devel:true */
/* global wp */
/* global wpErp */
/* global RbsErpPayroll */

window.wperp = window.wperp || {};

(function($) {
    'use strict';

    var RBS_ERP_PAYROLL = {

        /**
         * Initialize the events
         *
         * @return {void}
         */
        initialize: function() {
            $( '.addition_name').on( 'change', this.onChangeloadAdditionInfo );

            $('.add_employee_pay_calendar').on('click', this.onClickLoadEmployeeInfoForPayCalendar);
            $('.remove_employee_from_pay_calendar').on('click', this.onClickRemoveEmployeeFromPayCalendar);


            var addition_item_array = [];
            $('.delete-row').each(function(){
                addition_item_array.push(this.value);
            });

            $(".add-row").click(function(){

                var basic_salary = $("#basic_salary").val();

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
                    if(basic_salary==''){
                        alert('Please enter basic salary.')
                        $("#basic_salary").focus();
                        return false;
                    }
                    var rate = '%'
                }else if (amount_type=='hour') {
                    rate = ' Hour'

                } else {
                    rate = ' TK.'
                }
                addition_item_array.push(addition_value);

                var markup = "<tr><td><input type='hidden' name='selected_addition_id[]' value='"+addition_value+"'>" +
                    "<input type='hidden' name='selected_addition_name[]' value='"+addition_name+"'>" + addition_name +
                    "</td><td><input type='hidden' name='selected_addition_amount[]' value='"+addition_amount+"'>" +
                    "<input type='hidden' name='selected_amount_type[]' value='"+amount_type+"'>" + addition_amount + rate+ "</td><td><button type='button' title='Remove' value='"+addition_value+"' class='btn btn-danger btn-xs delete-row'>X</button></td></tr>";
                $("table.selected_item tbody").append(markup);
            });

            // Find and remove selected table rows
            $("table.selected_item tbody").on('click',".delete-row", function(){
                var removeItem = $(this).val();
                var dataId = $(this).data('id');


               if(confirm('Are you sure remove this item?')) {
                   $(this).parents("tr").remove();
                   addition_item_array = jQuery.grep(addition_item_array, function(value) {
                       return value != removeItem;
                   });
                   if(dataId){
                       var action = 'remove_join_addition_item';
                       $.ajax({
                           type: 'POST',
                           url: RbsErpPayroll.ajaxurl,
                           data: {
                               'action': action,
                               'dataId': dataId
                           },success: function (response) {
                               $(addition_amount).val(response.addition_amount);
                               $(amount_type).val(response.amount_type);
                           }
                       });
                   }



               }
            });
            var rowCount = $('.pay_calendar_setup tbody input:checkbox:checked').length;
            $('.employee_count').html('('+rowCount+')');

            $('.pay_calendar_setup tbody').on('change','input[type="checkbox"]',function(){

                var countCheckedCheckboxes = $('.pay_calendar_setup tbody input[type="checkbox"]').filter(':checked').length;
                $('.employee_count').html('('+countCheckedCheckboxes+')');

            });


            //this.initializededuction;

        },

        onChangeloadAdditionInfo: function() {
            var self = $(this),
                addition_id = self.val(),
                addition_amount = $( '.addition_amount' ),
                amount_type = $( '.amount_type' );

            if(addition_id=='' || addition_id=='-1') {
                $(addition_amount).val(0);
                return false;
            }
            var action = 'on_change_load_addition_info';
            $.ajax({
                type: 'POST',
                url: RbsErpPayroll.ajaxurl,
                data: {
                    'action': action,
                    'addition_id': addition_id
                },success: function (response) {
                    $(addition_amount).val(response.addition_amount);
                    $(amount_type).val(response.amount_type);
                }
            });

        },

        initializededuction: function() {

            $( '.deduction_name').on( 'change', this.onChangeLoadDeductionInfo );

            var deduction_item_array = [];
            $('.deduction-delete-row').each(function(){
                deduction_item_array.push(this.value);
            });

            $(".add-row-deduction").click(function(){

                var basic_salary = $("#basic_salary").val();

                var deduction_value = $(".deduction_name").val();
                var deduction_name = $(".deduction_name option:selected").text();
                var deduction_amount = $(".deduction_amount").val();
                var amount_type = $(".deduction_amount_type").val();
                if(deduction_value=='-1'){
                   alert('Please select allowance item');
                   return false;
                }
                if(deduction_amount==''){
                    alert('Please enter allowance amount');
                    return false;
                }
                if( $.inArray(deduction_value, deduction_item_array) !== -1 ) {

                    alert("This deduction item already added.");
                    return false;
                }
                if(amount_type=='percentage'){
                    if(basic_salary==''){
                        alert('Please enter basic salary.')
                        $("#basic_salary").focus();
                        return false;
                    }
                    var rate = '%'
                }else if (amount_type=='hour') {
                    rate = ' Hour'

                }else {
                    rate = ' TK.'
                }
                deduction_item_array.push(deduction_value);

                var markup = "<tr><td><input type='hidden' name='selected_deduction_id[]' value='"+deduction_value+"'>" +
                    "<input type='hidden' name='selected_deduction_name[]' value='"+deduction_name+"'>" + deduction_name +
                    "</td><td><input type='hidden' name='selected_deduction_amount[]' value='"+deduction_amount+"'>" +
                    "<input type='hidden' name='selected_deduction_amount_type[]' value='"+amount_type+"'>" + deduction_amount + rate+ "</td><td><button type='button' title='Remove' value='"+deduction_value+"' class='btn btn-danger btn-xs deduction-delete-row'>X</button></td></tr>";
                $("table.selected_deduction_item tbody").append(markup);
            });

            // Find and remove selected table rows
            $("table.selected_deduction_item tbody").on('click',".deduction-delete-row", function(){
                var removeItem = $(this).val();
                var dataId = $(this).data('id');


               if(confirm('Are you sure remove this item?')) {
                   $(this).parents("tr").remove();
                   deduction_item_array = jQuery.grep(deduction_item_array, function(value) {
                       return value != removeItem;
                   });
                   if(dataId){
                       var action = 'remove_join_deduction_item';
                       $.ajax({
                           type: 'POST',
                           url: RbsErpPayroll.ajaxurl,
                           data: {
                               'action': action,
                               'dataId': dataId
                           },success: function (response) {
                               $(deduction_amount).val(response.deduction_amount);
                               $(amount_type).val(response.amount_type);
                           }
                       });
                   }



               }
            });

        },

        onChangeLoadDeductionInfo: function() {
            var self = $(this),
                deduction_id = self.val(),
                deduction_amount = $( '.deduction_amount' ),
                deduction_amount_type = $( '.deduction_amount_type' );

            if(deduction_id=='' || deduction_id=='-1') {
                $(deduction_amount).val(0);
                return false;
            }
            var action = 'on_change_load_deduction_info';
            $.ajax({
                type: 'POST',
                url: RbsErpPayroll.ajaxurl,
                data: {
                    'action': action,
                    'deduction_id': deduction_id
                },success: function (response) {
                    $(deduction_amount).val(response.deduction_amount);
                    $(deduction_amount_type).val(response.amount_type);
                }
            });

        },

        onClickLoadEmployeeInfoForPayCalendar: function() {
            var self = $(this),
                company_id = $( '#company_id' ).val(),
                branch_id = $( '#branch_id' ).val(),
                pay_calender_id = $( '#pay_calender_id' ).val();

            var action = 'on_click_load_employee_info';
            $.ajax({
                type: 'POST',
                url: RbsErpPayroll.ajaxurl,
                data: {
                    'action': action,
                    'company_id': company_id,
                    'branch_id': branch_id,
                    'pay_calender_id': pay_calender_id
                },success: function (response) {

                    $('.pay_calendar_setup tbody').html(response);
                    // var rowCount = $('.pay_calendar_setup tbody tr').length;
                    var rowCount = $('.pay_calendar_setup tbody input:checkbox:checked').length;
                    $('.employee_count').html('('+rowCount+')');
                }
            });

            $(".all_employee_check_uncheck").change(function () {
                $(".pay_calendar_setup tbody input:checkbox").prop('checked', $(this).prop("checked"));
                var rowCount = $('.pay_calendar_setup tbody input:checkbox:checked').length;
                $('.employee_count').html('('+rowCount+')');
            });

        },

        onClickRemoveEmployeeFromPayCalendar: function() {
            var self = $(this),
                pceId = $(self).val(),
                action = 'on_click_remove_employee_from_pay_calendar';

            if(confirm('Are you sure remove this item?')) {
                $(this).parents("tr").remove();
                $.ajax({
                    type: 'POST',
                    url: RbsErpPayroll.ajaxurl,
                    data: {
                        'action': action,
                        'pceId': pceId
                    },success: function (response) {

                        var rowCount = $('.pay_calendar_setup tbody input:checkbox:checked').length;
                        $('.employee_count').html('('+rowCount+')');
                    }
                });
            }


        }



    };

    $(function() {
        RBS_ERP_PAYROLL.initialize();
        RBS_ERP_PAYROLL.initializededuction();
    });

})(jQuery, this);