/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
require([
    'underscore',
    'jquery'
], function (_, jquery) {

    var mailgunKey = jquery('#system_smtp_mailgun_key');
    var mailgunDomains = jquery('#system_smtp_mailgun_domain');

    var newOptions = {};

    mailgunKey.change(function() {

        var mailgunKeyValue = mailgunKey.val();

        new Ajax.Request('/mailgun/ajax/domains', {
            evalScripts: true,
            parameters: {'form_key': FORM_KEY, 'mailgun_key': mailgunKeyValue},
            onSuccess: function(transport) {

                var result = transport.responseText.evalJSON();
                mailgunDomains.empty(); // remove old options

                var jsonDomains = result.domains;
                FORM_KEY = result.form_key;

                jquery.each(jsonDomains, function(key,value) {
                    mailgunDomains.append(jquery("<option></option>")
                        .attr("value", value.label).text(value.value));
                });


            }.bind(this),
            onFailure: function(transport) {
                mailgunDomains.empty();
            }
        });

    });

});
