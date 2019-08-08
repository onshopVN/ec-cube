jQuery(document).ready(function() {
    let $postalCode = jQuery('.p-postal-code'),
        $region = jQuery('.p-region-id'),
        $address = jQuery('.p-street-address');
    $postalCode.on('change', function (e) {
        let pref = '',
            address = '',
            code = jQuery(this).val();

        $.ajax({
            url: '//mabuuchinh.vn/API/serviceApi/v1/MBC',
            data: JSON.stringify({ textsearch: code }),
            dataType: 'json',
            type: 'POST',
            contentType: 'application/json; charset=utf-8',
            beforeSend () {
                $region.attr('disabled', 'disabled');
                $address.attr('disabled', 'disabled');
            },
            success(data) {
                if (data.length > 0 && data[0].hasOwnProperty('name')) {
                    let idx = data[0].name.indexOf('tỉnh');
                    if (idx) {
                        pref = data[0].name.slice(idx).replace('tỉnh ', '');
                        address = data[0].name.substring(0, idx).replace(code + ' - ', '');
                    }
                    if (pref) {
                        let prefSelected = jQuery('.p-region-id > option:contains("'+pref+'")').val();
                        if (prefSelected) {
                            $region.val(prefSelected);
                        }
                    }
                    if (address) {
                        $address.val(address);
                    }
                }
            },
            complete () {
                $region.attr('disabled', null);
                $address.attr('disabled', null);
            }
        });
    });
});