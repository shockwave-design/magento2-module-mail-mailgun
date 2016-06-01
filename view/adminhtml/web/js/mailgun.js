/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
require([
    'jquery'
], function ($) {

    $(document).ready(function () {
        var mailgunKey = $('#system_smtp_mailgun_key');
        var mailgunDomains = $('#system_smtp_mailgun_domain');

        var newOptions = {};

        mailgunKey.on('blur', function () {
            var mailgunKeyValue = mailgunKey.val();

            new Ajax.Request('/mailgun/ajax/domains', {
                evalScripts: true,
                parameters: {'form_key': FORM_KEY, 'mailgun_key': mailgunKeyValue},
                onSuccess: function (transport) {

                    var result = transport.responseText.evalJSON();
                    mailgunDomains.empty(); // remove old options

                    var jsonDomains = result.domains;
                    FORM_KEY = result.form_key;

                    $.each(jsonDomains, function (key, value) {
                        mailgunDomains.append($("<option></option>")
                            .attr("value", value.label).text(value.value));
                    });


                }.bind(this),
                onFailure: function (transport) {
                    mailgunDomains.empty();
                }
            });
        });
    });
});
