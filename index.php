<?php
// Started By : Nihal Sharma
$GLOBALS['apiuploadpath'] = 'http://127.0.0.1/Reimbursement%20Module/api/uploads/';
?>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reimbursement Module</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <style>
        #contact {
            text-align: center;
        }

        #contact>div {
            padding: 5px;
            margin: 5px;
        }

        #contact input {
            max-width: 100px;
            margin: 5px;
        }

        .reimbursmnet-day-data {
            text-align: left;
            margin-left: 25% !important;
        }

        .reimbursmnet-day-data-date {
            display: flex;
        }

        .reimbursmnet-day-data-date * {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 0 10px;
        }

        .single-day-popup-holder {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.7);
            transition: opacity 500ms;
            display: none;
        }

        .single-day-popup {
            margin: 70px auto;
            padding: 20px;
            background: #fff;
            border-radius: 5px;
            width: 30%;
            position: relative;
            transition: all 5s ease-in-out;
        }

        .single-day-popup h2 {
            margin-top: 0;
            color: #333;
            font-family: Tahoma, Arial, sans-serif;
        }

        .single-day-popup .close {
            position: absolute;
            top: 20px;
            right: 30px;
            transition: all 200ms;
            font-size: 30px;
            font-weight: bold;
            text-decoration: none;
            color: #333;
        }

        .single-day-popup .close:hover {
            color: #06D85F;
        }

        .single-day-popup .content {
            max-height: 30%;
            overflow: auto;
        }

        .single-day-popup label:last-of-type {
            padding: 0 10px;
            text-align: center;
            position: absolute;
            right: 50%;
        }

        .single-day-popup label:first-of-type {
            padding: 0 10px;
            text-transform: uppercase;
            right: auto;
            left: 0;
        }

        .single-day-popup a {
            padding: 0 10px;
            text-align: center;
            position: absolute;
            right: 50%;
        }

        @media screen and (max-width: 700px) {

            .single-day-popup {
                width: 70%;
            }
        }
    </style>
    <script>
        jQuery(document).ready(function() {

            jQuery(document).on('change','#purpose,#mode', function() {
                var val = jQuery(this).val();
                jQuery(this).next().hide();
                            if (val == 'other') {
                                jQuery(this).next().show();
                            }
            });

            jQuery('#selectmonth').on('change', function() {
                var val = jQuery(this).val();
                jQuery.ajax({
                    url: window.location.origin + window.location.pathname + "ajax.php",
                    data: {
                        date: val,
                        getdata: 1
                    },
                    async: true,
                    success: function(result) {
                        var results = JSON.parse(result);
                        var rdetail = results ? JSON.parse(results['rdetail']) : {
                            'null': "null"
                        };
                        jQuery('[name="rtype"][id="'+results['rtype']+'"]').prop("checked", true);;
                        var final_html = jQuery('<div></div>');
                        Object.keys(rdetail).forEach(element => {
                            var html = jQuery(jQuery('.reimbursmnet-day-data>div:last-child')[0].outerHTML);
                            var data_date = results ? element : jQuery('#selectmonth').val() + "-" + "01";
                            html.find('[name]').each(function() {
                                var name = jQuery(this).attr('name');
                                var val = results ? rdetail[element][name] : '';
                                if (name == "attachment[]") {
                                    jQuery(this).attr('value', "<?= $GLOBALS['apiuploadpath'] ?>"+ rdetail[element]['attachment']);
                                    return;
                                }
                                jQuery(this).attr('value', val);
                            });
                            html.attr('data-date', data_date);
                            html.find('.rembursment-date').text(data_date).attr('data-date', data_date);
                            final_html.append(html);
                        });

                        jQuery(".reimbursmnet-day-data").html(final_html.html());
                        jQuery(".reimbursmnet-day-data").find('select').each(function() {
                            var val = jQuery(this).attr('value');
                            jQuery(this).find("option[value='" + val + "']").prop('selected', true);
                        }).trigger('change');;
                    }
                });
            })
            jQuery(document).on("click", '.reimbursmnet-day-data-date', function() {
                event.preventDefault();
                var date = jQuery(this).find('.rembursment-date').attr('data-date');
                var val = jQuery('#selectmonth').val();
                jQuery.ajax({
                    url: window.location.origin + window.location.pathname + "ajax.php",
                    data: {
                        date: val,
                        getdata: 1,
                        singledate: date
                    },
                    async: true,
                    success: function(result) {
                        var results = JSON.parse(result);
                        var html = '<h2>' + date + '</h2>';
                        html += '<a class="close" href="#">&times;</a>';
                        Object.keys(results).forEach(element => {
                            html += '<div class="data-holder">';
                            html += '<label>' + element + '</label>';
                            if(element == 'attachment'){
                                html += '<a href="<?= $GLOBALS['apiuploadpath'] ?>' + results[element]+ '">Get File</a>';
                            }else{
                                html += '<label>' + results[element] + '</label>';
                            }
                            html += '</div>';
                        });
                        jQuery('.single-day-popup').html(html);
                        jQuery('.single-day-popup-holder').show();
                        jQuery('.single-day-popup .close').click(function() {
                            event.preventDefault();
                            jQuery('.single-day-popup-holder').hide();
                        });

                    }
                });

            });


            jQuery('#selectmonth').each(function() {
                var val = jQuery(this).val();
                if (!val) {
                    var dt = new Date();
                    var month = dt.getFullYear() + "-" + pad(dt.getMonth());
                    jQuery(this).val(month);
                }
            }).trigger('change');

            jQuery('.add-more').click(function() {
                var html = jQuery('.reimbursmnet-day-data>div:last-child').clone();
                var date = parseInt(html.attr('data-date').split("-")[2]) + 1;
                date = jQuery('#selectmonth').val() + '-' + pad(date);
                html.find('[name]').each(function() {
                    jQuery(this).val('');
                });
                html.attr('data-date', date);
                html.find('.rembursment-date').attr('data-date', date).text(date);
                jQuery('.reimbursmnet-day-data').append(html);
            });

            jQuery('#contact').submit(function() {
                event.preventDefault();
                var dat_div = jQuery('.reimbursmnet-day-data>div');
                var length = dat_div.length;
                var inputVal = {};

                var formdata = new FormData(jQuery(this)[0]);
                formdata.append('submit', 1);
                formdata.append('location', window.location.origin + window.location.pathname);
                dat_div.each(function() {
                    var date = jQuery(this).attr('data-date');
                    var inputValues = {};
                    jQuery(this).find('input,select').each(function() {
                        var type = jQuery(this).prop("type");
                        var name = jQuery(this).attr("name");
                        // checked radios/checkboxes
                        if ((type == "checkbox" || type == "radio") && this.checked) {
                            inputValues[name] = jQuery(this).val();
                        }
                        // all other fields, except buttons
                        else if (type != "button" && type != "submit" && type != "file") {
                            inputValues[name] = jQuery(this).val();
                        }
                    });

                    inputVal[date] = inputValues;

                });
                inputVal['rtype'] = jQuery('[name="rtype"]:checked').attr('id');;
                inputVal['month'] = jQuery(this).find('#selectmonth').val();
                formdata.append('inputs', JSON.stringify(inputVal));
                jQuery.ajax({
                    url: window.location.origin + window.location.pathname + "ajax.php",
                    type: "POST",
                    data: formdata,
                    processData: false,
                    contentType: false,
                    cache: false,
                    success: function(result) {
                        alert('Form Submited');
                    }
                });
            });
        });

        function pad(d) {
            return (d < 10) ? '0' + d.toString() : d.toString();
        }

        function change_class_date(jEle) {
            var val = jEle.val();
            jQuery('.data-holder').each(function() {
                var cur_date = jQuery(this).data('date').split('-')[2];
                var date = val + '-' + cur_date;
                jQuery(this).attr('data-date', date);
                jQuery(this).find('.rembursment-date').attr('data-date', date).text(date);
            });
        }
    </script>
