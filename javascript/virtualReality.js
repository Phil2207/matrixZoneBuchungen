$(function() {
    // --------------------------------------------------------------------------------------------
    // Initialisierung der CSS-Komponenten
    // --------------------------------------------------------------------------------------------
    $('.sidenav').sidenav();
    $('.modal').modal({
        onCloseEnd: function() {
            $('#virtualRealityForm').attr('current-record', 'none');
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
        url: "virtualReality.php",
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
        $('.modal-title').html('virtualRealitybuchung erstellen');
        $('#reset').click();
    });

    // --------------------------------------------------------------------------------------------
    // Funktion fürs (Neu) laden
    // --------------------------------------------------------------------------------------------
    function eintraegeLaden(data) {

        $('.virtualReality-list').html($('#virtualReality_liste_template'));
        $('#virtualReality_liste_template').show();
        data.forEach(element => {
            var template = $('#virtualReality_liste_template').outerHTML();
            var date = element.virtualReality_date.split('-');
            var dateFormatted = `${date[2]}.${date[1]}.${date[0]}`;
            element.virtualReality_date = dateFormatted;

            var time = element.virtualReality_time.split(':');
            var timeFormatted = `${time[0]}:${time[1]}`;
            element.virtualReality_time = timeFormatted;

            var datensatz = Mustache.to_html(template, element);
            $(datensatz).appendTo('.virtualReality-list').prop('id', `virtualReality_liste_${element.id}`);
            $(`#virtualReality_liste_${element.id}`).find('.name>i').css('color', element.farbe);
        });

        $('#virtualReality_liste_template').hide();

        // --------------------------------------------------------------------------------------------
        // Bearbeiten eines Eintrags
        // --------------------------------------------------------------------------------------------
        $('.edit-btn').on('click', function() {
            var virtualReality_id = $(this).parents('.virtualReality-list-content').attr('data-id');
            $('#virtualRealityForm').attr('current-record', virtualReality_id);
            var modal = M.Modal.getInstance($('#modal'));
            $('.modal-title').html('Eintrag bearbeiten');

            $('#virtualReality_date').siblings('label').addClass('active');
            $('#virtualReality_time').siblings('label').addClass('active');
            $('#virtualReality_person').siblings('label').addClass('active');
            $('#virtualReality_telephone').siblings('label').addClass('active');
            $('#virtualReality_name').siblings('label').addClass('active');
            $('#virtualReality_company').siblings('label').addClass('active');

            $.ajax({
                type: "GET",
                url: "virtualReality.php",
                data: {
                    action: 'getdatabyid',
                    virtualReality_id: virtualReality_id
                },
                dataType: "json",
                success: function(response) {
                    $('#virtualReality_date').val(response[0].virtualReality_date);
                    $('#virtualReality_time').val(response[0].virtualReality_time);
                    $('#virtualReality_person').val(response[0].virtualReality_person);
                    $('#virtualReality_telephone').val(response[0].virtualReality_telephone);
                    $('#virtualReality_name').val(response[0].virtualReality_name);
                    $('#virtualReality_company').val(response[0].virtualReality_company);

                    modal.open();
                }
            });
        });

        // --------------------------------------------------------------------------------------------
        // Löschen eines Eintrags
        // --------------------------------------------------------------------------------------------
        $('.delete-btn').on('click', function() {
            var virtualReality_id = $(this).parents('.virtualReality-list-content').attr('data-id');
            $.ajax({
                type: "DELETE",
                url: "virtualReality.php",
                data: {
                    virtualReality_id: virtualReality_id,
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
                html: `Eintrag mit Id ${virtualReality_id} Gelöscht`
            });
        });
    }

    // --------------------------------------------------------------------------------------------
    // Formular absenden
    // --------------------------------------------------------------------------------------------
    $('#virtualRealityForm').submit(function(e) {
        var virtualReality_date = $('#virtualReality_date').val();
        var virtualReality_time = $('#virtualReality_time').val();
        var virtualReality_person = $('#virtualReality_person').val();
        var virtualReality_telephone = $('#virtualReality_telephone').val();
        var virtualReality_name = $('#virtualReality_name').val();
        var virtualReality_company = $('#virtualReality_company').val();

        var virtualReality_id = $('#virtualRealityForm').attr('current-record');

        if (virtualReality_id == 'none') {
            // --------------------------------------------------------------------------------------------
            // Eintrag erstellen
            // --------------------------------------------------------------------------------------------
            $.ajax({
                type: 'POST',
                url: "virtualReality.php",
                data: {
                    action: 'postdata',
                    virtualReality_date: virtualReality_date,
                    virtualReality_time: virtualReality_time,
                    virtualReality_person: virtualReality_person,
                    virtualReality_telephone: virtualReality_telephone,
                    virtualReality_name: virtualReality_name,
                    virtualReality_company: virtualReality_company
                },
                dataType: 'json',
                success: function(data) {
                    eintraegeLaden(data);
                    $('#reset').click();
                    M.Modal.getInstance($('#modal')).close();
                    M.toast({
                        html: `${virtualReality_time} erstellt`
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
                url: "virtualReality.php",
                data: {
                    action: 'putdata',
                    virtualReality_id: virtualReality_id,
                    virtualReality_date: virtualReality_date,
                    virtualReality_time: virtualReality_time,
                    virtualReality_person: virtualReality_person,
                    virtualReality_telephone: virtualReality_telephone,
                    virtualReality_name: virtualReality_name,
                    virtualReality_company: virtualReality_company
                },
                dataType: 'json',
                success: function(data) {
                    eintraegeLaden(data);
                    $('#reset').click();
                    M.Modal.getInstance($('#modal')).close();
                    M.toast({
                        html: `${virtualReality_time} bearbeitet`
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