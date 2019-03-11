$(function() {
    // --------------------------------------------------------------------------------------------
    // Initialisierung der CSS-Komponenten
    // --------------------------------------------------------------------------------------------
    $('.sidenav').sidenav();
    $('.modal').modal({
        onCloseEnd: function() {
            $('#lasertagForm').attr('current-record', 'none');
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
        url: "lasertag.php",
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
        $('.modal-title').html('Eintrag erstellen');
        $('#reset').click();
    });

    // --------------------------------------------------------------------------------------------
    // Funktion fürs (Neu) laden
    // --------------------------------------------------------------------------------------------
    function eintraegeLaden(data) {

        $('.lasertag-list').html($('#lasertag_liste_template'));
        $('#lasertag_liste_template').show();
        data.forEach(element => {
            var template = $('#lasertag_liste_template').outerHTML();
            var date = element.lasertag_date.split('-');
            var dateFormatted = `${date[2]}.${date[1]}.${date[0]}`;
            element.lasertag_date = dateFormatted;

            var time = element.lasertag_time.split(':');
            var timeFormatted = `${time[0]}:${time[1]}`;
            element.lasertag_time = timeFormatted;

            var datensatz = Mustache.to_html(template, element);
            $(datensatz).appendTo('.lasertag-list').prop('id', `lasertag_liste_${element.id}`);
            $(`#lasertag_liste_${element.id}`).find('.name>i').css('color', element.farbe);
        });

        $('#lasertag_liste_template').hide();

        // --------------------------------------------------------------------------------------------
        // Bearbeiten eines Eintrags
        // --------------------------------------------------------------------------------------------
        $('.edit-btn').on('click', function() {
            var lasertag_id = $(this).parents('.lasertag-list-content').attr('data-id');
            $('#lasertagForm').attr('current-record', lasertag_id);
            var modal = M.Modal.getInstance($('#modal'));
            $('.modal-title').html('Eintrag bearbeiten');

            $('#lasertag_date').siblings('label').addClass('active');
            $('#lasertag_time').siblings('label').addClass('active');
            $('#lasertag_person').siblings('label').addClass('active');
            $('#lasertag_telephone').siblings('label').addClass('active');
            $('#lasertag_name').siblings('label').addClass('active');
            $('#lasertag_company').siblings('label').addClass('active');

            $.ajax({
                type: "GET",
                url: "lasertag.php",
                data: {
                    action: 'getdatabyid',
                    lasertag_id: lasertag_id
                },
                dataType: "json",
                success: function(response) {
                    $('#lasertag_date').val(response[0].lasertag_date);
                    $('#lasertag_time').val(response[0].lasertag_time);
                    $('#lasertag_person').val(response[0].lasertag_person);
                    $('#lasertag_telephone').val(response[0].lasertag_telephone);
                    $('#lasertag_name').val(response[0].lasertag_name);
                    $('#lasertag_company').val(response[0].lasertag_company);

                    modal.open();
                }
            });
        });

        // --------------------------------------------------------------------------------------------
        // Löschen eines Eintrags
        // --------------------------------------------------------------------------------------------
        $('.delete-btn').on('click', function() {
            var lasertag_id = $(this).parents('.lasertag-list-content').attr('data-id');
            $.ajax({
                type: "DELETE",
                url: "lasertag.php",
                data: {
                    lasertag_id: lasertag_id,
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
                html: `Eintrag mit Id ${lasertag_id} Gelöscht`
            });
        });
    }

    // --------------------------------------------------------------------------------------------
    // Formular absenden
    // --------------------------------------------------------------------------------------------
    $('#lasertagForm').submit(function(e) {
        var lasertag_date = $('#lasertag_date').val();
        var lasertag_time = $('#lasertag_time').val();
        var lasertag_person = $('#lasertag_person').val();
        var lasertag_telephone = $('#lasertag_telephone').val();
        var lasertag_name = $('#lasertag_name').val();
        var lasertag_company = $('#lasertag_company').val();

        var lasertag_id = $('#lasertagForm').attr('current-record');

        if (lasertag_id == 'none') {
            // --------------------------------------------------------------------------------------------
            // Eintrag erstellen
            // --------------------------------------------------------------------------------------------
            $.ajax({
                type: 'POST',
                url: "lasertag.php",
                data: {
                    action: 'postdata',
                    lasertag_date: lasertag_date,
                    lasertag_time: lasertag_time,
                    lasertag_person: lasertag_person,
                    lasertag_telephone: lasertag_telephone,
                    lasertag_name: lasertag_name,
                    lasertag_company: lasertag_company
                },
                dataType: 'json',
                success: function(data) {
                    eintraegeLaden(data);
                    $('#reset').click();
                    M.modal.getInstance($('#modal')).close();
                    M.toast({
                        html: `${lasertag_time} erstellt`
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
                url: "lasertag.php",
                data: {
                    action: 'putdata',
                    lasertag_id: lasertag_id,
                    lasertag_date: lasertag_date,
                    lasertag_time: lasertag_time,
                    lasertag_person: lasertag_person,
                    lasertag_telephone: lasertag_telephone,
                    lasertag_name: lasertag_name,
                    lasertag_company: lasertag_company
                },
                dataType: 'json',
                success: function(data) {
                    eintraegeLaden(data);
                    $('#reset').click();
                    M.modal.getInstance($('#modal')).close();
                    M.toast({
                        html: `${lasertag_time} bearbeitet`
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