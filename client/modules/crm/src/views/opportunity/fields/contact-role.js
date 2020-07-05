

Espo.define('crm:views/opportunity/fields/contact-role', 'views/fields/enum', function (Dep) {

    return Dep.extend({

        searchTypeList: ['anyOf', 'noneOf'],

        setup: function () {
            this.params.options = this.getMetadata().get('entityDefs.Contact.fields.opportunityRole.options');
            this.params.translation = 'Contact.options.opportunityRole';

            Dep.prototype.setup.call(this);
        }

    });

});