</head>

<body>
    <div class="single-day-popup-holder">
        <div class="single-day-popup">

        </div>

    </div>
    <form id="contact" action="" method="post" enctype="multipart/form-data">
        <h3>Reimbursement Module </h3>
        <div>
            <label for="selectmonth">Select Month:</label>
            <input id="selectmonth" value="<?= $_REQUEST['date'] ?>" name="selectmonth" type="month" tabindex="1" autofocus>
        </div>
        <div>
            <input id="rconveyance" type="radio" name="rtype" tabindex="1">
            <label for="rconveyance">Conveyance</label>
            <input id="rhotel" type="radio" name="rtype" tabindex="1">
            <label for="rhotel">Hotel</label>
            <input id="rfood" type="radio" name="rtype" tabindex="1">
            <label for="rfood">Food</label>
        </div>
        <div>
            <input id="rmobile" type="radio" name="rtype" tabindex="1">
            <label for="rmobile">Mobile</label>
            <input id="rinternet" type="radio" name="rtype" tabindex="1">
            <label for="rinternet">Internet</label>
            <input id="rother" type="radio" name="rtype" tabindex="1">
            <label for="rother">Other</label>
        </div>
        <div class="reimbursmnet-day-data">

            <div class="data-holder" data-date="">
                <div class="reimbursmnet-day-data-date">
                    <label for="rembursment-date">Date</label>
                    <p name="rembursment-date" class="rembursment-date" data-date=""></p>
                </div>
                <div>
                    <label for="from">From</label>
                    <input placeholder="From" name="from" type="text" tabindex="1" required>
                    <label for="to">From</label>
                    <input placeholder="To" name="to" type="text" tabindex="1" required>
                    <label for="purpose">Purpose:</label>
                    <select name="purpose" id="purpose" required>
                        <option value="marketvisit">Market Visit</option>
                        <option value="othercity">Other City</option>
                        <option value="officevisit">Office Visit</option>
                        <option value="training">Training</option>
                        <option value="other">Other</option>
                    </select>
                    <input placeholder="Other"  name="purposeother" id="purposeother" type="text">
                    <label for="mode">Mode:</label>
                    <select name="mode" id="mode">
                        <option value="bike">Bike</option>
                        <option value="bus">Bus</option>
                        <option value="taxi">Taxi</option>
                        <option value="train">Train</option>
                        <option value="auto">Auto</option>
                        <option value="other">Other</option>
                    </select>
                    <input placeholder="Other"  name="modeother" id="modeother" type="text">
                </div>
                <div>
                    <label for="km">Km</label>
                    <input placeholder="KM" name="km" type="text" tabindex="1">
                    <label for="invno">InvNo</label>
                    <input placeholder="Invoice No." name="invno" type="text" tabindex="1">
                    <label for="amt">Amt</label>
                    <input placeholder="Amount" name="amt" type="text" tabindex="1" required>
                    <label for="attachment">Attachment</label>
                    <input name="attachment[]" type="file" tabindex="1">
                </div>
            </div>
        </div>

        <div>
            <input value="Add More" class="add-more" data-nxtdate="2" type="button" tabindex="1" required>
        </div>
        <div>
            <button name="submit" type="submit" id="contact-submit" data-submit="...Sending">Submit</button>
        </div>
    </form>

</body>

</html>