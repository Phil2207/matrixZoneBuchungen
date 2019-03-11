$(function() {
    // --------------------------------------------------------------------------------------------
    // Initialisierung der CSS-Komponenten
    // --------------------------------------------------------------------------------------------
    $('.sidenav').sidenav();
    $('.modal').modal({
        onCloseEnd: function() {
            $('#bowlingForm').attr('current-record', 'none');
            $('#reset').click();
        }
    });

    $('.date-year').html(`© ${new Date().getFullYear()} Phil`);

    $('.datepicker').datepicker({
        format: 'yyyy-mm-dd'
    });

    $('.timepicker').timepicker({
        twelveHour: false
    });

    // --------------------------------------------------------------------------------------------
    // Einträge werden geladen
    // --------------------------------------------------------------------------------------------
    $.ajax({
        type: "GET",
        url: "bowling.php",
        data: {
            action: 'getdata',
        },
        dataType: "json",
        success: function(data) {
            eintraegeLaden(data);
        },
        error: function(data) {
            var response = data.responseJSON;
            response.forEach(element => {
                M.toast({
                    html: `Fehler bei ${element}`
                })
            });
        }
    });

    $('.modal-trigger').on('click', function() {
        $('.modal-title').html('Bowlingbuchung erstellen');
        $('#reset').click();
    });

    // --------------------------------------------------------------------------------------------
    // Funktion fürs (Neu) laden
    // --------------------------------------------------------------------------------------------
    function eintraegeLaden(data) {

        $('.bowling-list').html($('#bowling_liste_template'));
        $('#bowling_liste_template').show();
        data.forEach(element => {
            var template = $('#bowling_liste_template').outerHTML();
            var date = element.bowling_date.split('-');
            var dateFormatted = `${date[2]}.${date[1]}.${date[0]}`;
            element.bowling_date = dateFormatted;

            var time = element.bowling_time.split(':');
            var timeFormatted = `${time[0]}:${time[1]}`;
            element.bowling_time = timeFormatted;

            var datensatz = Mustache.to_html(template, element);
            $(datensatz).appendTo('.bowling-list').prop('id', `bowling_liste_${element.id}`);
            $(`#bowling_liste_${element.id}`).find('.name>i').css('color', element.farbe);
        });

        $('#bowling_liste_template').hide();

        // --------------------------------------------------------------------------------------------
        // Bearbeiten eines Eintrags
        // --------------------------------------------------------------------------------------------
        $('.edit-btn').on('click', function() {
            var bowling_id = $(this).parents('.bowling-list-content').attr('data-id');
            $('#bowlingForm').attr('current-record', bowling_id);
            var modal = M.Modal.getInstance($('#modal'));
            $('.modal-title').html('Eintrag bearbeiten');

            $('#bowling_date').siblings('label').addClass('active');
            $('#bowling_time').siblings('label').addClass('active');
            $('#bowling_person').siblings('label').addClass('active');
            $('#bowling_telephone').siblings('label').addClass('active');
            $('#bowling_name').siblings('label').addClass('active');
            $('#bowling_company').siblings('label').addClass('active');

            $.ajax({
                type: "GET",
                url: "bowling.php",
                data: {
                    action: 'getdatabyid',
                    bowling_id: bowling_id
                },
                dataType: "json",
                success: function(response) {
                    $('#bowling_date').val(response[0].bowling_date);
                    $('#bowling_time').val(response[0].bowling_time);
                    $('#bowling_person').val(response[0].bowling_person);
                    $('#bowling_telephone').val(response[0].bowling_telephone);
                    $('#bowling_name').val(response[0].bowling_name);
                    $('#bowling_company').val(response[0].bowling_company);

                    modal.open();
                }
            });
        });

        // --------------------------------------------------------------------------------------------
        // Löschen eines Eintrags
        // --------------------------------------------------------------------------------------------
        $('.delete-btn').on('click', function() {
            var bowling_id = $(this).parents('.bowling-list-content').attr('data-id');
            $.ajax({
                type: "DELETE",
                url: "bowling.php",
                data: {
                    bowling_id: bowling_id,
                    action: 'deletedata',
                },
                dataType: 'json',
                success: function(data) {
                    eintraegeLaden(data);
                },
                error: function(data) {
                    var response = data.responseJSON;
                    response.forEach(element => {
                        M.toast({
                            html: `Fehler bei ${element}`
                        })
                    });
                }
            });

            M.toast({
                html: `Eintrag mit Id ${bowling_id} Gelöscht`
            });
        });
    }

    // --------------------------------------------------------------------------------------------
    // Formular absenden
    // --------------------------------------------------------------------------------------------
    $('#bowlingForm').submit(function(e) {
        var bowling_date = $('#bowling_date').val();
        var bowling_time = $('#bowling_time').val();
        var bowling_person = $('#bowling_person').val();
        var bowling_telephone = $('#bowling_telephone').val();
        var bowling_name = $('#bowling_name').val();
        var bowling_company = $('#bowling_company').val();

        var bowling_id = $('#bowlingForm').attr('current-record');

        if (bowling_id == 'none') {
            // --------------------------------------------------------------------------------------------
            // Eintrag erstellen
            // --------------------------------------------------------------------------------------------
            $.ajax({
                type: 'POST',
                url: "bowling.php",
                data: {
                    action: 'postdata',
                    bowling_date: bowling_date,
                    bowling_time: bowling_time,
                    bowling_person: bowling_person,
                    bowling_telephone: bowling_telephone,
                    bowling_name: bowling_name,
                    bowling_company: bowling_company
                },
                dataType: 'json',
                success: function(data) {
                    eintraegeLaden(data);
                    $('#reset').click();
                    M.Modal.getInstance($('#modal')).close();
                    M.toast({
                        html: `${bowling_time} erstellt`
                    });
                },
                error: function(data) {
                    var response = data.responseJSON;
                    response.forEach(element => {
                        M.toast({
                            html: `Fehler bei ${element}`
                        });
                        $(`#${element}`).addClass('invalid');
                    });
                }
            });
        } else {
            // --------------------------------------------------------------------------------------------
            // Eintrag bearbeiten
            // --------------------------------------------------------------------------------------------
            $.ajax({
                type: 'PUT',
                url: "bowling.php",
                data: {
                    action: 'putdata',
                    bowling_id: bowling_id,
                    bowling_date: bowling_date,
                    bowling_time: bowling_time,
                    bowling_person: bowling_person,
                    bowling_telephone: bowling_telephone,
                    bowling_name: bowling_name,
                    bowling_company: bowling_company
                },
                dataType: 'json',
                success: function(data) {
                    eintraegeLaden(data);
                    $('#reset').click();
                    M.Modal.getInstance($('#modal')).close();
                    M.toast({
                        html: `${bowling_time} bearbeitet`
                    });
                },
                error: function(data) {
                    var response = data.responseJSON;
                    response.forEach(element => {
                        M.toast({
                            html: `Fehler bei ${element}`
                        });
                        $(`#${element}`).addClass('invalid');
                    });
                }
            });
        }

        e.preventDefault();
    });
});

jQuery.fn.extend({
    outerHTML: function() {
        return jQuery('<div />').append(this.eq(0).clone()).html();
    }
});