@layout('layouts/main_fluid')

@section('content')
    <div class="row-fluid">
        @include('main/left')
        <div class="span9">
            <br/>

            <div class="row-fluid">
                <div class="span12">
                    <h3>Softland - OLGA</h3>
                    <label for="startDate">Periodo: </label>
                    <input name="startDate" id="startDate" class="date-picker"/>
                    <div class="row-fluid">
                        <div id="dtes" class="span12">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
@parent
{{-- <style> --}}
.ui-datepicker-calendar { display: none; }

.table td.column-neto {text-align: right;}
.table td.column-olga {text-align: right;}
.table td.column-delta {text-align: right;}
.table td {cursor:hand;cursor:pointer;}
input.number_filter {width: 80px;}
select.select_filter {width: 80px;}

{{-- </style> --}}
@endsection

@section('js')
@parent
$(function () {
    $('select').on('change', function() {
        alert( this.value ); // or $(this).val()
    });

    $('.date-picker').datepicker(
            {
                dateFormat: "mm/yy",
                changeMonth: true,
                changeYear: true,
                showButtonPanel: true,
                onClose: function (dateText, inst) {

                    function isDonePressed() {
                        return ($('#ui-datepicker-div').html().indexOf('ui-datepicker-close ui-state-default ui-priority-primary ui-corner-all ui-state-hover') > -1);
                    }

                    if (isDonePressed()) {
                        var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                        var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                        $(this).datepicker('setDate', new Date(year, month, 1)).trigger('change');
                        $.ajax({
                            type: 'GET',
                            url: "/main/dtesOlgaData/"+year+"/"+(parseInt(month)+1),
                            dataType: "html",
                            success: function(data) {
                                $('#dtes').html(data);
                            }
                        });
                        $('.date-picker').focusout()//Added to remove focus from datepicker input box on selecting date
                    }
                },
                beforeShow: function (input, inst) {

                    inst.dpDiv.addClass('month_year_datepicker')

                    if ((datestr = $(this).val()).length > 0) {
                        year = datestr.substring(datestr.length - 4, datestr.length);
                        month = datestr.substring(0, 2);
                        $(this).datepicker('option', 'defaultDate', new Date(year, month - 1, 1));
                        $(this).datepicker('setDate', new Date(year, month - 1, 1));
                        $(".ui-datepicker-calendar").hide();
                    }
                }
            })
});
@endsection
